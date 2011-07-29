<?php
require "../authentication.inc";
require_once "HTML/Template/ITX.php";
require_once "$rootDir/libVidyalaya/db.inc";
require_once "$rootDir/libVidyalaya/vidyalaya.inc";
require_once "$rootDir/libVidyalaya/HtmlFactory.inc";

if (!$connection = @ mysql_connect($hostname, $username, $password))
  die("Cannot connect");
if (!mysql_selectdb($databasename, $connection))
  showerror();

session_start();
if (!isset($_SESSION["count"])) {
	$_SESSION["count"]=0;
	$_SESSION["start"]= time();
}
// Connect to an authenticated session or relocate to logout.php
sessionAuthenticate();


$students = GetAllData();
$studentId=$_GET["studentId"];
if ($studentId =="") $studentId="1446";

//print "I was here, $studentId\n";
foreach ($students as $id => $student) {
	$template = new HTML_Template_ITX("../templates");
	if ($student->id == $studentId) {
		
		$html = DisplayStudentMedicalInformation($template,  $student);
		print $html;
		break;
	}
}




?>