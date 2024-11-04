@extends('layouts.master')

@section('title', 'Item Reviews')

@section('content')
<div class="item-reviews-container container">
    <h1>{{ $details['item'][0]->productname }}</h1>
    
    <div class="item-details">
        <p><strong>Manufacturer:</strong> {{ $details['manufacturer'][0]->manname }}</p>
    </div>

    <a href="{{ url("add_review/{$details['item'][0]->id}") }}" style="text-decoration: none;" class="btn">Add Review</a>

    <div class="reviews-section">
        <h2>Customer Reviews</h2>
        <hr> 
        @if(count($details['reviews']) > 0) 
            @foreach ($details['reviews'] as $review)
                <div class="review">
                    <h3>Rating: {{ $review->rating }}/5</h3>
                    <p>{{ $review->reviewtext }}</p>
                    <small>Reviewed by: {{ $review->username }} on {{ $review->postdate }}</small>
                    <a class="btn edit_btn" style="padding: 5px 10px !important; text-decoration: none; margin-bottom: 10px;" href="{{url("/edit_review/{$review->item_id}/{$review->id}")}}">Edit Review</a>
                </div>
                <hr> 
            @endforeach
        @else
            <p>No reviews available for this item yet.</p>
        @endif
    </div>

    <div class="back-link">
        <a style="color: #66b2ff; text-decoration: none;"href="{{ url('/') }}">Back to Items List</a>
    </div>
</div>
@endsection
