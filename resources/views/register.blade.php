@extends('layouts.master')

@section('title', 'Register')

@section('content')
<div class="register-container">
    <h2>Create an Account</h2>

    <form method="POST" action="add_user_action">
        {{csrf_field()}} 

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
        </div>

        <button type="submit" class="btn">Register</button>
        
        @if (isset($error))
            <div class="errormsg">
                <p>{{ $error }}</p>
            </div>  
        @endif
    </form>
    <p>Already have an account? <a href="{{url("/login")}}">Login here</a>.</p>
</div>
@endsection