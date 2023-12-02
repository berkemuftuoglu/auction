<?php

// FIXME: At the moment, I've allowed these values to be set manually.
// But eventually, with a database, these should be set automatically
// ONLY after the user's login credentials have been verified via a 
// database query.
session_start();
//$_SESSION['logged_in'] = false;
//$_SESSION['account_type'] = 'seller';
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap and FontAwesome CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/font-awsome-4.7.0.min.css">

  <!-- Custom CSS file -->
  <link rel="stylesheet" href="css/custom.css">
  
  <!--CHANGEME!-->
  <title>Group 28 Auction Website</title>
</head>


<body>

  <!-- Navbars -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light mx-2">
    <a class="navbar-brand" href="dashboard.php">Group 28 Auction Website </a><!--CHANGEME!-->
  </nav>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <ul class="navbar-nav align-middle">
      <li class="nav-item mx-1">
        <a class="nav-link" href="browse.php">Browse</a>
      </li>
      <?php
      if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == '1') {
        echo ('
	<li class="nav-item mx-1">
      <a class="nav-link" href="mybids.php">My Bids</a>
    </li>
  <li class="nav-item mx-1">
      <a class="nav-link" href="watchlist.php">Watchlist</a>
    </li>
	<li class="nav-item mx-1">
      <a class="nav-link" href="recommendations.php">Recommended</a>
    </li>
  <li class="nav-item mx-1">
      <a class="nav-link" href="won_auctions.php">My Won Auctions</a>
    </li>');
      }
      if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == '0') {
        echo ('
	<li class="nav-item mx-1">
      <a class="nav-link" href="mylistings.php">My Listings</a>
    </li>
	<li class="nav-item ml-3">
      <a class="nav-link btn border-light" href="create_auction.php">+ Create auction</a>
    </li>');
      }
      ?>
    </ul>

    <ul class="navbar-nav ml-auto">
      <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) { ?>
        <!-- <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="photos/user.png" alt="Profile Avatar" class="avatar-img">
          </a>
          <ul class="dropdown-menu dropdown-menu-right">
           <a class="dropdown-item" href="#">Profile</a>
            <!-- <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="logout.php">Logout</a>

          </ul>
        </li> -->
        <a href='logout.php' class='btn btn-link btn-sm'>Logout</a>
      <?php } else { ?>
    </ul>
    <button type="button" class="btn nav-link" data-toggle="modal" data-target="#loginModal">Login</button>
  <?php } ?>
  </nav>
  <!-- Login modal -->
  <div class="modal fade" id="loginModal">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Login</h4>
        </div>


        <!-- Modal body -->
        <div class="modal-body">
          <form method="POST" action="login_result.php">
            <div class="form-group">
              <label for="email">Email</label>
              <input type="text" class="form-control" id="email" placeholder="Email">
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary form-control">Sign in</button>
          </form>
          <div class="text-center">or <a href="register.php">create an account</a></div>
        </div>

      </div>
    </div>
  </div>
  <!-- End modal -->