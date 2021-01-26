<?php
// Display a Bootstrap alert
// Documentation: https://getbootstrap.com/docs/4.0/components/alerts/
function print_alert($message, $messageStyle)
{
    // $message: a string of the message you want to display
    // $messageStyle: a string determining the color of the alert [red = 'danger'; green = 'success'; blue = 'primary'; yello = 'warning'].
    // For all colors see documentation!
    echo '<div style="text-align:center;" class="alert alert-' . $messageStyle . ' align="center" role="alert">' . $message . '</div>';
}

// Check if user_email is an validated email (used in process_registration.php)
function invalid_user_email($user_email)
{
    // takes user_email as input and validates it if it is actually an email address
    if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $result = false; // if user_email is validated
    } else {
        $result = true; // if user_email is not validated
    }
    return $result;
}

// Check if the user_password matches the user_password_repeat (used in process_registration.php)
function match_user_password($user_password, $user_password_repeat)
{
    // takes user_password and user_password_repeat from registration form as input and compares them
    if ($user_password === $user_password_repeat) {
        $result = false; // if user_password and user_password_repeat are the same
    } else {
        $result = true; // if user_password and user_password_repeat are NOT the same
    }
    return $result;
}

// Check if the user_password has between 8 and 16 characters (used in process_registration.php)
function user_password_length($user_password_length)
{
    // takes user_password and user_password_repeat from registration form as input and compares them
    if (8 <= $user_password_length && $user_password_length <= 40) {
        $result = false; // if user_password and user_password_repeat are the same
    } else {
        $result = true; // if user_password and user_password_repeat are NOT the same
    }
    return $result;
}

// check if register submission is empty (used in process_registration.php)
function register_is_empty($user_name, $user_email, $user_password, $user_password_repeat)
{
    // checks if $user_name, $user_email, $user_password, $user_password_repeat are empty or not
    if (empty($user_name) || empty($user_email) || empty($user_password) || empty($user_password_repeat)) {
        $result = true; // if one or more of the fields is empty
    } else {
        $result = false; // if all fields are filled out
    }
    return $result;
}

// check if create_auction submission is empty (used in create_auction_result.php)
function create_auction_form_is_empty($product_title, $product_year, $product_category_id, $product_condition_id, $product_starting_price, $product_reserve_price, $product_end_date)
{
    // checks if $product_title, $product_year, $product_category_id, $product_condition_id, $product_starting_price, $product_reserve_price, $product_end_date are empty or not
    if (empty($product_title) || empty($product_year) || empty($product_category_id) || empty($product_condition_id) || empty($product_starting_price) || empty($product_reserve_price) || empty($product_end_date)) {
        $result = true; // if one or more of the fields is empty
    } else {
        $result = false; // if all fields are filled out
    }
    return $result;
}

// check if product_starting_price is valid (used in create_auction_result.php)
function invalid_pricing($product_starting_price, $product_reserve_price)
{
    if ($product_starting_price > $product_reserve_price) {
        $result = true; // condition is NOT satisfactory. 
    } else {
        $result = false; // condition is satisfactory. 
    }
    return $result;
}

// Check if a user is unique based on user_name and user_email (used in process_registration.php)
function unique_user($connection, $user_name, $user_email)
{
    // takes $connection, $user_name, $user_email to check if user is already registered with user_name or user_email
    $sql = "
            SELECT
                u.user_email,
                u.user_name
            FROM
                users AS u
            WHERE
                u.user_email LIKE ?;";

    // Create a prepared statement
    $stmt = mysqli_stmt_init($connection);
    // Prepare the prepared statement
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        // If error then return error
        print_alert("There is an SQL error. Please try again or contact the developers", "danger");
    } else {
        // Bind input from login to the placeholder ("?" above)
        mysqli_stmt_bind_param($stmt, "s", $user_email);
        // Execute parameters
        mysqli_stmt_execute($stmt);
        // Get result from database
        $result = mysqli_stmt_get_result($stmt);
        // Fetch results
        $result_fetched = mysqli_fetch_assoc($result);
        // Store database query results
        $user_name_uniqueness = $result_fetched['user_name'];
        $user_email_uniqueness = $result_fetched['user_email'];
    }
    // compare the results from the database (user_...._uniqueness)to the user input (user_...)
    if ($user_name === $user_name_uniqueness && $user_email === $user_email_uniqueness) {
        $result = true; // if the user is NOT unique
    } else {
        $result = false; // if the user is unique
    }
    return $result;
}

// Helper function to help figure out what time to display
function display_time_remaining($interval)
{
    if ($interval->days == 0 && $interval->h == 0) { // Less than one hour remaining: print mins + seconds:
        $time_remaining = $interval->format('%im %Ss');

    } else if ($interval->days == 0) { // Less than one day remaining: print hrs + mins:
        $time_remaining = $interval->format('%hh %im');

    } else { // At least one day remaining: print days + hrs:
        $time_remaining = $interval->format('%ad %hh');
    }

    return $time_remaining;
}

// This function prints an HTML <li> element containing an auction listing
function print_listing_li($user_id, $product_id, $product_title, $product_details, $price, $num_bids, $product_image_url, $product_end_date)
{
    include_once("../watchlist_funcs.php"); // import used for watchlist functionality

    if (strlen($product_details) > 250) { // Truncate long descriptions
        $desc_shortened = substr($product_details, 0, 250) . '...';

    } else {
        $desc_shortened = $product_details;
    }

    if ($num_bids == 1) {
        $bid = ' bid';
    } else {
        $bid = ' bids';
    }

    // Calculate time to auction end
    $now = new DateTime();

    if ($now > $product_end_date) {
        $time_remaining = 'This auction has ended';

    } else { // Get interval:
        $time_to_end = date_diff($now, $product_end_date);
        $time_remaining = display_time_remaining($time_to_end) . ' remaining';
    }

    // Handle input for watchlist buttons
    $user_watching_product = user_watching_product($user_id, $product_id); // is user watching this product? TRUE/FALSE
    $has_session = isset($user_id); // is the user logged in? if not, do not show watchlist buttons


    // Check if user is logged in
    if (!$has_session) {
        $button = ''; // if user is not logged in, watchlist should not be shown
    } // If logged in, print watchlist button based on whether item is in watchlist already
    else {
        // Button if user is watching the product
        if ($user_watching_product) {
            $button = ' <div id="watch_nowatch-' . $product_id .'" style="display: none">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist(' . $user_id . ' , ' . $product_id . ')">+ Add to watchlist</button>
                        </div>
            
                        <div id="watch_watching-' . $product_id .'"  class="btn-group">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist(' . $user_id . ' , ' . $product_id . ')">- Remove watch</button>
                        </div>';
            // Buttons if user is not watching the product
        } else {
            $button = ' <div id="watch_nowatch-' . $product_id .'" class="btn-group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist(' . $user_id . ' , ' . $product_id . ')">+ Add to watchlist</button>
                        </div>
            
                        <div id="watch_watching-' . $product_id .'" style="display: none" class="btn-group">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist(' . $user_id . ' , ' . $product_id . ')">- Remove watch</button>
                        </div>';
        }
    }

    // Print HTML
    echo('
    <li class="list-group-item d-flex justify-content-between">
    <div><a href="listing.php?product_id=' . $product_id . '"><img src="' . $product_image_url . '" class="crop_listing_thumbnail" alt="product pic" ></a></div>
    <div class="p-2 mr-5" style="width: 70%"><h5><a href="listing.php?product_id=' . $product_id . '">' . $product_title . '</a></h5>' . $desc_shortened . '</div>
   
    
    <div class="text-center text-nowrap col-2">
        <span style="font-size: 1.5em">£' . number_format($price, 2) . '</span><br/>' . $num_bids . $bid . '<br/>' . $time_remaining . '<br/>
        ' . $button . '
    </div>
    </li>
  ');
}?>


    <script>
        // This performs an asynchronous call to a PHP function using POST method.
        // Sends item ID as an argument to remove item from watchlist when user clicks it.
        function addToWatchlist(user_id, product_id) {
            $.ajax('../watchlist_funcs.php', {
                type: "POST",
                data: {
                    functionname: 'add_to_watchlist',
                    arguments: [user_id, product_id]
                },

                success:
                // Callback function for when call is successful and returns obj
                    function (obj) {
                        console.log(obj);
                        var objT = obj.trim();

                        if (objT == "success") {
                            $("#watch_nowatch-" + product_id).hide();
                            $("#watch_watching-" + product_id).show();

                        } else {
                            var mydiv = document.getElementById("watch_nowatch-" + product_id);

                            // TODO: NTH: do better, maybe flash a warning alert.
                            mydiv.appendChild(document.createElement("br"));
                            mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
                        }
                    },

                error:
                    function (obj) {
                        console.log("Add to watchlist failed");
                    }
            });
        }

  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to remove item from watchlist when user clicks it.
  function removeFromWatchlist(user_id, product_id) {
      $.ajax('../watchlist_funcs.php', {
      type: "POST",
      data: {
        functionname: 'remove_from_watchlist',
        arguments: [user_id , product_id]
      },

      success:
        // Callback function for when call is successful and returns obj
        function (obj) {
          console.log(obj);
          var objT = obj.trim();

          if (objT == "success") {
            $("#watch_watching-" + product_id).hide();
            $("#watch_nowatch-" + product_id).show();
          } else {
            var mydiv = document.getElementById("watch_watching-" + product_id);
            mydiv.appendChild(document.createElement("br"));
            mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
          }
        },

      error:
        function (obj) {
          console.log("Remove from watchlist failed");
        }
    });
  }
</script>


<?php

// This function prints an HTML <li> element containing a recommendation listing (specific layout for recommendations)
function print_recommendation_li($product_id, $product_title, $product_details, $price, $num_bids, $product_image_url, $product_end_date)
{
    if (strlen($product_details) > 50) { // Truncate long descriptions
        $desc_shortened = substr($product_details, 0, 50) . '...';

    } else {
        $desc_shortened = $product_details;
    }

    if ($num_bids == 1) {
        $bid = ' bid';
    } else {
        $bid = ' bids';
    }

    // Calculate time to auction end
    $now = new DateTime();

    if ($now > $product_end_date) {
        $time_remaining = 'This auction has ended';

    } else { // Get interval:
        $time_to_end = date_diff($now, $product_end_date);
        $time_remaining = display_time_remaining($time_to_end) . ' remaining';
    }


    // Print HTML
    echo('
    <div class="card col-md-3 p-1 text-center" style="width: 18rem;">
        <div>
            <a href="listing.php?product_id=' . $product_id . '">
                <img class="card-img-top crop_recommendation_thumbnail"
                 src="' . $product_image_url . '"
                 alt="Card image cap">
             </a>
        </div> 
             
        <div class="card-body">
            <h5><a href="listing.php?product_id=' . $product_id . '">' . $product_title . '</a></h5>' . $desc_shortened . '
            <!-- here an example for you Benedikt :) -->
            <div class=" text-nowrap"><span style="font-size: 1.5em">£' . number_format($price, 2) . '</span><br/>' . $num_bids . $bid . '<br/>' . $time_remaining . '</div>
            <a href="listing.php?product_id=' . $product_id . '" class="btn btn-primary mt-3">Place your bid!</a>
        </div>
    </div>
    <div class="p-2"></div>
  ');
}


// This function returns a variable that can be used to obtain the current_bid,
// product_status_id, product_title, product_image_url and product_details for
// a specified product_id.
function get_listing_details($product_id)
{
    // Connect to database.
    include('functions/connect_to_database.php');

    // Create SQL query.
    $sql = "
       SELECT (
            SELECT IFNULL(
                (SELECT MAX(bid_amount)
                FROM bid 
                WHERE product_id = ?),
                (SELECT product_starting_price
                FROM product
                WHERE product_id = ?)
            )
        ) AS current_price, 
        product_status_id, 
        product_title, 
        product_image_url, 
        product_details, 
        product_end_date
        FROM product
        WHERE product_id = ?;
  ";

    // Create a prepared statement.
    $stmt = mysqli_stmt_init($connection);

    // Test that the prepared statement is valid.
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL ERROR: get_listing_details()";
        $listing_result = array('');

    } else {
        // Bind inputs to '?' placeholder.
        mysqli_stmt_bind_param($stmt, "iii", $product_id, $product_id, $product_id);

        // Execute the SQL query.
        mysqli_stmt_execute($stmt);

        // Obtain results and assign to variable.
        $listing_result = mysqli_stmt_get_result($stmt);
    }

    return $listing_result;
}

// This function returns whether a user has an item in their watch list. The
// arguments supplied are the users user_id and the product_id. If the user is
// watching an item this returns true if not it returns false to the caller.
function user_watching_product($user_id, $product_id)
{
    // Connect to database.
    include('functions/connect_to_database.php');

    // Create SQL query.
    $sql = "
        SELECT IF( EXISTS(
            SELECT *
            FROM watching
            WHERE user_id = ?
            AND product_id = ?), 1, 0) AS watch_query;
    ";

    // Create a prepared statement
    $stmt = mysqli_stmt_init($connection);

    // Test that the prepared statement is valid.
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $query_result = false;

    } else {
        // Bind inputs to '?' placeholder.
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);

        // Execute the SQL query.
        mysqli_stmt_execute($stmt);

        // Obtain results and assign to variable.
        $interim = mysqli_stmt_get_result($stmt);

        // Assign return variable to True or False based database query result.
        while ($row = mysqli_fetch_assoc($interim)) {
            if ($row['watch_query'] == 0) {
                $query_result = false;

            } elseif ($row['watch_query'] == 1) {
                $query_result = true;
            }
        }
    }

    return $query_result;
}

function current_price_number_only($product_id)
{
    // Connect to database.
    include('functions/connect_to_database.php');

    // Create SQL query.
    $sql = "
       SELECT (
            SELECT IFNULL(
                (SELECT MAX(bid_amount)
                FROM bid 
                WHERE product_id = ?),
                (SELECT product_starting_price
                FROM product
                WHERE product_id = ?)
            )
        ) AS current_price
        FROM product
        WHERE product_id = ?;
  ";

    // Create a prepared statement.
    $stmt = mysqli_stmt_init($connection);

    // Test that the prepared statement is valid.
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL error: get_current_price()";
        $result = array('');

    } else {
        // Bind inputs to '?' placeholder.
        mysqli_stmt_bind_param($stmt, "iii", $product_id, $product_id, $product_id);

        // Execute the SQL query.
        mysqli_stmt_execute($stmt);

        // Obtain results and assign to variable.
        $listing_result = mysqli_stmt_get_result($stmt);

        $listing_result = mysqli_fetch_assoc($listing_result);

        return $listing_result['current_price'];
    }
}

// Import PHPMailer into global namespace for use in the below email function.
use PHPMailer\PHPMailer\PHPMailer;

// Send an email using PHPMailer to other users in an auction excluding the
// user that just placed a bid notifying others of bid activity.
function email_watchers($product_id, $user_id) 
{
    // Connect to database
    include("functions/connect_to_database.php");

    // Create sql query to get all users involved in bid but not the most 
    // recent bidder.
    $sql = "SELECT u.user_email
            FROM users AS u
            INNER JOIN watching AS w
            ON u.user_id = w.user_id
            WHERE w.product_id = ?
            AND u.user_id != ?;
    ";

    // Create a prepared statement.
    $stmt = mysqli_stmt_init($connection);

    // Test that the prepared statement is valid.
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo("failed");

        $num_results = 0;

    } else {
        // Bind inputs to '?' placeholder.
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $user_id);

        // Execute the SQL query.
        mysqli_stmt_execute($stmt);

        // Obtain results and assign to variable.
        $result = mysqli_stmt_get_result($stmt);

        // Obtain the number of rows.
        $num_results = mysqli_num_rows($result);
    };

    // if no result found from the query then no email needs to be sent; no
    // other users are watching the product or the query failed; Return early.
    if ($num_results < 0) {
        return;
    }

    // use PHPMailer package
    include "PHPMailer/PHPMailer.php";
    include "PHPMailer/SMTP.php";
    include "PHPMailer/Exception.php";

    // email message preparation.
    $name = "WinAuction";
    $subject = "WinAuction Notification System";
    $body = '
        <center>
            <h3>😱 Someone just bidded on an item you were watching 😱</h3>
            <h4>Quickly!! go in app to not miss out!</h4>
            <img src="https://bit.ly/3fG0bgv" alt="Run Forest!" width="500" height="333">
        </center>
    ';
    $mailer = "works.first.time@gmail.com";

    // create new PHPMailer object
    $mail = new PHPMailer();

    // SMTP settings
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;

    // very unsafe, if this was a real project would use environment variables
    // and not check credentials into the repository.
    $mail->Username = "works.first.time@gmail.com";
    $mail->Password = 'db_project'; 
    $mail->SMTPSecure = "ssl";
    $mail->Port = 465;

    // Email settings
    $mail->setFrom($mailer, $name);
    $mail->addAddress('noreply@winauction.com');
    $mail->addReplyTo('noreply@winauction.com', 'No Reply');

    // messages sent to watchers of items as BCC. Every email sent to a single
    // user takes a non negligible amount of processing time. It is more performant
    // to send all emails in one fell swoop than lock-up the application for the 
    // most recent bidder. 
    foreach ($result as $email) {
        $mail->addBCC($email['user_email']);
    }

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = $body;

    // send the email. If an error occurs, echo this out.
    if (!$mail->send()) {
        echo("email send failure");
    }
}

// SQL Query to get min/max prices of bids/starting (price for price slider input browse.php)
// Details: it gets max/min current price if a bid has placed for product; gets starting price if no bid has been placed
function get_price_min_max($connection)
{
    $price_query = "SELECT
                    MAX(
                      IFNULL(
                        b.bid_amount,
                        p.product_starting_price
                      )
                    ) AS max_price,
                    MIN(
                      IFNULL(
                        b.bid_amount,
                        p.product_starting_price
                      )
                    ) AS min_price
                    FROM
                      product AS p
                    LEFT JOIN bid AS b
                    ON
                      b.product_id = p.product_id
                    WHERE DATE(p.product_end_date) > DATE(NOW());";

    $price_limits = mysqli_query($connection, $price_query);
    if ($price_limits === false) {
        throw new Exception(mysqli_error($connection));
    }

    $price_results = mysqli_fetch_row($price_limits);
    return $price_results;
}

// SQL Query to get earliest/latest year of product_year (for price slider input browse.hp)
function get_year_min_max($connection)
{
    $year_query = "SELECT
                    MAX(
                        p.product_year)
                        AS max_year,
                    MIN(
                      p.product_year
                    ) AS min_year
                    FROM
                      product AS p
                    WHERE DATE(p.product_end_date) > DATE(NOW());";

    $year_limits = mysqli_query($connection, $year_query);
    if ($year_limits === false) {
        throw new Exception(mysqli_error($connection));
    }

    $year_results = mysqli_fetch_row($year_limits);
    return $year_results;
}

// Get & print all product_categories from the database (e.g. used for drop-down selection)
function get_product_categories($connection, $current_category)
{
    $query = "SELECT product_category_name, product_category_id 
                FROM product_category;";
    $product_category_list = mysqli_query($connection, $query);
    if ($product_category_list === false) {
        throw new Exception(mysqli_error($connection));
    }

    // Create "all" default option

    // Create drop down menu of categories
    while ($row = mysqli_fetch_assoc($product_category_list)) {
        echo $row['product_category_name'];
        echo '<option value=' . $row["product_category_id"]; // print category optinos
        if (isset($current_category) && $current_category == $row['product_category_id']) {
            echo " selected>"; // if category has been selected earlier; display that category as selected
        } else {
            echo ">";
        }
        echo $row['product_category_name'] . '</option>';
    }
}

// used to check if there is an user registered, and if not alert user (used in login_result.php)
function validate_user_existence($user_email_from_database)
{
    if (empty($user_email_from_database)) {
        // message if $user_email_from_database is empty
        print_alert("No user registered with this email. You will get redirected to the register page. Please register as an user.", "danger");
        header("refresh:3; url=register.php");

    }
}

function validate_login($user_email_from_login_input, $user_email_from_database, $user_password_from_login_input, $user_password_from_database, $user_id_from_database, $user_role_name_from_database, $user_name_from_database)
{
    // Open Session
    if ($user_email_from_login_input == $user_email_from_database && $user_password_from_login_input == $user_password_from_database) {
        // if successful then start session
        session_start();
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user_id_from_database;
        $_SESSION['account_type'] = $user_role_name_from_database;
        $_SESSION['user_name'] = $user_name_from_database;

        // Redirect to tell users that they successfully logged in
        header("Location: browse.php?success=login");
    } // or generate error messages
    // empty input fields
    elseif (empty($user_password_from_login_input) or empty($user_email_from_login_input)) {
        header("Location: browse.php?error=no_login_input");
    } // tell user that email is not registered
    elseif ($user_email_from_login_input != $user_email_from_database) {
        header("Location: browse.php?error=email_wrong");
    } // tell user that password is wrong
    elseif ($user_password_from_login_input != $user_password_from_database) {
        header("Location: browse.php?error=password_wrong");
    }

}

function browse($connection, $user_id, $curr_page, $results_per_page, $product_search_keyword, $product_category, $browse_sorting_order, $product_price_limit, $product_year_limit)
{
    $query_search =
        "SELECT
            p.product_id,
            p.product_title,
            p.product_year,
            p.product_details,
            p.product_end_date,
            p.product_starting_price,
            p.product_image_url,
        IFNULL(
            MAX(b.bid_amount),
            p.product_starting_price
        ) AS max_bid,
        IFNULL(COUNT(b.bid_id),
        0) AS bid_count
        FROM
            product AS p
        LEFT JOIN bid AS b
        ON
            p.product_id = b.product_id
        WHERE
            DATE(p.product_end_date) > DATE(NOW())
        AND p.product_year <= ? AND p.product_status_id = 0"; // additional statements to be concatenated

    $parameter_types = "i"; // used in binding parameters to query (ff for two floats of max/min prices)

    // Concatenate product_name and product_details search keywords to query
    if (isset($product_search_keyword)) {
        // Verifying user input search keyword & assigning value
        $product_search_keyword = mysqli_real_escape_string($connection, "%" . $product_search_keyword . "%");
        // Concatenate product search keyword to query
        $query_search = $query_search . " AND (p.product_title LIKE ? OR p.product_details LIKE ?)";
        $parameter_types = $parameter_types . "ss"; // concatenate with string type parameter
    }

    // Concatenate product_category selection to query
    if (isset($product_category)) {
        // Verifying user input category & assigning value
        $product_category = intval(mysqli_real_escape_string($connection, $product_category));
        // Concatenate category condition to query
        $query_search = $query_search . " AND p.product_category_id = ?";
        $parameter_types = $parameter_types . "i"; // concatenate with int type parameter
    }

    // Concatenate GROUP BY statement to query
    $query_search = $query_search . " GROUP BY p.product_id";

    // Concatenate price limit statement to query
    $query_search = $query_search . " HAVING max_bid <= ?";
    $parameter_types = $parameter_types . "d"; // concatenate with double type parameter

    // Concatenate SORT BY statement to query
    switch ($browse_sorting_order) {
        case 'pricelow':
            $query_search = $query_search . " ORDER BY max_bid ASC";
            break;
        case 'pricehigh':
            $query_search = $query_search . " ORDER BY max_bid DESC";
            break;
        case 'datelow':
            $query_search = $query_search . " ORDER BY p.product_end_date ASC";
            break;
        case 'datehigh':
            $query_search = $query_search . " ORDER BY p.product_end_date DESC";
    }

    // Run query for first tiem to obtain number of results
    // Create prepared statement for the actual query
    $query_statement = mysqli_stmt_init($connection);
    // Check for failure of query statement
    if (!mysqli_stmt_prepare($query_statement, $query_search)) {
        echo "SQL statement failed";
        return 1;
    } else {
        // Bind parameters to placeholders in query (ss for two string placeholders)
        switch ($parameter_types) {
            case "issd": // check whether keyword is provided
                mysqli_stmt_bind_param($query_statement, $parameter_types, $product_year_limit, $product_search_keyword, $product_search_keyword, $product_price_limit);
                break;
            case "iid": // check whether category is provided
                mysqli_stmt_bind_param($query_statement, $parameter_types, $product_year_limit, $product_category, $product_price_limit);
                break;
            case "issid": // check whether keyword & category is provided
                mysqli_stmt_bind_param($query_statement, $parameter_types, $product_year_limit, $product_search_keyword, $product_search_keyword, $product_category, $product_price_limit);
                break;
            default:
                mysqli_stmt_bind_param($query_statement, $parameter_types, $product_year_limit, $product_price_limit);
        }
    }

    // Run parameters inside database
    mysqli_stmt_execute($query_statement);
    $search_result = mysqli_stmt_get_result($query_statement);
    $num_results = mysqli_num_rows($search_result);

    // Concatenate LIMIT & OFFSET statement for pagination
    $query_search = $query_search . " LIMIT ? OFFSET ?;";
    $parameter_types = $parameter_types . "ii";

    // Calculate lower limit no. of search results
    $lower_limit = ($curr_page - 1) * $results_per_page;

    // Create prepared statement for the actual query
    $query_statement = mysqli_stmt_init($connection);
    // Check for failure of query statement
    if (!mysqli_stmt_prepare($query_statement, $query_search)) {
        echo "SQL statement failed";
        die();
    } else {
        // Bind parameters to placeholders in query (ss for two string placeholders)
        switch ($parameter_types) {
            case "issdii": // check whether keyword is provided
                mysqli_stmt_bind_param($query_statement, $parameter_types, $product_year_limit, $product_search_keyword, $product_search_keyword, $product_price_limit, $results_per_page, $lower_limit);
                break;
            case "iidii": // check whether category is provided
                mysqli_stmt_bind_param($query_statement, $parameter_types, $product_year_limit, $product_category, $product_price_limit, $results_per_page, $lower_limit);
                break;
            case "issidii": // check whether keyword & category is provided
                mysqli_stmt_bind_param($query_statement, $parameter_types, $product_year_limit, $product_search_keyword, $product_search_keyword, $product_category, $product_price_limit, $results_per_page, $lower_limit);
                break;
            default:
                mysqli_stmt_bind_param($query_statement, $parameter_types, $product_year_limit, $product_price_limit, $results_per_page, $lower_limit);
        }

        // Run query second time to account for current page results (incl. LIMIT, OFFSET) inside database
        mysqli_stmt_execute($query_statement);
        $search_result = mysqli_stmt_get_result($query_statement);

        // Close connection to database
        mysqli_close($connection);

        if ($num_results < 1) {
            print_alert("Sorry, no search results here.", "danger");
        }

        // Print list of auction results in while loop
        while ($row = mysqli_fetch_assoc($search_result)) {
            $item_id = $row['product_id'];
            $title = $row['product_title'];
            $description = $row['product_details'];
            $current_price = $row['max_bid'];
            $num_bids = $row['bid_count'];
            $image_url = $row['product_image_url'];
            try {
                $end_date = new DateTime($row['product_end_date']);
            } catch (Exception $e) {
                print_alert("DateTime Error product_end_date", "danger");
            }


            print_listing_li($user_id, $item_id, $title, $description, $current_price, $num_bids, $image_url, $end_date);
        }
        return $num_results; //
    }
}

// Get number of notifications for current user (displayed in header.php)
function get_notification_num($connection, $user_id)
{
    // Query for notifications
    $notification_query = "SELECT
                                COUNT(n.notification_id) AS count_notification
                            FROM
                                notification AS n
                            RIGHT JOIN users AS u
                            ON
                                u.user_id = n.user_id
                            WHERE
                                u.user_id = ?
                            GROUP BY
                                u.user_id;";

    $notification_query_statement = mysqli_stmt_init($connection);

    // Parameters for prepared statement
    $parameter_types = "i";

    // Bind parameters to placeholders in query (ss for two string placeholders)
    if (!mysqli_stmt_prepare($notification_query_statement, $notification_query)) {
        print_alert("SQL Query Failure", "danger");
        return 1;
    } else {
        mysqli_stmt_bind_param($notification_query_statement, $parameter_types, $user_id);
    }

    // Run parameters inside database
    mysqli_stmt_execute($notification_query_statement);
    $notification_number_result = mysqli_stmt_get_result($notification_query_statement);

    // Get & return result (single row result)
    $notification_number = mysqli_fetch_row($notification_number_result);
    return $notification_number[0];
}

// This function deletes all notifications of a user (if notification_id = NULL
// it deletes a specific notification of one user if notification_id is specified
function delete_notification($connection, $user_id, $notification_id)
{
    $del_notification_query = "DELETE
                                FROM notification
                                WHERE user_id = ?";

    $parameter_types = "i";


    // If notification_id is provided, delete one notification
    // Therefore specify notification_id to be deleted
    if (isset($notification_id)) {
        $del_notification_query = $del_notification_query . " AND notification_id = ?;";
        $parameter_types = $parameter_types . "i";

    } // If no notification_id is provided, all notifications of current users are deleted
    else {
        $del_notification_query = $del_notification_query . ";";
    }

    $del_notification_stmt = mysqli_stmt_init($connection);

    // Check for failure of query statement
    if (!mysqli_stmt_prepare($del_notification_stmt, $del_notification_query)) {
        print_alert("SQL Query Failure", "danger");
        return;
    } else {
        // Deletions for one notification
        if ($parameter_types === "ii") {
            mysqli_stmt_bind_param($del_notification_stmt, $parameter_types, $user_id, $notification_id);
        } // Deletions for all notifications of user
        elseif ($parameter_types === "i") {
            mysqli_stmt_bind_param($del_notification_stmt, $parameter_types, $user_id);
        } else {
            print_alert("Sorry, there was a problem with deleting in the database", "danger");
        }
    }
    // Run parameters inside database
    $success = mysqli_stmt_execute($del_notification_stmt);

    // Print alert to user whether deletion was successful
    if ($success) {
        print_alert("Successfully deleted!", "success");
    } else {
        print_alert("Sorry, deletion did somehow not work!", "warning");
    }
}

// Function used in recommendations.php
// Returns an array of the top 3 products that are currently on auction
function recommendation_hottest_products($connection)
{
    include('functions/connect_to_database.php');

    // define time to compare product_end_date below
    $timenow = gmdate('Y-m-d H:i:s', time());

    $sql = "
			SELECT
                p.product_id,
                p.product_title,
                p.product_details,
                p.product_image_url,
                p.product_starting_price,
                -- ensures that the p.product_starting_price (if bids <= 0) and subquery_max_bid.max_bid_amount is shown (if bids > 0)
                -- of course this is here not really relevant, as if there are bids, there is also a bid_amount > product_starting_price
                IFNULL(MAX(subquery1.bid_amount), p.product_starting_price) AS bid_amount,
                subquery1.amount_of_bids
            FROM
                product as p,
				-- calculate amount_of_bids and amount_of_bids
				(SELECT DISTINCT
						b.product_id,
						COUNT(DISTINCT b.bid_id) AS amount_of_bids,
						MAX(b.bid_amount) AS bid_amount
					FROM
						bid AS b
					Group by
						b.product_id
					ORDER BY
						b.product_id) AS subquery1
            WHERE
                -- ensures that amount_of_bids and max_of_bid_amount actually matches to the individual p.product_id
				p.product_id = subquery1.product_id
			AND
			    -- excludes products that are not active anymore from displaying, while user matching can still be based on product_id of inactive products
                p.product_status_id = 0
            AND
                -- ensures that only active products are selected based on dates (double security  measure)
	            '" . $timenow . "' < DATE(p.product_end_date)
			Group BY
				p.product_id,
                p.product_title,
                p.product_details,
                p.product_image_url,
                p.product_starting_price,
                subquery1.bid_amount,
                subquery1.amount_of_bids
			ORDER BY
				subquery1.amount_of_bids DESC
			LIMIT 3";

// Get result from database
    $result = $connection->query($sql);
    $num_results = mysqli_num_rows($result);
    if ($num_results < 1) {
        print_alert("Sorry, there was a problem finding the hottest products.", "danger");
        return 0;
    }


    // Fetch results
    while ($row = mysqli_fetch_array($result)) {
        $product_title = $row['product_title'];
        $item_id = $row['product_id'];
        $title = $row['product_title'];
        $description = $row['product_details'];
        $current_price = $row['bid_amount'];
        $num_bids = $row['amount_of_bids'];
        if ($row['product_image_url'] == null) {
            $image_url = 'src/example.jpg';
        } else {
            $image_url = $row['product_image_url'];
        }
        $end_date = new DateTime($row['product_end_date']);

        // print recommendation box (function in functions.php)
        print_recommendation_li($item_id, $title, $description, $current_price, $num_bids, $image_url, $end_date);
    }
}

// Function used in recommendations.php
// Returns an array of the top 3 (based on amount of bids) products that are recommended based on current users bid behavior
function recommendation_collaborative_filtering($connection, $user_id_from_Session)
{
    include('functions/connect_to_database.php');

    // define time to compare product_end_date below
    $timenow = gmdate('Y-m-d H:i:s', time());

    // sql query
    $sql = "
            SELECT 
                p.product_id,
                p.product_title,
                p.product_details,
                p.product_image_url,
                p.product_end_date,
                -- ensures that the p.product_starting_price (if bids <= 0) and subquery_max_bid.max_bid_amount is selected (if bids > 0)
                IFNULL(MAX(subquery1.bid_amount),
                        p.product_starting_price) AS bid_amount,
                subquery1.amount_of_bids
            FROM
                product AS p,
                -- calculates amount_of_bids and amount_of_bids
                (SELECT DISTINCT
                    b.product_id,
                        COUNT(DISTINCT b.bid_id) AS amount_of_bids,
                        MAX(b.bid_amount) AS bid_amount
                FROM
                    bid AS b
                GROUP BY b.product_id
                ORDER BY amount_of_bids DESC) AS subquery1
            WHERE
                -- ensures that amount_of_bids and max_of_bid_amount actually matches to the individual p.product_id
                subquery1.product_id = p.product_id
            AND
                -- ensures that no product_id was bidded on by current user, while it also ensures
                -- that the product_id is a recommendation as it checks if the current user has
                -- bidded on other products (excluded from recommendations) of the user_id that bid
                -- of the product_id that is recommended
                p.product_id IN
                    (SELECT DISTINCT
                        b.product_id
                    FROM
                        bid AS b
                    WHERE
                        -- checks that the products that are recommended are from the other users as the current users
                        b.user_id IN
                            -- selects the distinct user_id of the users which have a similar bidding behaviour as current bidder
                            (SELECT DISTINCT
                                subquery2.user_id
                            FROM
                                -- selects product_id and user_id of current bid
                                (SELECT DISTINCT
                                    b.product_id, b.user_id
                                FROM
                                    bid AS b
                                WHERE
                                    b.user_id IN
                                        -- selects all user_ids of the other users that were bidding
                                        (SELECT DISTINCT
                                            b.user_id
                                        FROM
                                            bid AS b
                                        WHERE
                                            b.user_id <> '" . $user_id_from_Session . "')) AS subquery2
                            WHERE
                                subquery2.product_id IN
                                    -- selects distinct product_id where the current user bid on
                                    (SELECT DISTINCT
                                        b.product_id
                                    FROM
                                        bid AS b
                                    WHERE
                                        user_id = '" . $user_id_from_Session . "'))
                    AND
                        -- checks that the current user has not already bidded on the products that are recommended 
                        b.product_id NOT IN
                            -- selects the distinct product_id of the products where the current user has bid on
                            (SELECT DISTINCT
                                subquery3.product_id
                            FROM
                                -- selects all product_ids and user_ids of other users that were bidding
                                (SELECT DISTINCT
                                    b.product_id, b.user_id
                                FROM
                                    bid AS b
                                WHERE
                                    b.user_id IN 
                                        -- selects distinct user_ids of the other users that are bidding
                                        (SELECT DISTINCT
                                            b.user_id
                                        FROM
                                            bid AS b
                                        WHERE
                                            b.user_id <> '" . $user_id_from_Session . "')) AS subquery3
                            WHERE
                                subquery3.product_id IN
                                    -- selects distinct product_id of products the current bidder has bidded on
                                    (SELECT DISTINCT
                                        b.product_id
                                    FROM
                                        bid AS b
                                    WHERE
                                        user_id = '" . $user_id_from_Session . "')))
            AND
                -- ensures that only active products are selected based on product_status
                p.product_status_id = 0
            AND
                -- ensures that only active products are selected based on dates (double security  measure)
	            '" . $timenow . "' < DATE(p.product_end_date)
            GROUP BY
                p.product_id,
                p.product_title,
                p.product_details,
                p.product_image_url,
                p.product_starting_price,
                subquery1.amount_of_bids
            ORDER BY
                subquery1.amount_of_bids DESC
            LIMIT 3";

// Get result from database
    $result = $connection->query($sql);
    $num_results = mysqli_num_rows($result);
    if ($num_results < 1) {
        print_alert("Sorry, we cannot give you personalised recommendations quite yet. Place a few more bids 😉", "warning");
        return 0;
    }

// Fetch results
    while ($row = mysqli_fetch_array($result)) {
        $product_title = $row['product_title'];
        $item_id = $row['product_id'];
        $title = $row['product_title'];
        $description = $row['product_details'];
        $current_price = $row['bid_amount'];
        $num_bids = $row['amount_of_bids'];
        if ($row['product_image_url'] == null) {
            $image_url = 'src/example.jpg';
        } else {
            $image_url = $row['product_image_url'];
        }
        $end_date = new DateTime($row['product_end_date']);

        // print recommendation box (function in functions.php)
        print_recommendation_li($item_id, $title, $description, $current_price, $num_bids, $image_url, $end_date);
    }

}

// Insert notification into database
// requires the user_id and a string message as input
function notify_user($connection, $user_id, $message) {
    $query =    "INSERT INTO notification 
                (notification_id, user_id, notification_message, notification_creation_date)
                VALUES (null, " .$user_id. ", '" .$message. "', null);";

    // Get result from database
    $result = mysqli_query($connection, $query);
}

// Get number of notifications for current user
function get_watched_items_num($connection, $user_id)
{
    // Query for notifications
    $watch_count_query =    "SELECT
                                COUNT(w.user_id)
                            FROM
                                watching AS w,
                                users AS u,
                                product AS p
                            WHERE
                                w.user_id = u.user_id AND w.product_id = p.product_id AND u.user_id = ? AND DATE(p.product_end_date) > DATE(NOW())
                            GROUP BY
                                w.user_id;";

    $watch_count_query_stmt = mysqli_stmt_init($connection);


    // Parameters for prepared statement
    $parameter_types = "i";

    // Bind parameters to placeholders in query (ss for two string placeholders)
    if (!mysqli_stmt_prepare($watch_count_query_stmt, $watch_count_query)) {
        print_alert("SQL Query Failure", "danger");
        return 1;
    } else {
        mysqli_stmt_bind_param($watch_count_query_stmt, $parameter_types, $user_id);
    }

    // Run parameters inside database
    mysqli_stmt_execute($watch_count_query_stmt);
    $watch_count_result = mysqli_stmt_get_result($watch_count_query_stmt);

    if (mysqli_num_rows($watch_count_result)  < 1) {
        return 0;
    }
    else {
        // Get & return result (single row result)
        $num_watchlist = mysqli_fetch_row($watch_count_result);
        return $num_watchlist[0];
    }



}

// Get all watchlist items of current user (used in watchlist.php) & print them via print_listing_li
function get_watchlist_items($connection, $user_id)
{
    $watchlist_query = "SELECT
                            p.product_id,
                            p.product_title,
                            p.product_year,
                            p.product_details,
                            p.product_end_date,
                            p.product_starting_price,
                            p.product_image_url,
                            IFNULL(
                                MAX(b.bid_amount),
                                p.product_starting_price
                            ) AS max_bid,
                            IFNULL(COUNT(b.bid_id),
                            0) AS bid_count
                        FROM
                            product AS p
                        LEFT JOIN bid AS b
                        ON
                            b.product_id = p.product_id
                        WHERE
                            p.product_id IN(
                            SELECT
                                w.product_id
                            FROM
                                users AS u,
                                watching AS w
                            WHERE
                                u.user_id = w.user_id AND u.user_id = ? AND DATE(p.product_end_date) > DATE(NOW()))
                            GROUP BY
                                p.product_id;";

    $parameter_types = "i";

    // Create prepared statement for the actual query
    $watchlist_query_statement = mysqli_stmt_init($connection);
    // Check for failure of query statement
    if (!mysqli_stmt_prepare($watchlist_query_statement, $watchlist_query)) {
        echo "SQL statement failed";
        return;
    } else {
        mysqli_stmt_bind_param($watchlist_query_statement, $parameter_types, $user_id);
    }

    // Run parameters inside database
    mysqli_stmt_execute($watchlist_query_statement);
    $watchlist_query_result = mysqli_stmt_get_result($watchlist_query_statement);

    $num_results = mysqli_num_rows($watchlist_query_result);

    if ($num_results < 1) {
        print_alert("You have nothing on your watchlist. Come back after you added some auctions! 😊", "warning");
    }

    // Print list of auction results in while loop
    while ($row = mysqli_fetch_assoc($watchlist_query_result)) {
        $product_id = $row['product_id'];
        $product_title = $row['product_title'];
        $product_details = $row['product_details'];
        $current_price = $row['max_bid'];
        $num_bids = $row['bid_count'];
        $product_end_date = $row['product_end_date'];
        if ($row['product_image_url'] == null) {
            $product_image_url = 'src/example.jpg';
        } else {
            $product_image_url = $row['product_image_url'];
        }
        try {
            $product_end_date = new DateTime($row['product_end_date']);
        } catch (Exception $e) {
            print_alert("DateTime Error product_end_date", "danger");
        }
        print_listing_li($user_id, $product_id, $product_title, $product_details, $current_price, $num_bids, $product_image_url, $product_end_date);
    }
}