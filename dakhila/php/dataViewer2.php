<?php
$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
require_once "HTML/Template/ITX.php";
require_once "$rootDir/libVidyalaya/db.inc";
require_once "$rootDir/libVidyalaya/vidyalaya.inc";
require_once "$rootDir/libVidyalaya/HtmlFactory.inc";
require_once "$rootDir/libVidyalaya/reports.inc";
require_once "$rootDir/libVidyalaya/dataviewer.inc";


// login is special since it is not authenticated
$command=isset($_GET["command"]) ? $_GET["command"] : "login";
if (empty($command)) $command="login";
$dataviewer = new DataViewer("../templates");
switch ($command) {
case "login":
  $dataviewer->login();
  break;
default:
 $dataviewer->DoIt($command);

}
?>
