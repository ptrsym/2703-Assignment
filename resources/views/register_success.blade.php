@extends('layouts.master')

@section('title', 'Success')

@section('content')
<div class="container">
        <div class="home-content">
            <h1>{{ session('infomsg') }}</h1>
        </div>
    </div>
@endsection