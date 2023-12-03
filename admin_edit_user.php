<?php require("utilities.php"); ?>
<?php require("database.php"); ?>
<?php include_once("admin_header.php")?>

<?php
// Check if user_id is set in GET parameter
if (isset($_GET['user_id'])) {
    $connection = db_connect();

    $user_id = $_GET['user_id'];

    // Query to fetch user details
    $query = "SELECT user_id, email, first_name, last_name FROM Users WHERE user_id = '$user_id'";
    $result = db_query($connection, $query);
    $user = db_fetch_single($result);

    if (!$user) {
        echo "User not found.";
        exit;
    }
} else {
    echo "No user ID provided.";
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
</head>
<body>

  <?php include_once("admin_header.php"); ?>

  <div class="container mt-4">
    <h1>Edit User</h1>
    <form action="admin_user_result.php" method="post">
      <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">

      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" class="form-control" name="email" id="email" value="<?php echo $user['email']; ?>" required>
      </div>

      <div class="form-group">
        <label for="first_name">First Name:</label>
        <input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo $user['first_name']; ?>" required>
      </div>

      <div class="form-group">
        <label for="last_name">Last Name:</label>
        <input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo $user['last_name']; ?>" required>
      </div>

      <button type="submit" name="action" value="update" class="btn btn-primary">Update User</button>
      <button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete User</button>
    </form>
  </div>

</body>
</html>

<?php
  db_disconnect($connection);
?>
