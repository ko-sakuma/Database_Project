<?php 
$login_required = True;
$seller_status_required = True;
require_once('functions/connect_to_database.php');
require_once('functions/functions.php');

// check that the form in create_auction.php was submitted
if (isset($_POST["submit"])) {
    // Get the create_auction.php form submission and store them as variables
    //$product_id = NULL and $product_creation_date = NULL
    $user_id = intval($_SESSION['user_id']); 
    $product_title = strval($_POST['product_title']);
    $product_status_id = intval(0);
    $product_year = intval($_POST['product_year']);
    $product_details = strval($_POST['product_details']); //nullable
    $product_category_id = intval($_POST['product_category_id']); 
    $product_image_url = strval($_POST['product_image_url']); //nullable
    $product_condition_id = intval($_POST['product_condition_id']);
    $product_starting_price = floatval($_POST['product_starting_price']);
    $product_reserve_price = floatval($_POST['product_reserve_price']);
    $product_end_date = strval($_POST['product_end_date']); 

    // calling create_auction_form_is_empty function to check if any required fields are empty.
    if (create_auction_form_is_empty($product_title, $product_year, $product_category_id, $product_condition_id, $product_starting_price, $product_reserve_price, $product_end_date) !== false) {
        header("Location: create_auction.php?error=auction_form_is_empty"); // if at least 1 empty input field
        exit();
    }

    //calling invalid_pricing function to check if reserve price is smaller than start price or not.
    if (invalid_pricing($product_starting_price, $product_reserve_price) !== false){
        header("Location: create_auction.php?error=invalid_pricing"); // if reserve price is smaller than start price
        exit();
    }

    include_once('header.php');

    // Add user input to the product table in our database
    $sql = "INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_details`, `product_category_id`, `product_image_url`, `product_condition_id`, `product_starting_price`, `product_reserve_price`, `product_end_date`, `product_creation_date` ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Create a prepared statement
    $stmt = mysqli_stmt_init($connection);

    // Prepare the prepared statement
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        // If error then return error
        print_alert("There is an SQL error. Please try again or contact the developers", "danger");
    } else {
        // Bind the output from the registration form to the placeholder ("?" above)
        mysqli_stmt_bind_param($stmt, "iiisisisiddss", $NULL, $user_id, $product_status_id, $product_title, $product_year, $product_details, $product_category_id, $product_image_url, $product_condition_id, $product_starting_price, $product_reserve_price, $product_end_date, $NULL);

        // Execute parameters
        mysqli_stmt_execute($stmt);

        //print success message if an auction is successfully created. User can then jump to mylistings.php through the jumplink.
        echo('<div class="text-center">Auction successfully created! <a href="mylistings.php">View your new listing.</a></div>');
        notify_user($connection, $user_id, 'Congratulations! Your auction is now successfully created!');
    }
}

// if the submit button was not pressed then send user back to create_auction.php
else {
    print_alert("You did not submit the create auction form. If you would like to create an auction, please submit the form properly.", "danger");
    header('create_auction.php');
    exit();
}

include_once("footer.php");

?>
