@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 3rem;">
    <div style="max-width: 450px; margin: 0 auto;">
        <div class="card">
            <h2 class="text-center">Login to your Account</h2>
            <p class="text-center">Welcome back! Please enter your details.</p>

            <form action="{{ url('api/login') }}" method="POST" class="mt-4">
                @csrf <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Login
                    </button>
                </div>
                
                <p class="text-center mt-3">
                    Don't have an account? <a href="{{ route('register') }}">Sign up</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection