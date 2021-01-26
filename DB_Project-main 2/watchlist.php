<?php
$pageTitle = "Watchlist";
$login_required = True;
include_once("header.php");

// If user is not logged in, they should not be able to use this page.
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}
?>

<div class="container">

    <h2 class="my-3">Your Watchlist</h2>

    <!-- HTML: create container -->
    <div class="container mt-5">
        <!-- HTML: create list group -->
        <ul class="list-group">


            <?php
            // Get all watchlist items and print them
            get_watchlist_items($connection, $user_id)?>

        </ul>
    </div>

<?php include_once("footer.php")?>