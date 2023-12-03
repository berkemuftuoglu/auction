<?php 
include_once("header.php");
require("utilities.php");
require("database.php");

$has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
if (!$has_session) {
  echo ('<div class="text-center">Please Login.</div>');
  header("location: login.php");
  exit;
}
?>

<div class="container mt-5">
  <h2 class="my-3">My listings</h2>
  <ul class="list-group">
    <?php

    $user_id = $_SESSION['user_id'];
    // Establish database connection
    $connection = db_connect();

    // TODO: Use user_id to make a query to the database.
    $auction_query = "SELECT auction.auction_title,
                        auction.start_time,
                        auction.end_time,
                        auction.reserve_price,
                        auction.starting_price,
                        item.item_id,
                        item.name,
                        item.description,
                        item.photo
  FROM auction
  INNER JOIN item ON auction.item_id = item.item_id
  WHERE auction.user_id = '$user_id'";
    $auction_result = db_query($connection, $auction_query);

    // Check if item exists
    if (db_num_rows($auction_result) == 0) {
      echo "<div>Item not found.</div>";
      db_disconnect($connection);
      exit;
    }
    while ($row = mysqli_fetch_assoc($auction_result)) {
      $item_id = $row['item_id'];
      $item_name = $row['name'];
      $item_description = $row['description'];
      $item_photo = $row['photo'];
      $auction_title = $row['auction_title'];
      $auction_reserve_price = $row['reserve_price'];
      $auction_starting_price = $row['starting_price'];
      $auction_title = $row['auction_title'];
      $auction_start_time = $row['start_time'];
      $auction_end_time = $row['end_time'];


      if (strlen($item_description) > 250) {
        $desc_shortened = substr($item_description, 0, 250) . '...';
      } else {
        $desc_shortened = $item_description;
      }

      $format = 'Y-m-d H:i:s';
      $auction_start_time = DateTime::createFromFormat($format, $auction_start_time);
      $auction_end_time = DateTime::createFromFormat($format, $auction_end_time);

      $now = new DateTime();
      if ($now > $auction_end_time) {
        $time_remaining = 'This auction has ended';
      } else {
        // Get interval:
        $time_to_end = date_diff($now, $auction_end_time);
        $time_remaining = display_time_remaining($time_to_end) . ' remaining';
      }

    ?>


      <li class="list-group-item d-flex justify-content-between mt-3">
        <div class="d-flex align-items-center p-2 mr-5">
          <img src="<?php echo $item_photo; ?>" alt="<?php echo $auction_title; ?>" class="img-fluid" style="max-width: 120px; max-height: 120px;">
          <div class="ml-3">
            <h5><a href="listing.php?item_id=<?php echo $item_id; ?>">
                <?php echo $auction_title; ?>
              </a> </h5>
            <?php echo $desc_shortened; ?>
          </div>
        </div>
        <div class="text-center text-nowrap">Starting Price <span style="font-size: 1.5em">£
            <?php echo number_format($auction_starting_price, 2); ?>
          </span><br />Reserve Price £
          <?php echo $auction_reserve_price; ?><br />
          <?php echo $time_remaining; ?>
        </div>
      </li>

    <?php
    }
    ?>
  </ul>
</div>



<?php include_once("footer.php"); ?>