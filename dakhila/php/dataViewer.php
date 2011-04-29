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

$template = new HTML_Template_ITX("../templates");

$students = GetAllData();

$command=$_GET["command"];

switch ($command) {
	case "Family":
		$familyId=$_GET["familyId"];
		if ($familyId =="") $familyId="47";
		
		foreach (Family::$objArray as $family) {
			if ($family->id == $familyId) {
				DisplayFamilyV2($template, $family, $students);
				break;
			}
		}
		$html= $template->get();
		break;
		
	case "Registration":
		$familyId=$_GET["familyId"];
		if ($familyId =="") $familyId="47";
		
		foreach (Family::$objArray as $family) {
			if ($family->id == $familyId) {
				DisplayRegistration($template, $family, $students);
				break;
			}
		}
		$html= $template->get();
		break;
		
		
		case "MedicalForm":
		$studentId=$_GET["studentId"];
		if ($studentId =="") $studentId="1446";
		
		foreach ($students as $id => $student) {
			if ($student->id == $studentId) {
				DisplayStudentMedicalInformationV2($template, $student);
			}
		}
		$html= $template->get();
		break;
	default:
		$html = "<p>Please specify a valid command for the data you want to see";
}

print $html;

?>