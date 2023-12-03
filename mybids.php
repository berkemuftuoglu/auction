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

<div class="container">

  <h2 class="my-3">My bids</h2>

  <ul class="list-group">
    <?php

    $user_id = $_SESSION['user_id'];
    // Establish database connection
    $connection = db_connect();

    // TODO: Use user_id to make a query to the database.
    $auction_query = "SELECT 
                        U.user_id,
                        B.price,
                        B.time_of_bid,
                        I.item_id,
                        I.name AS item_name,
                        I.description AS item_description,
                        I.colour AS item_colour,
                        I.condition AS item_condition,
                        I.category As item_category,
                        I.photo AS item_photo,
                        A.auction_id,
                        A.start_time AS auction_start_time,
                        A.end_time AS auction_end_time,
                        A.auction_title,
                        A.reserve_price,
                        A.starting_price,
                      (SELECT MAX(B2.price) FROM Bids B2 WHERE B2.auction_id = A.auction_id) AS highest_bid
                       FROM Users U
                       JOIN Bids B ON U.user_id = B.user_id
                       JOIN Auction A ON B.auction_id = A.auction_id
                       JOIN Item I ON A.item_id = I.item_id
                       WHERE U.user_id = '$user_id'
                       ORDER BY B.time_of_bid DESC";
    $auction_result = db_query($connection, $auction_query);

    // Check if item exists
    if (db_num_rows($auction_result) == 0) {
      echo "<div>Bid not found.</div>";
      db_disconnect($connection);
      exit;
    }
    while ($row = mysqli_fetch_assoc($auction_result)) {
      $item_id = $row['item_id'];
      $item_name = $row['item_name'];
      $item_description = $row['item_description'];
      $item_photo = $row['item_photo'];
      $auction_id = $row['auction_id'];
      $auction_reserve_price = $row['reserve_price'];
      $auction_starting_price = $row['starting_price'];
      $auction_title = $row['auction_title'];
      $auction_start_time = $row['auction_start_time'];
      $auction_end_time = $row['auction_end_time'];
      $bid_price = $row['price'];
      $bid_time = $row['time_of_bid'];
      $highest_bid = $row['highest_bid'];


      if (strlen($item_description) > 250) {
        $desc_shortened = substr($item_description, 0, 250) . '...';
      } else {
        $desc_shortened = $item_description;
      }

      // need to have the times at DateTime objects
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
            <h5><a href="listing.php?item_id=<?php echo urlencode($row['item_id']); ?>">
                <?php echo $auction_title; ?>
              </a> </h5>
            <?php echo $desc_shortened; ?>
          </div>
        </div>

        <div class="text-center text-nowrap">
          <ul class="list-group list-group-flush">
            <li class="list-group-item"> Your Bid <span style="font-size: 1.5em">£<?php echo number_format($bid_price, 2); ?></span> </li>
            <li class="list-group-item"> You Placed the bid on <?php echo date("F j, Y, g:i a", strtotime($bid_time)); ?> </li>
            <li class="list-group-item"> Starting Price £ <?php echo number_format($auction_starting_price, 2); ?> </li>
            <li class="list-group-item"> Reserve Price £ <?php echo number_format($auction_reserve_price, 2); ?></li>
            <li class="list-group-item"> <?php echo $time_remaining; ?> </li>
            <?php
            echo "<br />";
            if ($bid_price == $highest_bid) {
              echo "<span class='badge badge-pill badge-success'>Highest bid</span>";
            } else {
              echo "<span class='badge badge-pill badge-danger'>Not the highest bid</span>";
              if ($now < $auction_end_time) {
                echo "<form action='listing.php?item_id=" . urlencode($item_id) . "' method='post'>
                <input type='submit' class='btn btn-primary btn-sm mt-2' value='Place a Bid'/>
                </form>";
              }
            }
            ?>
          </ul>
        </div>

      </li>

    <?php
    }
    db_free_result($auction_result);
    db_disconnect($connection);
    ?>

  </ul>

  <!-- <div class="card mt-3">
  <div class="card-header">
  <span class='badge badge-pill badge-danger'>Not a Highest Bid</span> <button type="button" class="btn btn-primary btn-sm">Place a Bid</button>
  </div>
    <div class="row no-gutters">
      <div class="col-md-3">
        <img src="photos/empty.png" class="card-img img-fluid" alt="Image">
      </div>
      <div class="col-md-3">
        <div class="card-body">
          <h5 class="card-title">Card title</h5>
          <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">Cras justo odio</li>
            <li class="list-group-item">Dapibus ac facilisis in</li>
            <li class="list-group-item">Vestibulum at vestibulum</li>
          </ul>
        </div>
        
      </div>
      
    </div>
    <div class="card-footer text-muted">
    2 days ago
  </div> -->
</div>


<?php include_once("footer.php") ?>