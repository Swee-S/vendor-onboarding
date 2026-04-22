<!DOCTYPE html>
<html>
<head>
    <title>Edit Vendor Application</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 720px; }
        label { font-size: 14px; font-weight: bold; }
        fieldset { border: 1px solid #ddd; border-radius: 4px; }
        legend { padding: 0 8px; }
    </style>
</head>
<body>

<a href="{{ route('vendors.index') }}">← Back to list</a>
<h2>Edit Application — <em>{{ $vendor->business_name }}</em></h2>

@if($errors->any())
    <ul style="color:red; background:#fff0f0; padding:12px 20px; border-radius:4px; border:1px solid #f5c6cb;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form action="{{ route('vendors.update', $vendor) }}" method="POST">
    @csrf
    @method('PUT')
    @include('vendors._form')
    <button type="submit" style="padding:8px 24px; font-size:15px;">Update Application</button>
</form>

</body>
</html>