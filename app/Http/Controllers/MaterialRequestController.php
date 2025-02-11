<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Project;
use App\Models\MrDetail;
use App\Models\Warehouse;
use Illuminate\Support\Arr;
use App\Models\IssuePurpose;
use Illuminate\Http\Request;
use App\Models\MaterialRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class MaterialRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_auth:admin');
    }


    public function index()
    {
        $title = 'Inventory Transaction';
        $subtitle = 'Material Requests';
        Session::forget('mr_transaction');
        return view('materialrequests.index', compact('title', 'subtitle'));
    }

    public function getMaterialRequest(Request $request)
    {
        $materialRequests = MaterialRequest::select('material_requests.*', 'warehouses.warehouse_name', 'projects.project_code', 'users.name')
            ->leftJoin('warehouses', 'material_requests.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('projects', 'material_requests.project_id', '=', 'projects.id')
            ->leftJoin('users', 'material_requests.user_id', '=', 'users.id')
            ->orderBy('mr_doc_num', 'desc')
            ->get();

        $datatable = datatables()->of($materialRequests)
            ->addIndexColumn()
            ->addColumn('documentNumber', fn($materialRequest) => $materialRequest->mr_doc_num)
            ->addColumn('postingDate', fn($materialRequest) => date('d-M-Y', strtotime($materialRequest->mr_posting_date)))
            ->addColumn('projectCode', fn($materialRequest) => $materialRequest->project_code)
            ->addColumn('warehouse', fn($materialRequest) => $materialRequest->warehouse_name)
            ->addColumn('remarks', fn($materialRequest) => $materialRequest->mr_remarks)
            ->addColumn('user', fn($materialRequest) => $materialRequest->name)
            ->addColumn('workOrder', fn($materialRequest) => $this->getWorkOrder($materialRequest->it_wo_id))
            ->addColumn('status', function ($materialRequest) {
                if ($materialRequest->mr_status === 'open') {
                    return '<span class="label label-success">Open</span>';
                } elseif ($materialRequest->mr_status === 'closed' && $materialRequest->is_cancelled === 'no') {
                    return '<span class="label label-default">Closed</span>';
                } elseif ($materialRequest->mr_status === 'closed' && $materialRequest->is_cancelled === 'yes') {
                    return '<span class="label label-danger">Cancel</span>';
                }
            })
            ->filter(function ($instance) use ($request) {
                if ($request->has('search')) {
                    $search = $request->get('search');
                    $instance->collection = $instance->collection->filter(function ($row) use ($search) {
                        return stripos($row['mr_doc_num'], $search) !== false
                            || stripos($row['mr_posting_date'], $search) !== false
                            || stripos($row['project_code'], $search) !== false
                            || stripos($row['warehouse_name'], $search) !== false
                            || stripos($row['mr_remarks'], $search) !== false
                            || stripos($row['name'], $search) !== false
                            || stripos($row['status'], $search) !== false
                            || stripos($row['workOrder'], $search) !== false;
                    });
                }
            })
            ->addColumn('action', 'materialrequests.action')
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


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Inventory Transaction';
        $subtitle = 'Material Requests';

        $warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->where('warehouse_type', 'main')
            ->orderBy('warehouse_name', 'asc')
            ->get();

        $projects = Project::where('project_status', 'active')->orderBy('project_code', 'asc')->get();

        $items = Item::where('item_status', 'active')->orderBy('item_code', 'asc')->get();

        $issuepurposes = IssuePurpose::where('purpose_status', 'active')->orderBy('purpose_name', 'asc')->get();

        $sessionData = Session::get('mr_transaction');

        // generate gr number
        $mr_no = static::generateDocNum();

        return view('materialrequests.create', compact('title', 'subtitle', 'warehouses', 'projects', 'items', 'mr_no', 'sessionData', 'issuepurposes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $docNum = $request->input('mr_doc_num');
            // check if gr doc num exists
            if (MaterialRequest::where('mr_doc_num', $docNum)->exists()) {
                // generate new gr doc num
                do {
                    $newDocNum = static::generateDocNum();
                } while (MaterialRequest::where('mr_doc_num', $newDocNum)->exists());

                $request->merge(['mr_doc_num' => $newDocNum]);
            }

            $data = $request->all();

            // simpan gr_transaction ke session
            // Session::put('mr_transaction', [
            //     'gi' => $data,
            //     'giDetails' => [],
            //     'inventory' => [],
            // ]);

            $mr = new MaterialRequest();
            $mr->mr_doc_num = $data['mr_doc_num'];
            $mr->mr_posting_date = $data['mr_posting_date'];
            $mr->warehouse_id = $data['warehouse_id'];
            $mr->project_id = $data['project_id'];
            $mr->issue_purpose_id = $data['issue_purpose_id'];
            $mr->it_wo_id = $data['it_wo_id'] ?? null;
            $mr->mr_remarks = $data['mr_remarks'];
            $mr->mr_status = $data['mr_status'];
            $mr->is_cancelled = 'no';
            $mr->user_id = auth()->user()->id;
            $mr->save();

            $check = Arr::exists($data, 'item_id');
            if ($check == true) {
                foreach ($data['item_id'] as $item => $value) {
                    $materialrequests = array(
                        'material_request_id' => $mr->id,
                        'item_id' => $data['item_id'][$item],
                        'mr_qty' => $data['mr_qty'][$item],
                        'mr_line_remarks' => $data['mr_line_remarks'][$item]
                    );
                    // tambahkan gr detail ke session
                    // Session::push('mr_transaction.giDetails', $materialrequests);
                    MrDetail::create($materialrequests);

                    // Update stok di tabel Inventory
                    // Session::push('mr_transaction.inventory', [
                    //     'item_id' => $data['item_id'][$item],
                    //     'warehouse_id' => $data['warehouse_id'],
                    //     'mr_qty' => $data['mr_qty'][$item],
                    // ]);
                    // app(InventoryController::class)->transactionOut($data['item_id'][$item], $data['warehouse_id'], $data['mr_qty'][$item]);
                }
            }

            DB::commit();
            // return redirect()->route('goodissues.issuebatch');
            return redirect('materialrequests')->with('success', 'Material Request added successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    function generateDocNum()
    {
        $mr_count = MaterialRequest::whereYear('created_at', Carbon::now()->format('Y'))->count();
        $mr_no = 'MR-' . Carbon::now()->format('y') . Carbon::now()->format('m') . str_pad($mr_count + 1, 4, '0', STR_PAD_LEFT);

        return $mr_no;
    }

    // public function issueBatch()
    // {
    //     $title = 'Inventory Transactions';
    //     $subtitle = 'Batch Selection';
    //     $sessionData = Session::get('mr_transaction');
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
    public function show(MaterialRequest $materialrequest)
    {
        // dd($materialrequest->gidetails);
        $title = 'Material Request';
        $subtitle = 'Material Request';

        return view('materialrequests.show', compact('title', 'subtitle', 'materialrequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaterialRequest $materialrequest)
    {
        $title = 'Material Request';
        $subtitle = 'Material Request';

        $warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->where('warehouse_type', 'main')
            ->orderBy('warehouse_name', 'asc')
            ->get();

        $projects = Project::where('project_status', 'active')->orderBy('project_code', 'asc')->get();

        $items = Item::where('item_status', 'active')->orderBy('item_code', 'asc')->get();

        $issuepurposes = IssuePurpose::where('purpose_status', 'active')->orderBy('purpose_name', 'asc')->get();

        $sessionData = Session::get('mr_transaction');

        // generate mr number
        $mr_no = static::generateDocNum();

        return view('materialrequests.edit', compact('title', 'subtitle', 'warehouses', 'projects', 'items', 'mr_no', 'sessionData', 'materialrequest', 'issuepurposes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaterialRequest $materialrequest)
    {
        try {
            DB::beginTransaction();

            $materialrequest->mr_posting_date = $request->input('mr_posting_date');
            $materialrequest->project_id = $request->input('project_id');
            $materialrequest->issue_purpose_id = $request->input('issue_purpose_id');
            $materialrequest->it_wo_id = $request->input('it_wo_id') ?? null;
            $materialrequest->mr_remarks = $request->input('mr_remarks');
            $materialrequest->user_id = auth()->user()->id;
            $materialrequest->save();

            DB::commit();
            return redirect()->route('materialrequests.show', $materialrequest)->with('success', 'Material Request successfully updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialRequest $materialrequest)
    {
        //
    }

    public function forget()
    {
        Session::forget('mr_transaction');

        return redirect('materialrequests')->with('success', 'Session Destroyed');
    }

    public function print(MaterialRequest $materialrequest)
    {
        $title = 'Inventory Transaction';
        $subtitle = 'Material Request';

        // URL API dan API key
        $url = 'http://192.168.32.37/arka-rest-server/api/it_wo_store/';
        $apiKey = 'arka123';

        // Melakukan permintaan GET ke API dengan parameter
        $response = Http::get($url, [
            'arka-key' => $apiKey,
            'id_wo' => $materialrequest->it_wo_id
        ]);

        // Memeriksa apakah respons sukses
        if ($response->successful() && isset($response['status']) && $response['status'] === true) {
            $data = $response['data'][0] ?? null;

            if ($data) {
                // Mengirimkan data ke view
                return view('materialrequests.print', compact('title', 'subtitle', 'materialrequest', 'data'));
            } else {
                return redirect()->back()->withErrors('Data IT WO tidak ditemukan.');
            }
        } else {
            // Jika ada kesalahan
            return redirect()->back()->withErrors('Error saat mengambil data IT WO.');
        }

        // return view('materialrequests.print', compact('title', 'subtitle', 'materialrequest'));
        // return view('goodissues.print', compact('title', 'subtitle', 'goodissue'));
    }

    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            // Ambil dokumen good issue yang akan dibatalkan
            $mr = MaterialRequest::findOrFail($id);

            // Periksa apakah dokumen sudah dibatalkan sebelumnya
            if ($mr->is_cancelled === 'yes') {
                return redirect()->back()->with('error', 'Material Request document is already cancelled.');
            }

            // Update status dokumen menjadi dibatalkan
            $mr->mr_status = 'closed';
            $mr->is_cancelled = 'yes';
            $mr->save();

            // Ambil detail dari dokumen good issue
            // $mrDetails = MrDetail::where('material_request_id', $mr->id)->get();

            // Kembalikan stok ke inventory untuk setiap item
            // foreach ($mrDetails as $detail) {
            //     app(InventoryController::class)->transactionIn(
            //         $detail->item_id,
            //         $mr->warehouse_id,
            //         $detail->mr_qty
            //     );
            // }

            DB::commit();
            return redirect()->route('materialrequests.index')->with('success', 'Material Request cancelled successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
