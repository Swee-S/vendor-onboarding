<!DOCTYPE html>
<html>
<head>
    <title>Vendor Applications</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: #f5f5f5; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; display:inline-block; }
        .draft     { background: #e0e0e0; color: #333; }
        .submitted { background: #fff3cd; color: #856404; }
        .approved  { background: #d4edda; color: #155724; }
        .rejected  { background: #f8d7da; color: #721c24; }
        .sent_back { background: #cce5ff; color: #004085; }
        .masked    { color: #999; font-style: italic; }
        input[type=text], select { padding: 5px; margin-right: 6px; }
        button { padding: 5px 12px; cursor: pointer; }
    </style>
</head>
<body>

{{-- Top bar --}}
<div style="display:flex; justify-content:space-between; align-items:center;">
    <p style="margin:0;">
        Logged in as: <strong>{{ auth()->user()->email }}</strong>
        @if(auth()->user()->isAdmin())
            <span style="color:green;">(Admin)</span>
        @else
            <span style="color:blue;">(User)</span>
        @endif
    </p>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
</div>

<h2>Vendor Applications</h2>

{{-- Flash messages --}}
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

{{-- Search & Filter --}}
<form method="GET" action="{{ route('vendors.index') }}" style="margin-bottom:12px;">
    <input type="text" name="search"
           placeholder="Search business name or PAN"
           value="{{ request('search') }}"
           style="width:220px;">

    <select name="status">
        <option value="">All Statuses</option>
        @foreach(['draft','submitted','approved','rejected','sent_back'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                {{ ucfirst(str_replace('_', ' ', $s)) }}
            </option>
        @endforeach
    </select>

    <button type="submit">Search</button>
    <a href="{{ route('vendors.index') }}" style="margin-left:6px;">Clear</a>
</form>

<a href="{{ route('vendors.create') }}">+ New Application</a>
<p>Total: <strong>{{ $vendors->count() }}</strong></p>

<table>
    <tr>
        <th>#</th>
        <th>Business Name</th>
        <th>Type</th>
        <th>Contact Person</th>
        <th>Mobile</th>
        <th>PAN</th>
        <th>GST</th>
        <th>Bank Account</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    @forelse($vendors as $vendor)
    @php $canSee = auth()->user()->isAdmin() || $vendor->user_id == auth()->id(); @endphp
    <tr>
        <td>{{ $vendor->id }}</td>
        <td>
            <a href="{{ route('vendors.show', $vendor) }}">{{ $vendor->business_name }}</a>
        </td>
        <td>{{ $vendor->business_type }}</td>
        <td>{{ $vendor->contact_person_name }}</td>

        {{-- Mobile --}}
        <td>
            @if($canSee)
                {{ $vendor->contact_mobile }}
            @else
                <span class="masked">{{ $vendor->maskedMobile() }}</span>
            @endif
        </td>

        {{-- PAN --}}
        <td>
            @if($canSee)
                {{ $vendor->pan_number }}
            @else
                <span class="masked">{{ $vendor->maskedPan() }}</span>
            @endif
        </td>

        {{-- GST --}}
        <td>
            @if($canSee)
                {{ $vendor->gst_number ?? '—' }}
            @else
                <span class="masked">
                    {{ $vendor->gst_number ? $vendor->maskedGst() : '—' }}
                </span>
            @endif
        </td>

        {{-- Bank Account --}}
        <td>
            @if($canSee)
                {{ $vendor->account_number }}
            @else
                <span class="masked">{{ $vendor->maskedAccountNumber() }}</span>
            @endif
        </td>

        {{-- Status + reason --}}
        <td>
            <span class="badge {{ $vendor->status }}">
                {{ ucfirst(str_replace('_', ' ', $vendor->status)) }}
            </span>
            @if(in_array($vendor->status, ['sent_back','rejected']) && $vendor->latestStatusLog)
                <br><small style="color:red;">{{ $vendor->latestStatusLog->remarks }}</small>
            @endif
        </td>

        {{-- Actions --}}
        <td>
            <a href="{{ route('vendors.show', $vendor) }}">View</a>

            {{-- Edit & Submit: creator + draft/sent_back only --}}
            @if($vendor->user_id == auth()->id() && in_array($vendor->status, ['draft','sent_back']))
                | <a href="{{ route('vendors.edit', $vendor) }}">Edit</a>

                <form action="{{ route('vendors.submit', $vendor) }}" method="POST" style="display:inline;">
                    @csrf
                    | <button type="submit"
                        onclick="return confirm('Submit this application for review?')">
                        Submit
                    </button>
                </form>
            @endif

            {{-- Admin actions: submitted only --}}
            @if(auth()->user()->isAdmin() && $vendor->status == 'submitted')

                <form action="{{ route('vendors.approve', $vendor) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" onclick="return confirm('Approve this vendor?')">
                        Approve
                    </button>
                </form>

                <br>

                <form action="{{ route('vendors.reject', $vendor) }}" method="POST" style="margin-top:4px;">
                    @csrf
                    <input type="text" name="remarks" placeholder="Rejection reason" required style="width:140px;">
                    <button type="submit">Reject</button>
                </form>

                <form action="{{ route('vendors.send_back', $vendor) }}" method="POST" style="margin-top:4px;">
                    @csrf
                    <input type="text" name="remarks" placeholder="Send back reason" required style="width:140px;">
                    <button type="submit">Send Back</button>
                </form>

            @endif
        </td>
    </tr>
    @empty
        <tr>
            <td colspan="10" style="text-align:center; color:#888;">No vendor applications found.</td>
        </tr>
    @endforelse
</table>

</body>
</html>