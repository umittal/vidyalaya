<?php
require "authentication.inc";
require_once "HTML/Template/ITX.php";

session_start();

// Connect to a authenticated session or relocate to logout.php
sessionAuthenticate();

$message = "";

// Check if there is a password error message
if (isset($_SESSION["passwordMessage"]))
{
  $message = $_SESSION["passwordMessage"];
  unset($_SESSION["passwordMessage"]);
}

// Display the page (including the message)
$template = new HTML_Template_ITX("./templates");
$template->loadTemplatefile("password.tpl", true, true);
$template->setVariable("USERNAME", $_SESSION["loginUsername"]);
$template->setVariable("MESSAGE", $message);
$template->parseCurrentBlock();
$template->show();
?>
