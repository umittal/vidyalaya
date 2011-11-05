<?php

$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";

function authenticateUserOld($connection, $username, $password)
{
  // Test the username and password parameters
  if (!isset($username) || !isset($password))
    return false;

  // Create a digest of the password collected from
  // the challenge
  $password_digest = md5(trim($password));

  // Formulate the SQL find the user
  $query = "SELECT password FROM users WHERE email = '{$username}'
            AND password = '{$password_digest}'";

  // Execute the query
  if (!$result = @ mysql_query ($query, $connection))
    showerror();

  // exactly one row? then we have found the user
  if (mysql_num_rows($result) != 1)
    return false;
  else
    return true;
}

// Connects to a session and checks that the user has
// authenticated and that the remote IP address matches
// the address used to create the session.
function sessionAuthenticateOld()
{
  // Check if the user hasn't logged in
  if (!isset($_SESSION["loginUsername"]))
  {
    // The request does not identify a session
    $_SESSION["message"] = "You are not authorized to access the URL 
                            {$_SERVER["REQUEST_URI"]}";

    header("Location: /dakhila/logout.php");
    exit;
  }

  // Check if the request is from a different IP address to previously
  if (!isset($_SESSION["loginIP"]) || 
     ($_SESSION["loginIP"] != $_SERVER["REMOTE_ADDR"]))
  {
    // The request did not originate from the machine
    // that was used to create the session.
    // THIS IS POSSIBLY A SESSION HIJACK ATTEMPT

    $_SESSION["message"] = "You are not authorized to access the URL 
                            {$_SERVER["REQUEST_URI"]} from the address 
                            {$_SERVER["REMOTE_ADDR"]}";

    header("Location: logout.php");
    exit;
  }
}

?>
