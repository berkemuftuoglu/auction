<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php require("database.php")?>

<div class="container">
<h2 class="my-3">Auctions I've Won</h2>

<?php
  $has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
  $user_id = $_SESSION['user_id'];

  $connection = db_connect();
  $won_auctions = "SELECT item.item_id,
                          auction.auction_title,
                          item.description,
                          bids.price AS your_bid,
                          item.photo,
                          users.user_id
                   FROM auction
                   JOIN bids ON auction.auction_id = bids.auction_id
                   JOIN item ON auction.item_id = item.item_id
                   JOIN users ON auction.user_id = users.user_id
                   WHERE bids.user_id = '$user_id'
                   AND bids.price = (
                    SELECT MAX(price) AS 'your_bid'
                    FROM bids
                    WHERE auction_id = auction.auction_id
                   )
                   AND auction.end_time < NOW();";
  $won_results = db_query($connection, $won_auctions);
  confirm_result_set($won_results);

  $num_results = db_num_rows($won_results); 
  $results_per_page = 10;
  $max_page = ceil($num_results / $results_per_page);

  if ($num_results == 0) {
    echo "You haven't won any auctions";
  };

  db_free_result($won_auctions);
  db_disconnect($connection);

  function checkRated($raterUserId, $ratedUserId, $soldItem) {
    $connection = db_connect();
    
    $stmt = "SELECT COUNT(*) AS num_ratings
             FROM ratings 
             WHERE rater_user_id = '$raterUserId'
             AND rated_user_id = '$ratedUserId'
             AND item_id = '$soldItem'";

    $rating_call = db_query($connection, $stmt);
    confirm_result_set($rating_call);

    $rating_count = db_fetch_single($rating_call)["num_ratings"];
    db_free_result($rating_call);

    return $rating_count;
  }
  
  function rateSeller($raterUserId, $ratedUserId, $ratingValue, $soldItem) {
    $connection = db_connect();
    $existingRatingCount = checkRated($raterUserId, $ratedUserId, $soldItem);

    if ($existingRatingCount == 0) {
        $stmt = "INSERT INTO ratings (rater_user_id, rated_user_id, rating_value, item_id)
                 VALUES ('$raterUserId', '$ratedUserId', '$ratingValue', '$soldItem')";
        $rate_call = db_query($connection, $stmt);
        confirm_result_set($rate_call);
        db_free_result($rate_call);

        $stmt = "UPDATE users
                 SET total_ratings = total_ratings + 1,
                 average_rating = ((average_rating * total_ratings) + '$ratingValue') / total_ratings
                 WHERE user_id = '$ratedUserId'";
        $rated_call = db_query($connection, $stmt);
        confirm_result_set($rated_call);
        db_free_result($rated_call);
    }
    db_disconnect($connection);
} 

function getUserRating($raterUserId, $ratedUserId, $soldItem) {
    $connection = db_connect();
    $stmt = "SELECT rating_value
             FROM ratings
             WHERE rater_user_id = '$raterUserId'
             AND rated_user_id = '$ratedUserId'
             AND item_id = '$soldItem'";
    $rating_call = db_query($connection, $stmt);
    confirm_result_set($rating_call);

    $rating = db_fetch_single($rating_call);

    $userRating = $rating["rating_value"];

    db_free_result($rating_call);
    db_disconnect($connection);
    
    return $userRating;
}
  
  while($row = db_fetch_single($won_results)) {
    $item_photo = $row['photo'];
    $item_id = $row["item_id"];
    $title = $row["auction_title"];
    $description = $row["description"];
    $your_bid = $row["your_bid"];
    $seller_id = $row["user_id"];

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        if (isset($_POST['rate'])) {
            rateSeller($user_id, $seller_id, $_POST['rate'], $item_id);
        }
      }


    
    if (strlen($description) > 250) {
        $desc_shortened = substr($description, 0, 250) . '...';
      } else {
        $desc_shortened = $description;
      }

  echo '
  <li class="list-group-item d-flex justify-content-between">
      <img src=' . $item_photo . ' alt=' . $title . ' class="img-fluid" style="max-width: 120px; max-height: 120px;">
      <div class="p-2 mr-5"><h5><a href="listing.php?item_id=' . $item_id . '">' . $title . '</a></h5>' . $desc_shortened . '</div>
      <div class="text-center text-nowrap">Your Bid Price:<br><span style="font-size: 1.5em">£' . number_format($your_bid, 2) . '</span><br/>' . $num_bids . $bid . '<br/>' . $time_remaining . '</div>
      <div class="text-center text-nowrap">';

      if (checkRated($user_id, $seller_id, $item_id) == 0){
        echo "Rate this Seller!";
        echo('
        <form method="post" action="">
            <select name="rate" id="rate">
                <option value=0>0</option>
                <option value=1>1</option>
                <option value=2>2</option>
                <option value=3>3</option>
                <option value=4>4</option>
                <option value=5>5</option>
            </select>
            <input type="submit" name="submit" value="Submit">
        </form>');
      } else {
        echo ('Your Rating <br><span style="font-size: 1.5em">');
        $seller_rating = getUserRating($user_id, $seller_id, $item_id);
        echo $seller_rating;
      }

    echo '</span></div>   
    </li>';

  }

?>