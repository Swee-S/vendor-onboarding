<!DOCTYPE html>
<html>
<head>
    <title>Add Vendor</title>
</head>
<body>

<h2>Add Vendor</h2>

@if($errors->any())
    <ul style="color:red;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form action="{{ route('vendors.store') }}" method="POST">
    @csrf

  <label>Name:</label>
<input type="text" name="name"><br><br>

<label>Email:</label>
<input type="email" name="email"><br><br>

<label>Phone:</label>
<input type="text" name="phone"><br><br>


<label>PAN:</label>
<input type="text" name="pan"><br><br>

<label>Address:</label>
<textarea name="address"></textarea><br><br>

    <button type="submit">Save</button>
</form>

</body>
</html>