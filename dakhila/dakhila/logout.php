<?php
  require_once "HTML/Template/ITX.php";
  session_start();

  $message = "";

  // An authenticated user has logged out -- be polite and thank them for
  // using your application.
  if (isset($_SESSION["loginUsername"]))
    $message .= "Thanks {$_SESSION["loginUsername"]} for
                 using the Application.";

  // Some script, possibly the setup script, may have set up a 
  // logout message
  if (isset($_SESSION["message"]))
  {
    $message .= $_SESSION["message"];
    unset($_SESSION["message"]);
  }

  // Destroy the session.
  session_destroy();

  // Display the page (including the message)
  $template = new HTML_Template_ITX("./templates");
  $template->loadTemplatefile("logout.tpl", true, true);
  $template->setVariable("MESSAGE", $message);
  $template->parseCurrentBlock();
  $template->show();
?>
