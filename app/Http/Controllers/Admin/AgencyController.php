<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agency;
use Illuminate\Support\Facades\Hash;

class AgencyController extends Controller
{
    public function index()
    {
        $agencies = Agency::latest()->get();

        return view(
            'admin.agencies.index',
            compact('agencies')
        );
    }

    public function create()
    {
        return view('admin.agencies.create');
    }

    public function store(Request $request)
    {
        Agency::create([

            'agency_name' => $request->agency_name,
            'owner_name'  => $request->owner_name,
            'mobile'      => $request->mobile,
            'email'       => $request->email,

            'password' => Hash::make(
                $request->password
            ),

            'commission' => $request->commission,
            'country'    => $request->country,
            'status'     => $request->status,
            'wallet'     => 0

        ]);

        return redirect()
            ->route('agency.index')
            ->with('success','Agency Created Successfully');
    }

    public function view($id)
    {
        $agency = Agency::find($id);

        return view(
            'admin.agencies.view',
            compact('agency')
        );
    }

    public function edit($id)
    {
        $agency = Agency::find($id);

        return view(
            'admin.agencies.edit',
            compact('agency')
        );
    }

    public function update(Request $request,$id)
    {
        $agency = Agency::find($id);

        $agency->update([

            'agency_name' => $request->agency_name,
            'owner_name'  => $request->owner_name,
            'mobile'      => $request->mobile,
            'email'       => $request->email,
            'commission'  => $request->commission,
            'country'     => $request->country,
            'status'      => $request->status

        ]);

        return redirect()
            ->route('agency.index')
            ->with('success','Agency Updated Successfully');
    }

    public function delete($id)
    {
        Agency::find($id)->delete();

        return redirect()
            ->route('agency.index')
            ->with('success','Agency Deleted Successfully');
    }
}