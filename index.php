<?php
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
// Start the session or resume the existing one
  header("Location: login.php");
?>