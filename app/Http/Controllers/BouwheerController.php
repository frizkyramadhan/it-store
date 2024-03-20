<?php

namespace App\Http\Controllers;

use App\Imports\BouwheerImport;
use App\Models\Bouwheer;
use App\Models\Plant;
use Illuminate\Http\Request;

class BouwheerController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_auth:admin');
    }

    public function index()
    {
        $title = 'Business Master Data';
        $subtitle = 'List of Bouwheers';
        $bouwheers = Bouwheer::orderBy('bouwheer_name', 'asc')->get();

        return view('bouwheers.index', compact('title', 'subtitle', 'bouwheers'));
    }
    /**
     * Get the bouwheers using the given request to datatables serverside.
     *
     * @param Request $request The request object.
     * @return mixed
     */
    public function getBouwheers(Request $request)
    {
        $bouwheers = Bouwheer::select('bouwheers.*')
            ->orderBy('bouwheer_name', 'asc');

        return datatables()->of($bouwheers)
            ->addIndexColumn()
            ->addColumn('bouwheer_name', function ($bouwheers) {
                return $bouwheers->bouwheer_name;
            })
            ->addColumn('alias', function ($bouwheers) {
                return $bouwheers->alias;
            })
            ->addColumn('bouwheer_status', function ($bouwheers) {
                if ($bouwheers->bouwheer_status == 'active') {
                    return '<span class="label label-success">Active</span>';
                } elseif ($bouwheers->bouwheer_status == 'inactive') {
                    return '<span class="label label-danger">Inactive</span>';
                }
            })
            ->addColumn('bouwheer_remarks', function ($bouwheers) {
                return $bouwheers->bouwheer_remarks;
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function ($w) use ($request) {
                        $search = $request->get('search');
                        $w->orWhere('bouwheer_name', 'LIKE', "%$search%")
                            ->orWhere('alias', 'LIKE', "%$search%")
                            ->orWhere('bouwheer_status', 'LIKE', "%$search%")
                            ->orWhere('bouwheer_remarks', 'LIKE', "%$search%");
                    });
                }
            })
            ->addColumn('action', 'bouwheers.action')
            ->rawColumns(['bouwheer_status', 'action'])
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

        $bouwheer = new Bouwheer;
        $bouwheer->bouwheer_name = $data['bouwheer_name'];
        $bouwheer->alias = $data['alias'];
        $bouwheer->bouwheer_status = $data['bouwheer_status'];
        $bouwheer->bouwheer_remarks = $data['bouwheer_remarks'];
        $bouwheer->save();

        return redirect('bouwheers')->with('success', 'Bouwheer added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bouwheer $bouwheer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bouwheer $bouwheer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bouwheer $bouwheer)
    {
        $data = $request->all();
        $bouwheer->update($data);

        return redirect('bouwheers')->with('success', 'Bouwheer edited successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bouwheer $bouwheer)
    {
        // if destroy meets integrity constraint violation, Cannot delete or update a parent row: a foreign key constraint fails, return error
        if ($bouwheer->warehouses()->count() > 0) {
            return redirect('bouwheers')->with('error', 'Cannot delete a parent row: a foreign key constraint fails');
        } else {
            $bouwheer->delete();
            return redirect('bouwheers')->with('success', 'Bouwheer deleted successfully');
        }
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'bouwheer' => 'required|mimes:xls,xlsx',
        ]);

        $bouwheer = $request->file('bouwheer');

        if ($request->hasFile('bouwheer')) {
            $import_bouwheer = new BouwheerImport;
            $import_bouwheer->import($bouwheer);

            if ($import_bouwheer->failures()->isNotEmpty()) {
                return back()->withFailures($import_bouwheer->failures());
            }
        }

        return redirect('bouwheers')->with('success', 'Bouwheer imported successfully');
    }
}
