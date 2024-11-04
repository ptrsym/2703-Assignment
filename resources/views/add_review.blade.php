@extends('layouts.master')

@section('content')
<div class="review-container">
    <h1>Add Review</h1>
    @if (session('error'))
    <div class="errormsg">
        <p>Error: {{session('error')}}</p>
    </div>
    @endif
    @if (isset(session('result')['error']))
    <div class="errormsg">
        <p>{{session('result')['error']}}</p>
    </div>
    @endif
    @if (isset(session('result')['success']))
    <div class="successmsg">
        <p>{{session('result')['success']}}</p>
    </div>
    @endif
    <form method="POST" action="{{ url('add_review_action') }}">
        {{csrf_field()}}

        <input type="hidden" name="item_id" value="{{$item_id}}">

        <div class="form-group username-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control username-field" required>
        </div>

        <div class="form-group rating-group">
            <label for="rating">Rating:</label>
            <select id="rating" name="rating" class="form-control rating-field" required>
                <option value="">Select Rating</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>

        <div class="form-group reviewtext-group">
            <label for="reviewtext">Review:</label>
            <textarea id="reviewtext" name="reviewtext" class="form-control reviewtext-field" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>
    <hr>
    <a href="{{url("/reviews/{$item_id}")}}">
        <button type="submit" class="btn btn-primary">Back</button>
    </a>
</div>
@endsection
