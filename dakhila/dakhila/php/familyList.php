<?php
function GetEnrolledStudentCountForFamily($id, $students) {
	$count=0;
	
	foreach ($students as $id2 => $student) {
		if($student->IsEnrolled && $student->family->id == $id) {
			$count++;
		}
	}
return $count;
}


$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
require_once "HTML/Template/ITX.php";
require_once "$rootDir/libVidyalaya/db.inc";
require_once "$rootDir/libVidyalaya/vidyalaya.inc";

VidSession::sessionAuthenticate();

$students = GetAllData();

// Template stuff
$template = new HTML_Template_ITX("../templates");
$template->loadTemplatefile("FamilyList.tpl", true, true);

$templateName="FAMILYITEM";
$template->setCurrentBlock($templateName);


$familyOne =0; 
$familyTwo = 0;
$familyMoreThanTwo = 0;
foreach (Family::$objArray as $family) {
 	$count = GetEnrolledStudentCountForFamily($family->id, $students);
 	if ($count > 0) {
	 	// update template
		$template->setVariable("COUNT", $count);
		$familyLink = sprintf("<a href=\"/dakhila/php/dataViewer.php?command=Family&familyId=%s\">%s</a>", $family->id, $family->id);
      	$template->setVariable("ID", $familyLink);
	    //$template->setVariable("ID", $family->id);
	    $template->setVariable("MOTHER", $family->mother->firstName . " " . $family->mother->lastName);
	    $template->setVariable("FATHER", $family->father->firstName . " " . $family->father->lastName);
	    $template->setVariable("PHONE", $family->phone);
	    
	    // Housekeeping
	    $totalStudents += $count;
	    if ($count == 1) $familyOne++;
	    if ($count == 2) $familyTwo++;
	    if ($count > 2) $familyMoreThanTwo++;
 	}
 	$template->parseCurrentBlock();
}

$totalRevenue = $familyOne * 350 + $familyTwo * 400 + $familyMoreThanTwo * 400;

setlocale(LC_MONETARY, 'en_US');

$templateName="FAMILYSUMMARY";
$template->setCurrentBlock($templateName);
$template->setVariable("STUDENTS", $totalStudents);
$template->setVariable("FAMILYONE", $familyOne);
$template->setVariable("FAMILYTWO", $familyTwo);
$template->setVariable("FAMILYTWOMORE", $familyMoreThanTwo);
$template->setVariable("TOTALFAMILY", $familyOne+$familyTwo+$familyMoreThanTwo);
$template->setVariable("REVENUE", money_format('%n', $totalRevenue));
$template->setVariable("AVERAGE", money_format('%n', $totalRevenue/$totalStudents));
$template->setVariable("AVGFAMILY", money_format('%n', $totalRevenue/($familyOne+$familyTwo+$familyMoreThanTwo)));


$template->parseCurrentBlock();

//Output the web page
$template->show();


?>