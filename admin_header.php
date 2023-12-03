<?php
  session_start();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap and FontAwesome CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/font-awesome-4.7.0.min.css">

  <!-- Custom CSS file -->
  <link rel="stylesheet" href="css/custom.css">
  
  <title>Group 28 Auction Website - Admin Panel</title>
</head>

<body>

  <!-- Navbars -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light mx-2">
    <a class="navbar-brand" href="dashboard.php">Group 28 Auction Website - Admin Panel</a>
  </nav>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark d-flex justify-content-between">
    <div>
      <ul class="navbar-nav">
        <!-- Navigation for Admin Users -->
        <li class="nav-item mx-1">
          <a class="nav-link" href="admin_users.php">Users</a>
        </li>
        <!-- Navigation for Admin Auctions -->
        <li class="nav-item mx-1">
          <a class="nav-link" href="admin_auctions.php">Auctions</a>
        </li>
      </ul>
    </div>

    <!-- Right aligned item -->
    <div>
      <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) { ?>
        <a href='logout.php' class='nav-link d-inline'>Logout</a>
      <?php } ?>
    </div>
  </nav>

</body>
</html>
