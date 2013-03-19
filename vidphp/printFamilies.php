<?php

$libDir="../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";
require_once "HTML/Template/ITX.php";
require_once "$libDir/HtmlFactory.inc";
require_once "$libDir/reports.inc";
require_once "../MPDF53/mpdf.php";

//$dompdfDir = "../dompdf2";
//require_once("$dompdfDir/dompdf_config.inc.php");

function GetEnrolledStudentCountForFamily($id, $students) {
	$count=0;
	
	foreach ($students as $id2 => $student) {
		if($student->IsEnrolled && $student->family->id == $id) {
			$count++;
		}
	}
	return $count;
}

function GetCommonTemplate ($template) {
	// Header 
	$template->loadTemplatefile("Layout.tpl", true, true);
	$template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');

	$template->addBlockFile('TOP', 'F_TOP', 'LayoutTop2.tpl');
	$template->touchBlock('F_TOP');

	$template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
	$template->touchBlock('F_CONTENT');

	$template->setCurrentBlock('HEADER');
	$template->setVariable("HEADER", '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
		width="700" height="70" 
		alt="php5 logo"/></a>');
	$template->parseCurrentBlock();

	$template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
	$template->touchBlock('F_BOTTOM');

	$template->setCurrentBlock('FOOTER');
	$template->setVariable("FOOTER", "Copyright (c) 2011 Vidyalya Inc.<br />Private and Confidential - For Internal Use only");
	$template->parseCurrentBlock();
}

function GetPdfForRoster($classId) {
  $template = new HTML_Template_ITX("../dakhila/templates");
  GetCommonTemplate( $template);
  print "Trying for class id $classId\n";
  DisplayClassRoster($template, $classId);
  
  $html= $template->get();
  $mpdf=new mPDF();
  $mpdf->WriteHTML($html);
  return $mpdf->Output($pdfFile, "S");
#  return HtmlToPdf( $html);

}



function printRosterClass($class) {
	$printDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/roster/tabular/";
	$pdf = GetPdfForRoster($class->id);
	$fileName = $printDir . $class->short() . ".pdf";
	file_put_contents("$fileName", $pdf);
	echo "printed $fileName\n";
}

function printRosterYear () {
	$classCount = array();
	foreach (Enrollment::GetAllEnrollmentForFacilitySession(1, 2011) as $item) {
		if (empty($classCount[$item->class->id])) $classCount[$item->class->id]=0;
		$classCount[$item->class->id]++;
	}
	
	foreach (AvailableClass::GetAllYear(2011) as $item) {
	  $count = empty($classCount[$item->id]) ? 0 :$classCount[$item->id];
	  if ($count==0) continue;
	  printRosterClass($item);
	}
}

function printAllFamilies() {
  foreach (FamilyTracker::GetAllForYear(Calendar::RegistrationYear())  as $tracker) {
    if ($tracker->currentYear == EnumFamilyTracker::pendingInvitation)
      Reports::RegistrationPacketFamily(Family::GetItemById($tracker->family));
  }
}

function printAllFamiliesOld($students) {
	foreach (Family::$objArray as $family) {
		$count = GetEnrolledStudentCountForFamily($family->id, $students);
		if ($count > 0 || $family->category->id == FamilyCategory::Waiting) 		printOneFamily($family);
	}
}
//printRosterYear (); exit();
//printAllFamilies(); exit();
$entry = GetSingleIntArgument();
print "printing  $entry\n";
Reports::RegistrationPacketFamily(Family::GetItemById($entry));
?>
