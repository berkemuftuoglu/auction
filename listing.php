<?php include_once("header.php") ?>
<?php require("utilities.php") ?>
<?php require("database.php") ?>

<?php

$has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$user_id = $_SESSION['user_id'];

// Get info from the URL:
$item_id = $_GET['item_id'] ?? null;

if (!$item_id) {
  echo ("<div>Error: Item ID is missing.</div>");
  exit;
}



// Establish database connection
$connection = db_connect();

// TODO: Use item_id to make a query to the database.
$item_query = "SELECT auction.auction_title,
                        auction.start_time,
                        auction.end_time,
                        item.name,
                        item.description,
                        item.photo
  FROM auction
  INNER JOIN item ON auction.item_id = item.item_id
  WHERE auction.auction_id = '$item_id'";
$item_result = db_query($connection, $item_query);

// Check if item exists
if (db_num_rows($item_result) == 0) {
  echo "<div>Error: Item not found.</div>";
  db_disconnect($connection);
  exit;
}

// Get item details
$item = db_fetch_single($item_result);
$title = $item['auction_title'];
$description = $item['description'];
$item_photo = $item['photo'];
$end_time = new DateTime($item['end_time']); //NOT SURE ABOUT THIS ONE

// Get current price and number of bids
$bid_query = "SELECT b.price AS current_price, u.user_id, u.first_name, u.last_name, COUNT(*) AS num_bids
              FROM Bids b
              INNER JOIN Users u ON b.user_id = u.user_id
              WHERE b.auction_id = '$item_id'
              AND b.price = (SELECT MAX(price) FROM Bids WHERE auction_id = '$item_id')
              GROUP BY b.price, u.user_id, u.first_name, u.last_name";

$bid_result = db_query($connection, $bid_query);
$bid_data = db_fetch_single($bid_result);

// Check if there is any bid data
if ($bid_data) {
  $current_price = $bid_data['current_price'] ?: '0.00';
  $current_winner = $bid_data['first_name'] . ' ' . $bid_data['last_name']; // Concatenate first name and last name
  $num_bids = $bid_data['num_bids'];
} else {
  // Default values if no bids are found
  $current_price = '0.00';
  $current_winner = 'None';
  $num_bids = 0;
}


$watching = false;
if ($has_session) {
    $watchlist_query = "SELECT 1
                        FROM Watchlist
                        WHERE user_id = '$user_id' AND
                              item_id = $item_id";
    $watchlist_result = db_query($connection, $watchlist_query);
    $watching = db_num_rows($watchlist_result) > 0; // True if watching
}


// Clean up the result sets
db_free_result($item_result);
db_free_result($bid_result);
if (isset($watchlist_result)) {
  db_free_result($watchlist_result);
}


// TODO: Note: Auctions that have ended may pull a different set of data,
//       like whether the auction ended in a sale or was cancelled due
//       to lack of high-enough bids. Or maybe not.

// Calculate time to auction end:
$now = new DateTime();

if ($now < $end_time) {
  $time_to_end = date_diff($now, $end_time);
  $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
}

// TODO: If the user has a session, use it to make a query to the database
//       to determine if the user is already watching this item.
//       For now, this is hardcoded.
//DONE $has_session = true;
//DONE $watching = false;
?>


<div class="container">



  <div class="row"> <!-- Row #1 with auction title + watch button -->
    <div class="col-sm-8"> <!-- Left col -->
      <h2 class="my-3"><?php echo ($title); ?></h2>
    </div>
    <div class="col-sm-4 align-self-center"> <!-- Right col -->
      <?php
      /* The following watchlist functionality uses JavaScript, but could
        just as easily use PHP as in other places in the code */
      if ($now < $end_time) :
      ?>
        <div id="watch_nowatch" <?php if ($has_session && $watching) echo ('style="display: none"'); ?>>
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>
        </div>
        <div id="watch_watching" <?php if (!$has_session || !$watching) echo ('style="display: none"'); ?>>
          <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
          <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
        </div>
      <?php endif /* Print nothing otherwise */ ?>
    </div>
  </div>

  <div class="row"> <!-- Row #0 with item image -->
    <div class="col-sm-8">
      <?php if (!empty($item['photo'])) : ?>
        <img src="<?php echo $item['photo']; ?>" alt="Item Image" class="img-fluid">
      <?php endif; ?>
    </div>
  </div>


  <div class="row"> <!-- Row #2 with auction description + bidding info -->
    <div class="col-sm-8"> <!-- Left col with item info -->

      <div class="itemDescription">
        <?php echo ($description); ?>
      </div>

    </div>

    <div class="col-sm-4"> <!-- Right col with bidding info -->

      <p>
        <?php if ($now > $end_time) : ?>
      <p>This auction ended <?php echo htmlspecialchars($end_time->format('j M H:i')); ?></p>
      <!-- Added code to display auction result -->
      <?php
          // Query to fetch auction result details
          // TODO: Print the result of the auction here?
          echo "<p>The winning bid was $" . number_format($current_price, 2) . " " . $current_winner . "</p>";
      ?>
    <?php else : ?>
      Auction ends <?php echo (date_format($end_time, 'j M H:i') . $time_remaining) ?></p>
      <p class="lead">Current bid: £<?php echo (number_format($current_price, 2)) ?></p>

      <!-- Bidding form -->
      <form method="POST" action="place_bid.php">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">£</span>
          </div>
          <input type="number" class="form-control" id="bid">
        </div>
        <button type="submit" class="btn btn-primary form-control">Place bid</button>
      </form>
    <?php endif ?>


    </div> <!-- End of right col with bidding info -->

  </div> <!-- End of row #2 -->



  <?php include_once("footer.php") ?>


  <script>
    // JavaScript functions: addToWatchlist and removeFromWatchlist.

    function addToWatchlist(button) {
      console.log("These print statements are helpful for debugging btw");

      // This performs an asynchronous call to a PHP function using POST method.
      // Sends item ID as an argument to that function.
      $.ajax('watchlist_funcs.php', {
        type: "POST",
        data: {
          functionname: 'add_to_watchlist',
          arguments: [<?php echo ($item_id); ?>]
        },

        success: function(obj, textstatus) {
          // Callback function for when call is successful and returns obj
          console.log("Success");
          var objT = obj.trim();

          if (objT == "success") {
            $("#watch_nowatch").hide();
            $("#watch_watching").show();
          } else {
            var mydiv = document.getElementById("watch_nowatch");
            mydiv.appendChild(document.createElement("br"));
            mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
          }
        },

        error: function(obj, textstatus) {
          console.log("Error");
        }
      }); // End of AJAX call

    } // End of addToWatchlist func

    function removeFromWatchlist(button) {
      // This performs an asynchronous call to a PHP function using POST method.
      // Sends item ID as an argument to that function.
      $.ajax('watchlist_funcs.php', {
        type: "POST",
        data: {
          functionname: 'remove_from_watchlist',
          arguments: [<?php echo ($item_id); ?>]
        },

        success: function(obj, textstatus) {
          // Callback function for when call is successful and returns obj
          console.log("Success");
          var objT = obj.trim();

          if (objT == "success") {
            $("#watch_watching").hide();
            $("#watch_nowatch").show();
          } else {
            var mydiv = document.getElementById("watch_watching");
            mydiv.appendChild(document.createElement("br"));
            mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
          }
        },

        error: function(obj, textstatus) {
          console.log("Error");
        }
      }); // End of AJAX call

    } // End of addToWatchlist func
  </script>