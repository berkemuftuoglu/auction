<?php
require_once('database.php');

// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.

// For now, I will just set session variables and redirect.

session_start();

// Create database connection
$connection = db_connect();

// Extract $_POST variables
$username = $_POST['username'];
$password = $_POST['password'];

// Prepare SQL query
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = db_query($connection, $query);
confirm_result_set($result);
$user = db_fetch_single($result);


if (db_num_rows($result) === 1) {
    // Login successful
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $username;

    // TODO: Set 'account_type' or other session variables as needed
    $_SESSION['account_type'] = $user['account_type'];

    echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');
    header("refresh:5;url=index.php");
} else {
    // Login failed
    echo('<div class="text-center">Login failed. Invalid username or password.</div>');
}

// Free result set and close database connection
mysqli_free_result($result);
db_disconnect($connection);
?>