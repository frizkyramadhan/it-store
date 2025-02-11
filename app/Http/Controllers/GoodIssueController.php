<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Project;
use App\Models\GiDetail;
use App\Models\GoodIssue;
use App\Models\Warehouse;
use Illuminate\Support\Arr;
use App\Models\IssuePurpose;
use Illuminate\Http\Request;
use App\Models\MaterialRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\InventoryController;

class GoodIssueController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_auth:admin');
    }

    public function index()
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Good Issue';
        Session::forget('gi_transaction');
        return view('goodissues.index', compact('title', 'subtitle'));
    }

    public function getGoodIssue(Request $request)
    {
        $goodIssues = GoodIssue::select('good_issues.*', 'warehouses.warehouse_name', 'projects.project_code', 'users.name')
            ->leftJoin('warehouses', 'good_issues.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('projects', 'good_issues.project_id', '=', 'projects.id')
            ->leftJoin('users', 'good_issues.user_id', '=', 'users.id')
            ->orderBy('gi_doc_num', 'desc')
            ->get();

        $datatable = datatables()->of($goodIssues)
            ->addIndexColumn()
            ->addColumn('documentNumber', fn($goodIssue) => $goodIssue->gi_doc_num)
            ->addColumn('postingDate', fn($goodIssue) => date('d-M-Y', strtotime($goodIssue->gi_posting_date)))
            ->addColumn('projectCode', fn($goodIssue) => $goodIssue->project_code)
            ->addColumn('warehouse', fn($goodIssue) => $goodIssue->warehouse_name)
            ->addColumn('remarks', fn($goodIssue) => $goodIssue->gi_remarks)
            ->addColumn('user', fn($goodIssue) => $goodIssue->name)
            ->addColumn('workOrder', fn($goodIssue) => $this->getWorkOrder($goodIssue->it_wo_id))
            ->addColumn('status', fn($goodIssue) => '<span class="' . ($goodIssue->is_cancelled === 'yes' ? 'label label-danger' : 'label label-success') . '">' . ($goodIssue->is_cancelled === 'yes' ? 'Canceled' : 'Open') . '</span>')
            ->filter(function ($instance) use ($request) {
                if ($request->has('search')) {
                    $search = $request->get('search');
                    $instance->collection = $instance->collection->filter(function ($row) use ($search) {
                        return stripos($row['gi_doc_num'], $search) !== false
                            || stripos($row['gi_posting_date'], $search) !== false
                            || stripos($row['project_code'], $search) !== false
                            || stripos($row['warehouse_name'], $search) !== false
                            || stripos($row['gi_remarks'], $search) !== false
                            || stripos($row['name'], $search) !== false
                            || stripos($row['status'], $search) !== false
                            || stripos($row['workOrder'], $search) !== false;
                    });
                }
            })
            ->addColumn('action', 'goodissues.action')
            ->rawColumns(['status', 'action'])
            ->toJson();

        return $datatable;
    }

    private function getWorkOrder($workOrderId)
    {
        if (!$workOrderId) {
            return '-';
        }

        $url = 'http://192.168.32.37/arka-rest-server/api/it_wo_store/';
        $apiKey = 'arka123';

        $response = Http::get($url, [
            'arka-key' => $apiKey,
            'id_wo' => $workOrderId
        ]);

        return $response['data'][0]['no_wo'] ?? '-';
    }

    public function getMrReference($id)
    {
        $mr = MaterialRequest::with(['mrdetails.item', 'project', 'warehouse'])
            ->find($id);

        if ($mr->mr_status === 'closed') {
            return response()->json([
                'success' => 'false',
                'message' => 'Material Request is already closed.'
            ], 422);
        }
        // tambahkan keterangan IT WO dari function getWorkOrder
        $mr->it_wo_no = $this->getWorkOrder($mr->it_wo_id);

        return response()->json([
            'success' => 'true',
            'data' => $mr
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Good Issue';

        $warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->where('warehouse_type', 'main')
            ->orderBy('warehouse_name', 'asc')
            ->get();

        $projects = Project::where('project_status', 'active')->orderBy('project_code', 'asc')->get();

        $issuepurposes = IssuePurpose::where('purpose_status', 'active')->orderBy('purpose_name', 'asc')->get();

        $items = Item::where('item_status', 'active')->orderBy('item_code', 'asc')->get();

        $materialRequests = MaterialRequest::where('mr_status', 'open')->orderBy('mr_doc_num', 'desc')->get();
        // tambahkan keterangan IT WO dari function getWorkOrder
        foreach ($materialRequests as $mr) {
            $mr->it_wo_no = $this->getWorkOrder($mr->it_wo_id);
        }

        $sessionData = Session::get('gi_transaction');

        // generate gr number
        $gi_no = static::generateDocNum();

        $mr = null;
        if ($request->mr_id) {
            $mr = MaterialRequest::with(['mrdetails.item', 'project', 'warehouse'])->find($request->mr_id);
        }

        return view('goodissues.create', compact('title', 'subtitle', 'warehouses', 'projects', 'issuepurposes', 'items', 'gi_no', 'sessionData', 'mr', 'materialRequests'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $docNum = $request->input('gi_doc_num');
            // check if gr doc num exists
            if (GoodIssue::where('gi_doc_num', $docNum)->exists()) {
                // generate new gr doc num
                do {
                    $newDocNum = static::generateDocNum();
                } while (GoodIssue::where('gi_doc_num', $newDocNum)->exists());

                $request->merge(['gi_doc_num' => $newDocNum]);
            }

            $data = $request->all();

            // simpan gr_transaction ke session
            // Session::put('gi_transaction', [
            //     'gi' => $data,
            //     'giDetails' => [],
            //     'inventory' => [],
            // ]);

            $gi = new GoodIssue();
            $gi->gi_doc_num = $data['gi_doc_num'];
            $gi->gi_posting_date = $data['gi_posting_date'];
            $gi->warehouse_id = $data['warehouse_id'];
            $gi->project_id = $data['project_id'];
            $gi->issue_purpose_id = $data['issue_purpose_id'];
            $gi->it_wo_id = $data['it_wo_id'] ?? null;
            $gi->material_request_id = $data['mr_id'] ?? null; // Add this line
            $gi->gi_remarks = $data['gi_remarks'];
            $gi->gi_status = $data['gi_status'];
            $gi->total_cost = $data['total_cost'];
            $gi->is_cancelled = 'no';
            $gi->user_id = auth()->user()->id;
            $gi->save();

            if ($request->input('mr_id')) {
                MaterialRequest::where('id', $request->input('mr_id'))
                    ->update(['mr_status' => 'closed']);
            }

            $check = Arr::exists($data, 'item_id');
            if ($check == true) {
                foreach ($data['item_id'] as $item => $value) {
                    $goodissues = array(
                        'good_issue_id' => $gi->id,
                        'item_id' => $data['item_id'][$item],
                        'gi_qty' => $data['gi_qty'][$item],
                        'price' => $data['price'][$item],
                        'gi_line_total' => $data['gi_line_total'][$item],
                        'gi_line_remarks' => $data['gi_line_remarks'][$item]
                    );
                    // tambahkan gr detail ke session
                    // Session::push('gi_transaction.giDetails', $goodissues);
                    GiDetail::create($goodissues);

                    // Update stok di tabel Inventory
                    // Session::push('gi_transaction.inventory', [
                    //     'item_id' => $data['item_id'][$item],
                    //     'warehouse_id' => $data['warehouse_id'],
                    //     'gi_qty' => $data['gi_qty'][$item],
                    // ]);
                    app(InventoryController::class)->transactionOut($data['item_id'][$item], $data['warehouse_id'], $data['gi_qty'][$item]);
                }
            }

            DB::commit();
            // return redirect()->route('goodissues.issuebatch');
            return redirect('goodissues')->with('success', 'Good Issue added successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    function generateDocNum()
    {
        $gi_count = GoodIssue::whereYear('created_at', Carbon::now()->format('Y'))->count();
        $gi_no = 'GI-' . Carbon::now()->format('y') . Carbon::now()->format('m') . str_pad($gi_count + 1, 4, '0', STR_PAD_LEFT);

        return $gi_no;
    }

    // public function issueBatch()
    // {
    //     $title = 'Inventory Transactions';
    //     $subtitle = 'Batch Selection';
    //     $sessionData = Session::get('gi_transaction');
    //     // dd($sessionData);
    //     if (empty($sessionData)) {
    //         return redirect('goodissues/create')->with('warning', 'Session Empty');
    //     }

    //     $items = DB::table('items')->select('items.*', 'inventories.stock', 'inventories.warehouse_id')
    //         ->join('inventories', 'items.id', '=', 'inventories.item_id')
    //         ->whereIn('items.id', $sessionData['gi']['item_id'])
    //         ->where('items.is_batch', 'yes')
    //         ->where('inventories.warehouse_id', $sessionData['gi']['warehouse_id'])
    //         ->get();

    //     $warehouse = DB::table('warehouses')->select('id', 'warehouse_name')->where('id', $sessionData['gi']['warehouse_id'])->first();

    //     $batches = DB::table('batch_inventories')
    //         ->select('batch_inventories.*', 'batches.item_id', 'batches.batch_no', 'batches.mfg_date', 'batches.batch_status', 'items.item_code', 'items.shelf_life')
    //         ->join('batches', 'batch_inventories.batch_id', '=', 'batches.id')
    //         ->join('items', 'batches.item_id', '=', 'items.id')
    //         ->whereIn('batches.item_id', $sessionData['gi']['item_id'])
    //         ->where('warehouse_id', $sessionData['gi']['warehouse_id'])
    //         ->where('batch_stock', '>', 0)
    //         ->where('batches.batch_status', '=', 'active')
    //         ->orderBy('items.item_code', 'asc')->orderBy('batches.batch_no', 'asc')->get();

    //     // dd($batches);

    //     return view('batches.issue', compact('title', 'subtitle', 'sessionData', 'items', 'warehouse', 'batches'));
    // }

    /**
     * Display the specified resource.
     */
    public function show(GoodIssue $goodissue)
    {
        // dd($goodissue->gidetails);
        $title = 'Inventory Transactions';
        $subtitle = 'Good Issue';
        // $warehouses = Warehouse::with('bouwheer')
        //     ->where('warehouse_status', 'active')
        //     ->where('warehouse_type', 'main')
        //     ->orderBy('warehouse_name', 'asc')
        //     ->get();


        return view('goodissues.show', compact('title', 'subtitle', 'goodissue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GoodIssue $goodissue)
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Good Issue';

        $warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->where('warehouse_type', 'main')
            ->orderBy('warehouse_name', 'asc')
            ->get();

        $projects = Project::where('project_status', 'active')->orderBy('project_code', 'asc')->get();

        $issuepurposes = IssuePurpose::where('purpose_status', 'active')->orderBy('purpose_name', 'asc')->get();

        $items = Item::where('item_status', 'active')->orderBy('item_code', 'asc')->get();

        $sessionData = Session::get('gi_transaction');

        // generate gr number
        $gi_no = static::generateDocNum();

        return view('goodissues.edit', compact('title', 'subtitle', 'warehouses', 'projects', 'issuepurposes', 'items', 'gi_no', 'sessionData', 'goodissue'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GoodIssue $goodissue)
    {
        try {
            DB::beginTransaction();

            $goodissue->gi_posting_date = $request->input('gi_posting_date');
            $goodissue->project_id = $request->input('project_id');
            $goodissue->issue_purpose_id = $request->input('issue_purpose_id');
            $goodissue->it_wo_id = $request->input('it_wo_id') ?? null;
            $goodissue->gi_remarks = $request->input('gi_remarks');
            $goodissue->user_id = auth()->user()->id;
            $goodissue->save();

            DB::commit();
            return redirect()->route('goodissues.show', $goodissue)->with('success', 'Good Issue successfully updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoodIssue $goodissue)
    {
        //
    }

    public function forget()
    {
        Session::forget('gi_transaction');

        return redirect('goodissues')->with('success', 'Session Destroyed');
    }

    public function print(GoodIssue $goodissue)
    {
        $title = 'Inventory Transactions';
        $subtitle = 'Good Issue';

        // URL API dan API key
        $url = 'http://192.168.32.37/arka-rest-server/api/it_wo_store/';
        $apiKey = 'arka123';

        // Melakukan permintaan GET ke API dengan parameter
        $response = Http::get($url, [
            'arka-key' => $apiKey,
            'id_wo' => $goodissue->it_wo_id
        ]);

        // Memeriksa apakah respons sukses
        if ($response->successful() && isset($response['status']) && $response['status'] === true) {
            $data = $response['data'][0] ?? null;

            if ($data) {
                // Mengirimkan data ke view
                return view('goodissues.print', compact('title', 'subtitle', 'goodissue', 'data'));
            } else {
                return redirect()->back()->withErrors('Data IT WO tidak ditemukan.');
            }
        } else {
            // Jika ada kesalahan
            return redirect()->back()->withErrors('Error saat mengambil data IT WO.');
        }

        // return view('goodissues.print', compact('title', 'subtitle', 'goodissue'));
    }

    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            // Ambil dokumen good issue yang akan dibatalkan
            $gi = GoodIssue::findOrFail($id);

            // Periksa apakah dokumen sudah dibatalkan sebelumnya
            if ($gi->is_cancelled === 'yes') {
                return redirect()->back()->with('error', 'Good Issue document is already cancelled.');
            }

            // Update status dokumen menjadi dibatalkan
            $gi->gi_status = 'closed';
            $gi->is_cancelled = 'yes';
            $gi->save();

            // Ambil detail dari dokumen good issue
            $giDetails = GiDetail::where('good_issue_id', $gi->id)->get();

            // Kembalikan stok ke inventory untuk setiap item
            foreach ($giDetails as $detail) {
                app(InventoryController::class)->transactionIn(
                    $detail->item_id,
                    $gi->warehouse_id,
                    $detail->gi_qty
                );
            }

            DB::commit();
            return redirect()->route('goodissues.index')->with('success', 'Good Issue cancelled successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
