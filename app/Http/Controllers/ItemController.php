<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Type;
use App\Models\Batch;
use App\Models\Group;
use App\Models\Inventory;
use App\Imports\ItemImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_auth:admin');
    }

    public function index()
    {
        $title = 'Item Master Data';
        $subtitle = 'List of Items';

        return view('items.index', compact('title', 'subtitle'));
    }

    /**
     * Get the items using the given request to datatables serverside.
     *
     * @param Request $request The request object.
     * @return mixed
     */
    public function getItems(Request $request)
    {
        $items = Item::leftJoin('groups', 'items.group_id', '=', 'groups.id')
            ->select('items.*', 'groups.group_name')
            ->orderBy('group_name', 'asc')
            ->orderBy('item_code', 'asc');

        return datatables()->of($items)
            ->addIndexColumn()
            ->addColumn('item_code', function ($items) {
                return $items->item_code;
            })
            ->addColumn('description', function ($items) {
                return $items->description;
            })
            ->addColumn('group_name', function ($items) {
                return $items->group_name;
            })
            ->addColumn('item_status', function ($items) {
                if ($items->item_status == 'active') {
                    return '<span class="label label-success">Active</span>';
                } elseif ($items->item_status == 'inactive') {
                    return '<span class="label label-danger">Inactive</span>';
                }
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function ($w) use ($request) {
                        $search = $request->get('search');
                        $w->orWhere('item_code', 'LIKE', "%$search%")
                            ->orWhere('description', 'LIKE', "%$search%")
                            ->orWhere('group_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addColumn('action', 'items.action')
            ->rawColumns(['item_status', 'action'])
            ->toJson();
    }

    public function getItemsForTransaction(Request $request)
    {
        $warehouseId = $request->input('warehouseId'); // Mengambil warehouse_id dari permintaan

        $items = Item::leftJoin('groups', 'items.group_id', '=', 'groups.id')
            ->select('items.*', 'groups.group_name')
            ->selectSub(function ($query) use ($warehouseId) {
                $query->select('stock')
                    ->from('inventories')
                    ->whereColumn('inventories.item_id', 'items.id')
                    ->where('warehouse_id', $warehouseId); // Menggunakan $warehouseId di sini
            }, 'stock')
            ->orderBy('group_name', 'asc')
            ->orderBy('item_code', 'asc');

        return datatables()->of($items)
            ->addIndexColumn()
            ->addColumn('item_code', function ($items) {
                return $items->item_code;
            })
            ->addColumn('description', function ($items) {
                return $items->description;
            })
            ->addColumn('group_name', function ($items) {
                return $items->group_name;
            })
            ->addColumn('stock', function ($items) {
                return $items->stock;
            })
            ->addColumn('item_status', function ($items) {
                if ($items->item_status == 'active') {
                    return '<span class="label label-success">Active</span>';
                } elseif ($items->item_status == 'inactive') {
                    return '<span class="label label-danger">Inactive</span>';
                }
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function ($w) use ($request) {
                        $search = $request->get('search');
                        $w->orWhere('item_code', 'LIKE', "%$search%")
                            ->orWhere('description', 'LIKE', "%$search%")
                            ->orWhere('group_name', 'LIKE', "%$search%");
                    });
                }
            })
            // ->addColumn('action', 'items.actionForTransaction')
            ->rawColumns(['item_status'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Item Master Data';
        $subtitle = 'Add New Item';
        $groups = Group::orderBy('group_name', 'asc')->get();

        return view('items.create', compact('title', 'subtitle', 'groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $item = new Item;
        $item->item_code = $data['item_code'];
        $item->description = $data['description'];
        $item->group_id = $data['group_id'];
        $item->item_status = $data['item_status'];
        $item->save();

        return redirect('items')->with('success', 'Item added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $title = 'Item Master Data';
        $subtitle = 'Detail Item';
        $groups = Group::orderBy('group_name', 'asc')->get();
        $inventories = DB::table('warehouses')
            ->select('warehouses.*', 'bouwheers.bouwheer_name')
            ->selectSub(function ($query) use ($item) {
                $query->select('stock')
                    ->from('inventories')
                    ->whereColumn('inventories.warehouse_id', 'warehouses.id')
                    ->where('inventories.item_id', $item->id)
                    ->limit(1);
            }, 'stock')
            ->leftJoin('bouwheers', 'warehouses.bouwheer_id', 'bouwheers.id')
            ->orderBy('warehouse_name', 'asc')
            ->get();

        return view('items.show', compact('title', 'subtitle', 'groups', 'item', 'inventories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $title = 'Item Master Data';
        $subtitle = 'Edit an item';
        $groups = Group::orderBy('group_name', 'asc')->get();

        return view('items.edit', compact('title', 'subtitle', 'groups', 'item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $data = $request->except('url');
        $item->update($data);

        $url = $request->input('url');
        return redirect($url)->with('success', 'Item edited successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return redirect('items')->with('success', 'Item deleted successfully');
    }

    public function searchItemByCode(Request $request)
    {
        $itemCode = $request->input('item_code');

        // Lakukan pencarian item berdasarkan item_code dengan wildcard
        $items = Item::where('item_code', 'LIKE', "%$itemCode%")->where('item_status', 'active')->orderBy('item_code', 'asc')->get();

        if ($items) {
            $itemData = [];
            foreach ($items as $item) {
                $itemData[] = [
                    'id' => $item->id,
                    'item_code' => $item->item_code,
                    'description' => $item->description,
                ];
            }
            return response()->json($itemData);
        } else {
            // Jika item tidak ditemukan, kembalikan response kosong
            return response()->json(null);
        }
    }

    // public function getBatchByItem(Request $request)
    // {
    //     $id = $request->input('id');
    //     $item_id = $request->input('item_id');

    //     $batches = Batch::leftJoin('batch_inventories', 'batches.id', '=', 'batch_inventories.batch_id')
    //         ->leftJoin('warehouses', 'batch_inventories.warehouse_id', '=', 'warehouses.id')
    //         ->select('batches.*', 'batch_inventories.batch_stock', 'batch_inventories.warehouse_id', 'warehouses.warehouse_name')
    //         ->where('batch_inventories.warehouse_id', $id)->where('item_id', $item_id)
    //         ->orderBy('batch_no', 'asc');

    //     return datatables()->of($batches)
    //         ->addIndexColumn()
    //         ->addColumn('batch_no', function ($batches) {
    //             return $batches->batch_no;
    //         })
    //         ->addColumn('mfg_date', function ($batches) {
    //             return $batches->mfg_date;
    //         })
    //         ->addColumn('expire_date', function ($batches) {
    //             $mfgDate = Carbon::parse($batches->mfg_date);
    //             $shelfLife = $batches->item->shelf_life;

    //             $expireDate = $mfgDate->addMonths($shelfLife);
    //             return date('d-M-Y', strtotime($expireDate));
    //         })
    //         ->addColumn('expire_status', function ($batches) {
    //             $mfgDate = Carbon::parse($batches->mfg_date);
    //             $shelfLife = $batches->item->shelf_life;

    //             $now = now();
    //             $expireDate = $mfgDate->addMonths($shelfLife);
    //             if ($now > $expireDate) {
    //                 return '<span class="label label-danger">Expired</span>';
    //             } else {
    //                 return '<span class="label label-primary">Non Expired</span>';
    //             }
    //         })
    //         ->addColumn('batch_stock', function ($batches) {
    //             return $batches->batch_stock;
    //         })
    //         ->filter(function ($instance) use ($request) {
    //             if (!empty($request->get('search'))) {
    //                 $instance->where(function ($w) use ($request) {
    //                     $search = $request->get('search');
    //                     $w->orWhere('batch_no', 'LIKE', "%$search%")
    //                         ->orWhere('mfg_date', 'LIKE', "%$search%")
    //                         ->orWhere('expire_date', 'LIKE', "%$search%")
    //                         ->orWhere('expire_status', 'LIKE', "%$search%")
    //                         ->orWhere('batch_stock', 'LIKE', "%$search%");
    //                 });
    //             }
    //         })
    //         ->rawColumns(['expire_status'])
    //         ->toJson();
    // }

    public function import(Request $request)
    {
        $this->validate($request, [
            'item' => 'required|mimes:xls,xlsx',
        ]);

        $item = $request->file('item');

        if ($request->hasFile('item')) {
            $import_item = new ItemImport;
            $import_item->import($item);

            if ($import_item->failures()->isNotEmpty()) {
                return back()->withFailures($import_item->failures());
            }
        }

        return redirect('items')->with('success', 'Items imported successfully');
    }
}
