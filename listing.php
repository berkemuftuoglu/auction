<?php include_once("header.php") ?>
<?php require("utilities.php") ?>
<?php require("database.php") ?>

<?php

$has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$user_id = $_SESSION['user_id'];
$account_type = $_SESSION['account_type'];

// Get info from the URL:
$item_id = $_GET['item_id'] ?? null;

if (!$item_id) {
  echo ("<div>Error: Item ID is missing.</div>");
  exit;
}

// Establish database connection
$connection = db_connect();

// Fetch item details
$item_query = "SELECT name, description, photo FROM Item WHERE item_id = '$item_id'";
$item_result = db_query($connection, $item_query);

// Check if item exists
if (db_num_rows($item_result) == 0) {
  echo "<div>Error: Item not found.</div>";
  db_disconnect($connection);
  exit;
}

$item = db_fetch_single($item_result);
$name = $item['name'];
$description = $item['description'];
$item_photo = $item['photo'];

// Check if the item is part of an auction
$auction_query = "SELECT auction_id, start_time, end_time, auction_title
                  FROM Auction WHERE item_id = '$item_id'";
$auction_result = db_query($connection, $auction_query);
$auction_exists = db_num_rows($auction_result) > 0;


if ($auction_exists) {
  // Item is part of an auction
  $auction_data = db_fetch_single($auction_result);
  $auction_id = $auction_data['auction_id'];
  $title = $auction_data['auction_title'];
  $start_time = new DateTime($auction_data['start_time']);
  $end_time = new DateTime($auction_data['end_time']);

  // Get current price and number of bids
  $bid_query = "SELECT b.price AS current_price, u.user_id, u.first_name, u.last_name, COUNT(*) AS num_bids
  FROM Bids b
  INNER JOIN Users u ON b.user_id = u.user_id
  WHERE b.auction_id = '$auction_id'
  AND b.price = (SELECT MAX(price) FROM Bids WHERE auction_id = '$auction_id')
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



db_free_result($bid_result);

// Calculate time to auction end:
$now = new DateTime();
if ($now < $end_time) {
  $time_to_end = date_diff($now, $end_time);
  $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
}
} else {
  //Item is not part of an auction
  $title = $name;
}



// TODO: Use item_id to make a query to the database.

// Get current price and number of bids

//watchlist related
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
if (isset($watchlist_result)) {
  db_free_result($watchlist_result);
}


// TODO: Note: Auctions that have ended may pull a different set of data,
//       like whether the auction ended in a sale or was cancelled due
//       to lack of high-enough bids. Or maybe not.

// TODO: If the user has a session, use it to make a query to the database
//       to determine if the user is already watching this item.
//       For now, this is hardcoded.
//DONE $has_session = true;
//DONE $watching = false;
?>

<div class="container">
  <div class="row mt-4 mb-4"> <!-- Top row with title and watch button -->
    <div class="col-md-8"> <!-- Left col -->
      <h2 class="my-3"><?php echo ($title); ?></h2>
    </div>

    <?php if ($auction_exists && $account_type == '1'): ?>
      <div class="col-md-4 align-self-center text-right"> <!-- Right col -->
        <!-- Watchlist functionality -->
        <?php if ($now < $end_time) : ?>
          <div id="watch_nowatch" <?php if ($has_session && $watching) echo ('style="display: none"'); ?>>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">
              <i class="fa fa-plus" aria-hidden="true"></i> Add to watchlist
            </button>
          </div>
          <div id="watch_watching" <?php if (!$has_session || !$watching) echo ('style="display: none"'); ?>>
            <button type="button" class="btn btn-success btn-sm" disabled><i class="fa fa-eye" aria-hidden="true"></i> Watching</button>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">
              <i class="fa fa-times" aria-hidden="true"></i> Remove
            </button>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="row"> <!-- Row with item image -->
    <div class="col-md-8">
      <?php if (!empty($item['photo'])) : ?>
        <img src="<?php echo $item['photo']; ?>" alt="Item Image" class="img-fluid rounded">
      <?php endif; ?>
    </div>
  </div>

  <div class="row mt-3"> <!-- Row with item description and bidding info -->
    <div class="col-md-8"> <!-- Left col with item info -->
      <div class="itemDescription">
        <p><?php echo ($description); ?></p>
      </div>
    </div>

    <?php if ($auction_exists): ?>
      <div class="col-md-4"> <!-- Right col with bidding info -->
        <div class="card">
          <div class="card-body">
            <?php if ($now > $end_time) : ?>
              <h5 class="card-title">Auction Ended</h5>
              <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($end_time->format('j M H:i')); ?></small></p>
              <p>The winning bid was £<?php echo number_format($current_price, 2); ?></p>
            <?php elseif ($account_type == '1'): ?>
              <h5 class="card-title">Auction Details</h5>
              <p class="card-text">Ends: <?php echo date_format($end_time, 'j M H:i') . $time_remaining ?></p>
              <p class="lead">Current bid: £<?php echo number_format($current_price, 2) ?></p>
              <!-- Bidding form -->
              <form method="POST" action="place_bid.php">
                  <div class="input-group mb-3">
                      <div class="input-group-prepend">
                          <span class="input-group-text">£</span>
                      </div>
                      <input type="number" class="form-control" id="bid" name="bid">
                  </div>
                  <input type="hidden" name="auction_id" value="<?php echo $auction_id ?>">
                  <input type="hidden" name="current_price" value="<?php echo $current_price ?>">
                  <button type="submit" class="btn btn-primary">Place bid</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div> <!-- End of right col with bidding info -->
    <?php endif; ?>
  </div> <!-- End of row with item description and bidding info -->
</div> <!-- End of container -->

<?php include_once("footer.php") ?>




  <script>
    // JavaScript functions: addToWatchlist and removeFromWatchlist.

    function addToWatchlist(button) {

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
          var objT = obj.trim();

          console.log(objT);

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