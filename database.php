<?php
require_once('db_credentials.php');

function db_connect()
{
  $connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
  confirm_db_connect();
  return $connection;
}

function db_disconnect($connection)
{
  if (isset($connection)) {
    mysqli_close($connection);
  }
}

function confirm_db_connect()
{
  if (mysqli_connect_errno()) {
    $msg = "Database connection failed: ";
    $msg .= mysqli_connect_error();
    $msg .= " (" . mysqli_connect_errno() . ")";
    exit($msg);
  }
}

function confirm_result_set($result_set)
{
  if (!$result_set) {
    exit("Database query failed.");
  }
}

// Add this function to database.php to perform a query
function db_query($connection, $query)
{
  $result = mysqli_query($connection, $query);
  confirm_result_set($result);
  return $result;
}

// Function to fetch the single row of result
function db_fetch_single($result)
{
  return mysqli_fetch_assoc($result);
}

// Function to get the number of rows returned
function db_num_rows($result)
{
  return mysqli_num_rows($result);
}

function db_free_result($result)
{
  mysqli_free_result($result);
}

function db_fetch_array($result)
{
  return mysqli_fetch_array($result);
}
