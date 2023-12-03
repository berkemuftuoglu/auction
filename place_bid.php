<?php
require_once('database.php');
require_once('utilities.php');


session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo('<div class="text-center">You are not logged in. Redirecting to login page.</div>');
    header("refresh:2;url=login.php");
    exit;
}

// Create database connection
$connection = db_connect();

$user_id = $_SESSION['user_id'];

// Extract $_POST variables
$bid_amount = $_POST['bid'] ?? null;
$auction_id = $_POST['auction_id'] ?? null; // Adjusted to auction_id as per your database schema
$email = $_SESSION['email'];
$max_bid = $_POST['current_price'] ?? null;

// Fetch user ID from email
// $user_query = "SELECT user_id FROM Users WHERE email = '$email'";
// $user_result = db_query($connection, $user_query);
// $user_data = db_fetch_single($user_result);
// $user_id = $user_data['user_id'];

// Fetch email for user id
$user_query = "SELECT email FROM Users WHERE user_id = '$user_id'";
$user_result = db_query($connection, $user_query);
$user_data = db_fetch_single($user_result);
$email = $user_data['email'];

// Check auction status
$auction_query = "SELECT reserve_price, end_time FROM Auction WHERE auction_id = '$auction_id'";
$auction_result = db_query($connection, $auction_query);
if (db_num_rows($auction_result) == 0) {
    echo('<div class="text-center">Auction not found. Redirecting back.</div>');
    header("refresh:2;url=browse.php");
    exit;
}
$auction_data = db_fetch_single($auction_result);
$end_time = new DateTime($auction_data['end_time']);
$now = new DateTime();


#Do not allow bid if lower then
if($bid_amount < $max_bid){
    echo('<div class="text-center">Bid must be higher than current bid. Redirecting back.</div>');
    header("refresh:2;url=listing.php?item_id=" . $auction_id);
    exit;
}

if ($now > $end_time) {
    echo('<div class="text-center">Auction has ended. Redirecting back.</div>');
    header("refresh:2;url=listing.php?item_id=" . $auction_id);
    exit;
}

// Insert the bid into the database
$insert_bid_query = "INSERT INTO Bids (auction_id, user_id, time_of_bid, price) VALUES ('$auction_id', '$user_id', NOW(), '$bid_amount')";
$insert_bid_result = db_query($connection, $insert_bid_query);

if ($insert_bid_result) {
    echo('<div class="text-center">Bid placed successfully. Redirecting back.</div>');

    // ********************* Send out email **************************

    //send email to bidder
    echo $email;
    echo $subject;
    echo $content;
    $recipient = $email;
    $subject = "Bid placed!";
    $content = "<body>Your bid has been placed successfully! </body></br>";
    sendmail($recipient, $subject, $content);

    // **************************************************************


} else {
    echo('<div class="text-center">Failed to place bid. Redirecting back.</div>');
}
header("refresh:2;url=listing.php?item_id=" . $auction_id);

// Clean up and close database connection
db_disconnect($connection);
?>
