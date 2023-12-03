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

$connection = db_connect();
$query = "SELECT * FROM users WHERE user_id='$user_id'";
$result = db_query($connection, $query);
confirm_result_set($result);
$user = db_fetch_single($result);
db_free_result($result);
db_disconnect($connection);

function updateUserField($field, $new_value, $user_id) {
    $connection = db_connect();

    $update_query = "UPDATE users SET $field = '$new_value' WHERE user_id = '$user_id'";
    $stmt = db_query($connection, $update_query);

    confirm_result_set($stmt);

    db_free_result($stmt);
    db_disconnect($connection);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $user_id = $_POST['user_id'];

    if (isset($_POST['new_first_name'])) {
        updateUserField('first_name', $_POST['new_first_name'], $user_id);
    } elseif (isset($_POST['new_last_name'])) {
        updateUserField('last_name', $_POST['new_last_name'], $user_id);
    } elseif (isset($_POST['new_email'])) {
        updateUserField('email', $_POST['new_email'], $user_id);
    } elseif (isset($_POST['new_role'])) {
        if ($_POST['new_role'] == 'Seller') {
            updateUserField('role', 0, $user_id);
        } elseif ($_POST['new_role'] == 'Buyer') {
            updateUserField('role', 1, $user_id);
        } else {
            updateUserField('', '', '');
        }
    } elseif (isset($_POST['new_password'])) {
        updateUserField('password', $_POST['new_password'], $user_id);
    }

}
?>

<div class="container mt-5">
    <h2 class="my-3">Welcome, <?php echo $user['first_name']; ?>!</h2>
    <p>What would you like to do today?</p>
    <?php
        if ($user['role'] == 0) {

            $connection = db_connect();
            $stmt = "SELECT total_ratings, average_rating FROM users WHERE user_id = '$user_id'";
            $rating_call = db_query($connection, $stmt);
            confirm_result_set($rating_call);

            $rating = db_fetch_single($rating_call);

            $totalRatings = $rating["total_ratings"];
            $userRating = $rating["average_rating"];

            db_free_result($rating_call);
            db_disconnect($connection);

            echo '<h3>Selling Activites</h3>';
            echo '<li><a href="/browse.php">Browse Listings</a></li>';
            echo '<li><a href="/mylistings.php">See My Current Listings</a></li>';
            echo '<li><a href="/create_auction.php">Create a New Auction</a></li>';
            echo '<br>';
            echo '<h3>Your Current Rating is </h3>';
            if ($totalRatings == 0) {
                echo "You haven't been rated yet";
            } else {
                echo $userRating;
            }
            echo '<br>';
            echo '<br>';
        } elseif ($user['role'] == 1) {
            echo '<h3>Buying Activites</h3>';
            echo '<li><a href="/browse.php">Browse Listings</a></li>';
            echo '<li><a href="/mybids.php">See My Current Bids</a></li>';
            echo '<li><a href="/watchlist.php">Check Out My Watchlist</a></li>';
            echo '<li><a href="/recommendations.php">Look At Recommended Items</a></li>';
            echo '<li><a href="/recommendations.php">Review Won Auctions and Rate Sellers</a></li>';
            echo '<br>';
        };
    ?> 
    <h3>Update Profile Information</h3>
    <table>
        <tr>
            <th>First Name</th>
            <td><?php echo $user['first_name']?></td>
            <td>
                <form method="post" action="">
                    <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
                    <input type="text" name="new_first_name" placeholder="Change First Name">
                    <input type="submit" name="submit" value="Update">
                </form>
            </td>
        </tr>
        <tr>
            <th>Last Name</th>
            <td><?php echo $user['last_name']?></td>
            <td>
                <form method="post" action="">
                    <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
                    <input type="text" name="new_last_name" placeholder="Change Last Name">
                    <input type="submit" name="submit" value="Update">
                </form>
            </td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo $user['email']?></td>
            <td>
                <form method="post" action="">
                    <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
                    <input type="text" name="new_email" placeholder="Change Email">
                    <input type="submit" name="submit" value="Update">
                </form>
            </td>
        </tr>
        <tr>
            <th>Role</th>
            <td><?php if ($user['role'] == 0) {
                    echo "Seller";
                } elseif ($user['role'] == 1) {
                    echo "Buyer";
                } else {
                    echo "Admin";
                }?></td>
            <td>
                <form method="post" action="">
                    <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
                    <input type="text" name="new_role" placeholder="Buyer or Seller">
                    <input type="submit" name="submit" value="Update">
                </form>
            </td>
        </tr>
        <tr>
            <th>Password</th>
            <td><?php echo $user['password']?></td>
            <td>
                <form method="post" action="">
                    <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
                    <input type="text" name="new_password" placeholder="Change Password">
                    <input type="submit" name="submit" value="Update">
                </form>
            </td>
        </tr>
    </table>
</div>