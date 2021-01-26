<?php
    // obtain product_id from ajax post request
    $args = $_POST['listingargs'];

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
        print_alert("price unavailable please try again later", "warning");

    } else {
        // Bind inputs to '?' placeholder.
        mysqli_stmt_bind_param($stmt, "iii", $args[0], $args[0], $args[0]);

        // Execute the SQL query.
        mysqli_stmt_execute($stmt);

        // Obtain results and assign to variable.
        $listing_result = mysqli_stmt_get_result($stmt);

        $listing_result = mysqli_fetch_assoc($listing_result);

        echo("<b>Current Price: </b>£" . $listing_result['current_price']);
    }
?>
