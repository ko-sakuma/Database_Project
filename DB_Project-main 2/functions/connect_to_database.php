<?php

// Connect to database
// This script establishes a connection with the database based on the information given.
// Later this file should not be stored within the project folder or version control as it includes the database password.
// However, for this project this is totally ok.

// Information from MAMP welcome page
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_db = 'auction'; // need to change to your db name
$db_port = 8889; // adjust to your ports

// Establishes connection based on information above
$connection = mysqli_connect ($db_host, $db_user, $db_password, $db_db, $db_port);

// Checks if connection is established, if not an error message is returned
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}