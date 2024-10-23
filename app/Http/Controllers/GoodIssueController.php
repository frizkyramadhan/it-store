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
        $goodissues = GoodIssue::leftJoin('warehouses', 'good_issues.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('projects', 'good_issues.project_id', '=', 'projects.id')
            ->leftJoin('users', 'good_issues.user_id', '=', 'users.id')
            ->select('good_issues.*', 'warehouses.warehouse_name', 'projects.project_code', 'users.name')
            ->orderBy('gi_doc_num', 'desc');

        return datatables()->of($goodissues)
            ->addIndexColumn()
            ->addColumn('gi_doc_num', function ($goodissues) {
                return $goodissues->gi_doc_num;
            })
            ->addColumn('gi_posting_date', function ($goodissues) {
                return date('d-M-Y', strtotime($goodissues->gi_posting_date));
            })
            ->addColumn('project_code', function ($goodissues) {
                return $goodissues->project_code;
            })
            ->addColumn('warehouse_name', function ($goodissues) {
                return $goodissues->warehouse_name;
            })
            ->addColumn('gi_remarks', function ($goodissues) {
                return $goodissues->gi_remarks;
            })
            ->addColumn('name', function ($goodissues) {
                return $goodissues->name;
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function ($w) use ($request) {
                        $search = $request->get('search');
                        $w->orWhere('gi_doc_num', 'LIKE', "%$search%")
                            ->orWhere('gi_posting_date', 'LIKE', "%$search%")
                            ->orWhere('project_code', 'LIKE', "%$search%")
                            ->orWhere('warehouse_name', 'LIKE', "%$search%")
                            ->orWhere('gi_remarks', 'LIKE', "%$search%")
                            ->orWhere('name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addColumn('action', 'goodissues.action')
            ->rawColumns(['action'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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

        return view('goodissues.create', compact('title', 'subtitle', 'warehouses', 'projects', 'issuepurposes', 'items', 'gi_no', 'sessionData'));
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
            $gi->gi_remarks = $data['gi_remarks'];
            $gi->gi_status = $data['gi_status'];
            $gi->total_cost = $data['total_cost'];
            $gi->is_cancelled = 'no';
            $gi->user_id = auth()->user()->id;
            $gi->save();

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
    public function destroy(GoodIssue $goodIssue)
    {
        //
    }

    public function forget()
    {
        Session::forget('gi_transaction');

        return redirect('goodissues')->with('success', 'Session Destroyed');
    }
}
