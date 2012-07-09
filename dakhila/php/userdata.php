<?php
$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
require_once "HTML/Template/ITX.php";
require_once "$rootDir/libVidyalaya/db.inc";
require_once "$rootDir/libVidyalaya/vidyalaya.inc";
require_once "$rootDir/libVidyalaya/HtmlFactory.inc";
require_once "$rootDir/libVidyalaya/reports.inc";
require_once "$rootDir/libVidyalaya/userdata.inc";

VidSession::startSession(); //let us start a session, not same as authenticating user
$command=isset($_GET["command"]) ? $_GET["command"] : "register";
if (empty($command)) $command="register";
$userdata = new UserData("../templates");
switch ($command) {
case "register":// register is special since it is not authenticated
  $userdata->register();
  break;
default:
  $userdata->$command();
}

?>
