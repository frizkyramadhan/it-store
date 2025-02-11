<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Models\Batch;
use App\Models\Group;
use App\Models\Permit;
use App\Models\GiDetail;
use App\Models\GrDetail;
use App\Models\Inventory;
use App\Models\MaterialRequest;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        $subtitle = 'Dashboard';

        // $totalExplosive = Item::with('type')->whereHas('type', function ($query) {
        //     $query->where('type_name', 'Explosive');
        // })->count();

        // $totalGunBody = Item::with('type')->whereHas('type', function ($query) {
        //     $query->where('type_name', 'Gun Body');
        // })->count();

        $totalItem = Item::count();
        $totalGroup = Group::count();

        $totalStock = Inventory::sum('stock');

        $totalUser = User::count();

        $materialRequests = MaterialRequest::where('mr_status', 'open')->get();
        $outOfStocks = Inventory::with(['item', 'warehouse'])->where('stock', '<=', 5)->where('stock', '>', 0)->get();

        $lastIncoming = GrDetail::with(['item'])->latest()->limit(10)->get();
        $lastOutcoming = GiDetail::with(['item'])->latest()->limit(10)->get();

        // return view('dashboard', compact('title', 'subtitle', 'totalExplosive', 'totalGunBody', 'totalBatch', 'totalUser', 'outOfStocks', 'expiringBatches', 'expiringPermits'));
        return view('dashboard', compact('title', 'subtitle', 'totalItem', 'totalGroup', 'totalStock', 'totalUser', 'materialRequests',  'outOfStocks', 'lastIncoming', 'lastOutcoming'));
    }

    // untuk modal list all item
    public function searchItem(Request $request)
    {
        // $warehouseId = $request->input('warehouseId'); // Mengambil warehouse_id dari permintaan

        $items = Item::leftJoin('groups', 'items.group_id', '=', 'groups.id')
            ->select('items.*', 'groups.group_name')
            // ->selectSub(function ($query) use ($warehouseId) {
            //     $query->select('stock')
            //         ->from('inventories')
            //         ->whereColumn('inventories.item_id', 'items.id')
            //         ->where('warehouse_id', $warehouseId); // Menggunakan $warehouseId di sini
            // }, 'stock')
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
            // ->addColumn('stock', function ($items) {
            //     return $items->stock;
            // })
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
                            // ->orWhere('stock', 'LIKE', "%$search%")
                            // ->orWhere('type_name', 'LIKE', "%$search%")
                            ->orWhere('group_name', 'LIKE', "%$search%");
                    });
                }
            })
            // ->addColumn('action', 'items.actionForTransaction')
            ->rawColumns(['item_status'])
            ->toJson();
    }

    public function search(Request $request)
    {
        $title = 'Search Item';
        $subtitle = 'Item Detail';

        $search = $request->input('search');
        $item = Item::with(['group'])->where('item_code', 'like', '%' . $search . '%')->first();

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

        // $permits = DB::table('permits')
        //     ->select('permits.*', 'warehouses.warehouse_name', 'permit_details.*')
        //     ->leftJoin('warehouses', 'permits.warehouse_id', 'warehouses.id')
        //     ->leftJoin('permit_details', 'permits.id', 'permit_details.permit_id')
        //     ->where('permit_details.item_id', $detail->id)
        //     ->orderBy('permit_date', 'desc')->orderBy('warehouse_name', 'asc')
        //     ->get();

        return view('search', compact('title', 'subtitle', 'item', 'inventories'));
    }
}
