@extends('layouts.master')

@section('content')
<div class="review-container">
    <h1>Edit Review</h1>
    @if (session('error'))
    <div class="errormsg">
        <p>Error: {{ session('error') }}</p>
    </div>
    @endif
    @if (isset(session('result')['error']))
    <div class="errormsg">
        <p>{{ session('result')['error'] }}</p>
    </div>
    @endif
    @if (isset(session('result')['success']))
    <div class="successmsg">
        <p>{{ session('result')['success'] }}</p>
    </div>
    @endif
    
    <div class="username_edit">
        <p>{{$username}}</p>
        </div>
      
    <form method="POST" action="{{ url('edit_review_action') }}">
        {{ csrf_field() }}
        <input type="hidden" name="review_id" value="{{ $review_id }}">
        <input type="hidden" name="item_id" value="{{ $item_id }}">
        <input type="hidden" name="username" value="{{ $username }}">

        <div class="form-group rating-group">
            <label for="rating">Rating:</label>
            <select id="rating" name="rating" class="form-control rating-field" required>
                <option value="">Select Rating</option>
                <option value="1" {{ $review->rating == 1 ? 'selected' : '' }}>1</option>
                <option value="2" {{ $review->rating == 2 ? 'selected' : '' }}>2</option>
                <option value="3" {{ $review->rating == 3 ? 'selected' : '' }}>3</option>
                <option value="4" {{ $review->rating == 4 ? 'selected' : '' }}>4</option>
                <option value="5" {{ $review->rating == 5 ? 'selected' : '' }}>5</option>
            </select>
        </div>

        <div class="form-group reviewtext-group">
            <label for="reviewtext">Review:</label>
            <textarea id="reviewtext" name="reviewtext" class="form-control reviewtext-field" rows="5" required>{{ $review->reviewtext }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Review</button>
    </form>
</div>
@endsection
