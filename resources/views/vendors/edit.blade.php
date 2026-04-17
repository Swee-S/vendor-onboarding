<!DOCTYPE html>
<html>
<head>
    <title>Edit Vendor</title>
</head>
<body>

<h2>Edit Vendor</h2>

@if($errors->any())
    <ul style="color:red;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form action="{{ route('vendors.update', $vendor->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Name:</label>
    <input type="text" name="name" value="{{ $vendor->name }}"><br><br>

    <label>Email:</label>
    <input type="email" name="email" value="{{ $vendor->email }}"><br><br>

    <label>Phone:</label>
    <input type="text" name="phone" value="{{ $vendor->phone }}"><br><br>

    <label>PAN:</label>
<input type="text" name="pan"><br><br>

    <label>Address:</label>
    <textarea name="address">{{ $vendor->address }}</textarea><br><br>

    <button type="submit">Update</button>
</form>

</body>
</html>