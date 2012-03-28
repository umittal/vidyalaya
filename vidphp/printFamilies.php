<?php

$libDir="../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";
require_once "HTML/Template/ITX.php";
require_once "$libDir/HtmlFactory.inc";
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

function GetPdfForFamilyV2($family) {
	$students = Student::AllStudents();

	// Header 
	$template = new HTML_Template_ITX("../dakhila/templates");
	$template->loadTemplatefile("Layout.tpl", true, true);
	$template->addBlockFile('TOP', 'F_TOP', 'LayoutTop.tpl');
	$template->touchBlock('F_TOP');
	$html = $template->get();

	// Family Detail Form - one per family
	$template = new HTML_Template_ITX("../dakhila/templates");
	$template->loadTemplatefile("Layout.tpl", true, true);
	$template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
	$template->touchBlock('F_CONTENT');
	$template->setCurrentBlock('HEADER');
	$template->setVariable("HEADER", '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
		width="700" height="70" 
		alt="php5 logo"/></a>');
	$template->parseCurrentBlock();
	$template->setCurrentBlock('FOOTER');
	$template->setVariable("FOOTER", "Copyright (c) 2011 Vidyalya Inc.");
	$template->parseCurrentBlock();

	$template->addBlockFile('RESULT', 'F_RESULT', 'FamilyDetail.tpl');
	$template->touchBlock('F_RESULT');
	DisplayFamilyTemplateV3($template, $family);
	$html = $html . $template->get();	
	$html = $html . '<DIV style="page-break-after:always"></DIV>';
	
	// Registration Form - one per family
	$template = new HTML_Template_ITX("../dakhila/templates");
	$template->loadTemplatefile("Layout.tpl", true, true);
	$template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
	$template->touchBlock('F_CONTENT');
	$template->setCurrentBlock('HEADER');
	$template->setVariable("HEADER", '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
		width="700" height="70" 
		alt="php5 logo"/></a>');
	$template->parseCurrentBlock();
	$template->setCurrentBlock('FOOTER');
	$template->setVariable("FOOTER", "Copyright (c) 2011 Vidyalya Inc.");
	$template->parseCurrentBlock();

	$template->addBlockFile('RESULT', 'F_RESULT', 'Registration.tpl');
	$template->touchBlock('F_RESULT');
	DisplayRegistrationTemplate($template, $family, $students);
	$html = $html . $template->get();	
	
	// Medical Information Form - one per student
	foreach ($students as $id => $student) {
			if ($student->family->id == $family->id 
			    //			    && ($student->studentStatus->id == StudentStatus::Active || $student->studentStatus->id == StudentStatus::Waiting)

) {
				$html = $html . '<DIV style="page-break-after:always"></DIV>';
				
				$template = new HTML_Template_ITX("../dakhila/templates");
				$template->loadTemplatefile("Layout.tpl", true, true);
				$template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
				$template->touchBlock('F_CONTENT');
				$template->setCurrentBlock('HEADER');
				$template->setVariable("HEADER", '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
					width="700" height="70" 
					alt="php5 logo"/></a>');
				$template->parseCurrentBlock();
				$template->setCurrentBlock('FOOTER');
				$template->setVariable("FOOTER", "Copyright (c) 2011 Vidyalya Inc.");
				$template->parseCurrentBlock();
	
				$template->addBlockFile('RESULT', 'F_RESULT', 'MedicalInformation.tpl');
				$template->touchBlock('F_RESULT');
				DisplayStudentMedicatlInformationTemplate($template, $student);	
				$html = $html . $template->get();			
			}
	}
	
	// Footer
	$template = new HTML_Template_ITX("../dakhila/templates");
	$template->loadTemplatefile("Layout.tpl", true, true);
	$template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
	$template->touchBlock('F_BOTTOM');
	$html = $html . $template->get();

	return $html;
}


function GetPdfForMedical($student) {
	$dompdf = new DOMPDF();
	$template = new HTML_Template_ITX("../dakhila/templates");
	DisplayStudentMedicalInformationV2($template, $student);
	$html = $template->get();
	$html = str_replace('&nbsp;', '<span style="color:#fff;">x</span>',$html);
	$dompdf->load_html($html);
	file_put_contents("/tmp/whatiswrong.html", $html);
	$dompdf->render();	
	return $dompdf->output();
}


function printMedicalForms($students) {
	$printDir = "/tmp/abcd";

		foreach ($students as $id => $student) {
		if ($student->IsEnrolled) {
			
			$fileName = $printDir . "/" . $student->id . ".pdf";
			$pdf = GetPdfForMedical($student);
			file_put_contents("$fileName", $pdf);
			echo "printed $fileName\n";
		}
	}
}

function printOneFamily($family) {
  print "** Trying to print for $family->id ****\n";
	$printDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission";
	$html = GetPdfForFamilyV2($family);
	// Convert to PDF
	$html = str_replace('&nbsp;', '<span style="color:#fff;">x</span>',$html);

	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->render();	
	$pdf= $dompdf->output();

	$fileName = $printDir . "/pdf/Family-" . $family->id . ".pdf";
	file_put_contents("$fileName", $pdf);
	echo "printed $fileName\n";
	$fileName = $printDir . "/html/Family-" . $family->id . ".html";
	file_put_contents("$fileName", $html);
	echo "printed $fileName\n";
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
  foreach (FamilyTracker::GetAll()  as $tracker) {
    printOneFamily(Family::GetItemById($tracker->family));
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
printOneFamily (Family::GetItemById($entry));

echo "\n";
echo "Thank you for using my print program...\n";
		exit;

/*
while (1) {
	echo "Enter the id of family you want to print : ";
	$handle = fopen ("php://stdin","r");
	$line = trim(fgets($handle));
	if ($line == "q") break;
	if (intval($line) ==0 ) {
		print "found: $line, expecting an integer\n\n";
		continue;
	}
	printOneFamily (Family::GetItemById($line));
}
*/


//$students = GetAllData();
//printAllFamilies($students);

//printMedicalForms($students);

?>
