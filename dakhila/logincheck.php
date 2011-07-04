<?php
require 'libVidyalaya/db.inc';

$username = $_POST["loginUsername"];
$password = $_POST["loginPassword"];

// Authenticate the user
if (VidDb::authenticateUser($username, $password)) {
  header("Location: /dakhila/php/dataViewer2.php?command=home"); // Relocate back to the first page of the application
  exit;
} 

  // The authentication failed: setup a logout message
  $_SESSION["message"] = "Could not connect to the application as {" . $username . "}";
  header("Location: /dakhila/logout.php");
  exit;
?>
