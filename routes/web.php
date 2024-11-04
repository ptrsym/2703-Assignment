<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    $sort = request('sort', 'reviews');
    $isDescending = request('isDescending', 'high_to_low');
    //dd($isDescending, $sort);

    $items = get_items();

    //add the review number to each entry
    foreach ($items as $item) {
        $item->review_count = get_review_number($item->id);
        $item->average_rating = calc_average($item->id);
    }

    //convert to collection for sorting functionality
    $items = collect($items);

    //sort based on the value
    if ($sort == 'reviews') {
        $items = $items->sortBy('review_count');
    }
    if ($sort == 'rating') {
        $items = $items->sortBy('average_rating');
    }

    //sort based on configuration of h to l
    if ($isDescending == 'high_to_low') {
        $items = $items->reverse();
    }

    return view('home')
    ->with('items', $items)
    ->with('sort', $sort)
    ->with('isDescending', $isDescending);
});

Route::get('/login', function () {
    return view('login');
});

//unnecessary
Route::post('/login', function () {
    $username = (request('username'));
    $password = (request('password'));
    $error = "";

    $login = login($username, $password);
    if ($login){
        return redirect(url("/product_list"));
    } else {
        $error = "Invalid login credentials or user doesn't exist";
        return view('/login')->with('error', $error);
    }
});

Route::get('/register', function () {
    return view('register');
});

Route::get('/register/success', function () {
    return view('/register_success');
});

Route::post('register_user_action', function () {
    
    $username = (request('username'));
    $password = (request('password'));
    $password_confirmation = (request('password_confirmation'));
    $error = "";

    //reject if passwords dont match
    if ($password != $password_confirmation){
        $error = "Passwords do not match.";
        return view('/register')->with('error', $error);
    }

    //validate user input and handle
    $validation = validate_user($username);
    if ($validation['error']) {
        return view('register')->with('error', $validation['error']);
    } else {
        $validName = $validation['name'];
        $wasCleaned = $validation['wasCleaned'];
    }

    //try and add the user to DB if valid
    $result = register_user($validName, $password);

    //handle error case
    if ($result['error'] != null) {
        return view('register')->with('error', $result['error']);
    }
    //conditionally display success
    if ($wasCleaned) {
        return redirect(url("/register/success"))->with('infomsg', 
        sprintf("Registration successful but your name was changed to %s", $validName));
    } else {
        return redirect(url("/register/success"))->with('infomsg', "Registration successful!");
    }
});

//return details to reviews page pages on url
Route::get('/reviews/{id}', function($id) {
    $details = get_item_details($id);
    return view('reviews')->with('details', $details);
});

//navigate to a add review form based on url id
Route::get('/add_review/{id}', function ($id) {
    return view('add_review')->with('item_id', $id);
});

//add review
Route::post('add_review_action', function () {
    $username = (request('username'));
    $rating = request('rating');
    $reviewtext = request('reviewtext');
    $itemId = request('item_id');

    //ensure userid is valid
    $validatedUser = validate_user($username);

    //retrun and prompt user if error found
    if ($validatedUser['error']) {
        return redirect(url("/add_review/{$itemId}"))->with('error', $validatedUser['error']);
    }

    $validatedUsername = $validatedUser['name'];

    // check if user is in the system
    $userId = getUseridByName($validatedUsername);

    if ($userId) {
        //check if the user has already submitted a review for this item
        if(check_review($userId, $itemId)){
            return redirect(url("/add_review/{$itemId}"))->with('error', 'You can only review an item once.');
        }
        // add the user's review
        else {
            $result = add_review($validatedUsername, $rating, $reviewtext, $itemId);
            return redirect(url("/add_review/{$itemId}"))->with('result', $result);
        }
    }
    //add user to users if doesnt exist
    add_user($validatedUsername);
    //add the review
    $result = add_review($validatedUsername, $rating, $reviewtext, $itemId);
    return redirect(url("/add_review/{$itemId}"))->with('result', $result);
});


//navigate to add item page
Route::get('/add_item', function () {
    return view('add_item');
});


//handler for adding an item
Route::post('/add_item_action', function () {
    $product = request('productname');
    $manufacturer = request('manufacturer');

    //validate the product name and return if an error is found
    $validatedProduct = validate_name($product);
    if ($validatedProduct['error']) {
        return redirect(url("/add_item"))->with('result', $validatedProduct['error']);
    }

    //validate the manufacturer's name and return if error is found
    $validatedMan = validate_name($manufacturer);
    if ($validatedMan['error']) {
        return redirect(url("/add_item"))->with('result', $validatedMan['error']);
    }

    $result = add_item($product, $manufacturer);
    // dd($result);
    return redirect(url("/add_item"))->with('result', $result); 
});

    //routes to the edit_review form and populates it with the corresponding review details
Route::get('/edit_review/{item_id}/{review_id}', function ($item_id, $review_id) {
    $review_details = get_review_details($review_id);
    $review_username = getUsernameById($review_details->user_id);
    return view('edit_review')
    ->with('item_id', $item_id)
    ->with('review_id', $review_id)
    ->with('review', $review_details)
    ->with('username', $review_username);
});

Route::post('edit_review_action', function () {
    //retrieve details
    $item_id = request('item_id');
    $review_id = request('review_id');
    $rating = request('rating');
    $reviewtext = request('reviewtext');
    //update review
    update_review($review_id, $reviewtext, $rating);
    return redirect(url("/reviews/{$item_id}"))->with('item_id', $item_id);
});


// delete an item handler
 Route::delete('delete_item_action/{id}', function ($id) {
    $result = delete_item($id);
    //inform user of status
    if ($result) {
        return redirect('/')->with('success', 'Item deleted');
    } else {
        return redirect('/')->with('error', 'Error deleting item');
    }
 });


 Route::get('/manufacturer_list', function () {
    // get all manufacturers
    
    $mans = get_manufacturers();
    //for each manufacturer get all their items
    foreach ($mans as $man) {
        $items = get_items_by_man_id($man->id);
        //note the total number of items
        $total_items = count($items);
        $totalAvg = 0;
        // for each item get their average rating
        foreach ($items as $item) {
            $totalAvg += calc_average($item->id);
        }
        //account for no items by man
        if ($total_items > 0) {
            $man->average_rating = round($totalAvg / $total_items, 1);
        } else {
            $man->average_rating = 0;
        }
    }
    return view('manufacturer_list')->with('manufacturers', $mans);
 });

 Route::get('manufacturer_detail/{id}', function($id) {
    // get all man info by id
    $manufacturer = get_man_by_id ($id);
    // get all items from that man
    $items = get_items_by_man_id($id);
    //calc the avg for each item and append it to the array
    foreach ($items as $item) {
        $avg = calc_average($item->id);
        $item->average_review = $avg;
    }
    return view('manufacturer_detail')
    ->with('items', $items)
    ->with('manufacturer', $manufacturer);
 });

// get all manufacturers
 function get_manufacturers () {
     $sql = "select * from manufacturers";
     return DB::select($sql);
 }

 //get manufacturer details based on an id
function get_man_by_id ($id) {
    $sql = "select * from manufacturers where id =?";
    return DB::select($sql, array($id));
}

// get all items from a manufacturer based on the manufacturer's id
 function get_items_by_man_id ($man_id) {
    $sql = "select * from items where manufacturer_id = ?";
    return DB::select($sql, array($man_id));
 }

 //deletes an item and all associated reviews
function delete_item($id) {
    //delete reviews before the item is deleted
    $sql = "delete from reviews where item_id = ?";
    DB::delete($sql, array($id));
    //now delete the item
    $sql = "delete from items where id = ?";
    $deleted = DB::delete($sql, array($id));
    return $deleted;
}

//calculates the average of the reviews of an item based on its id and rounds to 1 decimal
function calc_average ($id) {
    $sql = "select rating from reviews where item_id = ?";
    $results = DB::select($sql, array($id));
    $sum = 0;
    //access each result and extract the rating as a sum
    foreach($results as $result) {
        $sum += $result->rating;
    }
    //count the total number of results and divide by sum. avoid division by 0 if no ratings
    $average = count($results) > 0 ? round($sum/count($results), 1) : 0;
    return $average;
}

// counts the total number of reviews of an item based off of the item_id
function get_review_number($id) {
    $sql = "select * from reviews where item_id = ?";
    $results = DB::select($sql, array($id));
    //count the number of results found
    $number = count($results);
    return $number;
}

// updates a review with new values
function update_review($review_id, $reviewtext, $rating) {
    $sql = "UPDATE reviews set reviewtext = ?, postdate = CURRENT_DATE, rating = ? where id =?";
    $updated = DB::update($sql, array($reviewtext, $rating, $review_id));
}

// retrieve instances where a user has already submitted a review
function check_review ($id, $itemId) {
    $sql = "select * from reviews where user_id = ? and item_id = ?";
    return DB::select($sql, array($id, $itemId));
}

// get all details on a review based on its id
function get_review_details ($review_id) {
    $sql = "select * from reviews where id = ?";
    $result = DB::select($sql, array($review_id));
    // handles the case where no review could be found
    return $result ? $result[0] : null; 
}

//get a user ID by the username if exists else null
function getUseridByName ($name) {
    $sql = "select id from users where username = ?";
    $result = DB::select($sql, array($name));
      // handles the case where no review could be found
    return $result ? $result[0]->id : null;
}

//get a user name by their ID
function getUsernameById ($id) {
    $sql = "select username from users where id = ?";
    $result = DB::select($sql, array($id));
      // handles the case where no review could be found
    return $result ? $result[0]->username : null;
}

//get all item names with their id from the DB
function get_items() {
    $sql = "select id, productname from items";
    return DB::select($sql);
}

// consolidate all details about an item for display
function get_item_details($id) {

    $sql = "select * from items where id =?";
    $item = DB::select($sql, array($id));

    $sql = "select manname from manufacturers where id =?";
    $manufacturer = DB::select($sql, array($item[0]->manufacturer_id));
    
    $sql = "select * from reviews where item_id=?";
    $reviews = DB::select($sql, array($id));

    //need to get the username from the id to display in the view
    foreach ($reviews as $review) {
        $sql = "select username from users where id =?";
        $user = DB::select($sql, array($review->user_id));
        $review->username = $user[0]->username;
    }
    return ['item'=>$item, 'manufacturer'=>$manufacturer, 'reviews' => $reviews];
}

// out of scope
function login($username, $password) {
    $sql = "select id from users where username = ? AND pass = ?";
    $result = DB::select($sql, array($username, $password));
    if ($result) {
        //found user
        return $result[0]->id;
    } else {
        //invalid
        return false;
    }
}

// adds a user based on their username
function add_user ($username) {
    $userId = getUseridByName($username);
    //checks if user already exists
    if ($userId) {
        return ['error'=> 'Duplicate user found.'];
    }
    //setting a default password because it isn't a register method can fix if needed later
    $sql = "INSERT into users (username, pass) values (?, 123)";
    DB::insert($sql, array($username));
}

// out of scope
function register_user($username, $password) {
    $sql = "select username from users where username = ?";
    $check = DB::select($sql, array($username));

    if (!empty($check)) {
        return ['error' => 'Duplicate username found, please try another username.', 'name' => $username];
    }

    $sql = "INSERT into users (username, pass) values (?, ?)";
    $result = DB::insert($sql, array($username, $password));
    return ['error' => null, 'name' => $username];    
}

// add an item  based on user entered values
function add_item($productname, $manname) {


    // check if the product is already in the system first
    $sql = "select productname from items where productname = ?";
    $check = DB::select($sql, array($productname));

    if (!empty($check)) {
        return ['error' => 'Product already exists', 'productname' => $productname];
    }

    //insert manufacturer and return id
    $manId = add_manufacturer($manname);

    // add the item and the manufacturer id
    $sql = "INSERT into items (productname, manufacturer_id) values (?, ?)";
    DB::insert($sql, array($productname, $manId));

    // return a success message to process
    return ['success' => 'Item added successfully', 'productname' => $productname];

}

// adds a review to the system
function add_review($username, $rating, $reviewtext, $itemId ) {
    $userId = getUseridByName($username);
    $sql = "INSERT into reviews (user_id, item_id, reviewtext, rating) VALUES (?, ?, ?, ?)";
    DB::insert($sql, array($userId, $itemId, $reviewtext, $rating));

    //verify submission
    $checkSql = "select * from reviews where user_id = ? and item_id = ? and reviewtext = ? and rating = ?";
    $review = DB::select($checkSql, array($userId, $itemId, $reviewtext, $rating));

    // if a review value was found
    if ($review) {
        return ['success' => 'User review submitted!'];
    } else {
        return ['error' => 'Failed to submit review'];
    }
}

// uses strpos to check if the invalid chars exist in a string !== ensures first index isnt misvalued
function validation_1($name) {
    return  
        strpos($name, '-') !== false || 
        strpos($name, '_') !== false ||
        strpos($name, '+') !== false ||
        strpos($name, '"') !== false;
    }   

    //clean the string of odd numbers
function validation_2($name) {
    return preg_replace('/[13579]/', '', $name);
}

//check if 2 or less chars
function validation_3($name) {
    if (strlen($name) < 3){
        return true;
    }
}

//consolidated validation logic
function validate_user ($name) {

    //flag to track the error message to send
    $hadOdd = false;

     // check for invalid characters
   if(validation_1($name)){ 
        $error = "Invalid characters detected -, _, +, \"";
        //return key value array to unpack
        return ['error' => $error, 'name' => null, 'wasCleaned' => false]; 
    }
    // identify and remove odd numbers
    if (preg_match('/[13579]/', $name)) {
        $hadOdd = true;
        $cleanedName = validation_2($name);
    } else {
        $cleanedName = $name;
    }
    // check if username is < 3 chars after cleaning
    if (validation_3($cleanedName)) {
    //check if username was cleaned and became too short
        if ($hadOdd) {
            $error = "Your name was cleaned of odd numbers and is now too short. Names must be longer than 2 characters.";
        } else {
            $error = "Names must be longer than 2 characters";
        }
        return ['error' => $error, 'name' => null, 'wasCleaned' => $hadOdd];
    }
    return ['error' => null, 'name' => $cleanedName, 'wasCleaned' => $hadOdd];   
}

function validate_name ($name) {

     // check for invalid characters
   if(validation_1($name)){ 
        $error = "Invalid characters detected -, _, +, \"";
        //return key value array to unpack
        return ['error' => $error, 'name' => null]; 
    }

    // check if username is < 3 chars after cleaning
    if (validation_3($name)) {
    //check if username was cleaned and became too short
        $error = "Names must be longer than 2 characters";
        return ['error' => $error, 'name' => null];
    }
    return ['error' => null, 'name' => $name];   
}

function add_manufacturer ($manname) {
    $sql = "select * from manufacturers where manname = ?";
    $check = DB::select($sql, array($manname));
    if(!empty($check)) {
        //return the ID of the manufacturer if it exists
        return $check[0]->id; 
    } else {
        $sql = "INSERT into manufacturers (manname) values (?)";
        DB::insert($sql, array($manname));
        //retrieve the id of the last entry
        return DB::getPdo()->lastInsertId();
    }
}

    