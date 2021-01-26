<?php
$pageTitle = "Browse";
$login = False;
include_once("header.php");

// Success handling of user registration, login and logout
if (isset($_GET["success"])) {
    if ($_GET["success"] == "registered") {
        print_alert("You successfully registered. Please log in.", "success");
    }
    if ($_GET["success"] == "login") {
        print_alert("Hi {$_SESSION['user_name']}, you successfully  logged in.", "success");
    }
    if ($_GET["success"] == "logout") {
        // Peace Emoji from https://emojipedia.org/victory-hand/
        print_alert("You successfully logged out. See you next time ✌️", "warning");
    }
}

// Error handling of user login
if (isset($_GET["error"])) {
    if ($_GET["error"] == "email_wrong") {
        print_alert("There is no user registered under this email. Please try again or register.", "danger");
    }
    if ($_GET["error"] == "password_wrong") {
        print_alert("The password is wrong. Please try again.", "danger");
    }
    if ($_GET["error"] == "no_login_input") {
        print_alert("You need to input password and email to login. Please try again.", "danger");
    }
}

// Create welcome message on page
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $welcome_string = "Welcome back, " . $_SESSION['user_name'] . "!";
} else {
    $welcome_string = "Hi! Create a free account to bid on items.";
    $user_id = NULL;
}
?>

    <div class="container">

        <h2 class="my-3"><?php echo $welcome_string ?></h2>

        <div id="searchSpecs">

            <!--Prepration Queries for Browse User Input-->
            <?php
            // Get min/max prices of bids/starting price for price slider input
            $price_results = get_price_min_max($connection);
            $price_max = $price_results[0];
            $price_min = $price_results[1];

            // Get min/max years of products price for year slider input
            $year_results = get_year_min_max($connection);
            $year_max = $year_results[0];
            $year_min = $year_results[1];
            ?>

            <form method="get" action="browse.php">
                <div class="row">

                    <!-- Search keyword -->
                    <div class="col-md-2 pr-0">
                        <div class="form-group">
                            <label for="keyword" class="sr-only">Search keyword:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-transparent pr-0 text-muted">
                                      <i class="fa fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control border-left-0" id="keyword" name="search" <?php
                                if (!isset($_GET['search']) || $_GET['search'] === "") {
                                    echo 'placeholder=Search for anything"';
                                } else {
                                    echo 'value="' . $_GET['search'] . '"';
                                }
                                ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Select Category -->
                    <?php // obtaining category from GET to determine current default
                    if (!isset($_GET['category'])) {
                        $product_category = null;
                    } else {
                        $product_category = $_GET['category'];
                    }
                    ?>

                    <div class="col-md-2 pr-0">
                        <div class="form-group">
                            <label for="cat" class="sr-only">Search within:</label>
                            <select class="form-control" name="category" id="cat">
                                <option <?php // if an option had been selected, keep this selection
                                if (!isset($product_category)) { // if no other category was selected; 'all categories' is default
                                    echo "selected";
                                } ?>
                                        value="all">All categories
                                </option>
                                <!-- Create drop down menu of categories -->
                                <?php get_product_categories($connection, $product_category) ?>
                            </select>
                        </div>
                    </div>

                    <!-- Order By  -->
                    <div class="col-md-3 pr-0">
                        <div class="form-inline">
                            <label class="mx-2" for="order_by">Sort by:</label>
                            <select class="form-control" name="sort" id="order_by">
                                <option <?php // if an option had been selected, keep this selection
                                if (!isset($_GET['sort'])) {
                                    echo "selected";
                                } ?>
                                        value="pricelow">
                                    Price (low to high)
                                </option>
                                <option <?php
                                if (isset($_GET['sort']) && $_GET['sort'] === "pricehigh") {
                                    echo "selected";
                                } ?>
                                        value="pricehigh">Price (high to low)
                                </option>
                                <option <?php
                                if (isset($_GET['sort']) && $_GET['sort'] === "datelow") {
                                    echo "selected";
                                } ?>
                                        value="datelow">Soonest expiry
                                </option>
                                <option <?php
                                if (isset($_GET['sort']) && $_GET['sort'] === "datehigh") {
                                    echo "selected";
                                } ?>
                                        value="datehigh">Latest expiry
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Year Slider  -->
                    <div class="col-md-2 pr-3 form-inline">
                        <input type="range" name="year_limit" id="year_range" class="form-control-range"
                               min=<?php echo $year_min ?> max=<?php echo $year_max ?>
                               value=<?php
                        // Set default value to max or last searched year
                        if (isset($_GET['year_limit'])) {
                            echo $_GET['year_limit'];
                        } else {
                            echo $year_max;
                        } ?>>
                        <p>Latest year: <span id="max_year"></span></p>
                    </div>
                    <!-- JavaScript for Year Slider  -->
                    <script>
                        var year_slider = document.getElementById("year_range");
                        var max_year = document.getElementById("max_year");
                        max_year.innerHTML = year_slider.value;

                        year_slider.oninput = function () {
                            max_year.innerHTML = this.value;
                        }
                    </script>

                    <!-- Price Slider  -->
                    <div class="col-md-2 pr-3 form-inline">
                        <input type="range" name="price_limit" id="price_range" class="form-control-range"
                               min=<?php echo $price_min ?> max=<?php echo $price_max ?>
                               value=<?php
                        // Set default value to max or last searched price
                        if (isset($_GET['price_limit'])) {
                            echo $_GET['price_limit'];
                        } else {
                            echo $price_max;
                        } ?>>
                        <p>Max Price: £<span id="max_price"></span></p>
                    </div>
                    <!-- JavaScript for Price Slider  -->
                    <script>
                        var price_slider = document.getElementById("price_range");
                        var max_price = document.getElementById("max_price");
                        max_price.innerHTML = price_slider.value;

                        price_slider.oninput = function () {
                            max_price.innerHTML = this.value;
                        }
                    </script>

                    <!-- SUBMIT BUTTON  -->
                    <div class="col-md-1 px-0">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div> <!-- end row -->
            </form> <!-- end inputs -->
        </div> <!-- end search specs bar -->
    </div>
<?php

// SQL QUERY FOR SEARCH/FILTERING

// Keywords for search
if (!isset($_GET['search']) || $_GET['search'] === "") {
    $product_search_keyword = NULL;
} else {
    // Verifying user input search keyword & assigning value
    $product_search_keyword = $_GET['search'];
}

// Category for search
if (!isset($_GET['category']) || $_GET['category'] === 'all') {
    $product_category = NULL;
} else {
    $product_category = $_GET['category'];
}

// Sortinf of search results
if (!isset($_GET['sort']) || ($_GET['sort']) === "pricelow") {
    $browse_sorting_order = "pricelow";
} else {
    $browse_sorting_order = $_GET['sort'];
}

// Upper price limit of search results
if (!isset($_GET['price_limit']) || $_GET['price_limit'] === "") {
    $product_price_limit = floatval($price_max); // if user does not provide limit: limit = highest price of products
} else {
    // Verifying user input search keyword & assigning value
    $product_price_limit = floatval($_GET['price_limit']);
}

// Upper year limit of search results
if (!isset($_GET['year_limit']) || $_GET['year_limit'] === "") {
    $product_year_limit = intval($year_max); // if user does not provide limit: limit = highest year of products
} else {
    // Verifying user input search keyword & assigning value
    $product_year_limit = intval($_GET['year_limit']);
}

// Current page number
if (!isset($_GET['page'])) {
    $curr_page = 1;
} else {
    $curr_page = $_GET['page'];
}
?>

    <!-- HTML: create container -->
    <div class="container mt-5">
        <!-- HTML: create list group -->
        <ul class="list-group">

            <?php
            // Run browse function (Database query, shows results, returns number of results)
            $results_per_page = 5;
            // browse() function (functions.php)
            $num_results = browse($connection, $user_id, $curr_page, $results_per_page, $product_search_keyword, $product_category, $browse_sorting_order, $product_price_limit, $product_year_limit, $product_year_limit);
            $max_page = ceil($num_results / $results_per_page);
            ?>

        </ul>

        <!-- Pagination for results listings -->
        <nav aria-label="Search results pages" class="mt-5">
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
                          <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
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
                          <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
                        </li>');
                }

                if ($curr_page != $max_page && $num_results > $results_per_page) {
                    echo('
                        <li class="page-item">
                          <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
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