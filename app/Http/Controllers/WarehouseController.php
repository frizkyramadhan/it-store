<?php

namespace App\Http\Controllers;

use App\Imports\WarehouseImport;
use App\Models\Bouwheer;
use App\Models\Warehouse;

use Illuminate\Http\Request;
use function Laravel\Prompts\select;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_auth:admin');
    }

    public function index()
    {
        $title = 'Business Master Data';
        $subtitle = 'List of Warehouses';
        $warehouses = Warehouse::orderBy('warehouse_name', 'asc')->get();
        $bouwheers = Bouwheer::orderBy('bouwheer_name', 'asc')->get();

        return view('warehouses.index', compact('title', 'subtitle', 'warehouses', 'bouwheers'));
    }

    /**
     * Get the warehouses using the given request to datatables serverside.
     *
     * @param Request $request The request object.
     * @return mixed
     */
    public function getWarehouses(Request $request)
    {
        $warehouses = Warehouse::leftJoin('bouwheers', 'warehouses.bouwheer_id', '=', 'bouwheers.id')
            ->select('warehouses.*', 'bouwheers.bouwheer_name')
            ->orderBy('warehouse_name', 'asc');

        return datatables()->of($warehouses)
            ->addIndexColumn()
            ->addColumn('bouwheer_name', function ($warehouses) {
                return $warehouses->bouwheer_name;
            })
            ->addColumn('warehouse_name', function ($warehouses) {
                return $warehouses->warehouse_name;
            })
            ->addColumn('warehouse_location', function ($warehouses) {
                return $warehouses->warehouse_location;
            })
            ->addColumn('warehouse_type', function ($warehouses) {
                return $warehouses->warehouse_type;
            })
            ->addColumn('warehouse_status', function ($warehouses) {
                if ($warehouses->warehouse_status == 'active') {
                    return '<span class="label label-success">Active</span>';
                } elseif ($warehouses->warehouse_status == 'inactive') {
                    return '<span class="label label-danger">Inactive</span>';
                }
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function ($w) use ($request) {
                        $search = $request->get('search');
                        $w->orWhere('warehouse_name', 'LIKE', "%$search%")
                            ->orWhere('bouwheer_name', 'LIKE', "%$search%")
                            ->orWhere('warehouse_type', 'LIKE', "%$search%")
                            ->orWhere('warehouse_status', 'LIKE', "%$search%")
                            ->orWhere('warehouse_location', 'LIKE', "%$search%");
                    });
                }
            })
            ->addColumn('action', 'warehouses.action')
            ->rawColumns(['warehouse_status', 'action'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $warehouse = new Warehouse;
        $warehouse->bouwheer_id = $data['bouwheer_id'];
        $warehouse->warehouse_name = $data['warehouse_name'];
        $warehouse->warehouse_status = $data['warehouse_status'];
        $warehouse->warehouse_location = $data['warehouse_location'];
        $warehouse->save();

        return redirect('warehouses')->with('success', 'Warehouse added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->all();
        $warehouse->update($data);

        return redirect('warehouses')->with('success', 'Warehouse edited successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->inventories()->count() > 0) {
            return redirect('warehouses')->with('error', 'Cannot delete a parent row: a foreign key constraint fails');
        } else {
            $warehouse->delete();

            return redirect('warehouses')->with('success', 'Warehouse deleted successfully');
        }
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'warehouse' => 'required|mimes:xls,xlsx',
        ]);

        $warehouse = $request->file('warehouse');

        if ($request->hasFile('warehouse')) {
            $import_warehouse = new WarehouseImport;
            $import_warehouse->import($warehouse);

            if ($import_warehouse->failures()->isNotEmpty()) {
                return back()->withFailures($import_warehouse->failures());
            }
        }

        return redirect('warehouses')->with('success', 'Items imported successfully');
    }
}
