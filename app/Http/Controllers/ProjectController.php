<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Imports\ProjectImport;

class ProjectController extends Controller
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
        $title = 'Business Master Data';
        $subtitle = 'List of Projects';
        $projects = Project::orderBy('project_name', 'asc')->get();

        return view('projects.index', compact('title', 'subtitle', 'projects'));
    }

    /**
     * Get the projects using the given request to datatables serverside.
     *
     * @param Request $request The request object.
     * @return mixed
     */
    public function getProjects(Request $request)
    {
        $projects = Project::select('projects.*')->orderBy('project_code', 'asc');

        return datatables()->of($projects)
            ->addIndexColumn()
            ->addColumn('project_code', function ($projects) {
                return $projects->project_code;
            })
            ->addColumn('project_name', function ($projects) {
                return $projects->project_name;
            })
            ->addColumn('project_status', function ($projects) {
                if ($projects->project_status == 'active') {
                    return '<span class="label label-success">Active</span>';
                } elseif ($projects->project_status == 'inactive') {
                    return '<span class="label label-danger">Inactive</span>';
                }
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function ($w) use ($request) {
                        $search = $request->get('search');
                        $w->orWhere('project_code', 'LIKE', "%$search%")
                            ->orWhere('project_name', 'LIKE', "%$search%")
                            ->orWhere('project_status', 'LIKE', "%$search%");
                    });
                }
            })
            ->addColumn('action', 'projects.action')
            ->rawColumns(['action', 'project_status'])
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

        $project = new Project;
        $project->project_code = $data['project_code'];
        $project->project_name = $data['project_name'];
        $project->project_status = $data['project_status'];
        $project->save();

        return redirect('projects')->with('success', 'Project added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $data = $request->all();
        $project->update($data);

        return redirect('projects')->with('success', 'Project edited successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // if destroy meets integrity constraint violation, Cannot delete or update a parent row: a foreign key constraint fails, return error
        if ($project->goodissues()->count() > 0) {
            return redirect('projects')->with('error', 'Cannot delete a parent row: a foreign key constraint fails');
        } else {
            $project->delete();
            return redirect('projects')->with('success', 'Project deleted successfully');
        }
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'project' => 'required|mimes:xls,xlsx',
        ]);

        $project = $request->file('project');

        if ($request->hasFile('project')) {
            $import_project = new ProjectImport;
            $import_project->import($project);

            if ($import_project->failures()->isNotEmpty()) {
                return back()->withFailures($import_project->failures());
            }
        }

        return redirect('projects')->with('success', 'Project imported successfully');
    }
}
