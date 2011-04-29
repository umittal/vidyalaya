<?php
require 'authentication.inc';
require 'libVidyalaya/db.inc';

if (!$connection = @ mysql_connect("vidyalaya.db.4718809.hostedresource.com", "vidyalaya", "Praveen38"))
  die("Cannot connect, what happened");

// Clean the data collected in the <form>
$loginUsername = mysqlclean($_POST, "loginUsername", 50, $connection);
$loginPassword = mysqlclean($_POST, "loginPassword", 10, $connection);

if (!mysql_selectdb("vidyalaya", $connection))
  showerror();

session_start();

// Authenticate the user
if (authenticateUser($connection, $loginUsername, $loginPassword))
{
  // Register the loginUsername
  $_SESSION["loginUsername"] = $loginUsername;

  // Register the IP address that started this session
  $_SESSION["loginIP"] = $_SERVER["REMOTE_ADDR"];

  // Relocate back to the first page of the application
  header("Location: home.php");
  exit;
}
else
{
  // The authentication failed: setup a logout message
  $_SESSION["message"] = 
    "Could not connect to the application as '{$loginUsername}'";

  // Relocate to the logout page
  header("Location: logout.php");
  exit;
}
?>
