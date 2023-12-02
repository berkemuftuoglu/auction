<?php require("utilities.php"); ?>
<?php require("database.php"); ?>
<?php include_once("admin_header.php")?>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $connection = db_connect();
    $auction_id =  $_POST['auction_id'];

    // Determine the action: update or delete
    $action = $_POST['action'];

    if ($action == 'update') {
        // Sanitize and prepare data for updating
        $auction_title = $_POST['auction_title'];
        $end_time = $_POST['end_time'];

        // Update query
        $query = "UPDATE Auction SET auction_title = '$auction_title', end_time = '$end_time' WHERE auction_id = $auction_id";

        if (db_query($connection, $query)) {
            echo "<div>Record updated successfully.</div>";
            header("Refresh: 2; URL=admin_auctions.php");
        } else {
            echo "Error updating record";
        }
    }



    elseif ($action == 'delete') {
        // Delete query
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
