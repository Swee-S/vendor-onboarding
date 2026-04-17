@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card shadow">
                <div class="card-header text-center bg-primary text-white">
                    <h4>Login</h4>
                </div>

                <div class="card-body">

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autofocus>

                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label>Password</label>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password" required>

                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Remember -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label">Remember Me</label>
                        </div>

                        <!-- Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                Login
                            </button>
                        </div>

                        <!-- Forgot -->
                        <div class="text-center mt-3">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">Forgot Password?</a>
                            @endif
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection