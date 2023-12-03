<?php include_once("admin_header.php")?>
<?php require("utilities.php")?>
<?php require("database.php")?>

<?php

// Assuming the admin's name is stored in the session (modify as needed)
$adminName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] ?? 'Admin';


$connection = db_connect();

// Query to count the number of users
$user_count_query = "SELECT COUNT(*) FROM Users";
$user_count_result = db_query($connection, $user_count_query);
$user_count = db_fetch_array($user_count_result)[0];

// Query to count the number of auctions
$auction_count_query = "SELECT COUNT(*) FROM Auction";
$auction_count_result = db_query($connection, $auction_count_query);
$auction_count = db_fetch_array($auction_count_result)[0];

// Query to get top categories (example of creative element)
$top_categories_query = "SELECT category, COUNT(*) as count FROM Item GROUP BY category ORDER BY count DESC LIMIT 3";
$top_categories_result = db_query($connection, $top_categories_query);
?>

<!doctype html>
<html lang="en">
<head>
</head>
<body>

  <div class="container mt-4">
    <h1>Hello, <?php echo $adminName; ?>!</h1>
    <p>Current number of users: <?php echo $user_count; ?></p>
    <p>Current number of auctions: <?php echo $auction_count; ?></p>

    <h3>Top Auction Categories</h3>
    <ul>
      <?php while ($row = db_fetch_single($top_categories_result)) {
        echo "<li>" . $row['category'] . " (" . $row['count'] . ")</li>";
      } ?>
    </ul>
  </div>

</body>
</html>

<?php
  db_disconnect($connection);
?>


<?php include_once("footer.php")?>
