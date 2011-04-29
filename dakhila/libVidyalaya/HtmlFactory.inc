<?php

function GenderMap($gender) {
	switch ($gender) {
		case 1:
			return "Girl";
		case 2:
			return "Boy";
		case 3:
			return "unknown";
		default:
			return "funny";
	}
}

function YesNoMap($bool) {
	switch ($bool) {
		case 1: return "Yes";
		default: return "No";
	}
}

function Layout(&$template) {
	$template->loadTemplatefile("Layout.tpl", true, true);

	$template->addBlockFile('TOP', 'F_TOP', 'LayoutTop.tpl');
	$template->touchBlock('F_TOP');

	$template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
	$template->touchBlock('F_CONTENT');
	
	$template->setCurrentBlock('HEADER');
	$template->setVariable("HEADER", '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
		width="800" height="80" 
		alt="php5 logo"/></a>');
	$template->parseCurrentBlock();
	
	$template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
	$template->touchBlock('F_BOTTOM');
	
	$template->setCurrentBlock('FOOTER');
	$template->setVariable("FOOTER", "Copyright (c) 2011 Vidyalya Inc.");
	$template->parseCurrentBlock();

}

// *****  Family Details
function DisplayFamilyTemplate (&$template, $family, $students) {

	$templateName="PARENTS";
	$template->setCurrentBlock($templateName);

	$template->setVariable("MF", "Father");
	$template->setVariable("NAME", $family->father->fullName());
	$template->setVariable("EMAIL", $family->father->email);
	$template->setVariable("CELL", $family->father->workPhone);
	$template->setVariable("CELL", $family->father->cellPhone);
	$template->setVariable("ISCONTACTABLE", YesNoMap($family->father->IsContactable));
	$template->parseCurrentBlock();

	$template->setVariable("MF", "Mother");
	$template->setVariable("EMAIL", $family->mother->email);
	$template->setVariable("CELL", $family->mother->workPhone);
	$template->setVariable("CELL", $family->mother->cellPhone);
	$template->setVariable("NAME", $family->mother->fullName());
	$template->setVariable("ISCONTACTABLE", YesNoMap($family->mother->IsContactable));
	$template->parseCurrentBlock();

	foreach ($students as $id => $student) {

		if ($student->family->id == $family->id) {
			DisplayChildren($template, $student);
		}
	}

	$templateName="HOME";
	$template->setCurrentBlock($templateName);
	$line2 = $family->address->addr2;
	if (! empty($line2)) {
		$template->setVariable("LINE2", ", ". $line2);
	}
	$template->setVariable("ID", $family->id);
	$template->setVariable("LINE1", $family->address->addr1);
	$template->setVariable("CITY", $family->address->city);
	$template->setVariable("STATE", $family->address->state);
	$template->setVariable("ZIP", $family->address->zipcode);
	$template->setVariable("HOMEPHONE", $family->phone);
	$category = $family->category->name;
	if ($family->category->id == 5) $category = $category . ", Date: " . $family->priority_date;
	$template->setVariable("CATEGORY", $category );
	$template->parseCurrentBlock();


}


function DisplayChildren(&$template, $student) {
	$templateName="CHILDREN";
	$template->setCurrentBlock($templateName);
	$template->setVariable("ID", $student->id);
	$template->setVariable("NAME", $student->fullName());
	$template->setVariable("EMAIL", $student->email);
	$template->setVariable("GENDER", GenderMap($student->gender));
	$dob = empty($student->dateOfBirth) ? "Unknown" : date("Y-m-d", strtotime($student->dateOfBirth));
	$template->setVariable("DOB",$dob);
	$template->setVariable("ENROLLED", YesNoMap($student->IsEnrolled));
	$template->parseCurrentBlock();


	$templateName="REGISTRATION";
	$template->setCurrentBlock($templateName);
	$template->setVariable("FIRSTNAME", $student->firstName);

	if ($student->studentStatus->id != StudentStatus::Inactive) {
		$reg = $student->registration;
		if (!empty($reg->language)) {
			$class = $reg->language->symbol;
			$classLink=sprintf("<a href=\"/dakhila/php/classDetails.php?className=%s\">%s</a>", $class, $class);
			$template->setVariable("LANGUAGE", $classLink);
			$template->setVariable("LROOM", $reg->language->room);
		} else {
			$template->setVariable("LANGUAGE", "Unknown");
		}

		if (!empty($reg->culture)) {
			$class = $reg->culture->description;
			$classLink=sprintf("<a href=\"/dakhila/php/classDetails.php?className=%s\">%s</a>", $class, $class);
			$template->setVariable("CULTURE", $classLink);
			$template->setVariable("CROOM", $reg->culture->room);
		} else {
			$template->setVariable("CULTURE", "Unknown");
		}


		if ($student->studentStatus->id == StudentStatus::Active) {
			$template->setVariable("LTEACHER", $reg->language->teachers);
			$template->setVariable("CTEACHER", $reg->culture->teachers);
		} else {
			$template->setVariable("LTEACHER", "Waiting List");
			$template->setVariable("CTEACHER", "Waiting List");
		}
	} else {
		$template->setVariable("LTEACHER", "Not Enrolled");
	}
	$template->parseCurrentBlock();


}

function DisplayFamilyV2(&$template, $family, $students) {
	Layout($template);
	$template->addBlockFile('RESULT', 'F_RESULT', 'FamilyDetail.tpl');
	$template->touchBlock('F_RESULT');
	DisplayFamilyTemplate($template, $family, $students);
}


// ******** Registration Form
function DisplayRegistrationTemplate (&$template, $family, $students) {

	$templateName="HOME";
	$template->setCurrentBlock($templateName);
	$template->setVariable("ID", $family->id);
	$template->setVariable("ADDRESS", $family->address->OneLineAddress());
	$template->parseCurrentBlock();

	$templateName="PARENTS";
	$template->setCurrentBlock($templateName);
	$template->setVariable("FATHER", $family->father->fullName());
	$template->setVariable("MOTHER", $family->mother->fullName());
	$template->parseCurrentBlock();

	$children = $family->Children();
	foreach ($children as $student) {
		if ($student->studentStatus->id != StudentStatus::Inactive)	DisplayRegistrationChildren($template, $student);
	}

	$templateName="SIGNATURE";
	$template->setCurrentBlock($templateName);
	$template->setVariable("PARENTSNAME", $family->parentsName());
	$template->parseCurrentBlock();

}


function DisplayRegistrationChildren(&$template, $student) {
	$templateName="STUDENTS";
	$template->setCurrentBlock($templateName);
	$template->setVariable("ID", $student->id);
	$template->setVariable("NAME", $student->fullName());

	$template->setVariable("LANGUAGE", $student->LanguageInterest());
	$age = $student->AgeAt(Calendar::RegistrationSession);
       if ($age == "Unknown") {
	 $template->setVariable("AGE", "Unknown");
       } else {
	 $template->setVariable("AGE", intval($age));
       }
	$template->setVariable("GRADE", $student->GradeAt(Calendar::RegistrationSession));
	
	
	$template->parseCurrentBlock();
}

function DisplayRegistration(&$template, $family, $students) {
	Layout($template);
	$template->addBlockFile('RESULT', 'F_RESULT', 'Registration.tpl');
	$template->touchBlock('F_RESULT');
	DisplayRegistrationTemplate($template, $family, $students);
}


// Medical Information Form
function DisplayStudentMedicatlInformationTemplate($template, $student) {
	$templateName="STUDENT";
	$template->setCurrentBlock($templateName);

	$template->setVariable("ID", $student->id);
	$template->setVariable("FIRST", $student->firstName);
	$template->setVariable("LAST", $student->lastName);
	$template->parseCurrentBlock();
	
	$templateName="PARENTS";
	$template->setCurrentBlock($templateName);
	$template->setVariable("PARENTSNAME", $student->parentsName());
	$template->parseCurrentBlock();
}


function DisplayStudentMedicalInformationV2(&$template, $student) {
	Layout($template);
	$template->addBlockFile('RESULT', 'F_RESULT', 'MedicalInformation.tpl');
	$template->touchBlock('F_RESULT');
	
	DisplayStudentMedicatlInformationTemplate($template, $student);

}


?>
