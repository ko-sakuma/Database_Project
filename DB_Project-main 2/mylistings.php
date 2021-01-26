<?php
$pageTitle = "My listings";
$login_required = True;
$seller_status_required = True;
include_once("header.php");
?>

<!-- HTML: create container -->
<div class="container mt-5">
<!-- HTML: create list group -->
<ul class="list-group">

<h2 class="my-3">My listings</h2>

<?php 
 // If user is not logged in or not a seller, they should not be able to use this page.
 if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'Seller') {
  header('Location: browse.php');
  }

// Perform a query to pull up their auctions.
  $sql = "
  SELECT
  MAX(
      IFNULL(
          b.bid_amount,
          p.product_starting_price
      )
  ) AS max_price,
  p.product_id,
  p.user_id,
  p.product_title,
  p.product_status_id,
  p.product_year,
  p.product_starting_price,
  p.product_reserve_price,
  p.product_image_url,
  p.product_details,
  p.product_end_date,
  p.product_creation_date
   
  FROM
    product AS p
  LEFT JOIN bid AS b
  ON
    b.user_id = p.user_id
  WHERE
    p.user_id = ?
  GROUP BY
    p.product_id;
  ";

  // Create a prepared statement.
  $stmt = mysqli_stmt_init($connection);

  // Test that the prepared statement is valid.
  if (!mysqli_stmt_prepare($stmt, $sql)) {
    print_alert("There is an SQL error. Please try again or contact the developers", "danger");
    $my_listing_result = array('');

  } else {
    // Bind inputs to '?' placeholder.
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    // Execute the SQL query.
    mysqli_stmt_execute($stmt);

    // Obtain results and assign to variable.
    $my_listing_result = mysqli_stmt_get_result($stmt);
  }

  // loop the results
  while ($row = mysqli_fetch_assoc($my_listing_result)) 
    {
      //product table
      $product_id = $row['product_id'];
      $max_price = current_price_number_only($product_id); 
      $user_id = $row['user_id']; 
      $product_title = $row['product_title']; 
      $product_details = $row['product_details']; 
      $product_starting_price = $row['product_starting_price'];
      $product_reserve_price = $row['product_reserve_price'];
      $product_end_date = new DateTime($row['product_end_date']); 
      
      if ($row['product_image_url'] == null) {
        $product_image_url = 'src/example.jpg';
      }
      else {
        $product_image_url = $row['product_image_url'];
      }
      
      //get the remaining time for the auction to end. 
      $now = new DateTime();
      
      if ($now > $product_end_date) {
          $time_remaining = 'This auction has ended';

      } else { // Get interval:
          $time_to_end = date_diff($now, $product_end_date);
          $time_remaining = display_time_remaining($time_to_end) . ' remaining';
    }

  // Print HTML
    if ($max_price >= $product_reserve_price) 
    {
      echo 
      ('
      <li class="list-group-item d-flex justify-content-between">
      <div><a href="listing.php?product_id=' . $product_id . '"><img src="' .$product_image_url. '" class="crop_listing_thumbnail" alt="example pic" ></a></div>
      <div class="p-2 mr-5" style="width: 70%"><h5><a href="listing.php?product_id=' . $product_id . '">' . $product_title . '</a></h5><i>' . $time_remaining . ' </i><div>' . $product_details . '</div></div>
      <div><div><small>Current Price:</small></div><div class="mylistings_price_green"><b>£'. $max_price .'</b></div> <div><small>Reserve Price:</small></div><div><b>£' . $product_reserve_price . '</b></div><div><small>Starting Price:</small></div><div><b>£'. $product_starting_price .'</b></div></div>
      </li>
      ');
    } else {
    echo 
    ('
    <li class="list-group-item d-flex justify-content-between">
    <div><a href="listing.php?product_id=' . $product_id . '"><img src="' .$product_image_url. '" class="crop_listing_thumbnail" alt="example pic" ></a></div>
    <div class="p-2 mr-5" style="width: 70%"><h5><a href="listing.php?product_id=' . $product_id . '">' . $product_title . '</a></h5><i>' . $time_remaining . ' </i><div>' . $product_details . '</div></div>
    <div><div><small>Current Price:</small></div><div class="mylistings_price_red"><b>£'. $max_price .'</b></div> <div><small>Reserve Price:</small></div><div><b>£' . $product_reserve_price . '</b></div><div><small>Starting Price:</small></div><div><b>£'. $product_starting_price .'</b></div></div>
    </li>
    ');
    }

}
  ?>

  <?php include_once("footer.php")?>
