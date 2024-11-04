@extends('layouts.master')

@section('title', 'Add New Item')

@section('content')
<div class="add-item-container">
    <h2>Add a New Item</h2>

    <form method="POST" action="{{ url('add_item_action') }}">
        {{ csrf_field() }}

        <div class="form-group">
            <label for="productname">Product Name</label>
            <input type="text" id="productname" name="productname" placeholder="Enter item name" required>
        </div>

        <div class="form-group">
            <label for="manufacturer">Manufacturer</label>
            <input type="text" id="manufacturer" name="manufacturer" placeholder="Enter manufacturer name" required>
        </div>

        <button type="submit" class="btn">Add Item</button>
        <a class="btn" style="text-decoration: none;" href="{{url('/')}}">Back</a>

        @if (session('result'))
            @if (is_string(session('result')))
            <div class="errormsg">
                <p>{{ session('result') }}</p>
            </div>
            @elseif (isset(session('result')['error']))
            <div class="errormsg">
                <p>{{ session('result')['error'] }} '{{session('result')['productname']}}'</p>
            </div> 
            @elseif (isset(session('result')['success']))
            <div class="successmsg">
                <p>{{ session('result')['productname']}} {{ session('result')['success'] }}</p>
            </div>
            @endif  
        @endif
    </form>
</div>
@endsection
