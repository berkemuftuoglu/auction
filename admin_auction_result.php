<?php require("utilities.php"); ?>
<?php require("database.php"); ?>
<?php include_once("admin_header.php"); ?>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $connection = db_connect();
    $auction_id = $_POST['auction_id'];
    $item_name = $_POST['item_name']; // Assuming this is passed from the form

    // Determine the action: update or delete
    $action = $_POST['action'];

    if ($action == 'update') {
        // First, get the item_id associated with the auction
        $getItemIdQuery = "SELECT item_id FROM Auction WHERE auction_id = $auction_id";
        $itemResult = db_query($connection, $getItemIdQuery);
        if ($itemRow = db_fetch_single($itemResult)) {
            $item_id = $itemRow['item_id'];

            // Update the Item table
            $updateItemQuery = "UPDATE Item SET name = '$item_name' WHERE item_id = $item_id";
            if (!db_query($connection, $updateItemQuery)) {
                echo "Error updating item name.";
            }
        }

        // Sanitize and prepare data for updating
        $auction_title = $_POST['auction_title'];
        $end_time = $_POST['end_time'];

        // Update query for Auction table
        $query = "UPDATE Auction SET auction_title = '$auction_title', end_time = '$end_time' WHERE auction_id = $auction_id";

        if (db_query($connection, $query)) {
            echo "<div>Record updated successfully.</div>";
            header("Refresh: 2; URL=admin_auctions.php");
        } else {
            echo "Error updating record";
        }
    } elseif ($action == 'delete') {
        // Delete query for Auction table
        $query = "DELETE FROM Auction WHERE auction_id = $auction_id";

        if (db_query($connection, $query)) {
            echo "<div>Auction deleted successfully.</div>";
            header("Refresh: 2; URL=admin_auctions.php");
        } else {
            echo "Error deleting record:";
        }
    }

    db_disconnect($connection);
} else {
    header("Location: admin_auctions.php");
}
?>
