<?php
$htmlDir = realpath(dirname(__FILE__)) . "/../html";
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
	$template->setVariable("FOOTER", "Copyright (c) 2012 Vidyalya Inc.");
	$template->parseCurrentBlock();
}

// *****  Person Details
function DisplayPersonTemplate (&$template, $person) {
  $family=$person->home;
	$templateName="HOME";
	$template->setCurrentBlock($templateName);
	$line2 = $family->address->addr2;
	if (! empty($line2)) {
		$template->setVariable("LINE2", ", ". $line2);
	}
	$template->setVariable("ID", $person->id());
	$template->setVariable("LINE1", $family->address->addr1);
	$template->setVariable("CITY", $family->address->city);
	$template->setVariable("STATE", $family->address->state);
	$template->setVariable("ZIP", $family->address->zipcode);
	$template->setVariable("HOMEPHONE", $family->phone);
	$template->parseCurrentBlock();

	$templateName="PERSON";
	$template->setCurrentBlock($templateName);
	$template->parseCurrentBlock();
	$template->setVariable("NAME", $person->fullName());
	$template->setVariable("GENDER", $person->gender);
	$template->setVariable("EMAIL", $person->email);
	$template->setVariable("WORK", $person->workPhone);
	$template->setVariable("CELL", $person->cellPhone);
	$template->parseCurrentBlock();


	if ($person->MFS != MFS::Student) {
	  $templateName="FAMILYLINK";
	  $template->setCurrentBlock($templateName);
	  $template->setVariable("ID", $person->mfsId);
	  $template->parseCurrentBlock();
	} else {
	  $templateName="STUDENTLINK";
	  $template->setCurrentBlock($templateName);
	  $template->setVariable("ID", $person->mfsId);
	  $template->parseCurrentBlock();

	  $templateName="PARENTS";
	  $template->setCurrentBlock($templateName);

	  $template->setVariable("MF", "Father");
	  $template->setVariable("ID", $family->id);
	  $template->setVariable("NAME", $family->father->fullName());
	  $template->setVariable("EMAIL", $family->father->email);
	  $template->setVariable("WORK", $family->father->workPhone);
	  $template->setVariable("CELL", $family->father->cellPhone);
	  $template->setVariable("ISCONTACTABLE", YesNoMap($family->father->IsContactable));
	  $template->parseCurrentBlock();

	  $template->setVariable("MF", "Mother");
	  $template->setVariable("ID", $family->id);
	  $template->setVariable("EMAIL", $family->mother->email);
	  $template->setVariable("WORK", $family->mother->workPhone);
	  $template->setVariable("CELL", $family->mother->cellPhone);
	  $template->setVariable("NAME", $family->mother->fullName());
	  $template->setVariable("ISCONTACTABLE", YesNoMap($family->mother->IsContactable));
	  $template->parseCurrentBlock();

	  $templateName="SHOWPARENTS";
	  $template->setCurrentBlock($templateName);
	  $template->parseCurrentBlock();
	}
	
	$isDirty = false;
	$teachers = Teachers::GetTeacherFromPerson($person, Calendar::CurrentYear());
	if (!is_null($teachers)) {
	  
	  $isDirty=true;
	  $templateName="ROLE";
	  $template->setCurrentBlock($templateName);

	  foreach ($teachers as $teacher) {
	    $template->setVariable("ROLE", "Teacher");
	    $template->setVariable("CLASS", $teacher->class->short()); 
	    $template->setVariable("ROOM", $teacher->class->room->roomNumber); 
	    $template->setVariable("ROLE", 0); 
	    $template->setVariable("CLASSID", $teacher->class->id); 
	    $template->parseCurrentBlock();
	  }
	}

	if ($isDirty && false) {
	  $templateName="CATEGORYBLOCK";
	  $template->setCurrentBlock($templateName);
	  $template->parseCurrentBlock();
	}

	// check for open events
	$events = Items::OpenEvents();
	if (!is_null($events)) {
	  foreach($events as $event) {
	    
	    $registration = ItemRegistration::RegisteredOpenItems($person->MFS, $person->mfsId, $event->id);
	    if (is_null($registration)) {
	      $status=""; $display="inline";
	      PersonDetailShowButton($template, $person, $event, "Register", "register");
	      PersonDetailShowButton($template, $person, $event, "Not Interested", "decline");
	    } else {
	      $action="nothing"; $actionlabel="No Further Action"; $display="none";
	      $count=count($registration);
	      if ($count == 1) {
		$reg = $registration[0];
		$status = $reg->statusText;
	      } else {
		$status = "$count items found";
	      }
	    }

	  $templateName="EVENTREGISTRATION";
	  $template->setCurrentBlock($templateName);

	    $template->setVariable("ID", $event->id);
	    $template->setVariable("URL", $event->url);
	    $template->setVariable("EVENT", $event->description);
	    $template->setVariable("DATE", $event->when);
	    $template->setVariable("COST", $event->cost);

	  $template->setVariable("STATUS", $status);
	  $template->setVariable("DISPLAY", $display);
	  $template->parseCurrentBlock();
	  }

	  $templateName="SHOWEVENTS";
	  $template->setCurrentBlock($templateName);
	  $template->parseCurrentBlock();

	}


}

function PersonDetailShowButton($template, $person, $event, $actionlabel, $action) {
	  $templateName="ACTIONBUTTON";
	  $template->setCurrentBlock($templateName);
	    $template->setVariable("MFS", $person->MFS);
	    $template->setVariable("MFSID", $person->mfsId);
	    $template->setVariable("ITEM", $event->id);
	  $template->setVariable("ACTION", $action);
	  $template->setVariable("ACTIONLABEL", $actionlabel);
	  $template->parseCurrentBlock();
}

// ******** Student
function DisplayPerson($template, $person) {
  if ($person == null) return;
	$template->addBlockFile('RESULT', 'F_RESULT', 'PersonDetail.tpl');
	$template->touchBlock('F_RESULT');
      	DisplayPersonTemplate($template, $person);
}



// *****  Family Details
function DisplayFamilyTemplate (&$template, $family, $students) {

  die("Do not use me, use DisplayFamilyTemplateV3 function\n");

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

	$templateName="CURRENTSESSION";
	$template->setCurrentBlock($templateName);
	$template->setVariable("SESSIONSTRING",Calendar::SessionFromYear(Calendar::CurrentYear()));
	$template->parseCurrentBlock();

	foreach ($family->Children() as  $student) {
	  DisplayChildren($template, $student);
	}

	if (VidSession::IsSuperUser()) {
	  $templateName="ADDCHILD";
	  $template->setCurrentBlock($templateName);
	  $template->setVariable("ID", $family->id);
	  $template->parseCurrentBlock();
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

	$templateName="TRACKER";
	$template->setCurrentBlock($templateName);
	$item=null;
	foreach (FamilyTracker::FamilyHistory($family->id) as $item) {
	  $template->setVariable("YEAR", $item->year+2010);
	  $previous = EnumFamilyTracker::NameFromId($item->previousYear);
	  if ($item->previousYear == EnumFamilyTracker::waitlist) $previous .= " ($family->priority_date)";
	  $template->setVariable("PREVIOUS", $previous);
	  $template->setVariable("CURRENT", EnumFamilyTracker::NameFromId($item->currentYear));
	  $template->setVariable("TUITION", $item->tuition);
	  $template->setVariable("REGDDATE", $item->registrationDate);
	  $template->parseCurrentBlock();
	  /*
	  if (Calendar::CurrentYear() == $item->year+2010 && $item->previousYear == EnumFamilyTracker::registered) {
	    $templateName="DVD2011";
	    //	    print "debug: print DVD thing\n";
	    $template->setCurrentBlock($templateName);
	    $template->setVariable("DVDYEAR", "2011");
	    $template->parseCurrentBlock();
	  }
	  */

	}

	foreach($family->allContacts() as $contact) {
	  DisplaySingleContact($template, $contact);
	}

	$sapi = php_sapi_name();
	if ($sapi == "cli") return;
	if (!VidSession::IsSuperUser()) return;
	if (is_null($item) || $item->year != (Calendar::RegistrationYear() - 2010)) {
	$trackerchange =  <<< WAITLISTME
	  <button data-dojo-type="dijit.form.Button" type="button">
    <script type="dojo/method" event="onClick" args="evt">
        // Do something:
	  waitlistme( $family->id);
    </script>
    Waitlist Me!
</button>

WAITLISTME;
	} else {
	  $trackerchange = file_get_contents("../html/TrackerChange.inc");
	  $trackerchange = str_replace("FAMILYID", $item->family, $trackerchange);
	}


	$templateName="CHANGEBLOCK";
	$template->setCurrentBlock($templateName);
	$template->setVariable("CHANGE", $trackerchange);
	$template->parseCurrentBlock();

}


function DisplayChildren(&$template, $student) {
	$templateName="CHILDREN";
	$template->setCurrentBlock($templateName);
	$template->setVariable("ID", $student->id);
	$template->setVariable("NAME", $student->fullName());
	$template->setVariable("EMAIL", $student->email);
	$template->setVariable("GRADE", $student->Grade());
	$template->setVariable("LANGUAGE", $student->LanguageInterest());
	$template->setVariable("GENDER", GenderMap($student->gender));
	$dob = empty($student->dateOfBirth) ? "Unknown" : date("m/d/Y", strtotime($student->dateOfBirth));
	$template->setVariable("DOB",$dob);
	//	$template->setVariable("ENROLLED", YesNoMap($student->IsEnrolled));
	$template->parseCurrentBlock();


	$templateName="REGISTRATION";
	$template->setCurrentBlock($templateName);

	foreach(Enrollment::GetEnrollmentStudent($student->id, Calendar::CurrentYear()) as $item) {
	  if ($item->class->course->department == Department::Culture ) {
	    $template->setVariable("CULTURE", $item->class->urlRoster());
	   $template->setVariable("CROOM", $item->class->room->roomNumber);
	    $template->setVariable("CTEACHER", Teachers::TeacherListClassHtml($item->class->id));
	  } else {
	    $template->setVariable("FIRSTNAME", $student->firstName);
	    $template->setVariable("LANGUAGE", $item->class->urlRoster());
	    $template->setVariable("LROOM", $item->class->room->roomNumber);
	    $template->setVariable("LTEACHER", Teachers::TeacherListClassHtml($item->class->id));
	    $template->parseCurrentBlock();

	    /*
	    $templateName="ENROLLEDCHILDREN";
	    $template->setCurrentBlock($templateName);
	    $namestring = $student->id . ": " . $student->fullName();
	    $first = 2010 +  Enrollment::firstYear($student->id) ;
	    if (Calendar::CurrentYear() <= $first)  $namestring = "<b> $namestring **NEW STUDENT PACK**</b>";
	    $template->setVariable("STUDENTNAME", "Student Folder -" . $namestring);

	    if ($item->class->course->department == Department::Kindergarten) {
	      $template->parseCurrentBlock();
	      $template->setCurrentBlock($templateName);
	      $template->setVariable("STUDENTNAME", "KG Note");
	    }
	    */
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
  if (is_null($family)) return;
	$template->addBlockFile('RESULT', 'F_RESULT', 'FamilyDetail.tpl');
	$template->touchBlock('F_RESULT');
	DisplayFamilyTemplateV3($template, $family);
}

function GetChangeUrl($class, $student) {
	 $url = $class->url();
	 //	 if (!VidSesssion::IsSuperUser()) return $url;
	 if (!$class->IsChangeAllowed()) return $url;
	 if (!VidSession::IsSuperUser()) return $url;

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
  global $htmlDir;
  $family = $student->family;
	$templateName="FATHER";
	$template->setCurrentBlock($templateName);


	$template->setVariable("NAME", $family->father->fullName());
	$template->setVariable("EMAIL", $family->father->email);
	$template->setVariable("WORK", $family->father->workPhone);
	$template->setVariable("CELL", $family->father->cellPhone);
	$template->setVariable("ISCONTACTABLE", YesNoMap($family->father->IsContactable));
	$template->parseCurrentBlock();


	$templateName="OTHERCONTACT";
	foreach ($student->contacts as $key => $phone) {
	  $template->setCurrentBlock($templateName);
	  $template->setVariable("TYPE",  $key);
	  if ($phone == null && VidSession::IsLoggedIn()) {
	    $template->setVariable("PHONE",  "Add me");
	  } else {
	    $contact = OtherContacts::ObjectFromKey($phone);
	    $template->setVariable("PHONE",  formatPhone($phone));
	    $template->setVariable("NAME",  $contact->name);

	    $address = $contact->addr1;
	    if ($contact->addr2 != null) $address .= "<br /> $contact->addr2";
	    if ($contact->zip != null) {
	      $zip = Zipcodes::ObjectFromKey($contact->zip);
	      $address .= "<br /> $zip->city $zip->state, $zip->zipcodes";
	    }
	    //	    $template->setVariable("ADDR",  $address);

	    //	    $template->setVariable("EMAIL",  $contact->email);
	    if (!empty($contact->url)) {
	      $url = "<a href='$contact->url' target='_blank'>url</a>";
	      //	      $template->setVariable("URL",  $url);
	    }
	  }
	  $template->parseCurrentBlock();
	}

	$emergency = formatPhone($student->contacts["Emergency"]);
	$primary = formatPhone($student->contacts["Primary"]);
	$dentist = formatPhone($student->contacts["Dentist"]);
	$hospital = formatPhone($student->contacts["Hospital"]);


	$change =  <<< BUTTONHERE2
	  <button data-dojo-type="dijit.form.Button" type="button">
    <script type="dojo/method" event="onClick" args="evt">
        // Do something:
	  changeContact('$emergency', '$primary', '$dentist', '$hospital', $student->id);
    </script>
    Update
</button>

BUTTONHERE2;
	$templateName="CHANGEBUTTON";
	if (VidSession::IsSuperUser()) {
	$template->setCurrentBlock($templateName);
	$template->setVariable("CHANGECONTACT",  $change);
	$template->parseCurrentBlock();
	}
	
	$templateName="FORMCONTENT";
	$template->setCurrentBlock($templateName);
	$template->setVariable("FORM",  file_get_contents("$htmlDir/formContact.html"));
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
	$template->setVariable("FAMILYID", $student->family->id);
	$template->setVariable("NAME", $student->fullName());
	$dob = empty($student->dateOfBirth) ? 
	  "Unknown" : 
	  date("Y-m-d", strtotime($student->dateOfBirth));
	//	$template->setVariable("DOB", $dob);
	$template->setVariable("CELL", $student->cellPhone);
	$template->setVariable("EMAIL", $student->email);
	$template->setVariable("LINE1", $family->address->addr1);
	$template->setVariable("CITY", $family->address->city);
	$template->setVariable("STATE", $family->address->state);
	$template->setVariable("ZIP", $family->address->zipcode);
	$template->setVariable("HOMEPHONE", $family->phone);
	$template->setVariable("GRADE", $student->Grade());
	$template->setVariable("AGE", intval($student->AgeAt(Calendar::CurrentSession)));
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

	$templateName="ACTIVITY";

	foreach (EventCalendar::ListStudentActivities($student) as $event) {
	$template->setCurrentBlock($templateName);
	$template->setVariable("DATEOFACTIVITY", formatDate($event->date));
	$template->setVariable("CLASSOFACTIVITY", $event->class->short());
	$template->setVariable("STARTTIME", $event->startTime);
	$template->setVariable("ENDTIME", $event->endTime);
	$template->setVariable("ACTIVITYDESCRIPTION", EventCalendarType::StringFromId($event->eventType));
	$template->parseCurrentBlock();
	}

	$templateName="SCHEDULE";
	foreach (Enrollment::GetEnrollmentStudent($student->id, Calendar::CurrentYear()) as $item) {
	$template->setCurrentBlock($templateName);
	$class=$item->class;
	$template->setVariable("SCHSTART", $class->startTime);
	$template->setVariable("SCHEND", $class->endTime);
	$template->setVariable("SCHROOM", $class->room->roomNumber);
	$template->setVariable("SCHCLASS", $class->short());
	$template->setVariable("SCHTEACHERS", Teachers::TeacherListClassHtml($class->id));
	$template->setVariable("SCHDESCRIPTION", $class->course->full);
	$template->parseCurrentBlock();
	}



}

// ******** Student
function DisplayStudent(&$template, $student) {
  global $htmlDir;
  if ($student == null) return;
	$template->addBlockFile('RESULT', 'F_RESULT', 'StudentDetail.tpl');
	$template->touchBlock('F_RESULT');

  $template->setCurrentBlock("HIDDENFORMS");
  $template->setVariable("CONTACTFORM", file_get_contents("$htmlDir/ContactForm.inc"));
  $template->parseCurrentBlock();
  
	DisplayStudentTemplate($template, $student);
}

function DisplayContactStudent(&$template, $student, $type) {
  $templateName="STUDENTS";
  $template->setCurrentBlock($templateName);
  $template->setVariable("TYPE",  $type);
  $template->setVariable("ID",  $student->id);
  $template->setVariable("NAME",  $student->fullName());
  $template->setVariable("PARENTS", $student->parentsName() );
  $template->setVariable("ENROLLED", "" );
  $template->parseCurrentBlock();
}

// ******** Other Contacts
function DisplaySingleContact (&$template, $contact) {
  if ($contact == null) return;
  $templateName="OTHERCONTACT";
  $template->setCurrentBlock($templateName);
  $template->setVariable("PHONE",  formatPhone($contact->phone));
  $template->setVariable("NAME",  $contact->name);
  $address = $contact->addr1;
  if ($contact->addr2 != null) $address .= "<br /> $contact->addr2";
  if ($contact->zip != null) {
    $zip = Zipcodes::ObjectFromKey($contact->zip);
    $address .= "<br /> $zip->city $zip->state, $zip->zipcodes";
  }
  $template->setVariable("ADDR",  $address);

  $template->setVariable("EMAIL",  $contact->email);
  if (!empty($contact->url)) {
    $url = "<a href='$contact->url' target='_blank'>url</a>";
    $template->setVariable("URL",  $url);
  }
  $template->parseCurrentBlock();
}
function DisplayContact(&$template, $contact) {
    global $htmlDir;
  if ($contact == null) return;
  $template->addBlockFile('RESULT', 'F_RESULT', 'OtherContact.tpl');
  $template->touchBlock('F_RESULT');

  $template->setCurrentBlock("HIDDENFORMS");
  $template->setVariable("STUDENTFORM", file_get_contents("$htmlDir/StudentForm.inc"));
  $template->setVariable("EDITFORM", file_get_contents("$htmlDir/editContact.inc"));
  $template->parseCurrentBlock();
  
  DisplaySingleContact($template, $contact);

  $fakemail =empty($contact->email) ? "ignore@me.com" : $contact->email;
  $fakeurl =empty($contact->url) ? "http://ignoreme.com" : $contact->url;

	$change =  <<< BUTTONHERE2
	  <button data-dojo-type="dijit.form.Button" type="button">
    <script type="dojo/method" event="onClick" args="evt">
        // Do something:
	  editContact('$contact->phone', '$contact->name', '$contact->addr1', '$contact->addr2', '$contact->zip', '$fakemail', '$fakeurl');
    </script>
    Edit
</button>

BUTTONHERE2;
	$templateName="EDITBUTTON";
	$template->setCurrentBlock($templateName);
	$template->setVariable("EDITCONTACT",  $change);
	$template->parseCurrentBlock();


  foreach (Student::FindStudentsForKey($contact->phone) as $student) {
    if ($student->contacts["Emergency"] == $contact->phone) DisplayContactStudent($template, $student, "Emergency");
    if ($student->contacts["Primary"] == $contact->phone) DisplayContactStudent($template, $student, "Primary");
    if ($student->contacts["Dentist"] == $contact->phone) DisplayContactStudent($template, $student, "Dentist");
    if ($student->contacts["Hospital"] == $contact->phone) DisplayContactStudent($template, $student, "Hospital");
  }

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

	$children = $family->EligibleChildren();
	foreach ($children as $student) {
		  DisplayRegistrationChildren($template, $student);
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
	if (!$student->WasEverEnrolled()) $template->setVariable("NEW", "NEW");
	
	
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
  global $htmlDir;
	$template->addBlockFile('RESULT', 'F_RESULT', 'AvailableClass.tpl');
	$template->touchBlock('F_RESULT');

	$templateName="HIDDENFORMS";
	$template->setCurrentBlock($templateName);
	$template->setVariable("PERSONFORM", file_get_contents("$htmlDir/PersonForm.inc"));
	$template->setVariable("ADDTEACHERDIALOG", file_get_contents("$htmlDir/addTeacherDialog.inc"));
	$template->parseCurrentBlock();
	
	$classCount = array();
	foreach (Enrollment::GetAllEnrollmentForFacilitySession($facility, $year) as $item) {
		if (empty($classCount[$item->class->id])) $classCount[$item->class->id]=0;
		$classCount[$item->class->id]++;
	}
	
	foreach (AvailableClass::GetAllYear($year) as $item) {
	  $count = empty($classCount[$item->id]) ? 0 :$classCount[$item->id];
	  //	  if ($count==0) continue;
	  $templateName="CLASS";
	  $template->setCurrentBlock($templateName);
	  
	  $url=$_SERVER['PHP_SELF'];
	  $classLink=sprintf("<a href=\"$url?command=ClassRoster&classId=%s\">%s</a>", $item->id, $item->id);
	  $template->setVariable("DEPARTMENT", Department::NameFromId( $item->course->department));
	  $template->setVariable("ROOM", $item->room->roomNumber);
	  $template->setVariable("ROOMID", $item->room->id);
	  $short = $item->short();
	  $template->setVariable("SHORT", sprintf("<a href=\"$url?command=ClassRoster&classId=%s\">%s</a>", $item->id, $short));
	  $template->setVariable("COUNT", $count);
	  
	  $teachers = Teachers::TeacherListClassHtml($item->id);
	  $addTeacherButton =  <<< BUTTONHERE
	  <button data-dojo-type="dijit.form.Button" type="button">
    <script type="dojo/method" event="onClick" args="evt">
        // Do something:
	    addTeacher($item->id, '$short' );
    </script>
    AddTeacher
</button>

BUTTONHERE;
	  //	  if ($item->year == (Calendar::RegistrationYear() - 2010)) 
	    $teachers .= $addTeacherButton;
	  $template->setVariable("TEACHERS", $teachers);


		//     		$template->setVariable("ID", $classLink);

		$template->setVariable("CAPACITY", $item->room->capacity);
		$template->setVariable("FREE", $item->room->capacity - $count);
		 
		$template->parseCurrentBlock();

	}
}

// ****************************
// Class Roster

function DisplayClassRoster($template, $classId) {
  global $htmlDir;
  $template->addBlockFile('RESULT', 'F_RESULT', 'ClassRoster.tpl');
  $template->touchBlock('F_RESULT');

  $templateName="HIDDENFORMS";
  $template->setCurrentBlock($templateName);
  $template->setVariable("PERSONFORM", file_get_contents("$htmlDir/PersonForm.inc"));
  $template->parseCurrentBlock();
  
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
    $template->setVariable("PHONE", $student->LanguageInterest());
				
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
  $heatMap = Rooms::GetHeatMap($facilityId);

  foreach (Rooms::GetRoomsFacility($facilityId) as $item) {
    $templateName="ROOM";
    $template->setCurrentBlock($templateName);

    $template->setVariable("ROOMID", $item->id);
    $template->setVariable("ID", $item->id);
    $template->setVariable("FACILITY", $item->facility->name );
    $template->setVariable("ROOMNUMNBER", $item->roomNumber);
    $template->setVariable("CAPACITY", $item->capacity);
    $template->setVariable("HINT", $item->hint);
    if (isset($heatMap{$item->id})) $template->setVariable("USAGE", $heatMap{$item->id});
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

    $count = isset($classCount[$item->id]) ?  $classCount[$item->id] : 0;

    $template->setVariable("SESSION", $item->session);
    $template->setVariable("TIME", "$item->startTime - $item->endTime" );
    $template->setVariable("CLASS", $roster);
    $template->setVariable("COUNT", $count);
    $template->parseCurrentBlock();
  }

}



?>
