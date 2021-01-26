<?php
$pageTitle = "Create a new auction";
$login_required = True;
$seller_status_required = True;
include_once("header.php");
require_once('functions/connect_to_database.php');
?>

<!-- create a form that passes data into create_auction_result.php -->
<form method="post" action="create_auction_result.php">
    <?php
    // add isset statements from error messages from process_registrations
    if (isset($_GET["error"])) {
        if ($_GET["error"] == "auction_form_is_empty") {
            print_alert("You are missing the required fields", "danger");
        }
        elseif ($_GET["error"] == "invalid_pricing") {
            print_alert("Reserve price cannot be less than the start price.", "danger");
        }
    }
    ?>
<!-- Display error messages when Required fields are empty --> 


<!-- Create auction form -->
<div class="container">
<div style="max-width: 800px; margin: 10px auto">
  <h2 class="my-3">Create new auction</h2>
  <div class="card">
    <div class="card-body">

<!-- Create auction title -->
<div class="form-group row">
  <label for="auctionTitle" class="col-sm-2 col-form-label text-right">Auction Title</label>
  <div class="col-sm-10">
    <input type="text" name="product_title" class="form-control" id="auctionTitle" placeholder="e.g. Chateau Cheval Blanc 1947" maxlength="100">                  
  <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> A short description of the item you're selling, which will display in listings.</small>    
  </div>
</div>

<!-- Create auction details -->
<div class="form-group row">
<label for="auctionDetails" class="col-sm-2 col-form-label text-right">Details</label>
  <div class="col-sm-10">
  <textarea class="form-control" name="product_details" id="auctionDetails" rows="4" maxlength="1000"></textarea>
  <small id="detailsHelp" class="form-text text-muted"> You can write up to 1000 characters. This message will be displayed in listings</small>
  </div>
</div>

<!-- Create Product Year -->
<div class="form-group row">
  <label for="auctionYear" class="col-sm-2 col-form-label text-right">Year</label>
  <div class="col-sm-10">
    <input type="number" name="product_year" class="form-control" id="auctionYear" placeholder="1984" min="0001" max=<?php echo date("Y"); ?>>                  
  <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Please enter the year with 4 digits (e.g. 1984)</small>    
  </div>
</div>

<!-- Create Image URL field -->
<div class="form-group row">
  <label for="auctionImage" class="col-sm-2 col-form-label text-right">Image</label>
  <div class="col-sm-10">
    <input type="text" name="product_image_url" class="form-control" id="auctionImage" placeholder="Enter Image URL" maxlength="2048">                  
  <small id="titleHelp" class="form-text text-muted">Please insert image url. URL cannot be longer than 2048 characters.</small>    
  </div>
</div>

<!-- Create auction category -->
<div class="form-group row">
<label for="auctionCategory" class="col-sm-2 col-form-label text-right">Category</label>
  <div class="col-sm-10">
  <select class="form-control" name="product_category_id" id="auctionCategory">
    <!-- CREATE DROP-DOWN MENU OF CATEGORIES FROM PRODUCT_CATEGORY TABLE -->
    <option value="" selected disabled hidden>Please select product category</option>
    <?php
      // -1 because no current category is fetched from the domain field. See functions.php
      get_product_categories($connection, -1)
    ?>
  </select>
  <small id="categoryHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Select a category for this item.</small>
  </div>
</div>

<!-- Create product condition-->
<div class="form-group row">
<label for="productCondition" class="col-sm-2 col-form-label text-right">Condition</label>
  <div class="col-sm-10">
  <select class="form-control" name="product_condition_id" id="productCondition">
    <!-- CREATE DROP-DOWN MENU OF PRODUCT CONDITION FROM PRODUCT_CONDITION TABLE -->
    <option value="" selected disabled hidden>Please select product condition</option>
    <?php
      $query = "SELECT product_condition_name FROM product_condition;";
      $result = mysqli_query($connection, $query);

      if ($result === false) {
      throw new Exception(mysql_error($connection));
      }
    ?>
    <?php while ($row = mysqli_fetch_assoc($result)) :?>
      <option value="1"><?php echo $row['product_condition_name']?></option>
    <?php endwhile; ?>
  </select>
  <small id="categoryHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Select the product condition for this item.</small>
  </div>
</div>

<!-- Create starting price -->
<div class="form-group row">
<label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Starting price</label>
  <div class="col-sm-10">
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text">£</span>
      </div>
        <input type="number" name="product_starting_price" min="0" class="form-control" id="auctionStartPrice">
    </div>
        <small id="startBidHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Initial bid amount.</small>
  </div>
</div>

<!-- Create reserve price -->
<div class="form-group row">
<label for="auctionReservePrice" class="col-sm-2 col-form-label text-right">Reserve price</label>
  <div class="col-sm-10">
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text">£</span>
      </div>
        <input type="number" name="product_reserve_price" min="0" class="form-control" id="auctionReservePrice">    
    </div>
      <small id="reservePriceHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Auctions that end below this price will not go through. This value is not displayed in the auction listing. You cannot set the reserve price below the Starting Price.</small>
  </div>
</div>

<!-- Create end date -->   
<div class="form-group row">
<label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
  <div class="col-sm-10">
    <input type="datetime-local" step="any" name="product_end_date" min=<?php echo date('Y-m-d\TH:i:s');?>>
      <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Set the Day and Time for the auction to end. You cannot set it in the past. </small>     
  </div>
</div>

<!-- Create submit button  -->  
<input type="submit" value="Create Auction" name="submit" class="btn btn-primary form-control">

</form>

<!-- Insert footer -->
<div>
<?php include_once("footer.php")?>
</div>
