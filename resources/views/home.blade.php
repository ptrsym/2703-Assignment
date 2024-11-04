@extends('layouts.master')

@section('title', 'Home')

@section('content')
<div class="home-container">
    <h1>Welcome to Pet Food Review</h1>

    @if(session('error'))
    <div class="errormsg">
        <p>{{session('error')}}</p>
    </div>
    @endif
    @if(session('success'))
    <div class="successmsg">
        <p>{{session('success')}}</p>
    </div>
    @endif
    <div class="sorting">
        <form method="GET" action="{{ url('/') }}">
            <label for="sort">Sort by:</label>
            <select name="sort" id="sort">
                <option value="reviews" {{$sort == 'reviews' ? 'selected' : '' }}>Number of Reviews</option>
                <option value="rating" {{$sort == 'rating' ? 'selected' : '' }}> Average Rating</option>
            </select>
            <select name="isDescending" id="isDescending">
                <option value="high_to_low" {{$isDescending == 'high_to_low' ? 'selected' : '' }}>Highest to Lowest</option>
                <option value="low_to_high" {{$isDescending == 'low_to_high' ? 'selected' : '' }}>Lowest to Highest</option>
            </select>
            <button class="btn" type="submit">Sort</button>
        </form>
    </div>

        @if ($items)
            @foreach ($items as $item)
            <div class="item-list">
                <h2><a style="color: #66b2ff; text-decoration: none;" href="{{ url("reviews/$item->id") }}">{{ $item->productname }}</a></h2>
                <form class="form-btn" action="{{url("delete_item_action/{$item->id}")}}" method="post">
                {{csrf_field()}}
                @method('DELETE')
                <button class="btn btn-delete" type="submit">
                    Delete Item
                </button>
            </form>
        </div>
        <div class="new_data">
            <p>Number of Reviews: {{$item->review_count}}</p>
            <p>Average Review Rating: {{$item->average_rating}}</p>
        </div> 
        <hr>
            @endforeach
        @else
            <p>No items available.</p>
        @endif
</div>
@endsection
