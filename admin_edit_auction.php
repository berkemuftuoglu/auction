<?php require("utilities.php"); ?>
<?php require("database.php"); ?>
<?php include_once("admin_header.php")?>

<?php
// Check if auction_id is set in GET parameter
if (isset($_GET['auction_id'])) {
    $connection = db_connect();

    $auction_id = $_GET['auction_id'];

    // Query to fetch auction details
    $query = "SELECT Auction.auction_id, Auction.auction_title, Auction.end_time, Item.name AS item_name 
              FROM Auction
              INNER JOIN Item ON Auction.item_id = Item.item_id
              WHERE Auction.auction_id = '$auction_id'";

    $result = db_query($connection, $query);
    $auction = db_fetch_single($result);

    if (!$auction) {
        echo "Auction not found.";
        exit;
    }

    // Convert end_time to a format suitable for datetime-local input
    $auction['end_time'] = str_replace(' ', 'T', $auction['end_time']);
} else {
    echo "No auction ID provided.";
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
</head>
<body>
  <div class="container mt-4">
    <h1>Edit Auction</h1>
    <form action="admin_auction_result.php" method="post">
      <input type="hidden" name="auction_id" value="<?php echo $auction['auction_id']; ?>">

      <div class="form-group">
        <label for="auction_title">Auction Title:</label>
        <input type="text" class="form-control" name="auction_title" id="auction_title" value="<?php echo $auction['auction_title']; ?>" required>
      </div>

      <div class="form-group">
        <label for="item_name">Item Name:</label>
        <input type="text" class="form-control" name="item_name" id="item_name" value="<?php echo $auction['item_name']; ?>" required>
      </div>

      <div class="form-group">
        <label for="end_time">End Time:</label>
        <input type="datetime-local" class="form-control" name="end_time" id="end_time" value="<?php echo $auction['end_time']; ?>" required>
      </div>

      <button type="submit" name="action" value="update" class="btn btn-primary">Update Auction</button>
      <button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this auction?');">Delete Auction</button>
    </form>
  </div>

</body>
</html>



<?php
  db_disconnect($connection);
?>