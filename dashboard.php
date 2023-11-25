<?php
include_once("header.php");
require("utilities.php");
require("database.php");

$has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$user_id = $_SESSION['user_id'];
if (!$has_session) {
    echo ('<div class="text-center">Please Login.</div>');
    header("location: login.php");
    exit;
}
// Establish database connection
$connection = db_connect();
$query = "SELECT * FROM users WHERE user_id='$user_id'";
$result = db_query($connection, $query);
confirm_result_set($result);
$user = db_fetch_single($result);
?>

<div class="container mt-5">
    <h2 class="my-3">Hello, <?php echo $user['first_name']; ?></h2>

</div>