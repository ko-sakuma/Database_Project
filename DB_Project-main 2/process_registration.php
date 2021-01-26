<?php
require_once("functions/connect_to_database.php");
require_once("functions/functions.php");


// check it the form in register.php was submitted
if (isset($_POST["submit"])) {

    // Get the register form submissions and store them as variables
    //$user_id = NULL; // as it is auto incrementing in the database
    $user_name = $_POST["user_name"];
    $user_email = $_POST["user_email"];
    $user_password = $_POST["user_password"];
    $user_password_repeat = $_POST["user_password_repeat"];
    $user_role_id = $_POST["user_role_id"];

    // password to check length
    $user_password_length = strlen($user_password);

    // Hash passwords
    $user_password = sha1($user_password);
    $user_password_repeat = sha1($user_password_repeat);

    // Check if user input to register is not empty
    if (register_is_empty($user_name, $user_email, $user_password, $user_password_repeat) !== false) {
        header("Location: register.php?error=register_is_empty"); // if at least 1 empty input field
        exit();
    }

    // Check if user_email is valid
    if (invalid_user_email($user_email) !== false) {
        header("Location: register.php?error=invalid_user_email"); // if invalid mail
        exit();
    }

    // Check if user_name and user_email is unique
    if (unique_user($connection, $user_name, $user_email) !== false) {
        header("Location: register.php?error=not_unique_user"); // if user is not unique
        exit();
    }

    // check if user_password == user_password_repeat
    if (match_user_password($user_password, $user_password_repeat) !== false) {
        header("Location: register.php?error=not_matching_user_password"); // if passwords do not match
        exit();
    }

    // check if user_password has 8 <= characters <= 16
    if (user_password_length($user_password_length) !== false) {
        header("Location: register.php?error=user_password_length_fail"); // if passwords do not match
        exit();
    }

    // Add user date to the users table in our database
    $sql = "
            INSERT INTO
                users (user_id,
                user_name,
                user_email,
                user_password,
                user_role_id)
            VALUES
                (?, ?, ?, ?, ?);";

    // Create a prepared statement
    $stmt = mysqli_stmt_init($connection);
    // Prepare the prepared statement
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        // If error then return error
        print_alert("There is an SQL error. Please try again or contact the developers", "danger");
    } else {
        // Bind the output from the registration form to the placeholder ("?" above)
        mysqli_stmt_bind_param($stmt, "sssss", $NULL, $user_name, $user_email, $user_password, $user_role_id);
        // Execute parameters
        mysqli_stmt_execute($stmt);

        // Notification: Welcome new user to the platform by sending him/her a notification
        // SQL query to get user_id via $user_email
        $sql = "
                SELECT
                    u.user_id
                FROM
                    users AS u
                WHERE
                    u.user_email LIKE '" . $user_email . "' ";

        // fetching result
        $result = $connection->query($sql);
        while ($row = mysqli_fetch_array($result)) {
            $user_id = $row['user_id'];
        }

        // Call function to notify users
        notify_user($connection, $user_id, 'Hi welcome to WinAuction. We hope that you will enjoy bidding on Products.');

        // Trigger alert message in browse.php if user successfully registered
        header("Location: browse.php?success=registered");
    }
} // if submit button was not pressed then send user back to register page
else {
    print_alert("You did not submit the registration form. If you like to register please submit the form.", "danger");
    header('register.php');
    exit();
}