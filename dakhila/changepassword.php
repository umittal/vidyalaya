<?php
require "libVidyalaya/db.inc";

VidSession::sessionAuthenticate();

// Clean the data collected from the user
$oldPassword = isset($_POST['oldPassword']) ?  $_POST['oldPassword'] : null;
$newPassword1 = isset($_POST['newPassword1']) ?  $_POST['newPassword1'] : null;
$newPassword2 = isset($_POST['newPassword2']) ?  $_POST['newPassword2'] : null;

$email =  $_SESSION["loginUsername"];
if (strcmp($newPassword1, $newPassword2) != 0 ) {
  $_SESSION["passwordMessage"] =     "new passwords do not match";
} else if (VidDb::authenticateOrdinaryUser($email , $oldPassword)) {
  if (VidDb::updatePassword($email, $newPassword1)){
    $_SESSION["passwordMessage"] = "Password changed for '{$email}'";
  } else {
    $_SESSION["passwordMessage"] = "Password update failed";
  }
}
else {
  $_SESSION["passwordMessage"] = "password not changed for '{$email}' because old password was not verified";
}

// Relocate to the password form
header("Location: password.php");
?>
