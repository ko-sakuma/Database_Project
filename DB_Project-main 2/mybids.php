<?php
// This page is for showing a user the auctions they've bid on.
// If user is not logged in, they should not be able to use this page.
$pageTitle = "My bids";
$login_required = True;
$buyer_status_required = True;
include_once("header.php");
?>

    <div class="container">
        <h2 class="my-3">My bids</h2>
        <!-- User input: group by  -->
        <form method="get" action="mybids.php">
            <div class="row">
                <div class="col-md-4 pr-0">
                    <div class="form-inline">
                        <select class="form-control mx-2" name="group_by" id="group_by">

                            <option <?php
                            if (!isset($_GET['group_by'])) {
                                echo "selected";
                            } ?>
                                    value="auction">Group by product (sorted by date)
                            </option>

                            <option <?php
                                    if (isset($_GET['group_by']) && $_GET['group_by'] === "bid") {
                                        echo "selected";
                                    } ?>value="bid">Show all bids (sorted by date)
                            </option>

                        </select>
                    </div>
                </div>
                <div class="col-md-2 px-0">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>

        <?php

        // Get selection for GROUP BY from user selection menu
        if ($_GET['group_by'] === 'bid') {
            $group_by_bid = True;

            $mybids_query = 'SELECT
                                p.product_id,
                                p.product_title,
                                p.product_image_url,
                                b.bid_amount,
                                b.bid_creation_date
                            FROM
                                bid AS b,
                                product AS p
                            WHERE
                                b.product_id = p.product_id AND b.user_id = ?
                            ORDER BY
                                b.bid_creation_date
                            DESC';

            // Header of the table to be printed varies depending on group by
            $table_header = '<li class="list-group-item d-flex justify-content-between">
                            <div class="col-md-1 px-0"></div>
                            <div class="col-md-3 px-0"><b>Product</b></a></div>
                            <div class="col-sm-3 px-0"><b>Date & Time</b></div>
                            <div class="col-sm-2 px-0"><b>Your bid amount</b></div>
                            <div class="col-sm-2 px-0"></div>
                        </li>';
        } else {
            $group_by_bid = False;

            $mybids_query = 'SELECT
                                p.product_id,
                                p.product_title,
                                p.product_image_url,
                                COUNT(b.bid_id) AS bid_count,
                                MAX(b.bid_amount) AS max_bid_amount,
                                MAX(b.bid_creation_date) AS latest_bid_date
                            FROM
                                bid AS b,
                                product AS p
                            WHERE
                                b.product_id = p.product_id AND b.user_id = ?
                            GROUP BY
                                p.product_id
                            ORDER BY
                                latest_bid_date
                            DESC';

            // Header of the table to be printed varies depending on group by
            $table_header = '<li class="list-group-item d-flex justify-content-between">
                            <div class="col-md-1 px-0"></div>
                            <div class="col-md-3 px-0"><b>Product</b></a></div>
                            <div class="col-sm-1 px-0"><b># of your bids</b></div>
                            <div class="col-sm-2 px-0"><b>Your highest bid</b></div>
                            <div class="col-sm-2 px-0"><b>Time of your last bid</b></div>
                            <div class="col-sm-2 px-0"></div>
                        </li>';
        }

        $mybids_query_complete = $mybids_query . ';'; // add semicolon to end query (later, for pages another statement is concatenated)

        // Query on bids / products

        // Fetch page numbers from GET
        if (!isset($_GET['page'])) {
            $curr_page = 1;
        } else {
            $curr_page = $_GET['page'];
        }

        // Define parameter types for prepared statement
        $parameter_types = "i";

        // Create prepared statement for the initial query (used to obtain number of results)
        $mybids_query_statement = mysqli_stmt_init($connection);
        // Check for failure of query statement
        if (!mysqli_stmt_prepare($mybids_query_statement, $mybids_query_complete)) {
            print_alert("SQL statement failed", "danger");
            return;
        } else {
            mysqli_stmt_bind_param($mybids_query_statement, $parameter_types, $user_id);
        }

        // Run parameters inside database to obtain number of results
        mysqli_stmt_execute($mybids_query_statement);
        $mybids_query_result = mysqli_stmt_get_result($mybids_query_statement);

        $num_results = mysqli_num_rows($mybids_query_result); // number of all results (without LIMIT/OFFSET for pages)
        $results_per_page = 10;

        // If there are no bids for this user, display message
        if ($num_results < 1) {
            print_alert("Hey, you are on an auction site - why aren't you bidding on products? Place some bids and come back! 😊", "warning");
        } else {
            // If num of results > max results per page: add limit/offset to query for pages
            $mybids_query_page = $mybids_query . ' LIMIT ? OFFSET ?;'; // limit & offset added for pages

            // Add LIMIT & OFFSET to parameter types
            $parameter_types = $parameter_types . "ii"; // limit & offset are added now

            // Calculate lower limit no. of search results
            $max_page = ceil($num_results / $results_per_page);
            $lower_limit = ($curr_page - 1) * $results_per_page;

            // Create prepared statement for the actual query
            $mybids_query_statement = mysqli_stmt_init($connection);

            // Check for failure of query statement
            if (!mysqli_stmt_prepare($mybids_query_statement, $mybids_query_page)) {
                print_alert("SQL statement for pages failed", "danger");
                return;
            } else {
                mysqli_stmt_bind_param($mybids_query_statement, $parameter_types, $user_id, $results_per_page, $lower_limit);
            }

            // Run parameters inside database (now with limit & offset)
            mysqli_stmt_execute($mybids_query_statement);
            $mybids_query_result = mysqli_stmt_get_result($mybids_query_statement); // result including LIMIT & OFFSET

            // Print statement with number of bids
            if ($group_by_bid) {
                echo "<h4>You made " . $num_results . " bids since you registered on this page.</h4>";
            } else {
                echo "<h4>You bid on " . $num_results . " products since you registered on this page.</h4>";
            }


            // Print table headers (previously defined based on GROUP BY selection)
            echo $table_header;

            // Display results
            if ($group_by_bid) {
                while ($row = mysqli_fetch_assoc($mybids_query_result)) {
                    echo('<li class="list-group-item d-flex justify-content-between">
                    <div class="col-md-1 px-0"><a href="listing.php?product_id=' . $row["product_id"] . '"><img src="' . $row["product_image_url"] . '" class="crop_mybids_thumbnail" alt="product pic" ></a></div>
                    <div class="col-md-3 px-0"><a href="listing.php?product_id=' . $row["product_id"] . '"><b>' . $row["product_title"] . '</b></a></div>
                    <div class="col-sm-3 px-0">' . $row["bid_creation_date"] . '</div>
                    <div class="col-sm-2 px-0">£' . $row["bid_amount"] . '</div>
    
                    <div class="col-sm-2 px-0">
                        <a href="listing.php?product_id=' . $row["product_id"] . '"><button type="submit" class="btn btn-primary">See auction</button></a>
                    </div>
                </li>');
                }
            } else {
                while ($row = mysqli_fetch_assoc($mybids_query_result)) {
                    echo('<li class="list-group-item d-flex justify-content-between">
                    <div class="col-md-1 px-0"><a href="listing.php?product_id=' . $row["product_id"] . '"><img src="' . $row["product_image_url"] . '" class="crop_mybids_thumbnail" alt="product pic" ></a></div>
                    <div class="col-md-3 px-0"><a href="listing.php?product_id=' . $row["product_id"] . '"><b>' . $row["product_title"] . '</b></a></div>
                    <div class="col-sm-1 px-0">' . $row["bid_count"] . '</div>
                    <div class="col-sm-2 px-0">£' . $row["max_bid_amount"] . '</div>
                    <div class="col-sm-2 px-0">' . $row["latest_bid_date"] . '</div>

    
                    <div class="col-sm-2 px-0">
                        <a href="listing.php?product_id=' . $row["product_id"] . '"><button type="submit" class="btn btn-primary">See auction</button></a>
                    </div>
                </li>');
                }
            }


        }
        ?>
        </ul>

        <!-- Pagination for results listings (inspired by browse.php) -->
        <nav aria-label="My Bids pages" class="mt-5">
            <ul class="pagination justify-content-center">

                <?php
                // Copy any currently-set GET variables to the URL.
                $querystring = "";
                foreach ($_GET as $key => $value) {
                    if ($key != "page") {
                        $querystring .= "$key=$value&amp;";
                    }
                }

                $high_page_boost = max(3 - $curr_page, 0);
                $low_page_boost = max(2 - ($max_page - $curr_page), 0);
                $low_page = max(1, $curr_page - 2 - $low_page_boost);
                $high_page = min($max_page, $curr_page + 2 + $high_page_boost);

                if ($curr_page != 1) {
                    echo('
                    <li class="page-item">
                      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
                        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
                        <span class="sr-only">Previous</span>
                      </a>
                    </li>');
                }

                for ($i = $low_page; $i <= $high_page; $i++) {
                    if ($i == $curr_page) {
                        // Highlight the link
                        echo('
                        <li class="page-item active">');
                    } else {
                        // Non-highlighted link
                        echo('
                        <li class="page-item">');
                    }

                    // Do this in any case
                    echo('
                      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
                    </li>');
                }

                if ($curr_page != $max_page && $num_results > $results_per_page) {
                    echo('
                    <li class="page-item">
                      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
                        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
                        <span class="sr-only">Next</span>
                      </a>
                    </li>');
                }
                ?>

            </ul>

        </nav>
    </div>
<?php include_once("footer.php") ?>