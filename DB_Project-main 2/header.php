<?php
// starts session after logging in.
session_start();
?>

<?php
// require functions/functions.php script to run all functions
require_once("functions/functions.php");
require_once('functions/connect_to_database.php');
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap and FontAwesome CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Custom CSS file -->
    <link rel="stylesheet" href="css/custom.css">
    <?php

    echo "<title>WinAuction - $pageTitle</title>" // now on every page one can add specific sub title (see Lab03, slide 18)
    ?>
</head>


<body>

<!-- Navbars -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mx-2">
    <!-- below is the site name-->
    <!-- Emoji is copied from https://emojipedia.org/wine-glass/ -->
    <a class="navbar-brand" href="browse.php">Welcome to WinAuction 🍷</a>
    <ul class="navbar-nav ml-auto">


        <li class="nav-item">

            <?php
            // Displays the user_name is you are registered.
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                // Check if seller status is required & redirect if not
                if (isset($seller_status_required) && $_SESSION['account_type'] != 'Seller') {
                    header('Location: browse.php');
                    die();
                } // Check if buyer status is required & redirect if not
                elseif (isset($buyer_status_required) && $_SESSION['account_type'] != 'Buyer') {
                    header('Location: browse.php');
                    die();
                }
                $user_name = $_SESSION['user_name'];
                echo '<p class="navbar">' . 'You are logged in as: ' . $user_name . '</p>';
            } // If user is not logged in:
            else {
                // check if the page requires login
                if ($login_required) {
                    // if yes, redirect to browse.php
                    header('Location: browse.php');
                }
            }
            ?>

        </li>

        <li class="nav-item">

            <?php
            // Displays either register (if not logged in) or nothing (if logged in), depending on user's current status (session).
            if (!isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == false) {
                echo '<a class="nav-link" href="register.php">Register</a>';
            }
            ?>

        </li>
        <li class="nav-item">

            <?php
            // Displays either login/register or logout on the right, depending on user's
            // current status (session).
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
                echo '<a class="nav-link" href="logout.php">Logout</a>';
                $user_id = $_SESSION['user_id'];

                // Query for number of notifications of user of current session
                $notification_number = get_notification_num($connection, $user_id);
                $watchlist_number = get_watched_items_num($connection, $user_id);

                // Adapt Bootstrap character of notification button depending on number of notifications
                // Make button grey if notifications = 0; make button blue if notifications > 0
                // Documentation: https://getbootstrap.com/docs/4.0/components/buttons/
                if ($notification_number > 0) {
                    $button_type = 'danger';
                } else {
                    $button_type = 'secondary';
                }
            } else {
                echo '<button type="button" class="btn nav-link" data-toggle="modal" data-target="#loginModal">Login</button>';
            }
            ?>
        </li>
    </ul>
</nav>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <ul class="navbar-nav align-middle">
        <li class="nav-item mx-1">
            <a class="nav-link" href="browse.php">Browse</a>
        </li>
        <?php
        if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'Buyer') {
            echo('
        <li class="nav-item mx-1">
          <a class="nav-link" href="mybids.php">My Bids</a>
        </li>
        <li class="nav-item mx-1">
          <a href="notifications.php">
            <button type="button" class="btn btn-' . $button_type . '">
              Notifications <span class="badge badge-light">' . $notification_number . '</span>
            </button>
          </a>
        </li>
        <li class="nav-item mx-1">
            <a class="nav-link" href="recommendations.php">Recommended</a>
        </li>
        <li class="nav-item mx-1">
            <a class="nav-link" href="watchlist.php">Watchlist (' . $watchlist_number . ')</a>
        </li>
        ');
        }
        if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'Seller') {
            echo('
        <li class="nav-item mx-1">
          <a class="nav-link" href="mylistings.php">My Listings</a>
        </li>
        <li class="nav-item mx-1">
          <a href="notifications.php">
            <button type="button" class="btn btn-' . $button_type . '">
              Notifications <span class="badge badge-light">' . $notification_number . '</span>
            </button>
          </a>
        </li>
        <li class="nav-item mx-1">
            <a class="nav-link" href="watchlist.php">Watchlist (' . $watchlist_number . ')</a>
        </li>
        <li class="nav-item ml-3">
          <a class="nav-link btn border-light" href="create_auction.php">+ Create auction</a>
        </li>
        ');
        }
        ?>
    </ul>
</nav>

<!-- Login modal -->
<div class="modal fade" id="loginModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Login</h4>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <!-- Login Form-->
                <form method="POST" action="login_result.php">
                    <div class="form-group">
                        <!-- Email-->
                        <label for="email">Email</label>
                        <input type="text" class="form-control" name="user_email" id="email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <!-- Password-->
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="user_password" id="password"
                               placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary form-control">Sign in</button>
                </form>
                <div class="text-center">or <a href="register.php">create an account</a></div>
            </div>

        </div>
    </div>
</div> <!-- End modal -->