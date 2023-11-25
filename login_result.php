<?php
require_once('database.php');

// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.

// For now, I will just set session variables and redirect.
session_start();

// Create database connection
$connection = db_connect();

// Extract $_POST variables
$email = $_POST['email'];
$password = $_POST['password'];

// Prepare SQL query
$query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
$result = db_query($connection, $query);
confirm_result_set($result);
$user = db_fetch_single($result);


if (db_num_rows($result) === 1) {

    // Login successful
    $_SESSION['logged_in'] = true;
    $_SESSION['email'] = $email;

    // TODO: Set 'account_type' or other session variables as needed
    $_SESSION['account_type'] = $user['role'];
    $_SESSION['user_id'] = $user['user_id'];

    echo ('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');
    header("refresh:2;url=dashboard.php");
} else {
    // Login failed
    echo ('<div class="text-center">Login failed. Invalid email or password.</div>');
    header("refresh:2;url=login.php");
}

// Free result set and close database connection
db_free_result($result);
db_disconnect($connection);
