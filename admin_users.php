<?php require("utilities.php"); ?>
<?php require("database.php"); ?>
<?php include_once("admin_header.php")?>

<?php
$connection = db_connect();

// Fetch all users
$query = "SELECT user_id, email, first_name, last_name FROM Users";
$result = db_query($connection, $query);
?>

<!doctype html>
<html lang="en">
<head>
</head>
<body>

  <div class="container mt-4">
    <h1>User Management</h1>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Email</th>
          <th>Name</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = db_fetch_single($result)) { ?>
          <tr>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
            <td>
              <a href="admin_edit_user.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-primary">Edit</a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

</body>
</html>

<?php
  db_disconnect($connection);
?>
