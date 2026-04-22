<!DOCTYPE html>
<html>
<head>
    <title>Vendor — {{ $vendor->business_name }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 800px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 24px; }
        th, td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; }
        th { background: #f5f5f5; width: 220px; }
        .badge { padding: 3px 10px; border-radius: 4px; font-size: 13px; font-weight: bold; display:inline-block; }
        .draft     { background:#e0e0e0; color:#333; }
        .submitted { background:#fff3cd; color:#856404; }
        .approved  { background:#d4edda; color:#155724; }
        .rejected  { background:#f8d7da; color:#721c24; }
        .sent_back { background:#cce5ff; color:#004085; }
        .masked    { color:#999; font-style:italic; }
        button { padding: 6px 14px; cursor: pointer; }
        input[type=text] { padding: 5px; width: 220px; }
    </style>
</head>
<body>

<a href="{{ route('vendors.index') }}">← Back to list</a>

<h2>
    {{ $vendor->business_name }}
    <span class="badge {{ $vendor->status }}">
        {{ ucfirst(str_replace('_', ' ', $vendor->status)) }}
    </span>
</h2>

{{-- Flash --}}
@if(session('success'))
    <p style="color:green; background:#d4edda; padding:8px 12px; border-radius:4px;">
        {{ session('success') }}
    </p>
@endif
@if(session('error'))
    <p style="color:red; background:#f8d7da; padding:8px 12px; border-radius:4px;">
        {{ session('error') }}
    </p>
@endif

@php $canSee = auth()->user()->isAdmin() || $vendor->user_id == auth()->id(); @endphp

{{-- Detail table --}}
<h3>Application Details</h3>
<table>
    <tr><th>Business Name</th>    <td>{{ $vendor->business_name }}</td></tr>
    <tr><th>Business Type</th>    <td>{{ $vendor->business_type }}</td></tr>
    <tr><th>Contact Person</th>   <td>{{ $vendor->contact_person_name }}</td></tr>
    <tr><th>Contact Email</th>    <td>{{ $vendor->contact_email }}</td></tr>

    <tr><th>Contact Mobile</th>
        <td>
            @if($canSee) {{ $vendor->contact_mobile }}
            @else <span class="masked">{{ $vendor->maskedMobile() }}</span>
            @endif
        </td>
    </tr>

    <tr><th>Company PAN</th>
        <td>
            @if($canSee) {{ $vendor->pan_number }}
            @else <span class="masked">{{ $vendor->maskedPan() }}</span>
            @endif
        </td>
    </tr>

    <tr><th>GST Number</th>
        <td>
            @if($canSee)
                {{ $vendor->gst_number ?? '—' }}
            @else
                <span class="masked">
                    {{ $vendor->gst_number ? $vendor->maskedGst() : '—' }}
                </span>
            @endif
        </td>
    </tr>

    <tr><th>Address</th>          <td>{{ $vendor->address }}</td></tr>
    <tr><th>City</th>             <td>{{ $vendor->city }}</td></tr>
    <tr><th>State</th>            <td>{{ $vendor->state }}</td></tr>
    <tr><th>Pincode</th>          <td>{{ $vendor->pincode }}</td></tr>

    <tr><th>Account Holder</th>   <td>{{ $vendor->account_holder_name }}</td></tr>

    <tr><th>Account Number</th>
        <td>
            @if($canSee) {{ $vendor->account_number }}
            @else <span class="masked">{{ $vendor->maskedAccountNumber() }}</span>
            @endif
        </td>
    </tr>

    <tr><th>IFSC Code</th>        <td>{{ $vendor->ifsc_code }}</td></tr>
    <tr><th>Submitted By</th>     <td>{{ $vendor->user->email ?? '—' }}</td></tr>
</table>

{{-- Creator actions --}}
@if($vendor->user_id == auth()->id() && in_array($vendor->status, ['draft','sent_back']))
    <div style="margin-bottom:20px;">
        <a href="{{ route('vendors.edit', $vendor) }}">
            <button>Edit Application</button>
        </a>
        &nbsp;
        <form action="{{ route('vendors.submit', $vendor) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" onclick="return confirm('Submit this application for admin review?')">
                Submit for Review
            </button>
        </form>
    </div>
@endif

{{-- Admin actions --}}
@if(auth()->user()->isAdmin() && $vendor->status == 'submitted')
    <div style="margin-bottom:20px;">
        <form action="{{ route('vendors.approve', $vendor) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" onclick="return confirm('Approve this vendor?')"
                style="background:#28a745; color:#fff; border:none;">
                Approve
            </button>
        </form>

        &nbsp;

        <form action="{{ route('vendors.reject', $vendor) }}" method="POST"
              style="display:inline-block; margin-top:8px;">
            @csrf
            <input type="text" name="remarks" placeholder="Rejection reason" required>
            <button type="submit" style="background:#dc3545; color:#fff; border:none;">Reject</button>
        </form>

        &nbsp;

        <form action="{{ route('vendors.send_back', $vendor) }}" method="POST"
              style="display:inline-block; margin-top:8px;">
            @csrf
            <input type="text" name="remarks" placeholder="Send back reason" required>
            <button type="submit" style="background:#007bff; color:#fff; border:none;">Send Back</button>
        </form>
    </div>
@endif

{{-- Status history --}}
<h3>Status History</h3>
<table>
    <tr>
        <th>Date & Time</th>
        <th>Action By</th>
        <th>From</th>
        <th>To</th>
        <th>Remarks</th>
    </tr>
    @forelse($vendor->statusLogs as $log)
        <tr>
            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
            <td>{{ $log->user->email ?? '—' }}</td>
            <td>{{ $log->from_status ?? '—' }}</td>
            <td>
                <span class="badge {{ $log->to_status }}">
                    {{ ucfirst(str_replace('_', ' ', $log->to_status)) }}
                </span>
            </td>
            <td>{{ $log->remarks ?? '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" style="text-align:center; color:#888;">No history yet.</td>
        </tr>
    @endforelse
</table>

</body>
</html>