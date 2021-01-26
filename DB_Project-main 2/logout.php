<?php

session_start();

unset($_SESSION['logged_in']);
unset($_SESSION['user_id']);
setcookie(session_name(), "", time() - 360);
session_destroy();

// Redirect to browse showing a log out alert
header("Location: browse.php?success=logout");
