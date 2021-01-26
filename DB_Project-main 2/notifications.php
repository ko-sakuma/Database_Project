<?php
$pageTitle = "Notifications";
$login_required = True;
include_once("header.php");
?>

<div class="container">

    <h2 class="my-3">Notifications</h2>

    <?php
    // Obtain number of notifications from header
    // If number > 0, then show a delete all button
    if ($notification_number > 0) {
        echo '<form method="post" action="notifications.php">
                    <input type="hidden" value="'.$user_id.'" name="user_id">
                        <div class="col-md-2 px-0">
                            <button type="submit" class="btn btn-danger">Delete all</button>
                        </div>
                    </form>';
    }
    ?>

    <!-- HTML: create container -->
    <div class="container mt-5">
        <!-- HTML: create list group -->
        <ul class="list-group">


            <?php
            // 1 HANDLE DELETIONS VIA POST REQUESTS
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // Delete one notification if both notification_id and user_id were specified
                if (isset($_POST['notification_id']) && isset($_POST['user_id'])) {

                    // Check that user_id transmitted over POST matches current user in session
                    if ($_POST['user_id'] == $_SESSION['user_id']) {
                        delete_notification($connection, $_POST['user_id'], $_POST['notification_id']);

                        // Refreshing page to update notification number within the header
                        header("Refresh:0");

                    }
                    else {
                        // if user_id does not match, deletion cannot be performed
                        print_alert("You cannot delete notifications of other users!", "danger");
                    }
                }
                // Delete all notifications of user if only user_id was specified
                elseif (isset($_POST['user_id'])) {

                    // Check that user_id transmitted over POST matches current user in session
                    if ($_POST['user_id'] == $_SESSION['user_id']) {
                        delete_notification($connection, $_POST['user_id'], NULL);

                        // Refreshing page to update notification number within the header
                        header("Refresh:0");
                    }
                    else {
                        // if user_id does not match, deletion cannot be performed
                        print_alert("You cannot delete notifications of other users!", "danger");
                    }
                }
                else {
                    print_alert("Sorry, something went wrong with deleting this notification. Perhaps it has been deleted already? 😊", "warning");
                }
            }



            // 2 RENDER PAGE FOR USER

            $notification_query =  "SELECT notification_id, notification_message, notification_creation_date
                            FROM notification
                            WHERE user_id = ?
                            ORDER BY notification_creation_date DESC";

            $notification_query_total = $notification_query . ";"; // query statement without limit/offset for pages

            // Fetch page numbers from GET
            if (!isset($_GET['page'])) {
                $curr_page = 1;
            }
            else {
                $curr_page = $_GET['page'];
            }

            $parameter_types = "i";

            // Create prepared statement for the actual query
            $notification_query_statement = mysqli_stmt_init($connection);
            // Check for failure of query statement
            if (!mysqli_stmt_prepare($notification_query_statement, $notification_query)){
                print_alert("SQL statement failed","danger");
                return;
            }
            else {
                mysqli_stmt_bind_param($notification_query_statement, $parameter_types, $user_id);
            }

            // Run parameters inside database
            mysqli_stmt_execute($notification_query_statement);
            $notification_query_result = mysqli_stmt_get_result($notification_query_statement);

            $num_results = mysqli_num_rows($notification_query_result);
            $results_per_page = 20;


            if ($num_results < 1) {
                print_alert("You're up to date - no notifications. Come back later! 😊", "warning");
                $num_results = 0;
                $max_page = 1;
            }
            else {
                // If num of results > max results per page: add limit/offset to query for pages
                $notification_query_page = $notification_query . ' LIMIT ? OFFSET ?;'; // limit & offset added for pages

                $parameter_types = $parameter_types . "ii"; // limit & offset are added now

                // Calculate lower limit no. of search results
                $max_page = ceil($num_results / $results_per_page);
                $lower_limit = ($curr_page - 1) * $results_per_page;

                // Create prepared statement for the actual query
                $notification_query_statement = mysqli_stmt_init($connection);

                // Check for failure of query statement
                if (!mysqli_stmt_prepare($notification_query_statement, $notification_query_page)){
                    print_alert("SQL statement for pages failed","danger");
                    return;
                }
                else {
                    mysqli_stmt_bind_param($notification_query_statement, $parameter_types, $user_id, $results_per_page, $lower_limit);
                }

                // Run parameters inside database (now with limit & offset)
                mysqli_stmt_execute($notification_query_statement);
                $notification_query_result = mysqli_stmt_get_result($notification_query_statement);
            }

            while ($row = mysqli_fetch_assoc($notification_query_result)) {
                echo('<li class="list-group-item d-flex justify-content-between">
            <div class="col-md-6 px-0"><b>'.$row["notification_message"].'</b></div>
            <div class="col-sm-2 px-0">'.$row["notification_creation_date"].'</div>

            <form method="post" action="notifications.php">
                <input type="hidden" value="'.$row["notification_id"].'" name="notification_id">
                <input type="hidden" value="'.$user_id.'" name="user_id">
                <div class="col-md-1 px-0">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
            </li>');
            }
            ?>
        </ul>

        <!-- Pagination for results listings -->
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
                      <a class="page-link" href="notifications.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
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
                    }
                    else {
                        // Non-highlighted link
                        echo('
                        <li class="page-item">');
                    }

                    // Do this in any case
                    echo('
                      <a class="page-link" href="notifications.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
                    </li>');
                }

                if ($curr_page != $max_page && $num_results > $results_per_page) {
                    echo('
                    <li class="page-item">
                      <a class="page-link" href="notifications.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
                        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
                        <span class="sr-only">Next</span>
                      </a>
                    </li>');
                }
                ?>
            </ul>
        </nav>
    </div>
