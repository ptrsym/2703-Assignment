@extends('layouts.master')

@section('title', 'Manufacturer Details')

@section('content')
<div class="manufacturer-details">
    <h1>Manufacturer: {{ $manufacturer[0]->manname }}</h1>

    @if (count($items) < 1)
        <p>No items available for this manufacturer.</p>
    @else
            @foreach ($items as $item)
                    <h2><a style="color: #66b2ff; text-decoration: none;" href="{{ url("reviews/{$item->id}") }}">{{ $item->productname }}</a></h2>
                    <p>Average Rating: {{ $item->average_review }}</p>
            @endforeach
    @endif
</div>
@endsection
