 <?php
  extract($_POST);
  require("database.php");

  if (!isset($_POST['functionname'])) {
    return;
  }
  // Extract arguments from the POST variables:
  $connection = db_connect();

  if ($_POST['functionname'] == "add_to_watchlist") {
    
    $user_id = $_POST['user_id'];
    $item_id = $_POST['item_id'];
    $sql = "INSERT INTO watchlist (user_id, item_id) VALUES ('$user_id', '$item_id')";
    $removeResult = mysqli_query($connection, $sql);

    if (mysqli_affected_rows($connection) > 0) {
      $res = "success";
    } else {
      $res = "failed";
    }

    $res = "success";
  } else if ($_POST['functionname'] == "remove_from_watchlist") {
    $user_id = $_POST['user_id'];
    $item_id = $_POST['item_id'];
    $sql = "DELETE FROM watchlist WHERE user_id = '$user_id' AND item_id = '$item_id'";
    $removeResult = mysqli_query($connection, $sql);

    if (mysqli_affected_rows($connection) > 0) {
      $res = "success";
    } else {
      $res = "failed";
    }
  }

  // Note: Echoing from this PHP function will return the value as a string.
  // If multiple echo's in this file exist, they will concatenate together,
  // so be careful. You can also return JSON objects (in string form) using
  // echo json_encode($res).
  echo $res;

  ?>