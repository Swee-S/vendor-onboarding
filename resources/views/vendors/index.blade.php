<!DOCTYPE html>
<html>
<head>
    <title>Vendors List</title>

    <style>
        button {
            padding: 5px 10px;
            margin: 2px;
        }
        input {
            margin: 2px;
        }
    </style>
</head>

<body>

<form action="{{ route('logout') }}" method="POST" style="float:right;">
    @csrf
    <button type="submit">Logout</button>
</form>

<p>Logged in as: {{ auth()->user()->email }}</p>

<h2>Vendor List</h2>

@if(auth()->user()->isAdmin())
    <p style="color: green;">Admin User</p>
@else
    <p style="color: blue;">Normal User</p>
@endif

<form method="GET" action="{{ route('vendors.index') }}">
    <input type="text" name="search" placeholder="Search Name or PAN" value="{{ request('search') }}">

    <select name="status">
        <option value="">All Status</option>
        <option value="draft">Draft</option>
        <option value="submitted">Submitted</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
        <option value="sent_back">Sent Back</option>
    </select>

    <button type="submit">Search</button>
</form>

<br>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<a href="{{ route('vendors.create') }}">+ Add Vendor</a>

<p>Total Vendors: {{ $vendors->count() }}</p>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>PAN</th>
        <th>Address</th>
        <th>Action</th>
        <th>Status</th>
    </tr>

    @foreach($vendors as $vendor)
    <tr>
        <td>{{ $vendor->id }}</td>
        <td>{{ $vendor->name }}</td>
        <td>{{ $vendor->email }}</td>

        {{-- Phone Masking --}}
        <td>
            @if(auth()->user()->isAdmin() || $vendor->user_id == auth()->id())
                {{ $vendor->phone }}
            @else
                {{ substr($vendor->phone, 0, 2) . 'XXXXXX' . substr($vendor->phone, -2) }}
            @endif
        </td>

        {{-- PAN Masking --}}
        <td>
            @if(auth()->user()->isAdmin() || $vendor->user_id == auth()->id())
                {{ $vendor->pan }}
            @else
                {{ substr($vendor->pan, 0, 5) . '****' . substr($vendor->pan, -1) }}
            @endif
        </td>

        <td>{{ $vendor->address }}</td>

        <td>

            {{-- Edit --}}
            @if(($vendor->status == 'draft' || $vendor->status == 'sent_back') && $vendor->user_id == auth()->id())
                <a href="{{ route('vendors.edit', $vendor->id) }}">Edit</a>
            @endif

            {{-- Submit --}}
            @if(($vendor->status == 'draft' || $vendor->status == 'sent_back') && $vendor->user_id == auth()->id())
                <form action="{{ route('vendors.submit', $vendor->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit">Submit</button>
                </form>
            @endif

            {{-- Admin Actions --}}
            @if(auth()->user()->isAdmin() && $vendor->status == 'submitted')

                <form action="{{ route('vendors.approve', $vendor->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit">Approve</button>
                </form>

                <br><br>

                <form action="{{ route('vendors.reject', $vendor->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="text" name="remarks" placeholder="Reject Reason" required>
                    <button type="submit">Reject</button>
                </form>

                <br><br>

                <form action="{{ route('vendors.sendBack', $vendor->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="text" name="remarks" placeholder="Send Back Reason" required>
                    <button type="submit">Send Back</button>
                </form>

            @endif

            {{-- Delete (ONLY OWNER) --}}
            @if($vendor->user_id == auth()->id())
                <form action="{{ route('vendors.destroy', $vendor->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            @endif

        </td>

        <td>
    <span>{{ $vendor->status }}</span>

    @if(($vendor->status == 'sent_back' || $vendor->status == 'rejected') && $vendor->latestStatusLog)
        <br>
        <small style="color:red;">
            Reason: {{ $vendor->latestStatusLog->remarks }}
        </small>
    @endif
</td>
    </tr>
    @endforeach
</table>

</body>
</html>