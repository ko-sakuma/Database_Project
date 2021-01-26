<?php
// This page is for showing a buyer recommended items based on their bid
// history.

$pageTitle = "Recommendations";
$login_required = True;
$buyer_status_required = True;
include_once("header.php");

// define user_id to call the functions below
$user_id = $_SESSION['user_id'];
?>

<!-- Personalised Recommendations -->

<div class="container">

    <h2 class="my-3">Users with similar interests also bid on:</h2>

    <div class="row">
        <?php

        // Collaborative Filtering (SQL query incl. fetching and echo/layout is done in recommendation_collaborative_filtering function)
        recommendation_collaborative_filtering($connection, $user_id);

        ?>
    </div>

    <!-- Hottest Products -->

    <div class="container">

        <h2 class="my-3">Hottest Products:</h2>

        <div class="row">
            <?php

            // Hottest Product (SQL query incl. fetching and echo/layout is done in recommendation_hottest_products)
            recommendation_hottest_products($connection, $user_id);

            ?>
        </div>
    </div>
</div>