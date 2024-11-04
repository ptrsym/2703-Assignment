@extends('layouts.master')

@section('title', 'Manufacturers')

@section('content')
<div class="home-container">
        @if ($manufacturers)
            @foreach ($manufacturers as $manufacturer)
            <div class="item-list">
                <h2><a style="color: #66b2ff; text-decoration: none;" href="{{ url("manufacturer_detail/$manufacturer->id") }}">{{ $manufacturer->manname }}</a></h2>
                <p>Average Rating: {{$manufacturer->average_rating}}</p>
            </div> 
        <hr>
            @endforeach
        @else
            <p>No manufacturers available.</p>
        @endif
</div>
@endsection

