
<?php require("utilities.php"); ?>
<?php require("database.php"); ?>
<?php include_once("admin_header.php")?>

<?php
$connection = db_connect();

// Fetch all auctions
$query = "SELECT Auction.auction_id, Auction.auction_title, Item.name, Auction.end_time
          FROM Auction
          INNER JOIN Item ON Auction.item_id = Item.item_id";
$result = db_query($connection, $query);
?>

<!doctype html>
<html lang="en">
<head>
</head>
<body>

  <div class="container mt-4">
    <h1>Auction Management</h1>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Item Name</th>
          <th>End Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = db_fetch_single($result)) { ?>
          <tr>
            <td><?php echo $row['auction_id']; ?></td>
            <td><?php echo $row['auction_title']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['end_time']; ?></td>
            <td>
              <a href="admin_edit_auction.php?auction_id=<?php echo $row['auction_id']; ?>" class="btn btn-primary">Edit</a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

</body>
</html>

<?php
  db_disconnect($connection);
?>
