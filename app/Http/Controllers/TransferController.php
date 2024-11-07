<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Transfer;
use App\Models\TrfDetail;
use App\Models\Warehouse;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\InventoryController;

class TransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_auth:admin');
    }

    public function index()
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Inventory Transfer';
        Session::forget('trf_transaction');
        return view('transfers.index', compact('title', 'subtitle'));
    }

    public function getTransfer(Request $request)
    {
        $transfers = Transfer::leftJoin('warehouses as w1', 'transfers.trf_from', '=', 'w1.id')
            ->leftJoin('warehouses as w2', 'transfers.trf_to', '=', 'w2.id')
            ->leftJoin('users', 'transfers.user_id', '=', 'users.id')
            ->select('transfers.*', 'w1.warehouse_name as from_warehouse', 'w2.warehouse_name as to_warehouse', 'users.name')
            ->orderBy('trf_doc_num', 'desc');

        return datatables()->of($transfers)
            ->addIndexColumn()
            ->addColumn('trf_doc_num', function ($transfers) {
                return $transfers->trf_doc_num;
            })
            ->addColumn('trf_posting_date', function ($transfers) {
                return date('d-M-Y', strtotime($transfers->trf_posting_date));
            })
            // ->addColumn('trf_type', function ($transfers) {
            //     return $transfers->trf_type;
            // })
            ->addColumn('from_warehouse', function ($transfers) {
                return $transfers->from_warehouse;
            })
            ->addColumn('to_warehouse', function ($transfers) {
                return $transfers->to_warehouse;
            })
            ->addColumn('trf_remarks', function ($transfers) {
                return $transfers->trf_remarks;
            })
            ->addColumn('name', function ($transfers) {
                return $transfers->name;
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function ($w) use ($request) {
                        $search = $request->get('search');
                        $w->orWhere('trf_doc_num', 'LIKE', "%$search%")
                            ->orWhere('trf_posting_date', 'LIKE', "%$search%")
                            // ->orWhere('trf_type', 'LIKE', "%$search%")
                            ->orWhere('w1.warehouse_name', 'LIKE', "%$search%")
                            ->orWhere('w2.warehouse_name', 'LIKE', "%$search%")
                            ->orWhere('trf_remarks', 'LIKE', "%$search%")
                            ->orWhere('name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addColumn('action', 'transfers.action')
            ->rawColumns(['action'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Inventory Transfer';
        $warehouses = Warehouse::with('bouwheer')->where('warehouse_status', 'active')->orderBy('warehouse_name', 'asc')->get();

        $items = Item::where('item_status', 'active')->orderBy('item_code', 'asc')->get();

        $sessionData = Session::get('trf_transaction');

        // generate gr number
        $trf_no = static::generateDocNum();

        // get transfer type out and status open
        $transferOuts = Transfer::with(['trfdetails', 'fromWarehouse', 'toWarehouse'])->where('trf_type', 'out')->where('trf_status', 'open')->orderBy('trf_doc_num', 'desc')->get();

        return view('transfers.create', compact('title', 'subtitle', 'warehouses', 'items', 'trf_no', 'transferOuts', 'sessionData'));
    }

    // public function listWarehouses(Request $request)
    // {
    //     $trf_type = $request->input('trf_type');

    //     if ($trf_type == 'out') {
    //         $from_warehouses = Warehouse::with('bouwheer')->where('warehouse_status', 'active')->where('warehouse_type', 'main')->orderBy('warehouse_name', 'asc')->get();
    //         $to_warehouses = Warehouse::with('bouwheer')->where('warehouse_status', 'active')->where('warehouse_type', 'main')->orderBy('warehouse_name', 'asc')->get();
    //     } else {
    //         $from_warehouses = Warehouse::with('bouwheer')->where('warehouse_status', 'active')->where('warehouse_type', 'transit')->orderBy('warehouse_name', 'asc')->get();
    //         $to_warehouses = Warehouse::with('bouwheer')->where('warehouse_status', 'active')->where('warehouse_type', 'main')->orderBy('warehouse_name', 'asc')->get();
    //     }

    //     $list_from = "<option value=''>Select Warehouse</option>";
    //     $list_to = "<option value=''>Select Warehouse</option>";

    //     foreach ($from_warehouses as $from) {
    //         $list_from .= "<option value='" . $from->id . "'>" . $from->warehouse_name . " (" . $from->bouwheer->bouwheer_name . ")" . "</option>";
    //     }

    //     foreach ($to_warehouses as $to) {
    //         $list_to .= "<option value='" . $to->id . "'>" . $to->warehouse_name . " (" . $to->bouwheer->bouwheer_name . ")" . "</option>";
    //     }

    //     $callback = array('list_from_warehouses' => $list_from, 'list_to_warehouses' => $list_to);

    //     return response()->json($callback);
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // check if gr doc num exists
            $docNum = $request->input('trf_doc_num');
            if (Transfer::where('trf_doc_num', $docNum)->exists()) {
                // generate new gr doc num
                do {
                    $newDocNum = static::generateDocNum();
                } while (Transfer::where('trf_doc_num', $newDocNum)->exists());

                $request->merge(['trf_doc_num' => $newDocNum]);
            }

            // check if transfer in, update transfer out status as reference closed
            // $outNum = $request->input('trf_ref_num');
            // if (Transfer::where('trf_doc_num', $outNum)->exists()) {
            //     $transferOut = Transfer::where('trf_doc_num', $outNum)->first();
            //     $transferOut->trf_status = 'closed';
            //     // $transferOut->save();
            // }

            $data = $request->all();

            // Session::put('trf_transaction', [
            //     'trf' => $data,
            //     'trfDetails' => [],
            //     'inventoryOut' => [],
            //     'inventoryIn' => [],
            // ]);

            $trf = new Transfer();
            $trf->trf_doc_num = $data['trf_doc_num'];
            $trf->trf_posting_date = $data['trf_posting_date'];
            // $trf->trf_type = $data['trf_type'];
            $trf->trf_type = 'out';
            // $trf->trf_ref_num = $data['trf_ref_num'];
            $trf->trf_from = $data['trf_from'];
            $trf->trf_to = $data['trf_to'];
            $trf->trf_remarks = $data['trf_remarks'];
            if ($trf->trf_type == 'in') {
                $trf->trf_status = 'closed';
            } else {
                $trf->trf_status = 'open';
            }
            $trf->is_cancelled = 'no';
            $trf->user_id = auth()->user()->id;
            $trf->save();

            $check = Arr::exists($data, 'item_id');
            if ($check == true) {
                foreach ($data['item_id'] as $item => $value) {
                    $transfers = array(
                        'transfer_id' => $trf->id,
                        'item_id' => $data['item_id'][$item],
                        'trf_qty' => $data['trf_qty'][$item],
                        'trf_line_remarks' => $data['trf_line_remarks'][$item]
                    );
                    // tambah transfer detail ke session
                    // Session::push('trf_transaction.trfDetails', $transfers);
                    TrfDetail::create($transfers);

                    // Update stok di tabel Inventory
                    // Session::push('trf_transaction.inventoryOut', [
                    //     'item_id' => $data['item_id'][$item],
                    //     'trf_from' => $data['trf_from'],
                    //     'trf_qty' => $data['trf_qty'][$item],
                    // ]);
                    // Session::push('trf_transaction.inventoryIn', [
                    //     'item_id' => $data['item_id'][$item],
                    //     'trf_to' => $data['trf_to'],
                    //     'trf_qty' => $data['trf_qty'][$item],
                    // ]);
                    app(InventoryController::class)->transactionOut($data['item_id'][$item], $data['trf_from'], $data['trf_qty'][$item]);
                    app(InventoryController::class)->transactionIn($data['item_id'][$item], $data['trf_to'], $data['trf_qty'][$item]);
                }
            }

            DB::commit();
            // return redirect()->route('transfers.transferbatch');
            return redirect('transfers')->with('success', 'Inventory Transfer added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    function generateDocNum()
    {
        $trf_count = Transfer::whereYear('created_at', Carbon::now()->format('Y'))->count();
        $trf_no = 'TR-' . Carbon::now()->format('y') . Carbon::now()->format('m') . str_pad($trf_count + 1, 4, '0', STR_PAD_LEFT);

        return $trf_no;
    }

    // function getTransferReference(Request $request)
    // {
    //     $id = $request->input('id');

    //     $transfer = Transfer::with(['trfdetails.item', 'fromWarehouse', 'toWarehouse'])->where('id', $id)->first();

    //     if ($transfer) {
    //         $output = [
    //             'success' => true,
    //             'transfer' => $transfer
    //         ];
    //         return response()->json($output);
    //     }
    // }

    // public function transferBatch()
    // {
    //     $title = 'Inventory Transactions';
    //     $subtitle = 'Batch Selection';
    //     $sessionData = Session::get('trf_transaction');
    //     // dd($sessionData);
    //     if (empty($sessionData)) {
    //         return redirect('transfers/create')->with('warning', 'Session Empty');
    //     }

    //     $items = DB::table('items')->select('items.*', 'inventories.stock', 'inventories.warehouse_id')
    //         ->join('inventories', 'items.id', '=', 'inventories.item_id')
    //         ->whereIn('items.id', $sessionData['trf']['item_id'])
    //         ->where('items.is_batch', 'yes')
    //         ->where('inventories.warehouse_id', $sessionData['trf']['trf_from'])
    //         ->get();

    //     $from_warehouse = DB::table('warehouses')->select('id', 'warehouse_name')->where('id', $sessionData['trf']['trf_from'])->first();
    //     $to_warehouse = DB::table('warehouses')->select('id', 'warehouse_name')->where('id', $sessionData['trf']['trf_to'])->first();

    //     $batches = DB::table('batch_inventories')
    //         ->select('batch_inventories.*', 'batches.item_id', 'batches.batch_no', 'batches.mfg_date', 'batches.batch_status', 'items.item_code', 'items.shelf_life')
    //         ->join('batches', 'batch_inventories.batch_id', '=', 'batches.id')
    //         ->join('items', 'batches.item_id', '=', 'items.id')
    //         ->whereIn('batches.item_id', $sessionData['trf']['item_id'])
    //         ->where('warehouse_id', $sessionData['trf']['trf_from'])
    //         ->where('batch_stock', '>', 0)
    //         ->where('batches.batch_status', '=', 'active')
    //         ->orderBy('items.item_code', 'asc')->orderBy('batches.batch_no', 'asc')->get();

    //     // dd($batches);

    //     return view('batches.transfer', compact('title', 'subtitle', 'sessionData', 'items', 'from_warehouse', 'to_warehouse', 'batches'));
    // }

    /**
     * Display the specified resource.
     */
    public function show(Transfer $transfer)
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Inventory Transfer';
        $warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->where('warehouse_type', 'main')
            ->orderBy('warehouse_name', 'asc')
            ->get();
        // dd($transfer);

        return view('transfers.show', compact('title', 'subtitle', 'warehouses', 'transfer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transfer $transfer)
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Inventory Transfer';
        $warehouses = Warehouse::with('bouwheer')->where('warehouse_status', 'active')->orderBy('warehouse_name', 'asc')->get();

        // $items = Item::where('item_status', 'active')->orderBy('item_code', 'asc')->get();

        // $sessionData = Session::get('trf_transaction');

        // generate gr number
        // $trf_no = static::generateDocNum();

        // get transfer type out and status open
        // $transferOuts = Transfer::with(['trfdetails', 'fromWarehouse', 'toWarehouse'])->where('trf_type', 'out')->where('trf_status', 'open')->orderBy('trf_doc_num', 'desc')->get();

        return view('transfers.edit', compact('title', 'subtitle', 'warehouses', 'transfer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transfer $transfer)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $transfer->update($data);

            DB::commit();
            return redirect()->route('transfers.show', $transfer)->with('success', 'Inventory Transfer successfully updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transfer $transfer)
    {
        //
    }

    public function print(Transfer $transfer)
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Inventory Transfer';

        return view('transfers.print', compact('title', 'subtitle', 'transfer'));
    }
}
