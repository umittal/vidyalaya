<?php
$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
require_once "$rootDir/libVidyalaya/dataviewer.inc";
require_once "$rootDir/libVidyalaya/userdata.inc";

$command=isset($_POST["command"]) ? $_POST["command"] : "";
//if (empty($command)) {
//  header("HTTP/1.1 301 Moved Permanently");
//  header("Location:  /dakhila/php/dataViewer2.php");
//  exit();
//}

$templateDir = "./templates";

$userdata = new UserData($templateDir);
$dataviewer = new DataViewer($templateDir);

if (empty($command)) {
  $dataviewer->login();
}

//print "I am  " __FUNCTION__." in ".__FILE__." at ".__LINE__."\n";
//print "I am at comamnd: $command  in ".__FILE__." at ".__LINE__."\n";
switch ($command) {
case "deleteobj":
case "FamilyFeeCheck":
  $dataviewer->DoIt($command);
default:
  print "unknown comamnd, please go back";
}


?>
