<?php
$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
require_once "HTML/Template/ITX.php";
require_once "$rootDir/libVidyalaya/db.inc";
require_once "$rootDir/libVidyalaya/vidyalaya.inc";
require_once "$rootDir/libVidyalaya/HtmlFactory.inc";

VidSession::sessionAuthenticate();

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
		
	case "CourseCatalog":
		DisplayCourseCatalog($template);
		$html= $template->get();
		break;		

	case "AvailableCourse":
		$year=$_GET["year"];
		if ($year =="") $year=Calendar::CurrentYear();
		$facility=$_GET["facility"];
		if ($facility =="") $facility=Facility::PHHS;
		
		DisplayAvailableClass($template, $year, $facility);
		$html= $template->get();
		break;		
		
	case "ClassRoster":
		$year=$_GET["classId"];
		if ($classId =="") $classId=75;
		DisplayClassRoster($classId);
		$html= $template->get();
		break;	
		
	case "RegistrationSummary":
		$sql = <<< SQLREGISTRATIONSUMMAY
		  select previousYear, currentYear, count(*) 
		  from FamilyTracker where year = 1 
		  group by previousYear, currentYear 
		  order by previousYear, currentYear

SQLREGISTRATIONSUMMAY;
		$result = VidDb::query($sql);
		$subTotal = 0; $total = 0; $current = null;
		print "<html><body><table width='400'>\n";
		print "<tr><th>Current Year</th><th>Next Year</th><th>Count</th></tr>\n";
		while ($row = mysql_fetch_array($result)) {
		  $currentYear = $row[0];
		  if ($current == null) $current = $currentYear;
		  if ($current !=  $currentYear) {
		    //show subtotal
			print "<tr><td colspan=2>";
			print "Subtotal </td><td align=right> " . $subTotal;
			print "</td></tr>\n<tr><td>&nbsp;</td></tr>\n";
			$current = $currentYear;
			$subTotal = 0;
		  }
			print "<tr><td>";
			print EnumFamilyTracker::NameFromId($row[0]) . "</td><td> " . EnumFamilyTracker::NameFromId($row[1]) .
			"</td><td align=right> " . $row[2] ;
			print "</td></tr>\n";
			$subTotal += $row[2];
			$total += $row[2];
		}
			print "<tr><td colspan=2>";
			print "Subtotal </td><td align=right> " . $subTotal;
			print "</td></tr>\n<tr><td>&nbsp;</td></tr>\n";
			print "<tr><td colspan=2>";
			print "Total </td><td align=right> " . $total;
			print "</td></tr>\n";
		print "\n</table></body></html>";		
		$html=""; // to avoid error at the bottom.
		break;

	default:
		$html = "<p>Please specify a valid command for the data you want to see";
}

print $html;

?>