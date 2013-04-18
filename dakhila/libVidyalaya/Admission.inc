<?php

$libDir = realpath(dirname(__FILE__));
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";

/** Error reporting */
error_reporting(E_ALL | E_STRICT | E_NOTICE);

class Admission {
  //  const DataFile = "/home/umesh/Dropbox/Vidyalaya-Management/Administration/2011.csv";
  const DataFile = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission/Admission.csv";
  const OrientationFile = "/home/umesh/workspace/vidphp/admission2011/orientation1.txt";
  //  const assesssmentFile = "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/data/assessment.csv";
  const assesssmentFile = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission/assessment.csv";
  const rosterDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/roster/";
  const volunteerCodeFile="/home/umesh/Dropbox/Vidyalaya-Management/Admission/Volunteer2011.pdf";
  const formHelpFile="/home/umesh/Dropbox/Vidyalaya-Management/Admission/Form Submission.pdf";
  const participationAgreementFile="/home/umesh/Dropbox/Vidyalaya-Management/Admission/ParticipationAgreement.pdf";


  public static $students = Array ();

  private static function sendItemEmail($familyId, $cd, $pb, $bag, $body) {
		
    $family = Family::GetItemById($familyId);

    $counter = 1;
    $table = "<table>\n";
    if ($cd!= 0) $table .= "<tr><td>". $counter++ . ". </td><td>Prayer CD</td><td>\$$cd</td></tr>\n";
    if ($pb!= 0) $table .= "<tr><td>". $counter++ . ". </td><td>Prayer Book</td><td>\$$pb</td></tr>\n";
    if ($bag!= 0) $table .= "<tr><td>". $counter++ . ". </td><td>Book Bag</td><td>\$$bag</td></tr>\n";
    $total = $bag+$cd+$pb;
    $table .= "<tr><td>&nbsp;</td><td>Total</td><td> \$$total</td></tr>\n";
    $table .= "</table>\n";
    $subject = "Additional Item Fulfillment, Family- $family->id";

    //		print $table;
    Mail::mailFamilyFromAdmission($family, $subject, $body . $table, 1);
  }

  private static function EnrollStudent($familyId, $studentId) {
    $student = Student::GetItemById($studentId);
    if (is_null($student)) {
      print "invalid student id $studentId\n";
      return;
    }
    if ($student->family->id != $familyId) {
      print "parent error: family $family, child $child, family should be " . $student->family->id . "\n";
      return;
    } 
    self::$students[$studentId] = $student;
  }

  public static function Payment2012() {
    $totalCD = array();
    $totalPB = array();
    $totalBag = array();
    $totalFamily = array();

    if (($handle = fopen(self::DataFile, "r")) !== FALSE) {
      $header = fgetcsv($handle, 0, ",");
      $header = fgetcsv($handle, 0, ",");
      $i=1;
      $totalTuition=0;
      $done=array();
      $familyTuition = array();
      while ((list($familyId,$Check , $base, $new , $adj , $CD , $PB , $Bag , $date , $total ,$foo, $ch1 , $ch2 , $ch3 )
	      = fgetcsv($handle, 0, ",")) !== FALSE) {
	if (!empty($familyId)) {
	  $base = str_replace('$', "",$base);
	  $new = str_replace('$', "",$new);
	  $adj = str_replace('$', "",$adj);

	  $CD = str_replace('$', "",$CD);
	  $PB = str_replace('$', "",$PB);
	  $Bag = str_replace('$', "",$Bag);
	  $total = str_replace('$', "",$total);
	  if ($total != $base+$new+$adj+$CD+$PB+$Bag) die ("error with total for family $familyId, check $Check\n");
	  
	  $tuition = $base+$new+$adj;
	  $familyTuition [$familyId] += $tuition;
	  $totalTuition += $tuition;

	  if (empty($totalFamily[$familyId])) {
	    $totalFamily[$familyId] =0;
	    $totalCD[$familyId] = 0;
	    $totalPB[$familyId] = 0;
	    $totalBag[$familyId] = 0;
	  }
	  $totalFamily[$familyId] +=$CD+$PB+$Bag;
	  $totalCD[$familyId] += $CD;
	  $totalPB[$familyId] += $PB;
	  $totalBag[$familyId] += $Bag;
	  
	  if (!empty($ch1)) self::EnrollStudent($familyId, $ch1);
	  if (!empty($ch2)) self::EnrollStudent($familyId, $ch2);
	  if (!empty($ch3)) self::EnrollStudent($familyId, $ch3);
	  //	  print "$familyId, $check, $base, $new, $adj, $cd, $pb, $bag, date = $date, $total, $ch1, $ch2, $ch3\n";
	} // if (!empty($family))
      } // while (list)
    } // if handle
	  
    $i=1;
    foreach ($familyTuition as $familyId => $tuition) {
      $tracker = FamilyTracker::GetItemById($familyId);
      if (empty($tracker)) throw new Exception("family $familyId not found in tracker, weird");
      if ($tuition != 0 &&  ($tracker->tuition != $tuition || $tracker->currentYear != EnumFamilyTracker::registered) ) {
	print "Error: family $familyId, File=$tuition, Tracker tuition=$tracker->tuition, status = " . $tracker->currentYear . "\n";
	// FamilyTracker::UpdateStatus($familyId, EnumFamilyTracker::registered , $tuition);
	//	      $sql = "update FamilyTracker set tuition = $tuition, currentYear = " .  EnumFamilyTracker::enum('registered');
	//	      $sql .= " where family = $family and year= " . FamilyTracker::currYear . ";\n";
	//	      $result = VidDb::query($sql);
	//	      print $i++ . "$sql \n";
					
      } elseif ($tuition == 0 && $tracker->currentYear == EnumFamilyTracker::registered) {
	print "check tracker for $familyId, it should not be marked registered\n";
      } else {
	//	print $i++ . " Family $familyId looks ok\n";
      }
      $done[$familyId] = 1;
    } // foreach

    foreach(FamilyTracker::GetAll() as $tracker) {
      $familyId = $tracker->family;
      if ($tracker->tuition != 0 || $tracker->currentYear == EnumFamilyTracker::registered) {
	if  ($done[$familyId] != 1) print "check family " . $tracker->family . "\n";
      }
    } // foreach

    //    $sql="select sum(tuition) from FamilyTracker where year= " . FamilyTracker::currYear;
    //    $result = VidDb::query($sql);
    //    $row = mysql_fetch_array($result);
    $databaseTuition = FamilyTracker::TuitionCollected();
    $tuitionCheck = $databaseTuition == $totalTuition ? "OK" : "FAIL";
    print "Total Tuition in file = " . $totalTuition . ", Database = " . $databaseTuition . ", Check: $tuitionCheck" . "\n";

    $lang = array();
    $grade = array();
    foreach (self::$students as $student) {
      $level = $student->GradeAt(Calendar::RegistrationSession);
      if ($level > 9) $level = 9;
      if ($level != "KG")
	empty($lang[$student->languagePreference]) ? $lang[$student->languagePreference]=1 : $lang[$student->languagePreference]++;
      empty($grade[$level]) ? $grade[$level]= 1 : $grade[$level]++ ;
    }
    foreach ($lang as $key => $value) {
      print "Language: $key, Count: $value\n";
    }
    foreach ($grade as $key => $value) {
      print "Grade: $key, Count: $value\n";
    }
			
    print "Count of Students = " . count(self::$students) . "\n";

  } // public static

  public static function itemDelivery2011() {
    $totalCD = array();
    $totalPB = array();
    $totalBag = array();
    $totalFamily = array();

    if (($handle = fopen(self::DataFile, "r")) !== FALSE) {
      $header = fgetcsv($handle, 0, ",");
      $header = fgetcsv($handle, 0, ",");
      $i=1;
      $totalTuition=0;
      $done=array();
      $fileTuition = array();
      while ((list($family,$Check , $base, $new , $DVD , $CD , $PB , $Bag , $Ann , $Total ,$foo, $ch1 , $ch2 , $ch3 )
	      = fgetcsv($handle, 0, ",")) !== FALSE) {
	if (!empty($family)) {
	  $CD = str_replace('$', "",$CD);
	  $PB = str_replace('$', "",$PB);
	  $Bag = str_replace('$', "",$Bag);

	  if (empty($totalFamily[$family])) {
	    $totalFamily[$family] =0;
	    $totalCD[$family] = 0;
	    $totalPB[$family] = 0;
	    $totalBag[$family] = 0;
	  }
	  $totalFamily[$family] +=$CD+$PB+$Bag;
	  $totalCD[$family] += $CD;
	  $totalPB[$family] += $PB;
	  $totalBag[$family] += $Bag;

	}
      }
    }

    $grandTotal = 0;
    foreach ($totalFamily as $familyId => $total) {
      if ($total == 0 ) continue;
      print "$familyId, $totalCD[$familyId], $totalPB[$familyId], $totalBag[$familyId], $totalFamily[$familyId]\n";
  EmailMessageText::findSubjectBody("002", $body, $subjectFoo);
  //self::sendItemEmail($familyId, $totalCD[$familyId], $totalPB[$familyId], $totalBag[$familyId], $body);
      $grandTotal += $total;
    }
    print "Grand Total = $grandTotal\n";
  }

  // ***********************

  private static function FamilyOpeningDay($tracker) {
    $family = Family::GetItemById($tracker->family);

    //    if ($family->id != 47) return;

    $body = str_replace("==AMOUNT==", $tracker->tuition, file_get_contents("openingday2012.inc"));
    $body = str_replace("==FAMILYID==", $tracker->family, $body);
    $footer="";
      $production=1;
      $subject = "Vidyalaya Opening Day 2012-13, Family- $family->id";

      print "Trying to send email to id " . $family->id . "\n";
      if ($production == 0) $subject = "[Test] $subject";
      $mail = Mail::SetupMailAdmissions();
      Mail::SetFamilyAddress($mail, $family, $production);
      $mail->Subject = $subject;
      $salutation = "<p>Dear " . $family->parentsName() . ",";
      $mail->Body = $salutation . $body . $footer;
      $mail->AltBody = "This is the body when user views in plain text format, opening day $family->id"; //Text Body

      $filename="/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/OpeningDay/pdf/Family-" . $family->id . ".pdf";
      $mail->AddAttachment($filename); // attachment
      $calendar="/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/OpeningDay/Calendar.pdf";
      $mail->AddAttachment($calendar); // attachment

      //      $map = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/Layout/BrooklawnBig.jpg";
      //      $mail->AddAttachment($map); // attachment

    $list = null; $done=array();
    /*
    foreach($enrollment as $item) {
      if (array_key_exists($item->student->id, $done)) continue;
      if ($item->student->family->id == $familyId) {
	$filename="/home/umesh/student2011/Student-" . $item->student->id . ".pdf";
	$mail->AddAttachment($filename); // attachment

	$done[$item->student->id] = 1;
      }
    }
    */

    //    return;
    if(!$mail->Send()) {
      echo "Mailer Error: Family: $family->id: " . $mail->ErrorInfo . "\n";
    }  else {
      echo "Message has been sent, Family: $family->id:\n";
    }

    //    die ("dying");
    sleep (5);
  }


  private static function BackToSchool($tracker) {
    $family = Family::GetItemById($tracker->family);

    if ($family->id != 543) return;

  EmailMessageText::findSubjectBody("003", $body1, $subject);
  EmailMessageText::findSubjectBody("004", $body, $subject);


    foreach (Enrollment::GetEnrollmentForFamily($tracker->family, $tracker->year) as $item) {
      switch ($item->class->course->department) {
      case Department::Culture:
	$visittime="11:05 - 11:20";
	break;
      case Department::Kindergarten:
	$visittime="11:30 - 11:45";
	break;
      default:
	$visittime="10:45 - 11:00";
      }
      $body .=  sprintf("<tr><td>%s</td><td>%s</td><td> %s </td><td>%s</td><td>%s</td><td>%s</td></tr>\n", 
			$item->student->firstName, $item->class->short(), 
			$item->class->room->roomNumber, $visittime, 
			Teachers::TeacherListClassHtml($item->class->id), $item->class->room->hint
			);
    }
    $body .= "</tbody></table>";

    //    $footer="<p>Regards</p><p>Praveen, Asmita, Reena, Kiron</p>";
    $footer ="";
      $production=0;
      $subject = "Vidyalaya Back to School Day 2012-13, Family- $family->id";

      print "Trying to send email to id " . $family->id . "\n";
      if ($production == 0) $subject = "[Test] $subject";
      $mail = Mail::SetupMailSpa();
      Mail::SetFamilyAddress($mail, $family, $production);
      $mail->Subject = $subject;
      $salutation = "<p>Dear " . $family->parentsName() . ",";
      $mail->Body = $salutation . $body . $footer;
      $mail->AltBody = "This is the body when user views in plain text format, opening day $family->id"; //Text Body

      $filename="/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/OpeningDay/pdf/Family-" . $family->id . ".pdf";
      $mail->AddAttachment($filename); // attachment
      //      $calendar="/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/OpeningDay/Calendar.pdf";
      //      $mail->AddAttachment($calendar); // attachment

      $map = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/Layout/BrooklawnBig.jpg";
      $mail->AddAttachment($map); // attachment

    $list = null; $done=array();
    /*
    foreach($enrollment as $item) {
      if (array_key_exists($item->student->id, $done)) continue;
      if ($item->student->family->id == $familyId) {
	$filename="/home/umesh/student2011/Student-" . $item->student->id . ".pdf";
	$mail->AddAttachment($filename); // attachment

	$done[$item->student->id] = 1;
      }
    }
    */

    //    return;
    if(!$mail->Send()) {
      echo "Mailer Error: Family: $family->id: " . $mail->ErrorInfo . "\n";
    }  else {
      echo "Message has been sent, Family: $family->id:\n";
    }

//    die ("dying");
    sleep (5);
  }


  public static function admissionConfirmationEmail($year) {
    //    $enrollment = Enrollment::GetAllEnrollmentForFacilitySession(Facility::Brooklawn, $year);
    $i=1;
    $fp = fopen("/tmp/familylist.csv", "w");
    foreach (FamilyTracker::RegisteredFamilies() as $item) {
      $family = Family::GetItemById($item->family);
      $csv = array();
      $csv[]=$i++;
      $csv[]=$family->id;
      $csv[]=$item->tuition;
      $csv[]=$family->mother->fullName();
      $csv[]=$family->father->fullName();
      $csv[]=$family->address->OneLineAddress();
      fputcsv($fp, $csv);
      //      self::FamilyOpeningDay($item);
      self::BackToSchool($item);
    }
  }

  // if a family wants to receive email regarding class assignment
  public static function FamilyClassAssignment($year) {
    $enrollment = Enrollment::GetAllEnrollmentForFacilitySession(Facility::Brooklawn, $year);
    $families = array();
    if (($fp=fopen("/tmp/mayank.csv", "r"))!=FALSE) {
      while (($data = fgetcsv($fp, 1000, ",")) != FALSE) {
	$pos = count($data) - 2;
	$student = Student::GetItemById($data[$pos]);
	//	print "$pos, Student: $data[$pos], family: " . $student->family->id . "\n";
	if (is_null($student)) {
	  print "\t **** NOT FOUND \n";
	  continue;
	}
	$families[$student->family->id] = $student->family;
      }
    }

    foreach ($families as $family) {
      //      print $family->id . ", " . $family->parentsName() . "\n";
      EmailMessageText::findSubjectBody("005", $body, $subject);
      $footer="";
      $production=1;
      $subject = "Vidyalaya Update, Family- $family->id";
      print "Trying to send email to id " . $family->id . "\n";
      if ($production == 0) $subject = "[Test] $subject";
      $mail = Mail::SetupMailSpa();
      Mail::SetFamilyAddress($mail, $family, $production);
      $mail->Subject = $subject;
      $salutation = "<p>Dear " . $family->parentsName() . ",";
      $mail->Body = $salutation . $body . $footer;
      $mail->AltBody = "This is the body when user views in plain text format, opening day $family->id"; //Text Body

      $filename="/home/umesh/student2011/Family-" . $family->id . ".pdf";
      $mail->AddAttachment($filename); // attachment


      $list = null; $done=array();
      foreach($enrollment as $item) {
	if (array_key_exists($item->student->id, $done)) continue;
	if ($item->student->family->id == $family->id) {
	  $filename="/home/umesh/student2011/Student-" . $item->student->id . ".pdf";
	  $mail->AddAttachment($filename); // attachment

	  $done[$item->student->id] = 1;
	}
      }

      if(!$mail->Send()) {
	echo "Mailer Error: Family: $family->id: " . $mail->ErrorInfo . "\n";
      }  else {
	echo "Message has been sent, Family: $family->id:\n";
      }

    }
  }

  public static function TeacherEmailAttendanceAssessment($year) {

      $footer = "";
      $subject="Attendance/Assessment Sheet";
      $production=1;

     //      if ($person->id() != "F227") continue;
    $draft="";
    if ($production != 1) {
      $subject = "[Test] $subject";
      $draft = "<p>This is a draft <br />";
    }

    foreach (Teachers::TeacherListYear($year) as $item) {
      $person=$item->person;
      $classshort=$item->class->short();
      if ($item->class->course->department > 3) continue;
      $room=$item->class->room->roomNumber;
    $content = file_get_contents("teacherattendanceassessment.html");
     print "Trying to send email to id " . $person->id() . ", Class - $classshort, subject: $subject\n";
      $mail = Mail::SetupMailUmesh();
      Mail::SetPersonAddress($mail, $person, $production);
      $mail->Subject = $subject;
      $salutation = "<p>Dear " . $person->fullName() . ",";
      $mail->Body = $draft . $salutation . $content . $footer;
      $mail->AltBody = "This is the body when user views in plain text format"; //Text Body

      $department=Department::NameFromId($item->class->course->department);
      $filename="/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/attendance/$department/excel/$classshort.xlsx";
      $mail->AddAttachment($filename); // attachment

      $filename="/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/assessment/excel/$classshort.xlsx";
      $mail->AddAttachment($filename); // attachment

      //      continue;

      if(!$mail->Send()) {
	echo "Mailer Error: Person: " . $person->id(). ": " . $mail->ErrorInfo . "\n";
      }  else {
	echo "Message has been sent, Person: " .$person->id() . ":\n";
      }
      //die("hello\n");
      sleep(1);
	  $mail->ClearAllRecipients(); 
	  $mail->ClearAttachments(); 
	  $mail->ClearCustomHeaders(); 
    }
  }

  public static function TeacherEmail($year) {
    foreach (Teachers::TeacherListYear($year) as $item) {
      $person=$item->person;
      $classshort=$item->class->short();
      $room=$item->class->room->roomNumber;
      EmailMessageText::findSubjectBody("006", $body1, $subject);
      EmailMessageText::findSubjectBody("007", $body, $subject);
      $footer = "Regards, <p>Administration Team<br />(sent by: Umesh Mittal)</p>";
      $subject="Welcome to Vidyalaya 2012-13";
      $production=0;

     print "Trying to send email to id " . $person->id() . "\n";
     //      if ($person->id() != "F227") continue;
      if ($production == 0) $subject = "[Test] $subject";
      $mail = Mail::SetupMailUmesh();
      Mail::SetPersonAddress($mail, $person, $production);
      $mail->Subject = $subject;
      $salutation = "<p>Dear " . $person->fullName() . ",";
      $mail->Body = $salutation . $body . $footer;
      $mail->AltBody = "This is the body when user views in plain text format"; //Text Body

      $filename="/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/OpeningDay/pdf/Teacher-" . $person->id() . ".pdf";
      $mail->AddAttachment($filename); // attachment

      $department=Department::NameFromId($item->class->course->department);
      $filename="/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/attendance/$department/excel/$classshort.xlsx";
      $mail->AddAttachment($filename); // attachment
      $filename="/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/roster/word/ClassWide/$classshort.docx";
      $mail->AddAttachment($filename); // attachment

      if(!$mail->Send()) {
	echo "Mailer Error: Person: " . $person->id(). ": " . $mail->ErrorInfo . "\n";
      }  else {
	echo "Message has been sent, Person: " .$person->id() . ":\n";
      }
      sleep(5);
//      die("i die");
    }
  }

  public static function VolunteerEmail($year) {
    foreach (Volunteers::GetAllYear($year) as $item) {
      $person=$item->person;
      EmailMessageText::findSubjectBody("008", $body, $subject);
      $footer = "";
      $subject="Welcome to Vidyalaya 2011";


      $production=1;
      print "Trying to send email to id " . $person->id() . "\n";
      //      if ($person->id() != "M9") continue;
      if ($production == 0) $subject = "[Test] $subject";
      $mail = Mail::SetupMailInfo();
      Mail::SetPersonAddress($mail, $person, $production);
      $mail->Subject = $subject;
      $salutation = "<p>Dear " . $person->fullName() . ",";
      $mail->Body = $salutation . $body . $footer;
      $mail->AltBody = "This is the body when user views in plain text format"; //Text Body

      $filename="/home/umesh/student2011/Volunteer-" . $person->id() . ".pdf";
      $mail->AddAttachment($filename); // attachment
      $mail->AddAttachment("/home/umesh/Dropbox/Vidyalaya-Management/Admission/ParticipationAgreement.pdf"); // attachment

      if(!$mail->Send()) {
	echo "Mailer Error: Person: " . $person->id(). ": " . $mail->ErrorInfo . "\n";
      }  else {
	echo "Message has been sent, Person: " .$person->id() . ":\n";
      }
    }
  }
	
	
  private static function checkStudent($ch1, &$enrolled, &$done) {
    //    print "I am here for $ch1\n";
    if (array_key_exists($ch1, $done)) {
      print "Error: $ch1 was already processed before\n";
      return;
    }
    $done[$ch1] = 1;
    if (!array_key_exists($ch1, $enrolled)) {
      print "Error: $ch1 is not enrolled yet, enroll it\n";
      return;
    }

  }

  private static function validateStudentsFileDatabase($year=null) {
    if (is_null($year)) $year=Calendar::CurrentYear();
    if ($year >= 2010) $year -= 2010;
    $enrolled = Enrollment::GetStudents($year);
    $done=array();


    $filename = Admission::DataFile;
    if (is_readable($filename)) {
      $handle = fopen($filename, "r");
      $header = fgetcsv($handle, 0, ",");
      $header = fgetcsv($handle, 0, ",");
      $i=1;
      $totalTuition=0;
      $done=array();
      $fileTuition = array();
      while ((list($familyId,$Check , $base, $new , $adj , $CD , $PB , $Bag , $date , $total ,$foo, $ch1 , $ch2 , $ch3 )
	      = fgetcsv($handle, 0, ",")) !== FALSE) {
	if (!empty($familyId)) {
	  //	  print "DEBUG: looking up students for family $familyId\n";
	  if (!empty($ch1)) self::checkStudent($ch1, $enrolled, $done);
	  if (!empty($ch2)) self::checkStudent($ch2, $enrolled, $done);
	  if (!empty($ch3)) self::checkStudent($ch3, $enrolled, $done);
	}
      }
    }

    foreach ($enrolled as $ch => $value) {
      if (!array_key_exists($ch, $done)) {
	print "Error: Student $ch is enrolled but does not exists in raw file\n";
      }
    }

    print "Counts: Enroll = " . count($enrolled) . ", done = " . count($done) . "\n";
  }

	
  public static function Validation($year) {
    print "1. Validate Registered Parents between enrollment and familytracker\n";
    // get list of families from enrollment
    $enrolledFamily = array();
    foreach(Enrollment::GetAllEnrollmentForFacilitySession(Facility::Brooklawn, $year) as $item) {
      $enrolledStudent[$item->student->id] = 1;
      $enrolledFamily[$item->student->family->id] = 1;
    }
    $done = array();
    // validate all registered familes from familytracker
    foreach (FamilyTracker::GetRegisteredFamiliesYear($year) as $item) {
      if (array_key_exists($item->family, $enrolledFamily)) {
	$done[$item->family] = 1;
      } else {
	print "Error: Family id $item->family is registered but has no enrolled students\n";
      }
    }

    // make sure all enrolled families are acounted for in tracker
    foreach ($enrolledFamily as $key => $item) {
      if (!array_key_exists($key, $done))
	print "Error: Family $key has kids but is not marked registered\n";
    }
    print "check #1 is complete\n\n";


    print "2. Validate Volunteers are not registered\n";
    foreach (Volunteers::GetAllYear($year) as $item) {
      $key = "$item->MFS:$item->mfsId";
      $volunteerRole[$key] = $item->role;
      switch ($item->MFS) {
      case MFS::Mother:
      case MFS::Father:
	if (array_key_exists($item->mfsId, $enrolledFamily))
	  print "Error: Family $item->mfsId is enrolled, remove it from volunteer list\n";
	break;
      case MFS::Student:
	if (array_key_exists($item->mfsId, $enrolledStudent))
	  print "Error: Student $item->mfsId is enrolled, remove it from volunteer list\n";
	break;
      default:
	print "Error: Found something other MFS in volunteer list. bad,very bad\n";
      }
    }


    // get all teachers, either they should be parent or volunteer. if volunteer. should be marketd as teachers
    print "3. Teachers: must be registered families or volunteers. If volunteer mark them teacher";
    foreach (Teachers::TeacherListYear($year) as $item) {
      $key = "$item->MFS:$item->mfsId";
      $teacherList[$key]=1;
      switch ($item->MFS) {
      case MFS::Mother:
      case MFS::Father:
	if (!array_key_exists($item->mfsId, $enrolledFamily)) {
	  // family is not enrolled, must be a volunteer
	  if (!array_key_exists($key, $volunteerRole)) {
	    print "Error: Key $key is teaching ". $item->class->short() .", not enrolled, should be added as a volunteer\n";
	    break;
	  }
	  $role = $volunteerRole[$key];
	  if (!($role & VolunteerRole::Teacher))
	    print "Error: change role for volunteer key $key from $role to include teacher \n";
	}
	break;
      case MFS::Student:
	if (!array_key_exists($key, $volunteerRole)) {
	  print "Error: Key $key is teaching ". $item->class->short() .", not enrolled, should be added as a volunteer\n";
	  break;
	}
	$role = $volunteerRole[$key];
	if (!($role & VolunteerRole::Teacher))
	  print "Error: change role for volunteer key $key from $role to include teacher \n";
	break;
      default:
	print "Error: Found something other MFS in teacher list. bad,very bad\n";
      }
      
    }

    print "4. validate all teacher volunteers are teaching\n";
    foreach ($volunteerRole as $key=>$role) {
      if (!($role & VolunteerRole::Teacher)) continue;
      // so we have a volunteer who is a teacher
      if(!array_key_exists($key, $teacherList))
	print "Error: key $key is setup as teacher in volunteer but not found in teacher list\n";
    }

    print "5. check students in database vs. csv file";
    self::validateStudentsFileDatabase($year);

    print "todo 6. validate student language preference and language assignment are aligned\n";
  }

  public static function PrintVolunteers($year) {
    $pdfDir = "/home/umesh/student2011";
    foreach (Volunteers::GetAllYear($year) as $item) {
      $full = $item->person->fullName();
      EmailMessageText::findSubjectBody("009", $openingHtml, $subject);
      $html = PrintFactory::GetHtmlForPersonDetail($item->person) . $openingHtml;

      $mfskey=MFS::CodeFromId($item->MFS) . $item->mfsId;
      $fileName = $pdfDir . "/Volunteer-" . $mfskey . ".pdf";
      file_put_contents("$fileName", PrintFactory::HtmlToPdf($html));
    }
  }

  public static function OpeningDay($year) {
    $fp = tmpfile();
    if (!$fp) die ("could not open $filename for writing");
    $pdfDir = "/home/umesh/student2011";
    
    // get all registered families
    $family= array();
    foreach (FamilyTracker::GetRegisteredFamiliesYear($year) as $item) {
      $familyarray[$item->family] = $item->family;
    }

print "Registered family count is " . count($familyarray) . "\n";

    $students = array(); $done = array();
    foreach(Enrollment::GetAllEnrollmentForFacilitySession(Facility::Brooklawn, $year) as $item) {
      if (array_key_exists($item->student->id, $done)) continue;
      $familyid= $item->student->family->id;
      if (!array_key_exists($familyid, $familyarray)) die ("family id $familyid in enrollment is not registered");
      if (array_key_exists($familyid, $students)) {
	$students[$familyid] .= ", " . $item->student->id;
      }else {
	$students[$familyid] = $item->student->id;
      }
      $done[$item->student->id]=1;
    }

    $teachers= array();
    foreach (Teachers::TeacherListYear($year) as $item) {
      $key = MFS::CodeFromId($item->MFS). $item->mfsId;
      $familyid = $item->person->home->id;
      if (!array_key_exists($familyid, $familyarray)) continue; // they get printed elsewhere
      if (array_key_exists($familyid, $teachers)) {
	$teachers[$familyid] .= ", " . $key;
      }else {
	$teachers[$familyid] = $key;
      }
    }

//    $cashfile="/home/umesh/Dropbox/Vidyalaya-Management/Administration/2011.csv";
//    if (($handle = fopen($cashfile, "r")) !== FALSE) {
    if (($handle = fopen(self::DataFile, "r")) !== FALSE) {
      $header = fgetcsv($handle, 0, ",");
      $header = fgetcsv($handle, 0, ",");
      $i=1;
      $totalTuition=0;

//      while ((list($family,$Check , $base, $new , $DVD , $CD , $PB , $Bag , $Ann , $Total ,$foo, $ch1 , $ch2 , $ch3 )
//	      = fgetcsv($handle, 0, ",")) !== FALSE) {
      while ((list($family,$Check , $base, $new , $adj , $CD , $PB , $Bag , $date , $Total ,$foo, $ch1 , $ch2 , $ch3 )
	      = fgetcsv($handle, 0, ",")) !== FALSE) {
	if (!empty($family)) {

	  $new = str_replace('$', "",$new);
	  $adj = str_replace('$', "",$adj);
	  $CD = str_replace('$', "",$CD);
	  $PB = str_replace('$', "",$PB);
	  $Bag = str_replace('$', "",$Bag);

	  //	  if ($family==473) print "473:  DVD\n";
	  if (empty($totalFamily[$family])) {
	    $totalFamily[$family] =0;
	    $totalnew[$family] = 0;
	    $totalAdj[$family] = 0;
	    $totalCD[$family] = 0;
	    $totalPB[$family] = 0;
	    $totalBag[$family] = 0;
	  }
	  $totalFamily[$family] +=$new+$adj+$CD+$PB+$Bag;
	  $totalnew[$family] += $new/50;
//	  $totalAdj[$family] += $adj/10;
	  $totalCD[$family] += $CD/10;
	  $totalPB[$family] += $PB/10;
	  $totalBag[$family] += $Bag/10;
	}
      }
    }

    foreach ($totalFamily as $familyid => $value){
      if ($value == 0) continue; // no money
      if (!array_key_exists($familyid, $familyarray)) die ("family id $familyid paid money but not registered");
    }



    fwrite($fp, "Fam, #, Stud, Teach, New, Adj, CD, PB, Bag, Parents\n");

    foreach ($familyarray as $item) {
      $csv = array();
      $fileName = $pdfDir . "/Family-" . $item  . ".pdf";
      $openingHtml = "<h3>Opening Day 2011 Item Delivery</h3>\n<table>";

      $csv[]=$item;
      $openingHtml .= "<tr><td>Family ID</td><td>$item</td></tr>\n";

      $value = count(explode(",",  $students[$item]));
      $csv[]= $value;
      $openingHtml .= "<tr><td>Student Count</td><td>$value</td></tr>\n";

      $value=$students[$item];
      $csv[]=$value;
      $openingHtml .= "<tr><td>Student Badges</td><td width='200px'>$value</td></tr>\n";

      $value=array_key_exists($item, $teachers) ? $teachers[$item] : "";
      $csv[]=$value;
      $openingHtml .= "<tr><td>Teacher Badges</td><td>$value</td></tr>\n";

      $value=$totalnew[$item] == 0 ? "" : $totalnew[$item];
      $csv[]=$value;
      $extraValue=$totalnew[$item] == 0 ? "" : "<span style='font-size:50%'>(Please tell us the T-shirt size(s))</span>";
      $openingHtml .= "<tr><td>New Student Packages</td><td>$value $extraValue</td></tr>\n";

//      $value=$totalDVD[$item] == 0 ?  "": $totalDVD[$item];
//      $csv[]=$value;
//      $openingHtml .= "<tr><td>DVD</td><td>$value</td></tr>\n";

      $value=$totalCD[$item] == 0 ?  "": $totalCD[$item];
      $csv[]=$value;
      $openingHtml .= "<tr><td>Audio CD</td><td>$value</td></tr>\n";

      $value=$totalPB[$item] == 0 ?  "": $totalPB[$item];
      $csv[]=$value;
      $openingHtml .= "<tr><td>Prayer Book</td><td>$value</td></tr>\n";

      $value=$totalBag[$item] == 0 ? "" : $totalBag[$item];
      $csv[]=$value;
      $openingHtml .= "<tr><td>Book Bag</td><td>$value</td></tr>\n";

      $familyobj = Family::GetItemById($item);
      $csv[]=$familyobj->parentsName();
      $openingHtml .= "</table>\n <div style='font-size:50%'><p>Note: Please print and bring this form to opening day, Septmber 18, 2011 Parsippany Hills High School. Review and mark any update to the personal information.</div>";

      fputcsv($fp, $csv);

//            $pdf = PrintFactory::HtmlToPdf(PrintFactory::GetHtmlForFamilyDetail($familyobj) . $openingHtml);
//            file_put_contents("$fileName", $pdf);
    }
    
    fflush($fp);
    fseek($fp, 0);
    $filename = self::rosterDir . "families.csv";
    file_put_contents("$filename", stream_get_contents($fp));
    fclose($fp);

    print "Total Families = " . count($familyarray) . ", Teaching families = " . count($teachers) . "\n";
  }

  private static function AdultLanguageMail($family) {
    if ($family->id != 162 ) return;
    $footer="<p>Regards,<p>Language Curriculum Team<br />(sent on behalf of : Asmita Mistry)</p>";
    $production=1;
    $subject = "New Language Class - Conversation";
    //    $body = file_get_contents("adult.inc");
    $body = file_get_contents("conversation.inc");
    if ($production == 0) $subject = "[Test] $subject";
    $mail = Mail::SetupMailSpaReplyAsmita();
    Mail::SetFamilyAddress($mail, $family, $production);
    $mail->Subject = $subject;
    $salutation = "<p>Dear " . $family->parentsName() . ",";
    $mail->Body = $salutation . $body . $footer;
    $mail->AltBody = "This is the body when user views in plain text format, opening day $family->id"; //Text Body
    
    if(!$mail->Send()) {
      echo "Mailer Error: Family: $family->id: " . $mail->ErrorInfo . "\n";
    }  else {
      echo "Message has been sent, Family: $family->id:\n";
    }

    sleep(2);

  }

  private static function islanglevelabove3($e) {
    if (!Department::IsLanguage($e->class->course->department)) return false;
    if ($e->class->course->level < 3) return false;
    return true;
  }

  public static function AdultLanguage() {
    $status = array();
    // registered families
    $i=1;
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::Brooklawn, 2012) as $enrollment) { 
      if (!self::islanglevelabove3($enrollment)) continue;
      $familyId = $enrollment->student->family->id;
      
      if (!array_key_exists($familyId, $status)) {
	$status[$familyId] = 0;
	//	print "$i. Trying to send email to id " . $familyId . "\n"; $i++;
	self::AdultLanguageMail($enrollment->student->family);
      }
    }
    return;

    foreach (Volunteers::GetAllYear(2011) as $item) { // volunteers
      $familyId = $item->person->home->id;
      if (!array_key_exists($familyId, $status)) {
	$status[$familyId] = 0;
	self::AdultLanguageMail($item->person->home);
      }
    }
  }

  private static function AnnounceExisting($family, $body, $subject) {

    $production=0;
    $mail =   Mail::SetupMailAdmissions();
    //    $subject = "Vidyalaya Admission 2013-14, Family $family->id, Last Call";
    if ($production == 0) $subject = "[Test] $subject";

    Mail::SetFamilyAddress($mail, $family, $production);
    $mail->Subject = $subject;

    // attachments
    $customizedPdf = "/home/umesh/Dropbox/Vidyalaya-Roster/2013-14/admission/pdf/Family-". $family->id . ".pdf";
    $mail->AddAttachment("$customizedPdf"); // attachment
    $mail->AddAttachment("/home/umesh/Dropbox/Vidyalaya-Management/Admission/Volunteer2011.pdf"); // attachment
    $mail->AddAttachment("/home/umesh/Dropbox/Vidyalaya-Management/Admission/ParticipationAgreement.pdf"); // attachment
  
    print "Family id: $family->id, Name: " . $family->parentsName() . " $subject\n";

    $salutation = "<p>Dear " . $family->parentsName() . ",";
    $body=preg_replace("/==FAMILYID==/", $family->id, $body);
    //    $mail->Body = $draft . $salutation . file_get_contents("../../vidphp/admission2011/reminder-all.html");
    $mail->Body = $draft . $salutation . $body;
    $mail->AltBody = "Family: $family->id"; //Text Body

    //        return;
    if(!$mail->Send()) {
      echo "Mailer Error: " . $mail->ErrorInfo . "\n";
    }  else {
      echo "Message has been sent\n";
    }
    die ("i sent one\n");
    sleep(5);
  }

  public static function ExistingFamilies() {
    $production=0;
    $mail =   Mail::SetupMailAdmissions();
    EmailMessageText::findSubjectBody("001", $body1, $subject);
    //    $subject = "Vidyalaya Admission 2013-14, Family $family->id, Last Call";
    $draft="";
    if ($production != 1) {
      $subject = "[Test] $subject";
      $draft = "<p>This is a draft <br />";
    }


    $i=1;
    foreach (FamilyTracker::GetAllForYear(Calendar::RegistrationYear()) as $tracker) {
      $body=$body1;
      if ($tracker->family != 351) continue;
	    //      if ($tracker->previousYear != EnumFamilyTracker::registered) continue;
      //      if ($tracker->currentYear != EnumFamilyTracker::pendingRegistration) continue;
      if ($tracker->currentYear != EnumFamilyTracker::pendingInvitation) continue;
      print $tracker->family . ", previous: " . EnumFamilyTracker::NameFromId($tracker->previousYear) . ", current: " 
	. EnumFamilyTracker::NameFromId($tracker->currentYear) . "\n";
      $family = Family::GetItemById($tracker->family);
      print "-->$i. Family id: $family->id, Name: " . $family->parentsName() . "\n";
      $i++;

      $mail->ClearAllRecipients(); 
      $mail->ClearAttachments(); 
      $mail->ClearCustomHeaders(); 
      Mail::SetFamilyAddress($mail, $family, $production);
      $mail->Subject = $subject;
      $salutation = "<p>Dear " . $family->parentsName() . ",";
      $body=preg_replace("/==FAMILYID==/", $family->id, $body);
      $mail->Body = $draft . $salutation . $body;
      $mail->AltBody = "Family: $family->id"; //Text Body

      $customizedDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2013-14/admission/pdf/";
      $customizedPdf = $customizedDir . "Family-". $family->id . ".pdf";
      // check if customized pdf exists
      if (!is_readable($customizedPdf)) die("File $customizedPdf does not exist\n");

      $mail->AddAttachment(self::formHelpFile); 
      $mail->AddAttachment($customizedPdf); 
      $mail->AddAttachment(self::volunteerCodeFile); 
      $mail->AddAttachment(self::participationAgreementFile);

      // continue;
      //die ("i die here");

      //      return;
      if(!$mail->Send()) {
	echo "Mailer Error: $family->id:  " . $mail->ErrorInfo . "\n";
	return;
      }  else {
	echo "Message has been sent, Family $family->id\n";
      }
      //      die("hello\n");
      sleep(1);
      
      
    }
    return;
  }

  public static function InviteNewV2() {
    $production=1;
    $mail =   Mail::SetupMailAdmissions();
    EmailMessageText::findSubjectBody("010", $body1, $subject);
    //    $subject = "Vidyalaya Admission 2013-14, Family $family->id, Last Call";
    $draft="";
    if ($production != 1) {
      $subject = "[Test] $subject";
      $draft = "<p>This is a draft <br />";
    }


    $i=1;
    foreach (FamilyTracker::GetAllForYear(Calendar::RegistrationYear()) as $tracker) {
      $body=$body1;
      //      if ($tracker->family != 351) continue;
	    //      if ($tracker->previousYear != EnumFamilyTracker::registered) continue;
      //      if ($tracker->currentYear != EnumFamilyTracker::pendingRegistration) continue;
      if ($tracker->currentYear != EnumFamilyTracker::pendingInvitation) continue;
      if ($tracker->previousYear == EnumFamilyTracker::registered) continue;
      //      if ($tracker->previousYear != EnumFamilyTracker::waitlist) die("famiy $family->id is neither registered nor waitlist");

      print $tracker->family . ", previous: " . EnumFamilyTracker::NameFromId($tracker->previousYear) . ", current: " 
	. EnumFamilyTracker::NameFromId($tracker->currentYear) . "\n";
      $family = Family::GetItemById($tracker->family);
      $customizedPdf = "/home/umesh/Dropbox/Vidyalaya-Roster/2013-14/admission/pdf/Family-". $family->id . ".pdf";
      if (!file_exists($customizedPdf)) Reports::RegistrationPacketFamily($family);
      print "-->$i. Family id: $family->id, Name: " . $family->parentsName() . "\n";
      $i++;

      $mail->ClearAllRecipients(); 
      $mail->ClearAttachments(); 
      $mail->ClearCustomHeaders(); 
      Mail::SetFamilyAddress($mail, $family, $production);
      $mail->Subject = $subject;
      $salutation = "<p>Dear " . $family->parentsName() . ",";
      $body=preg_replace("/==FAMILYID==/", $family->id, $body);
      $mail->Body = $draft . $salutation . $body;
      $mail->AltBody = "Family: $family->id"; //Text Body

      $customizedDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2013-14/admission/pdf/";
      $customizedPdf = $customizedDir . "Family-". $family->id . ".pdf";
      // check if customized pdf exists
      if (!is_readable($customizedPdf)) die("File $customizedPdf does not exist\n");

      $mail->AddAttachment(self::formHelpFile); 
      $mail->AddAttachment($customizedPdf); 
      $mail->AddAttachment(self::volunteerCodeFile); 
      $mail->AddAttachment(self::participationAgreementFile);

                  continue;
      //die ("i die here");

      //      return;
      if(!$mail->Send()) {
	echo "Mailer Error: $family->id:  " . $mail->ErrorInfo . "\n";
	return;
      }  else {
	echo "Message has been sent, Family $family->id\n";
	$count = FamilyTracker::UpdateStatus($family->id, EnumFamilyTracker::pendingRegistration, 0);
	if ($count != 1 ) {
	  print "problem updating database, count = $count\n";
	  return;
	}
	print "Database updated successfully\n";
      }
      //            die("hello\n");
      sleep(1);
      
      
    }
    return;
  }



  private static function AnnounceOrientation($family) {
    $production=1;
    $mail =   Mail::SetupMailAdmissions();
    Mail::SetFamilyAddress($mail, $family, $production);

    $subject = "Vidyalaya Admission 2012-13, Family $family->id";
    if ($production == 0) $subject = "[Test] $subject";
    $mail->Subject = $subject;
  

    // attachments
    $customizedPdf = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission/pdf/Family-". $family->id . ".pdf";
    $mail->AddAttachment("$customizedPdf"); // attachment
    $mail->AddAttachment("/home/umesh/Dropbox/Vidyalaya-Management/Admission/Volunteer2011.pdf"); // attachment
    $mail->AddAttachment("/home/umesh/Dropbox/Vidyalaya-Management/Admission/ParticipationAgreement.pdf"); // attachment
  
    print "Family id: $family->id, $subject; Name: " . $family->parentsName() . "\n";

    //return;
    $salutation = "<p>Dear " . $family->parentsName() . ",";
    //        $mail->Body = $draft . $salutation . file_get_contents("../../vidphp/admission2011/orientation2012.html");
    $mail->Body = $draft . $salutation . file_get_contents("../../vidphp/admission2011/postorientation2012.html");
    // $mail->Body = $draft . $salutation . file_get_contents("../../vidphp/admission2011/reminder-orientation.htnl");
    $mail->AltBody = "Family: $family->id"; //Text Body

    if(!$mail->Send()) {
      echo "Mailer Error: " . $mail->ErrorInfo . "\n";
      return;
    }  
    print  "Message has been sent  ";
    //    return;
    $count = FamilyTracker::UpdateStatus($family->id, EnumFamilyTracker::pendingRegistration, 0);
    if ($count != 1 ) {
      print "problem updating database, count = $count\n";
      return;
    }
    print "Database updated successfully\n";
  }

  public static function InviteNew() {
    $i=1;
    foreach (FamilyTracker::GetAll() as $tracker) {
      $family = Family::GetItemById($tracker->family);
      if ($tracker->currentYear != EnumFamilyTracker::pendingInvitation) continue;
      //if ($tracker->currentYear != EnumFamilyTracker::pendingRegistration) continue;
      if ($tracker->previousYear == EnumFamilyTracker::registered) continue;
      if ($tracker->previousYear != EnumFamilyTracker::waitlist) die("famiy $family->id is neither registered nor waitlist");
      print "$i. Family id: $family->id, Name: " . $family->parentsName() . "\n";
      $i++;

      $customizedPdf = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission/pdf/Family-". $family->id . ".pdf";
      if (!file_exists($customizedPdf)) Reports::RegistrationPacketFamily($family);
      self::AnnounceOrientation($family);
    }
    return;
  }

  public static function Mela2012() {
    $i=1;
    foreach (FamilyTracker::GetAll() as $tracker) {
      $family = Family::GetItemById($tracker->family);
      //      if ($tracker->currentYear != EnumFamilyTracker::pendingInvitation) continue;
      //if ($tracker->currentYear != EnumFamilyTracker::pendingRegistration) continue;
      if ($tracker->previousYear == EnumFamilyTracker::registered) continue;
      if ($tracker->previousYear != EnumFamilyTracker::waitlist) die("famiy $family->id is neither registered nor waitlist");
      print "$i. Family id: $family->id, Name: " . $family->parentsName() . "\n";
      $i++;

      $customizedPdf = "/home/umesh/Dropbox/Vidyalaya-Management/Administration/Mela2012.pdf";
      $production=1;
      $mail =   Mail::SetupMailInfo();
      Mail::SetFamilyAddress($mail, $family, $production);

      $subject = "Vidyalaya Mela Invitation - Sunday June 3, 2012, 10AM-1PM";
      if ($production == 0) $subject = "[Test] $subject";
      $mail->Subject = $subject;
      $mail->AddAttachment("$customizedPdf"); // attachment
      print "Family id: $family->id, $subject; Name: " . $family->parentsName() . "\n";

      //      continue;
      $salutation = "<p>Dear " . $family->parentsName() . ",";
      $mail->Body = $draft . $salutation . file_get_contents("../../vidphp/admission2011/mela.html");
      $mail->AltBody = "Family: $family->id"; //Text Body

      if(!$mail->Send()) {
	echo "Mailer Error: " . $mail->ErrorInfo . "\n";
	continue;
      }  
      print  "Message has been sent  ";
      sleep(5);
    } //foreach
    return;
  }



}

?>
