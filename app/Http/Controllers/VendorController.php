<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with('latestStatusLog');

        // SEARCH (name OR PAN)
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('pan', 'like', "%{$search}%");
            });
        }

        // STATUS FILTER
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vendors = $query->get();

        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:vendors,email',
            'phone' => 'required|digits:10',
            'pan' => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'
        ]);

        Vendor::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'pan' => $request->pan,
            'user_id' => auth()->id(),
            'status' => 'draft',
        ]);

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor created successfully');
    }

    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);

        if ($vendor->user_id != auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:vendors,email,' . $id,
            'phone' => 'required|digits:10',
            'pan' => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'
        ]);

        $vendor = Vendor::findOrFail($id);

        if ($vendor->user_id != auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        $vendor->update($request->all());

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor updated successfully');
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);

        if ($vendor->user_id != auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        $vendor->delete();

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor deleted successfully');
    }

    public function submit($id)
    {
        $vendor = Vendor::findOrFail($id);

        if ($vendor->user_id != auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        if (!in_array($vendor->status, ['draft', 'sent_back'])) {
            return back()->with('error', 'Only draft or sent back can be submitted');
        }

        $oldStatus = $vendor->status;

        $vendor->status = 'submitted';
        $vendor->save();

        DB::table('vendor_status_logs')->insert([
            'vendor_id' => $vendor->id,
            'user_id' => auth()->id(),
            'from_status' => $oldStatus,
            'to_status' => 'submitted',
            'remarks' => 'Submitted by user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Vendor submitted for approval');
    }

    public function approve($id)
    {
        $vendor = Vendor::findOrFail($id);

        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'Unauthorized');
        }

        $oldStatus = $vendor->status;

        $vendor->status = 'approved';
        $vendor->save();

        DB::table('vendor_status_logs')->insert([
            'vendor_id' => $vendor->id,
            'user_id' => auth()->id(),
            'from_status' => $oldStatus,
            'to_status' => 'approved',
            'remarks' => 'Approved by admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Vendor approved');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required'
        ]);

        $vendor = Vendor::findOrFail($id);

        $oldStatus = $vendor->status;

        $vendor->status = 'rejected';
        $vendor->save();

        DB::table('vendor_status_logs')->insert([
            'vendor_id' => $vendor->id,
            'user_id' => auth()->id(),
            'from_status' => $oldStatus,
            'to_status' => 'rejected',
            'remarks' => $request->remarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Vendor rejected');
    }

    public function sendBack(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required'
        ]);

        $vendor = Vendor::findOrFail($id);

        $oldStatus = $vendor->status;

        $vendor->status = 'sent_back';
        $vendor->save();

        DB::table('vendor_status_logs')->insert([
            'vendor_id' => $vendor->id,
            'user_id' => auth()->id(),
            'from_status' => $oldStatus,
            'to_status' => 'sent_back',
            'remarks' => $request->remarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Vendor sent back');
    }
}