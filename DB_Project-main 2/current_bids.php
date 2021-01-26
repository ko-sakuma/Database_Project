<?php
    // Obtain product_id and aution_ended arguments from ajax post request.
    $args = $_POST['tableargs'];


    // If auction ended, just return display no further information.
    if ($args[1]) {
        return;
    }

    // Connect to database.
    include('functions/connect_to_database.php');

    // Create SQL query.
    $sql = "
        SELECT users.user_name, bid.bid_amount, bid.bid_creation_date 
        FROM users, bid
        WHERE users.user_id = bid.user_id 
        AND bid.product_id = ? 
        ORDER BY bid.bid_creation_date DESC;
  ";

    // Create a prepared statement.
    $stmt = mysqli_stmt_init($connection);

    // Test that the prepared statement is valid.
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        print_alert("error populating table, please try again later", "warning");

        $num_results = 0;

    } else {
        // Bind inputs to '?' placeholder.
        mysqli_stmt_bind_param($stmt, "i", $args[0]);

        // Execute the SQL query.
        mysqli_stmt_execute($stmt);

        // Obtain results and assign to variable.
        $result = mysqli_stmt_get_result($stmt);

        // Obtain the number of rows.
        $num_results = mysqli_num_rows($result);
    };

    if ($num_results < 1) {
        echo('
            <i><br>No current bids... Why not be the first bidder!</i>
        ');

    } else {
        echo('
            <h5><br>Current Bids</h5>

            <!-- Scrollable container for if bid numbers grow large -->
            <div style="width:105%; height:300px; overflow-y:scroll;">

                <table class="table table-dark table-striped table-bordered table-sm table-condensed">
                    <tr>
                    <th>User:</th>
                    <th>Bid Amount:</th>
                    <th>Timestamp:</th>
                    <tr>
        ');

        // loop though results of database query and append each bid into table row.
        foreach ($result as $found_bid) {
            echo('
                    <tr>
                    <td>' . $found_bid['user_name'] . '</td>
                    <td>£' . $found_bid['bid_amount'] . '</td>
                    <td>' . $found_bid['bid_creation_date'] . '</td>
                    </tr>
            ');
        }

        echo('
                </table>
            </div>
        ');
    }
?>