<?php
  // check if functioname and arg is captured from the call.
  if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
    return;
  }
  // connect to database.
  include_once('functions/connect_to_database.php');

  // Extract arguments from the POST variables.
  $args = $_POST['arguments'];

  if ($_POST['functionname'] == "add_to_watchlist") {
    // Add user date to the users table in our database
    $sql = "
      INSERT INTO watching (user_id, product_id, watching_creation_date) 
      VALUES (?, ?, NULL);
    ";

    // Create a prepared statement
    $stmt = mysqli_stmt_init($connection);

    // Prepare the prepared statement
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        // If error then assign res to "failure"
      $res = "failure";

    } else {
        // Bind the output from the registration form to the placeholder ("?" above)
        mysqli_stmt_bind_param($stmt, "ii", $args[0], $args[1]);

        // Execute parameters
        mysqli_stmt_execute($stmt);

        $res = "success";
    }


  } else if ($_POST['functionname'] == "remove_from_watchlist") {
    $sql = "
      DELETE FROM watching
      WHERE user_id = ?
      AND product_id = ?;
    ";

    // Create a prepared statement
    $stmt = mysqli_stmt_init($connection);

    // Prepare the prepared statement
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        // If error then assign res to "failure"
      $res = "failure";

    } else {
        // Bind the output from the registration form to the placeholder ("?" above)
        mysqli_stmt_bind_param($stmt, "ii", $args[0], $args[1]);

        // Execute parameters
        mysqli_stmt_execute($stmt);

        $res = "success";
    }
  }

  echo $res;