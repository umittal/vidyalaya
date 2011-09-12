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
	$template->setVariable("WORK", $family->father->workPhone);
	$template->setVariable("CELL", $family->father->cellPhone);
	$template->setVariable("ISCONTACTABLE", YesNoMap($family->father->IsContactable));
	$template->parseCurrentBlock();

	$template->setVariable("MF", "Mother");
	$template->setVariable("EMAIL", $family->mother->email);
	$template->setVariable("WORK", $family->mother->workPhone);
	$template->setVariable("CELL", $family->mother->cellPhone);
	$template->setVariable("NAME", $family->mother->fullName());
	$template->setVariable("ISCONTACTABLE", YesNoMap($family->mother->IsContactable));
	$template->parseCurrentBlock();

	foreach (Student::GetAll() as $id => $student) {
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
	$template->parseCurrentBlock();


}

function DisplayFamilyTemplateV3 (&$template, $family) {

	$templateName="PARENTS";
	$template->setCurrentBlock($templateName);

	$template->setVariable("MF", "Father");
	$template->setVariable("NAME", $family->father->fullName());
	$template->setVariable("EMAIL", $family->father->email);
	$template->setVariable("WORK", $family->father->workPhone);
	$template->setVariable("CELL", $family->father->cellPhone);
	$template->setVariable("ISCONTACTABLE", YesNoMap($family->father->IsContactable));
	$template->parseCurrentBlock();

	$template->setVariable("MF", "Mother");
	$template->setVariable("EMAIL", $family->mother->email);
	$template->setVariable("WORK", $family->mother->workPhone);
	$template->setVariable("CELL", $family->mother->cellPhone);
	$template->setVariable("NAME", $family->mother->fullName());
	$template->setVariable("ISCONTACTABLE", YesNoMap($family->mother->IsContactable));
	$template->parseCurrentBlock();

	foreach ($family->Children() as  $student) {
	  DisplayChildren($template, $student);
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
	//	$template->setVariable("ENROLLED", YesNoMap($student->IsEnrolled));
	$template->parseCurrentBlock();


	$templateName="REGISTRATION";
	$template->setCurrentBlock($templateName);

	foreach(Enrollment::GetEnrollmentStudent($student->id, 2011) as $item) {
	  if ($item->class->course->department == Department::Culture ) {
	    $template->setVariable("CULTURE", $item->class->short());
	    $template->setVariable("CROOM", $item->class->room->roomNumber);
	    $template->setVariable("CTEACHER", Teachers::TeacherListClassHtml($item->class->id));
	  } else {
	    $template->setVariable("FIRSTNAME", $student->firstName);
	    $template->setVariable("LANGUAGE", $item->class->short());
	    $template->setVariable("LROOM", $item->class->room->roomNumber);
	    $template->setVariable("LTEACHER", Teachers::TeacherListClassHtml($item->class->id));
	  }
	}
	$template->parseCurrentBlock();
}

function DisplayFamilyV2(&$template, $family, $students) {
	
	$template->addBlockFile('RESULT', 'F_RESULT', 'FamilyDetail.tpl');
	$template->touchBlock('F_RESULT');
	DisplayFamilyTemplate($template, $family, $students);
}

// ******** Family - use this
function DisplayFamilyV3(&$template, $family) {
	$template->addBlockFile('RESULT', 'F_RESULT', 'FamilyDetail.tpl');
	$template->touchBlock('F_RESULT');
	DisplayFamilyTemplateV3($template, $family);
}

function GetChangeUrl($class, $student) {
	 $url = $class->url();
	  if (!$class->IsChangeAllowed()) return $url;
	  $name = $student->fullName();
	  $short = $class->short();
	  $change =  <<< BUTTONHERE
	  <button data-dojo-type="dijit.form.Button" type="button">
    <script type="dojo/method" event="onClick" args="evt">
        // Do something:
        changeClass("$name", $class->id, "$class->session", "$short", $student->id);
    </script>
    Change
</button>

BUTTONHERE;
	  return $url . $change;

}

function DisplayStudentTemplate (&$template, $student) {
  $family = $student->family;
	$templateName="FATHER";
	$template->setCurrentBlock($templateName);


	$template->setVariable("NAME", $family->father->fullName());
	$template->setVariable("EMAIL", $family->father->email);
	$template->setVariable("WORK", $family->father->workPhone);
	$template->setVariable("CELL", $family->father->cellPhone);
	$template->setVariable("ISCONTACTABLE", YesNoMap($family->father->IsContactable));
	$template->parseCurrentBlock();


	$templateName="MOTHER";
	$template->setCurrentBlock($templateName);
	$template->setVariable("EMAIL", $family->mother->email);
	$template->setVariable("FAMILYID", $family->id);
	$template->setVariable("WORK", $family->mother->workPhone);
	$template->setVariable("CELL", $family->mother->cellPhone);
	$template->setVariable("NAME", $family->mother->fullName());
	$template->setVariable("ISCONTACTABLE", YesNoMap($family->mother->IsContactable));
	$template->parseCurrentBlock();

	$templateName="STUDENT";
	$template->setCurrentBlock($templateName);
	$line2 = $family->address->addr2;
	if (! empty($line2)) {
		$template->setVariable("LINE2", ", ". $line2);
	}
	$template->setVariable("ID", $student->id);
	$template->setVariable("NAME", $student->fullName());
	$dob = empty($student->dateOfBirth) ? 
	  "Unknown" : 
	  date("Y-m-d", strtotime($student->dateOfBirth));
	$template->setVariable("DOB", $dob);
	$template->setVariable("CELL", $student->cellPhone);
	$template->setVariable("EMAIL", $student->email);
	$template->setVariable("LINE1", $family->address->addr1);
	$template->setVariable("CITY", $family->address->city);
	$template->setVariable("STATE", $family->address->state);
	$template->setVariable("ZIP", $family->address->zipcode);
	$template->setVariable("HOMEPHONE", $family->phone);
	$template->parseCurrentBlock();

	$sessionRows = array(); 
	foreach (Enrollment::GetEnrollmentHistory($student->id) as $item) {
	  $session=$item->class->session;
	  if (!isset($sessionRows[$session])  )
	    $sessionRows[$session]= array();
	  $sessionRows[$session][] = $item;
	}

	foreach ($sessionRows as $item1) {
	  $tata = $item1[0];
	  if ($tata->class->course->department == Department::Kindergarten) {
	    $templateName="KINDERGARTEN";
	    $template->setCurrentBlock($templateName);
	    $template->setVariable("KG", GetChangeUrl($tata->class, $student));
	    $template->setVariable("SESSION", $tata->class->session);
	    if (count($tata) != 1) {
	      print "Error, student detail, KG should have only KG, nothing else\n";
	    }
	  } else {
	    $templateName="ENROLLMENT";
	    $template->setCurrentBlock($templateName);
	    foreach ($item1 as $e) {
	      $template->setVariable("SESSION", $e->class->session);
	      if (Department::IsLanguage($e->class->course->department)) {
		$template->setVariable("LANGUAGE", GetChangeUrl($e->class, $student));
	      } elseif ($e->class->course->department == Department::Culture) {
		$template->setVariable("CULTURE", GetChangeUrl($e->class, $student));
	      } else {
		print "Error, student detail, expect language, culture only\n";
	      }
	    }

	  }
	  $template->parseCurrentBlock();
	}
}

// ******** Student
function DisplayStudent(&$template, $student) {
  if ($student == null) return;
	$template->addBlockFile('RESULT', 'F_RESULT', 'StudentDetail.tpl');
	$template->touchBlock('F_RESULT');
	DisplayStudentTemplate($template, $student);
}


// ******** Registration Form
function DisplayRegistrationTemplate (&$template, $family) {

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
	
	$template->addBlockFile('RESULT', 'F_RESULT', 'Registration.tpl');
	$template->touchBlock('F_RESULT');
	DisplayRegistrationTemplate($template, $family);
}
function DisplayRegistrationV2(&$template, $family) {
	$template->addBlockFile('RESULT', 'F_RESULT', 'Registration.tpl');
	$template->touchBlock('F_RESULT');
	DisplayRegistrationTemplate($template, $family);
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
	
	$template->addBlockFile('RESULT', 'F_RESULT', 'MedicalInformation.tpl');
	$template->touchBlock('F_RESULT');
	
	DisplayStudentMedicatlInformationTemplate($template, $student);

}

function DisplayStudentMedicalInformationV3(&$template, $student) {
	$template->addBlockFile('RESULT', 'F_RESULT', 'MedicalInformation.tpl');
	$template->touchBlock('F_RESULT');
	
	DisplayStudentMedicatlInformationTemplate($template, $student);

}


function DisplayCourseCatalog(&$template) {
	$template->addBlockFile('RESULT', 'F_RESULT', 'CourseCatalog.tpl');
	$template->touchBlock('F_RESULT');
	
	foreach (CourseCatalog::GetAll() as $course) {
		$templateName="COURSE";
		$template->setCurrentBlock($templateName);
		$template->setVariable("ID", $course->id);
		$template->setVariable("DEPARTMENT", Department::NameFromId($course->department));
		$template->setVariable("LEVEL", $course->level);
		$template->setVariable("SHORT", $course->short);
		$template->setVariable("FULL", $course->full);
		
		$template->parseCurrentBlock();
	}

}

// ****************************
// Available Class
function DisplayAvailableClass($template, $year, $facility) {
	$template->addBlockFile('RESULT', 'F_RESULT', 'AvailableClass.tpl');
	$template->touchBlock('F_RESULT');
	
	$classCount = array();
	foreach (Enrollment::GetAllEnrollmentForFacilitySession($facility, $year) as $item) {
		if (empty($classCount[$item->class->id])) $classCount[$item->class->id]=0;
		$classCount[$item->class->id]++;
	}
	
	foreach (AvailableClass::GetAllYear($year) as $item) {
	  $count = empty($classCount[$item->id]) ? 0 :$classCount[$item->id];
	  if ($count==0) continue;
		$templateName="CLASS";
		$template->setCurrentBlock($templateName);

		$classLink=sprintf("<a href=\"/dakhila/php/dataViewer2.php?command=ClassRoster&classId=%s\">%s</a>", $item->id, $item->id);
		$template->setVariable("DEPARTMENT", Department::NameFromId( $item->course->department));
		$template->setVariable("ROOM", $item->room->roomNumber);
		$template->setVariable("ROOMID", $item->room->id);
		$template->setVariable("SHORT", $item->short());
		$template->setVariable("COUNT", $count);

     		$template->setVariable("ID", $classLink);

		$template->setVariable("CAPACITY", $item->room->capacity);
		$template->setVariable("FREE", $item->room->capacity - $count);
		 
		$template->parseCurrentBlock();

		//		$template->setVariable("LEVEL", $item->course->level);
		//$template->setVariable("FULL", $item->course->full);
		//		$template->setVariable("SECTION", $item->section);
		//		$template->setVariable("YEAR", $item->year+2010);
		//		$template->setVariable("START", $item->startTime);
		//		$template->setVariable("END", $item->endTime);
		

	}
}

// ****************************
// Class Roster

function DisplayClassRoster($template, $classId) {
  $template->addBlockFile('RESULT', 'F_RESULT', 'ClassRoster.tpl');
  $template->touchBlock('F_RESULT');

  $class = AvailableClass::GetItemById($classId);
  $template->setCurrentBlock("ROSTERHEAD");
  $template->setVariable("SESSION", $class->session);
  $template->setVariable("SHORT", $class->short());
  $template->setVariable("LONG", $class->course->full);
  $template->setVariable("ROOM", $class->room->roomNumber);
  $template->setVariable("FACILITY", $class->room->facility->name);
  $template->setVariable("TEACHERS", Teachers::TeacherListClassHtml($class->id));
  $template->setVariable("COUNT", count(Enrollment::GetEnrollmentForClass ($class->id)));
  $template->parseCurrentBlock();

  foreach (Enrollment::GetEnrollmentForClass($classId)  as $item) {
    $student = $item->student;
    $template->setCurrentBlock("MOTHERROW");

    $template->setVariable("ID", $student->id);
    $template->setVariable("FAMILYID", $student->family->id);
    $template->setVariable("SFIRST", $student->firstName);
    $template->setVariable("SLAST", $student->lastName);
    $template->setVariable("SEMAIL", $student->email);
    $template->setVariable("AGE", (int)$student->AgeAt(Calendar::CurrentSession));
    $template->setVariable("GRADE", $student->Grade());
		
    $parent = $student->family->mother;
    $template->setVariable("PFIRST", $parent->firstName);
    $template->setVariable("PLAST", $parent->lastName);
    $template->setVariable("PEMAIL", $parent->email);
    $template->setVariable("PHONE", $student->family->phone);
		
    $template->parseCurrentBlock();
		
    $parent= $student->family->father;
    $template->setCurrentBlock("FATHERROW");

    $template->setVariable("PFIRST", $parent->firstName);
    $template->setVariable("PLAST", $parent->lastName);
    $template->setVariable("PEMAIL", $parent->email);
				
    $template->parseCurrentBlock();
    $template->setCurrentBlock("ROSTERROW");
    $template->parseCurrentBlock();
  }
}

// ****************************
// Rooms
function DisplayRooms($template, $year, $facilityId) {
  $template->addBlockFile('RESULT', 'F_RESULT', 'Rooms.tpl');
  $template->touchBlock('F_RESULT');
  foreach (Rooms::GetRoomsFacility($facilityId) as $item) {
    $templateName="ROOM";
    $template->setCurrentBlock($templateName);
    
    $template->setVariable("ROOMID", $item->id);
    $template->setVariable("ID", $item->id);
    $template->setVariable("FACILITY", $item->facility->name );
    $template->setVariable("ROOMNUMNBER", $item->roomNumber);
    $template->setVariable("CAPACITY", $item->capacity);
    $template->parseCurrentBlock();
  }
}

// ******** Room Detail
function DisplayRoom(&$template, $room) {
  $template->addBlockFile('RESULT', 'F_RESULT', 'RoomDetail.tpl');
  $template->touchBlock('F_RESULT');


  $templateName="ROOMHEAD";
  $template->setCurrentBlock($templateName);

  $template->setVariable("ID", $room->id);
  $template->setVariable("FACILITY", $room->facility->name  . "<br />" . $room->facility->address);
  $template->setVariable("ROOMNUMBER", $room->roomNumber);
  $template->setVariable("CAPACITY", $room->capacity);
  $template->parseCurrentBlock();

  $classCount = array();
  foreach (Enrollment::GetAllEnrollmentForRoom($room->id) as $item) {
    if (empty($classCount[$item->class->id])) $classCount[$item->class->id]=0;
    $classCount[$item->class->id]++;
  }
  

  foreach (AvailableClass::GetClassesForRoom($room->id) as $item) {
    $templateName="USAGE";
    $template->setCurrentBlock($templateName);

    $roster =  "<a href=\"/dakhila/php/dataViewer2.php?command=ClassRoster&classId=$item->id\">" . 
      $item->short() . "</a>";


    $template->setVariable("SESSION", $item->session);
    $template->setVariable("TIME", "$item->startTime - $item->endTime" );
    $template->setVariable("CLASS", $roster);
    $template->setVariable("COUNT", $classCount[$item->id]);
    $template->parseCurrentBlock();
  }

}



?>
