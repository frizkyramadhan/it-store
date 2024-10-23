<?php

namespace App\Http\Controllers;

use App\Models\IssuePurpose;
use Illuminate\Http\Request;

class IssuePurposeController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Item Master Data';
        $subtitle = 'List of Issue Purpose';
        $purposes = IssuePurpose::orderBy('id', 'asc')->get();

        return view('issuepurposes.index', compact('title', 'subtitle', 'purposes'));
    }


    public function getIssuePurposes(Request $request)
    {
        $purposes = IssuePurpose::select('issue_purposes.*')->orderBy('purpose_name', 'asc');

        return datatables()->of($purposes)
            ->addIndexColumn()
            ->addColumn('purpose_name', function ($purposes) {
                return $purposes->purpose_name;
            })
            ->addColumn('purpose_status', function ($purposes) {
                if ($purposes->purpose_status == 'active') {
                    return '<span class="label label-success">Active</span>';
                } elseif ($purposes->purpose_status == 'inactive') {
                    return '<span class="label label-danger">Inactive</span>';
                }
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function ($w) use ($request) {
                        $search = $request->get('search');
                        $w->orWhere('purpose_name', 'LIKE', "%$search%")
                            ->orWhere('purpose_status', 'LIKE', "%$search%");
                    });
                }
            })
            ->addColumn('action', 'issuepurposes.action')
            ->rawColumns(['action', 'purpose_status'])
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

        $purpose = new IssuePurpose;
        $purpose->purpose_name = $data['purpose_name'];
        $purpose->purpose_status = $data['purpose_status'];
        $purpose->save();

        return redirect('issuepurposes')->with('success', 'Issue Purpose added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(IssuePurpose $issuepurpose)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IssuePurpose $issuepurpose)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IssuePurpose $issuepurpose)
    {
        $data = $request->all();
        $issuepurpose->update($data);

        return redirect('issuepurposes')->with('success', 'Issue Purpose edited successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IssuePurpose $issuepurpose)
    {
        // if destroy meets integrity constraint violation, Cannot delete or update a parent row: a foreign key constraint fails, return error
        if ($issuepurpose->goodissues()->count() > 0) {
            return redirect('issuepurposes')->with('error', 'Cannot delete a parent row: a foreign key constraint fails');
        } else {
            $issuepurpose->delete();
            return redirect('issuepurposes')->with('success', 'Project deleted successfully');
        }
    }
}
