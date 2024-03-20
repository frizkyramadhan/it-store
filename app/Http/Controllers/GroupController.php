<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Type;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_auth:admin');
    }

    public function index()
    {
        $title = 'Item Master Data';
        $subtitle = 'List of Groups';
        $groups = Group::orderBy('group_name', 'asc')->get();

        return view('groups.index', compact('title', 'subtitle', 'groups'));
    }

    /**
     * Get the groups using the given request to datatables serverside.
     *
     * @param Request $request The request object.
     * @return mixed
     */
    public function getGroups(Request $request)
    {
        $groups = Group::select('groups.*')
            ->orderBy('group_name', 'asc');

        return datatables()->of($groups)
            ->addIndexColumn()
            ->addColumn('group_name', function ($groups) {
                return $groups->group_name;
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function ($w) use ($request) {
                        $search = $request->get('search');
                        $w->orWhere('group_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addColumn('action', 'groups.action')
            ->rawColumns(['action'])
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

        $group = new Group;
        $group->group_name = $data['group_name'];
        $group->save();

        return redirect('groups')->with('success', 'Group added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(group $group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, group $group)
    {
        $data = $request->all();
        $group->update($data);

        return redirect('groups')->with('success', 'Group edited successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(group $group)
    {
        if ($group->items()->count() > 0) {
            return redirect('groups')->with('error', 'Cannot delete a parent row: a foreign key constraint fails');
        } else {
            $group->delete();
            return redirect('groups')->with('success', 'Group deleted successfully');
        }
    }

    /**
     * Get the groups with types for a nested dropdown.
     *
     * @return \Illuminate\Http\Response
     */
}
