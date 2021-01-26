<?php
  require_once("functions/functions.php");
  require_once("functions/connect_to_database.php");
  $pageTitle = "Product Listing";
  include_once("header.php")
?>

<?php
  // Get product_info from URL querystring.
  $product_id = $_GET['product_id'];
  $user_id = $_SESSION['user_id'];

  // Get array of product listing details.
  $listing_details = get_listing_details($product_id);

  // Iterate through array and parse required fields from the database.
  while ($listing = mysqli_fetch_assoc($listing_details)) {
    $title = $listing['product_title'];
    $description = $listing['product_details'];
    // $current_price = $listing['current_price']; // TODO: remove? not needed anymore
    $status_id = $listing['product_status_id'];
    $image_url = $listing['product_image_url'];
    $end_time = DateTime::createFromFormat('Y-m-d H:i:s', $listing['product_end_date']); // convert into standard Time format.
  };

  // Calculate auction end time.
  $now = new DateTime();
  if ($now < $end_time) {
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
    $auction_ended = false;
  } else {
    $auction_ended = true;
  }
  
  // Validate a session exists and check if user is already watching the product.
  $has_session = $_SESSION['logged_in'];
  $watching = user_watching_product($_SESSION['user_id'], $product_id);
?>

<div class="container">

  <!-- Row #1 with auction title + watch button -->
  <div class="row"> 

    <!-- Left col -->
    <div class="col-sm-8"> 
      <h2 class="my-3"><?php echo($title); ?></h2>
    </div>

    <!-- Check if user is logged in to place a bid -->
    <?php if ($has_session == true): ?>

    <!-- Right col -->
    <div class="col-sm-4 align-self-center"> 
    <?php if ($now < $end_time):?>
      <div id="watch_nowatch" <?php if ($has_session && $watching) echo('style="display: none"');?>>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist(<?php echo($product_id);?>)">+ Add to watchlist</button>
      </div>

      <div id="watch_watching" <?php if (!$has_session || !$watching) echo('style="display: none"');?>>
        <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist(<?php echo($product_id);?>)">- Remove watch</button>
      </div>
    <?php endif?>
    </div>
  </div>

  <?php endif?>

  <!-- Row #2 with auction description + bidding info -->
  <div class="row"> 

    <!-- Left col with item info -->
    <div class="col-sm-8"> 
      <img class="listing-images img-responsive" src=<?php echo($image_url)?> alt="">
      <div class="itemDescription">
        <br><h4>Description:</h4>
        <?php echo($description); ?>
      </div>
    </div>

    <!-- Right col with bidding info -->
    <div class="col-sm-4"> 

    <!-- Auction date information -->
    <?php if ($now > $end_time): ?>
      <h3>Auction Ended</h3> 
      <p><?php echo(date_format($end_time, 'j M H:i')) ?><p>

    <?php else: ?>
      <!-- Current Price of Auction ASYNC JS FUNCTION -->
      <p class="lead" id="current_price"></p>
      <p>Auction ends <?php echo(date_format($end_time, 'j M H:i') . $time_remaining) ?></p>

      <!-- Check if user is logged in to place a bid -->
      <?php if ($has_session == true): ?>

        <!-- Bidding form -->
        <form name="place_bid_form" method="post" action="place_bid.php">
          <div class="form-group">
            <!-- Number input -->
            <div class="input-group">
              <div class="input-group-prepend"><span class="input-group-text">£</span></div>
              <input type="hidden" class="form-control" name="product_id" id="product_id" value="<?php echo($product_id);?>" >
              <input type="hidden" class="form-control" name="user_id" id="user_id" value="<?php echo($user_id);?>" >
              <input type="number" class="form-control no-spin" name="bid_amount" id="bid_amount" placeholder="Good luck..." min="" step="any" required> 
            </div>
          </div>
            <button type="submit" name="submit" class="btn btn-primary form-control">Place bid</button>
        </form>

      <!-- If user has no priveledges to make a bid -->
      <?php else: ?>
        <h5> You must be logged in to bid </h5>
      <?php endif ?>

    <?php endif ?>

      <!-- Print table of current bids for the product ASYNC JS FUNCTION -->
    <div id="update_bids"></div>

  </div>
</div>

<?php include_once("footer.php")?>

<script> 
  // Call this bid table population function as soon as DOM is ready and set to refresh every second.
  $(document).ready(function() {
    setInterval(() => {
      refresh_current_bids(<?php echo($product_id);?>, <?php echo($auction_ended);?>);
      refresh_listing_information(<?php echo($product_id);?>);
    }, 1000);
  });

  // Asynchronous table population using the product_id and bid status.
  function refresh_current_bids(product_id, auction_ended) {
    $.ajax({
      type: "POST",
      url: "current_bids.php",
      async: true,
      data: {
        tableargs: [product_id, auction_ended]
      },
      success: function(resp) {
        $('#update_bids').html(resp);
      },
      error: function() {
        console.log("cannot refresh current bids table");
      }
    });
  } 

  function refresh_listing_information(product_id) {
    $.ajax({
      type: "POST",
      url: "current_price.php",
      async: true,
      data: {
        listingargs: [product_id]
      },
      success: function(resp) {
        // update current price.
        $('#current_price').html(resp);
        $('#bid_amount').attr({
          "min": <?php echo(current_price_number_only($product_id));?> + 0.01,
        });
      },
      error: function() {
        console.log("cannot refresh listing information");
      }
    });
  }

  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to remove item from watchlist when user clicks it.
  function addToWatchlist(product_id) {
    $.ajax('watchlist_funcs.php', {
      type: "POST",
      data: {
        functionname: 'add_to_watchlist',
        arguments: [<?php echo($_SESSION['user_id']);?>, product_id]
      },

      success: 
        // Callback function for when call is successful and returns obj
        function (obj) {
          console.log(obj);
          var objT = obj.trim();
  
          if (objT == "success") {
            $("#watch_nowatch").hide();
            $("#watch_watching").show();

          } else {
            var mydiv = document.getElementById("watch_nowatch");

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
  function removeFromWatchlist(product_id) {
    $.ajax('watchlist_funcs.php', {
      type: "POST",
      data: {
        functionname: 'remove_from_watchlist', 
        arguments: [<?php echo($_SESSION['user_id']);?>, product_id]
      },

      success: 
        // Callback function for when call is successful and returns obj
        function (obj) {
          console.log(obj);
          var objT = obj.trim();
  
          if (objT == "success") {
            $("#watch_watching").hide();
            $("#watch_nowatch").show();

          } else {
            var mydiv = document.getElementById("watch_watching");
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