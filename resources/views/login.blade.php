@extends('layouts.master')

@section('title', 'Login')

@section('content')
<div class="login-container">
    <h2>Login to Your Account</h2>
    <form method="POST" action="{{ url("/login") }}">
        {{csrf_field()}}
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">Login</button>

        <p>Don't have an account? <a href="{{url("/register")}}">Sign up</a></p>
        @if (isset($error))
            <div class="errormsg">
                <p>{{$error}}</p>
            </div>
        @endif
    </form>
</div>
@endsection
