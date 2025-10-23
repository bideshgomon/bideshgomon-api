@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 3rem;">
    <div style="max-width: 450px; margin: 0 auto;">
        <div class="card">
            <h2 class="text-center">Create your Account</h2>
            <p class="text-center">Join BideshGomon today.</p>

            <form action="{{ url('api/register') }}" method="POST" class="mt-4">
                @csrf

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Create Account
                    </button>
                </div>

                <p class="text-center mt-3">
                    Already have an account? <a href="{{ route('login') }}">Login</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection