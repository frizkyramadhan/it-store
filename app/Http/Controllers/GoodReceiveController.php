<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Batch;
use App\Models\Vendor;
use App\Models\GrDetail;
use App\Models\Warehouse;
use App\Models\GoodReceive;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Session;

class GoodReceiveController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_auth:admin');
    }

    public function index()
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Good Receive';
        Session::forget('gr_transaction');
        return view('goodreceives.index', compact('title', 'subtitle'));
    }

    public function getGoodReceive(Request $request)
    {
        $goodReceives = GoodReceive::selectRaw('
            good_receives.*,
            vendors.vendor_name,
            warehouses.warehouse_name,
            users.name
        ')
            ->leftJoin('vendors', 'good_receives.vendor_id', '=', 'vendors.id')
            ->leftJoin('warehouses', 'good_receives.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('users', 'good_receives.user_id', '=', 'users.id')
            ->orderBy('gr_doc_num', 'desc')
            ->get();

        return datatables()->of($goodReceives)
            ->addIndexColumn()
            ->editColumn('gr_posting_date', fn($goodReceive) => date('d-M-Y', strtotime($goodReceive->gr_posting_date)))
            ->addColumn('status', fn($goodReceive) => '<span class="' . ($goodReceive->is_cancelled === 'yes' ? 'label label-danger' : 'label label-success') . '">' . ($goodReceive->is_cancelled === 'yes' ? 'Canceled' : 'Open') . '</span>')
            ->filter(function ($instance) use ($request) {
                if ($request->has('search')) {
                    $search = $request->get('search');
                    $instance->collection = $instance->collection->filter(function ($row) use ($search) {
                        return stripos($row['gr_doc_num'], $search) !== false
                            || stripos($row['gr_posting_date'], $search) !== false
                            || stripos($row['vendor_name'], $search) !== false
                            || stripos($row['warehouse_name'], $search) !== false
                            || stripos($row['gr_remarks'], $search) !== false
                            || stripos($row['name'], $search) !== false
                            || stripos($row['status'], $search) !== false;
                    });
                }
            })
            ->addColumn('action', 'goodreceives.action')
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Good Receive';
        $vendors = Vendor::where('vendor_status', 'active')->orderBy('vendor_name', 'asc')->get();
        $warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->where('warehouse_type', 'main')
            ->orderBy('warehouse_name', 'asc')
            ->get();

        $goodreceives = Item::where('item_status', 'active')->orderBy('item_code', 'asc')->get();

        // generate gr number
        $gr_no = static::generateDocNum();

        $sessionData = Session::get('gr_transaction');
        // Session::forget('gr_transaction');

        return view('goodreceives.create', compact('title', 'subtitle', 'vendors', 'warehouses', 'goodreceives', 'gr_no', 'sessionData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $docNum = $request->input('gr_doc_num');
            // check if gr doc num exists
            if (GoodReceive::where('gr_doc_num', $docNum)->exists()) {
                // generate new gr doc num
                do {
                    $newDocNum = static::generateDocNum();
                } while (GoodReceive::where('gr_doc_num', $newDocNum)->exists());

                $request->merge(['gr_doc_num' => $newDocNum]);
            }

            $data = $request->all();

            // simpan gr_transaction ke session
            // Session::put('gr_transaction', [
            //     'gr' => $data,
            //     'grDetails' => [],
            //     'inventory' => [],
            // ]);

            $gr = new GoodReceive();
            $gr->gr_doc_num = $data['gr_doc_num'];
            $gr->gr_posting_date = $data['gr_posting_date'];
            $gr->vendor_id = $data['vendor_id'];
            $gr->warehouse_id = $data['warehouse_id'];
            $gr->gr_remarks = $data['gr_remarks'];
            $gr->gr_status = $data['gr_status'];
            $gr->is_cancelled = 'no';
            $gr->user_id = auth()->user()->id;
            $gr->save(); // di comment jika menggunakan session batch

            $check = Arr::exists($data, 'item_id');
            if ($check == true) {
                foreach ($data['item_id'] as $item => $value) {
                    $goodreceives = array(
                        'good_receive_id' => $gr->id,
                        'item_id' => $data['item_id'][$item],
                        'gr_qty' => $data['gr_qty'][$item],
                        'gr_line_remarks' => $data['gr_line_remarks'][$item]
                    );

                    // tambahkan gr detail ke session
                    // Session::push('gr_transaction.grDetails', $goodreceives);
                    GrDetail::create($goodreceives); // simpannya di BatchController

                    // Update stok di tabel Inventory
                    // Session::push('gr_transaction.inventory', [
                    //     'item_id' => $data['item_id'][$item],
                    //     'warehouse_id' => $data['warehouse_id'],
                    //     'gr_qty' => $data['gr_qty'][$item],
                    // ]);
                    app(InventoryController::class)->transactionIn($data['item_id'][$item], $data['warehouse_id'], $data['gr_qty'][$item]); // simpannya di BatchController
                }
            }

            DB::commit();
            // redirect ke halaman input data batch
            // return redirect()->route('goodreceive.receivebatch');
            return redirect('goodreceive')->with('success', 'Good Receive added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    function generateDocNum()
    {
        $gr_count = GoodReceive::whereYear('created_at', Carbon::now()->format('Y'))->count();
        $gr_no = 'GR-' . Carbon::now()->format('y') . Carbon::now()->format('m') . str_pad($gr_count + 1, 4, '0', STR_PAD_LEFT);

        return $gr_no;
    }

    // public function receiveBatch()
    // {
    //     $title = 'Inventory Transactions';
    //     $subtitle = 'Batch Setup';
    //     $sessionData = Session::get('gr_transaction');

    //     if (empty($sessionData)) {
    //         return redirect('goodreceive/create')->with('warning', 'Session Empty');
    //     }

    //     $items = DB::table('items')->select('id', 'item_code', 'description')->whereIn('id', $sessionData['gr']['item_id'])->where('is_batch', 'yes')->get();
    //     $warehouse = DB::table('warehouses')->select('id', 'warehouse_name')->where('id', $sessionData['gr']['warehouse_id'])->first();

    //     // dd($sessionData);

    //     return view('batches.receive', compact('title', 'subtitle', 'sessionData', 'items', 'warehouse'));
    // }

    /**
     * Display the specified resource.
     */
    public function show(GoodReceive $goodreceive)
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Good Receive';
        $vendors = Vendor::where('vendor_status', 'active')->orderBy('vendor_name', 'asc')->get();
        $warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->where('warehouse_type', 'main')
            ->orderBy('warehouse_name', 'asc')
            ->get();

        return view('goodreceives.show', compact('title', 'subtitle', 'vendors', 'warehouses', 'goodreceive'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GoodReceive $goodreceive)
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Good Receive';
        $vendors = Vendor::where('vendor_status', 'active')->orderBy('vendor_name', 'asc')->get();
        $warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->where('warehouse_type', 'main')
            ->orderBy('warehouse_name', 'asc')
            ->get();

        // $goodreceives = Item::where('item_status', 'active')->orderBy('item_code', 'asc')->get();

        // generate gr number
        // $gr_no = static::generateDocNum();

        // $sessionData = Session::get('gr_transaction');
        // Session::forget('gr_transaction');

        return view('goodreceives.edit', compact('title', 'subtitle', 'vendors', 'warehouses', 'goodreceive'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GoodReceive $goodreceive)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $goodreceive->update($data);

            DB::commit();
            return redirect()->route('goodreceive.show', $goodreceive)->with('success', 'Good Receive successfully updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoodReceive $goodreceive)
    {
        //
    }

    public function forget()
    {
        Session::forget('gr_transaction');
        return redirect('goodreceive')->with('success', 'Session Destroyed');
    }

    public function print(GoodReceive $goodreceive)
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Good Receive';

        return view('goodreceives.print', compact('title', 'subtitle', 'goodreceive'));
    }

    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            // Ambil dokumen good receive yang akan dibatalkan
            $gr = GoodReceive::findOrFail($id);

            // Periksa apakah dokumen sudah dibatalkan sebelumnya
            if ($gr->is_cancelled === 'yes') {
                return redirect()->back()->with('error', 'Good Receive document is already cancelled.');
            }

            // Ambil detail dari dokumen good receive
            $grDetails = GrDetail::where('good_receive_id', $gr->id)->get();

            // Cek stok untuk setiap item di good receive detail
            foreach ($grDetails as $detail) {
                $stockAvailable = app(InventoryController::class)->checkStockByParams($detail->item_id, $gr->warehouse_id, $detail->gr_qty);

                // Jika stok tidak mencukupi, batalkan pembatalan
                if (!$stockAvailable) {
                    return redirect()->back()->with('error', 'Insufficient stock to cancel this Good Receive document.');
                }
            }

            // Update status dokumen menjadi dibatalkan
            $gr->gr_status = 'closed';
            $gr->is_cancelled = 'yes';
            $gr->save();

            // Kembalikan stok ke inventory untuk setiap item
            foreach ($grDetails as $detail) {
                app(InventoryController::class)->transactionOut(
                    $detail->item_id,
                    $gr->warehouse_id,
                    $detail->gr_qty
                );
            }

            DB::commit();
            return redirect()->route('goodreceive.index')->with('success', 'Good Receive cancelled successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
