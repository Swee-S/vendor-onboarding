<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\Vendor;
use App\Models\VendorStatusLog;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Services\StatusTransitionService;

class VendorController extends Controller
{
    // -------------------------------------------------------
    // List — search by business_name, pan_number, status
    // -------------------------------------------------------

    public function index(Request $request)
    {
        $query = Vendor::with('user', 'latestStatusLog');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('pan_number',   'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vendors = $query->latest()->get();

        return view('vendors.index', compact('vendors'));
    }

    // -------------------------------------------------------
    // Create
    // -------------------------------------------------------

    public function create()
    {
        return view('vendors.create');
    }

    public function store(StoreVendorRequest $request)
    {
        $data = $request->validated();

        // Encrypt bank account — never store plain text
        $data['account_number_encrypted'] = Crypt::encryptString($data['account_number']);
        unset($data['account_number']);

        $data['user_id'] = auth()->id();
        $data['status']  = 'draft';

        $vendor = Vendor::create($data);

        $this->logStatus($vendor, null, 'draft', 'Application created');

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor application created.');
    }

    // -------------------------------------------------------
    // Edit — only creator, only draft/sent_back
    // -------------------------------------------------------

    public function edit(Vendor $vendor)
    {
        $this->assertCreator($vendor);
        $this->assertEditableStatus($vendor);

        return view('vendors.edit', compact('vendor'));
    }

    public function update(UpdateVendorRequest $request, Vendor $vendor)
    {
        $this->assertCreator($vendor);
        $this->assertEditableStatus($vendor);

        $data = $request->validated();

        // Re-encrypt if account number was changed
        $data['account_number_encrypted'] = Crypt::encryptString($data['account_number']);
        unset($data['account_number']);

        $vendor->update($data);

        return redirect()->route('vendors.show', $vendor)
            ->with('success', 'Application updated.');
    }

    // -------------------------------------------------------
    // Show
    // -------------------------------------------------------

    public function show(Vendor $vendor)
    {
        $vendor->load('statusLogs.user');
        return view('vendors.show', compact('vendor'));
    }

    // -------------------------------------------------------
    // Submit (creator only, draft/sent_back → submitted)
    // -------------------------------------------------------

    public function submit(Vendor $vendor)
    {
        $this->assertCreator($vendor);
        StatusTransitionService::assertAllowed($vendor->status, 'submitted');

        $old = $vendor->status;
        $vendor->update(['status' => 'submitted']);
        $this->logStatus($vendor, $old, 'submitted', 'Submitted for review');

        return back()->with('success', 'Application submitted for approval.');
    }

    // -------------------------------------------------------
    // Admin actions — approve / reject / send_back
    // -------------------------------------------------------

    public function approve(Vendor $vendor)
    {
        $this->assertAdmin();
        StatusTransitionService::assertAllowed($vendor->status, 'approved');

        $old = $vendor->status;
        $vendor->update(['status' => 'approved']);
        $this->logStatus($vendor, $old, 'approved', 'Approved by admin');

        return back()->with('success', 'Vendor approved.');
    }

    public function reject(Request $request, Vendor $vendor)
    {
        $this->assertAdmin();
        $request->validate(['remarks' => 'required|string']);
        StatusTransitionService::assertAllowed($vendor->status, 'rejected');

        $old = $vendor->status;
        $vendor->update(['status' => 'rejected']);
        $this->logStatus($vendor, $old, 'rejected', $request->remarks);

        return back()->with('success', 'Vendor rejected.');
    }

    public function sendBack(Request $request, Vendor $vendor)
    {
        $this->assertAdmin();
        $request->validate(['remarks' => 'required|string']);
        StatusTransitionService::assertAllowed($vendor->status, 'sent_back');

        $old = $vendor->status;
        $vendor->update(['status' => 'sent_back']);
        $this->logStatus($vendor, $old, 'sent_back', $request->remarks);

        return back()->with('success', 'Application sent back for correction.');
    }

    // -------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------

    private function assertCreator(Vendor $vendor): void
    {
        if (auth()->id() !== $vendor->user_id) {
            abort(403, 'You do not own this application.');
        }
    }

    private function assertAdmin(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required.');
        }
    }

    private function assertEditableStatus(Vendor $vendor): void
    {
        if (!in_array($vendor->status, ['draft', 'sent_back'])) {
            abort(403, 'This application can no longer be edited.');
        }
    }

    private function logStatus(Vendor $vendor, ?string $from, string $to, string $remarks): void
    {
        VendorStatusLog::create([
            'vendor_id'   => $vendor->id,
            'user_id'     => auth()->id(),
            'from_status' => $from,
            'to_status'   => $to,
            'remarks'     => $remarks,
        ]);
    }
}