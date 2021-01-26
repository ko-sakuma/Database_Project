<?php
    require_once("functions/functions.php");
    require_once("functions/connect_to_database.php");
    $pageTitle = "Bid Confirmation";
    $login_required = True;
    $buyer_status_required = True;
    include_once("header.php");

    // capure form submission post
    $product_id = $_POST['product_id'];
    $bid_amount = $_POST['bid_amount'];
    $user_id = $_POST['user_id'];

    // Connect to database.
    include('functions/connect_to_database.php');

    // submit bid enables a user to submit a bid into the auction. This uses a transaction statement
    // to make sure that there are no anomalies due to concurrency within the bid system. The database
    // will hold a multi-table lock for the bids table and insert the bids in a sequential and 
    // orderly fashion.

    // Create SQL query, wrapped in a transaction lock statement to adequately handle concurrency.
    // Insert with condition that the current bid is greater than the max bid within the database.

    // By choosing InnoDB as the database engine we automatically get two-phase locking out of the box.
    // There is no need to imbue "START TRANSACTION", "COMMIT" and "ROLLBACK" statements manually to ensure
    // correctness under concurrent conditions (see: https://dev.mysql.com/doc/refman/5.7/en/innodb-transaction-model.html).

    $sql = "
        INSERT INTO bid (bid_id, product_id, user_id, bid_amount, bid_creation_date)
        VALUES (?, ?, ?, ?, ?);
    ";

    // Create a prepared statement.
    $stmt = mysqli_stmt_init($connection);

    // // Test that the prepared statement is valid.
    // // TODO: better notification of server side error here.
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        // submit bid failed redirect to listing page failure case.
        print_alert("Bid unsuccessful. Please try again.", "warning");

        header("Refresh: 2; URL=listing.php?product_id=".$product_id);

    } else {
        // Bind inputs to '?' placeholder.
        mysqli_stmt_bind_param($stmt, "siids", $NULL, $product_id, $user_id, $bid_amount, $NULL);

        // Execute the SQL query.
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $listing_result = mysqli_fetch_assoc($result);

        print_alert("Bid submitted successfully. Good Luck!", "success");

        // email watchers of the item that someone just placed a bid only after
        // the bid was successful.
        email_watchers($product_id, $user_id);

        header("Refresh: 2; URL=listing.php?product_id=".$product_id);
    }