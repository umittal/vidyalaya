<?php

$libDir="../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";
require_once "HTML/Template/ITX.php";
require_once "$libDir/HtmlFactory.inc";

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

function GetPdfForFamilyV2($family) {
	$dompdf = new DOMPDF();
	$students = Student::AllStudents();

	// Header 
	$template = new HTML_Template_ITX("/var/www/dakhila/templates");
	$template->loadTemplatefile("Layout.tpl", true, true);
	$template->addBlockFile('TOP', 'F_TOP', 'LayoutTop.tpl');
	$template->touchBlock('F_TOP');
	$html = $template->get();

	// Family Detail Form - one per family
	$template = new HTML_Template_ITX("/var/www/dakhila/templates");
	$template->loadTemplatefile("Layout.tpl", true, true);
	$template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
	$template->touchBlock('F_CONTENT');
	$template->setCurrentBlock('HEADER');
	$template->setVariable("HEADER", '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
		width="800" height="80" 
		alt="php5 logo"/></a>');
	$template->parseCurrentBlock();
	$template->setCurrentBlock('FOOTER');
	$template->setVariable("FOOTER", "Copyright (c) 2011 Vidyalya Inc.");
	$template->parseCurrentBlock();

	$template->addBlockFile('RESULT', 'F_RESULT', 'FamilyDetail.tpl');
	$template->touchBlock('F_RESULT');
	DisplayFamilyTemplate($template, $family, $students);
	$html = $html . $template->get();	
	$html = $html . '<DIV style="page-break-after:always"></DIV>';
	
	// Registration Form - one per family
	$template = new HTML_Template_ITX("/var/www/dakhila/templates");
	$template->loadTemplatefile("Layout.tpl", true, true);
	$template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
	$template->touchBlock('F_CONTENT');
	$template->setCurrentBlock('HEADER');
	$template->setVariable("HEADER", '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
		width="800" height="80" 
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
			if ($student->family->id == $family->id && ($student->studentStatus->id == StudentStatus::Active || $student->studentStatus->id == StudentStatus::Waiting)) {
				$html = $html . '<DIV style="page-break-after:always"></DIV>';
				
				$template = new HTML_Template_ITX("/var/www/dakhila/templates");
				$template->loadTemplatefile("Layout.tpl", true, true);
				$template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
				$template->touchBlock('F_CONTENT');
				$template->setCurrentBlock('HEADER');
				$template->setVariable("HEADER", '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
					width="800" height="80" 
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
	$template = new HTML_Template_ITX("/var/www/dakhila/templates");
	$template->loadTemplatefile("Layout.tpl", true, true);
	$template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
	$template->touchBlock('F_BOTTOM');
	$html = $html . $template->get();

	// Convert to PDF
	$html = str_replace('&nbsp;', '<span style="color:#fff;">x</span>',$html);
	$dompdf->load_html($html);
	$dompdf->render();	
	return $dompdf->output();
}


function GetPdfForMedical($student) {
	$dompdf = new DOMPDF();
	$template = new HTML_Template_ITX("/var/www/dakhila/templates");
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
	$printDir = "/home/umesh/package2011";
	$pdf = GetPdfForFamilyV2($family);
	$fileName = $printDir . "/Family-" . $family->id . ".pdf";
	file_put_contents("$fileName", $pdf);
	echo "printed $fileName\n";
}

function printAllFamilies($students) {
	foreach (Family::$objArray as $family) {
		$count = GetEnrolledStudentCountForFamily($family->id, $students);
		if ($count > 0 || $family->category->id == FamilyCategory::Waiting) 		printOneFamily($family);
	}
}


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
