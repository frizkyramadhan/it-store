<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_auth:admin');
    }

    public function index()
    {
        $title = 'Business Master Data';
        $subtitle = 'List of Vendors';
        $vendors = Vendor::orderBy('vendor_name', 'asc')->get();

        return view('vendors.index', compact('title', 'subtitle', 'vendors'));
    }

    /**
     * Get the vendors using the given request to datatables serverside.
     *
     * @param Request $request The request object.
     * @return mixed
     */
    public function getVendors(Request $request)
    {
        $vendors = Vendor::orderBy('vendor_name', 'asc');

        return datatables()->of($vendors)
            ->addIndexColumn()
            ->addColumn('vendor_name', function ($vendors) {
                return $vendors->vendor_name;
            })
            ->addColumn('vendor_address', function ($vendors) {
                return $vendors->vendor_address;
            })
            ->addColumn('vendor_phone', function ($vendors) {
                return $vendors->vendor_phone;
            })
            ->addColumn('vendor_status', function ($vendors) {
                if ($vendors->vendor_status == 'active') {
                    return '<span class="label label-success">Active</span>';
                } elseif ($vendors->vendor_status == 'inactive') {
                    return '<span class="label label-danger">Inactive</span>';
                }
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function ($w) use ($request) {
                        $search = $request->get('search');
                        $w->orWhere('vendor_name', 'LIKE', "%$search%")
                            ->orWhere('vendor_address', 'LIKE', "%$search%")
                            ->orWhere('vendor_phone', 'LIKE', "%$search%")
                            ->orWhere('vendor_status', 'LIKE', "%$search%");
                    });
                }
            })
            ->addColumn('action', 'vendors.action')
            ->rawColumns(['vendor_status', 'action'])
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

        $vendor = new Vendor;
        $vendor->vendor_name = $data['vendor_name'];
        $vendor->vendor_address = $data['vendor_address'];
        $vendor->vendor_phone = $data['vendor_phone'];
        $vendor->vendor_status = $data['vendor_status'];
        $vendor->save();

        return redirect('vendors')->with('success', 'Vendor added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(vendor $vendor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(vendor $vendor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, vendor $vendor)
    {
        $data = $request->all();
        $vendor->update($data);

        return redirect('vendors')->with('success', 'Vendor edited successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(vendor $vendor)
    {
        if ($vendor->goodreceives()->count() > 0) {
            return redirect('vendors')->with('error', 'Cannot delete a parent row: a foreign key constraint fails');
        } else {
            $vendor->delete();
            return redirect('vendors')->with('success', 'Vendor deleted successfully');
        }
    }
}
