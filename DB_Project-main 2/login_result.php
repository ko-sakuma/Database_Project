<?php
require_once('functions/connect_to_database.php');
require_once('functions/functions.php');

// Get the login form submissions and store them as variables
$user_email_login = "%{$_POST['user_email']}%"; // Used "%%" to use user_email later while querying database
$user_password_login = $_POST['user_password'];
// Hash user_password_login
$user_password_login_hashed = sha1($user_password_login);

// get login form submissions again and store them without "%%" to later compare it to existing values in database
$user_email_from_login_input = $_POST['user_email'];
// get hashed password to later compare it to existing values in database
$user_password_from_login_input = $user_password_login_hashed;

// Get the data (user_email, user_password, user_id and user_role_id) from the database to then compare it
// Get data
$sql = "
        SELECT
            u.user_email,
            u.user_password,
            u.user_id,
            u.user_name,
			user_role.user_role_name
		FROM
			users AS u
		LEFT JOIN
			user_role 
		ON
			user_role.user_role_id = u.user_role_id
		WHERE
			u.user_email LIKE ?;";

// Create a prepared statement
$stmt = mysqli_stmt_init($connection);
// Prepare the prepared statement
if (!mysqli_stmt_prepare($stmt, $sql)) {
    // If error then return error
    print_alert("There is an SQL error. Please try again or contact the developers", "danger");
} else {
    // Bind input from login to the placeholder ("?" above)
    mysqli_stmt_bind_param($stmt, "s", $user_email_login);
    // Execute parameters
    mysqli_stmt_execute($stmt);
    // Get result from database
    $result = mysqli_stmt_get_result($stmt);
    // Fetch results
    $result_fetched = mysqli_fetch_assoc($result);
    // Store database query results
    $user_email_from_database = $result_fetched['user_email'];
    $user_password_from_database = $result_fetched['user_password'];
    $user_id_from_database = $result_fetched['user_id'];
    $user_role_name_from_database = $result_fetched['user_role_name'];
    $user_name_from_database = $result_fetched['user_name'];
}

// Validating if user exists in the database
validate_user_existence($user_email_from_database);

// Validating if password matches the database and if so logging the user in
validate_login($user_email_from_login_input, $user_email_from_database, $user_password_from_login_input, $user_password_from_database, $user_id_from_database, $user_role_name_from_database, $user_name_from_database);

