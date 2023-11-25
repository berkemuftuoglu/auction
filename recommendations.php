<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php require("database.php")?>

<div class="container">

<h2 class="my-3">Recommendations for you</h2>

<?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  // TODO: Check user's credentials (cookie/session).
  $has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
  $user_id = $_SESSION['user_id'];

  // TODO: Perform a query to pull up auctions they might be interested in.
  $connection = db_connect();
  # the query pulls items from buyers' watchlists who are watching the same items as the current user
  $recommendations = "SELECT DISTINCT w3.item_id, 
                                      auction.auction_title , 
                                      item.description,
                                      MAX(bids.price) AS highest_bid,
                                      COUNT(bids.auction_id) AS num_bids,
                                      auction.end_time, 
                                      item.category                
                      FROM watchlist w1
                      INNER JOIN watchlist w2
                      ON w1.item_id = w2.item_id
                      INNER JOIN watchlist w3
                      ON w2.user_id = w3.user_id
                      RIGHT JOIN auction
                      ON w3.item_id = auction.item_id
                      RIGHT JOIN item 
                      ON w3.item_id = item.item_id
                      RIGHT JOIN bids
                      ON auction.auction_id = bids.auction_id
                      WHERE w1.user_id = '$user_id'
                      AND w2.user_id != '$user_id'
                      AND w1.item_id != w3.item_id
                      GROUP BY w3.item_id,
                               auction.auction_title,
                               auction.end_time";
  $recommended_results = db_query($connection, $recommendations);
  confirm_result_set($recommended_results);

  $num_results = db_num_rows($recommended_results); 
  $results_per_page = 10;
  $max_page = ceil($num_results / $results_per_page);

  if ($num_results == 0) {
    echo "No current recommendations";
  };

  // TODO: Loop through results and print them out as list items.
  
  while($row = db_fetch_single($recommended_results)) {
    $item_id = $row["item_id"];
    $title = $row["auction_title"];
    $description = $row["description"];
    $current_price = $row["highest_bid"];
    $num_bids = $row["num_bids"];
    $end_date = new DateTime($row["end_time"]);
    print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
  }
  
  db_free_result($recommended_results);
  db_disconnect($connection);

  
?>