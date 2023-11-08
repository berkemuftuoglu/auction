<?php
  // For now, index.php just redirects to browse.php, but you can change this
  // if you like.
  // User -> index.php -> LOGIN -> login_result -> browse.php

session_start();

// Check if the user is already logged in,
// if yes then redirect to the dashboard
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: dashboard.php");
    exit;
}

// If not logged in, redirect to login page
header("Location: temp.php");
exit;
?>