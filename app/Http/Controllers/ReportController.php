<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Exports\GoodReceiveExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function goodReceive(Request $request)
    {
        $title = 'Reports';
        $subtitle = 'Good Receive Report';
        $results  = null;

        $vendors = Vendor::where('vendor_status', 'active')->orderBy('vendor_name', 'asc')->get();
        $warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->where('warehouse_type', 'main')
            ->orderBy('warehouse_name', 'asc')
            ->get();

        $query = DB::table('good_receives')
            ->leftJoin('gr_details', 'good_receives.id', '=', 'gr_details.good_receive_id')
            ->leftJoin('items', 'gr_details.item_id', '=', 'items.id')
            // ->leftJoin('types', 'items.type_id', '=', 'types.id')
            ->leftJoin('groups', 'items.group_id', '=', 'groups.id')
            ->leftJoin('vendors', 'good_receives.vendor_id', '=', 'vendors.id')
            ->leftJoin('warehouses', 'good_receives.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('bouwheers', 'warehouses.bouwheer_id', '=', 'bouwheers.id')
            ->leftJoin('users', 'good_receives.user_id', '=', 'users.id')
            ->select('good_receives.*', 'gr_qty', 'gr_line_remarks', 'item_code', 'description', 'group_name', 'vendor_name', 'warehouse_name', 'bouwheer_name', 'name')
            ->orderBy('good_receives.id', 'desc');

        $conditions = [];

        if ($request->filled('from') && $request->filled('to')) {
            $conditions[] = ['gr_posting_date', '>=', $request->input('from')];
            $conditions[] = ['gr_posting_date', '<=', $request->input('to')];
        }

        if ($request->filled('vendor_id')) {
            $conditions[] = ['good_receives.vendor_id', '=', $request->input('vendor_id')];
        }

        if ($request->filled('warehouse_id')) {
            $conditions[] = ['good_receives.warehouse_id', '=', $request->input('warehouse_id')];
        }

        if ($request->filled('remarks')) {
            $conditions[] = ['gr_remarks', 'like', '%' . $request->input('remarks') . '%'];
        }

        if (!empty($conditions)) {
            $query->where($conditions);
            $results = $query->get();
        }

        return view('reports.goodreceive', compact('title', 'subtitle', 'vendors', 'warehouses', 'results'));
    }

    public function goodIssue(Request $request)
    {
        $title = 'Reports';
        $subtitle = 'Good Issue Report';
        $results  = null;

        $warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->where('warehouse_type', 'main')
            ->orderBy('warehouse_name', 'asc')
            ->get();

        $query = DB::table('good_issues')
            ->leftJoin('gi_details', 'good_issues.id', '=', 'gi_details.good_issue_id')
            ->leftJoin('items', 'gi_details.item_id', '=', 'items.id')
            // ->leftJoin('types', 'items.type_id', '=', 'types.id')
            ->leftJoin('groups', 'items.group_id', '=', 'groups.id')
            ->leftJoin('warehouses', 'good_issues.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('bouwheers', 'warehouses.bouwheer_id', '=', 'bouwheers.id')
            ->leftJoin('users', 'good_issues.user_id', '=', 'users.id')
            ->select('good_issues.*', 'gi_qty', 'gi_line_remarks', 'item_code', 'description', 'group_name', 'warehouse_name', 'bouwheer_name', 'name')
            ->orderBy('good_issues.id', 'desc');

        $conditions = [];

        if ($request->filled('from') && $request->filled('to')) {
            $conditions[] = ['gi_posting_date', '>=', $request->input('from')];
            $conditions[] = ['gi_posting_date', '<=', $request->input('to')];
        }

        if ($request->filled('warehouse_id')) {
            $conditions[] = ['good_issues.warehouse_id', '=', $request->input('warehouse_id')];
        }

        if ($request->filled('remarks')) {
            $conditions[] = ['gi_remarks', 'like', '%' . $request->input('remarks') . '%'];
        }

        if (!empty($conditions)) {
            $query->where($conditions);
            $results = $query->get();
        }

        return view('reports.goodissue', compact('title', 'subtitle', 'warehouses', 'results'));
    }

    public function transfer(Request $request)
    {
        $title = 'Reports';
        $subtitle = 'Inventory Transfer Report';
        $results  = null;

        $from_warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->orderBy('warehouse_name', 'asc')
            ->get();
        $to_warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->orderBy('warehouse_name', 'asc')
            ->get();

        $query = DB::table('transfers')
            ->leftJoin('trf_details', 'transfers.id', '=', 'trf_details.transfer_id')
            ->leftJoin('items', 'trf_details.item_id', '=', 'items.id')
            // ->leftJoin('types', 'items.type_id', '=', 'types.id')
            ->leftJoin('groups', 'items.group_id', '=', 'groups.id')
            ->leftJoin('warehouses as w1', 'transfers.trf_from', '=', 'w1.id')
            ->leftJoin('warehouses as w2', 'transfers.trf_to', '=', 'w2.id')
            ->leftJoin('bouwheers as b1', 'w1.bouwheer_id', '=', 'b1.id')
            ->leftJoin('bouwheers as b2', 'w2.bouwheer_id', '=', 'b2.id')
            ->leftJoin('users', 'transfers.user_id', '=', 'users.id')
            ->select('transfers.*', 'trf_qty', 'trf_line_remarks', 'item_code', 'description', 'group_name', 'w1.warehouse_name as from_warehouse', 'w2.warehouse_name as to_warehouse', 'b1.bouwheer_name', 'name')
            ->orderBy('transfers.id', 'desc');

        $conditions = [];

        if ($request->filled('from') && $request->filled('to')) {
            $conditions[] = ['trf_posting_date', '>=', $request->input('from')];
            $conditions[] = ['trf_posting_date', '<=', $request->input('to')];
        }

        if ($request->filled('from_warehouse')) {
            $conditions[] = ['transfers.trf_from', '=', $request->input('from_warehouse')];
        }

        if ($request->filled('to_warehouse')) {
            $conditions[] = ['transfers.trf_to', '=', $request->input('to_warehouse')];
        }

        if ($request->filled('remarks')) {
            $conditions[] = ['trf_remarks', 'like', '%' . $request->input('remarks') . '%'];
        }

        if (!empty($conditions)) {
            $query->where($conditions);
            $results = $query->get();
        }

        return view('reports.transfer', compact('title', 'subtitle', 'from_warehouses', 'to_warehouses', 'results'));
    }

    public function inventoryAuditReport(Request $request)
    {
        $title = 'Reports';
        $subtitle = 'Inventory Audit Report';
        $warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->orderBy('warehouse_name', 'asc')
            ->get();
        $results  = null;

        $from = $request->input('from');
        $to = $request->input('to');
        $itemCode = $request->input('item_code');
        $warehouseIds = $request->input('warehouse_ids');

        $goodReceivesQuery = DB::table('good_receives')
            ->select(
                'good_receives.gr_doc_num AS doc_num',
                'good_receives.gr_posting_date AS posting_date',
                'good_receives.warehouse_id',
                'warehouses.warehouse_name',
                'users.name',
                'items.item_code',
                'items.description',
                'gr_details.gr_qty AS qty',
                'gr_details.created_at',
                DB::raw("'gr' AS document")
            )
            ->leftJoin('gr_details', 'good_receives.id', '=', 'gr_details.good_receive_id')
            ->leftJoin('items', 'gr_details.item_id', '=', 'items.id')
            ->leftJoin('warehouses', 'good_receives.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('users', 'good_receives.user_id', '=', 'users.id');

        $goodIssuesQuery = DB::table('good_issues')
            ->select(
                'good_issues.gi_doc_num AS doc_num',
                'good_issues.gi_posting_date AS posting_date',
                'good_issues.warehouse_id',
                'warehouses.warehouse_name',
                'users.name',
                'items.item_code',
                'items.description',
                'gi_details.gi_qty AS qty',
                'gi_details.created_at',
                DB::raw("'gi' AS document")
            )
            ->leftJoin('gi_details', 'good_issues.id', '=', 'gi_details.good_issue_id')
            ->leftJoin('items', 'gi_details.item_id', '=', 'items.id')
            ->leftJoin('warehouses', 'good_issues.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('users', 'good_issues.user_id', '=', 'users.id');

        $transfersQuery = DB::table('transfers')
            ->select(
                'transfers.trf_doc_num AS doc_num',
                'transfers.trf_posting_date AS posting_date',
                'transfers.trf_from AS warehouse_id',
                'w2.warehouse_name',
                'users.name',
                'items.item_code',
                'items.description',
                'trf_details.trf_qty AS qty',
                'trf_details.created_at',
                DB::raw("'trf' AS document")
            )
            ->leftJoin('trf_details', 'transfers.id', '=', 'trf_details.transfer_id')
            ->leftJoin('items', 'trf_details.item_id', '=', 'items.id')
            ->leftJoin('warehouses AS w1', 'transfers.trf_from', '=', 'w1.id')
            ->leftJoin('warehouses AS w2', 'transfers.trf_to', '=', 'w2.id')
            ->leftJoin('users', 'transfers.user_id', '=', 'users.id');

        // Menerapkan filter jika ada input dari form
        if ($from && $to) {
            $goodReceivesQuery->whereBetween('good_receives.gr_posting_date', [$from, $to]);
            $goodIssuesQuery->whereBetween('good_issues.gi_posting_date', [$from, $to]);
            $transfersQuery->whereBetween('transfers.trf_posting_date', [$from, $to]);
        }

        if ($itemCode) {
            $goodReceivesQuery->where('items.item_code', 'LIKE', "%$itemCode%");
            $goodIssuesQuery->where('items.item_code', 'LIKE', "%$itemCode%");
            $transfersQuery->where('items.item_code', 'LIKE', "%$itemCode%");
        }

        if ($warehouseIds) {
            $goodReceivesQuery->whereIn('good_receives.warehouse_id', $warehouseIds);
            $goodIssuesQuery->whereIn('good_issues.warehouse_id', $warehouseIds);
            $transfersQuery->whereIn('transfers.trf_to', $warehouseIds);
        }

        // Jika tidak ada filter, maka kosongkan subquery
        if (!empty($from) || !empty($to) || !empty($itemCode) || !empty($warehouseIds)) {
            //Gabungkan ketiga bagian subquery menggunakan union
            $results = $goodReceivesQuery
                ->union($goodIssuesQuery)
                ->union($transfersQuery)
                ->orderBy('item_code', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('reports.audit', compact('title', 'subtitle', 'warehouses', 'results'));
    }

    public function permitReport(Request $request)
    {
        $title = 'Reports';
        $subtitle = 'Permit Report';
        $warehouses = Warehouse::with('bouwheer')
            ->where('warehouse_status', 'active')
            ->orderBy('warehouse_name', 'asc')
            ->get();
        $results  = null;

        $from = $request->input('from');
        $to = $request->input('to');
        $itemCode = $request->input('item_code');
        $warehouseIds = $request->input('warehouse_ids');

        $permitQuery = DB::table('permits')
            ->select(
                'permits.permit_no',
                'permits.permit_date',
                'permits.valid_month',
                'permits.warehouse_id',
                'warehouses.warehouse_name',
                'users.name',
                'items.id as item_id',
                'items.item_code',
                'items.description',
                'permit_details.si_qty',
                'permit_details.si_line_remarks',
                'permit_details.created_at'
            )
            ->leftJoin('permit_details', 'permits.id', '=', 'permit_details.permit_id')
            ->leftJoin('items', 'permit_details.item_id', '=', 'items.id')
            ->leftJoin('warehouses', 'permits.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('users', 'permits.user_id', '=', 'users.id');

        // Menerapkan filter jika ada input dari form
        if ($from && $to) {
            $permitQuery->whereBetween('permits.permit_date', [$from, $to]);
        }

        if ($itemCode) {
            $permitQuery->where('items.item_code', 'LIKE', "%$itemCode%");
        }

        if ($warehouseIds) {
            $permitQuery->whereIn('permits.warehouse_id', $warehouseIds);
        }

        // Jika tidak ada filter, maka kosongkan subquery
        if (!empty($from) || !empty($to) || !empty($itemCode) || !empty($warehouseIds)) {
            $results = $permitQuery
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('reports.permit', compact('title', 'subtitle', 'warehouses', 'results'));
    }
}
