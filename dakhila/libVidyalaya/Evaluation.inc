<?php

$libDir = realpath(dirname(__FILE__));
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";
require_once "$libDir/Admission.inc";
require_once  "PHPExcel/PHPExcel/IOFactory.php";

class Evaluation {
  const TopDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/evaluation/";
  const ReadDir  = "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/evaluation/input";
  const WriteDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/evaluation/report";
	
  private static $txt;
  private static $html;
  private static $pdf;
	
	
  private static function PrintThreeFiles($id) {
    $txtName = self::WriteDir . "/txt/$id" . ".txt";
    file_put_contents("$txtName", self::$txt);
		
    $htmlName = self::WriteDir . "/html/$id" . ".html";
    file_put_contents("$htmlName", self::$html);
		
    $pdfName = self::WriteDir . "/pdf/$id" . ".pdf";
    file_put_contents("$pdfName", HtmlToPdf(self::$html));
  }
	
  private static function shortToLong($short) {
    switch (strtoupper($short)) {
    case "N": return "Needs Improvement";
      break;
    case "S": return "Satisfactory";
      break;
    case "E": return "Excellent";
    case "N/A": case "NA": return "Not Evaluated";
			
    default: return $short;			;
      break;
    }
  }
	
  private static function VidyalayaHeader() {
    $timestamp = date("r");
    $htmlHeader = <<<EOT
      <html>
      <head>
      <link rel="stylesheet" href="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/site.css" 
      type="text/css" media="screen print" /> 
		
      </head>
      <body>
      <a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
      width="700" height="70" 
      alt="vidyalaya logo"/></a>
      <p class=smallFont> $timestamp </p>
EOT;
    return $htmlHeader;
  }

  private static function WriteStudentAssessment($category, $header, $row) {
    //				self::$htmlfh = fopen($filename, "w");
    $count = count($row);
    if ($count < 3) continue;
    $studentId = $row[1];
    $student = Student::GetItemById($studentId);
    $name = $row[2];
		
    // Studnet Information DIsplay
    self::$txt =  "$studentId ($name)\n";
    self::$html = self::VidyalayaHeader();
    self::$html .= "<div class=section>Student</div>\n";
    self::$html .= "<table class=evaluation>\n";
    self::$html .= "<tr><td>ID</td><td>$studentId</td></tr>\n";
    self::$html .= "<tr><td>Name</td><td></td><td>$name</td></tr>\n";
    self::$html .= "<tr><td>Parents</td><td></td><td>" . $student->parentsName() . "</td></tr>\n";
    self::$html .= "<tr><td>Teachers</td><td></td><td>" . $student->registration->language->teachers . "</td></tr>\n";
    // Get Enrollemnet details
    $history = Enrollment::GetLanguageHistory($studentId);
    usort ($history, "Enrollment::CompareSessionDepartment");

    $year = null;
    foreach ($history as $item) {
      $year = $item->class->year + 2010;
      self::$txt .=  $item->class->session . "	"	. $item->class->short() . "\n";
      self::$html .= "<tr><td>" . $item->class->session . "</td><td>" . $item->class->shortWithoutSection() . "</td>";
      self::$html .= "<td>" . $item->class->course->full . "</td></tr>\n";
    }
    if ($year != 2011) {
      self::$txt .=  "2011-12	Not Enrolled\n";
      self::$html .= "<tr><td>" . "2011-12" . "</td><td>" . "<i>Not Enrolled</i>" . "</td></tr>\n";
    }
		
    if (empty($year)) die ("No history found for student id $studentId\n");
    self::$html .= "</table>\n";
		
    $closeTable = 0;
		
    // Evaluation Display
    self::$html .= "<div class=section>Language Evaluation (2010-11)</div>\n";
    self::$html .= "<table class=evaluation>\n";
    for ($i=3; $i < $count; $i++) {
      if (empty($header[$i])) continue;
      if (!empty($category[$i])) {
	self::$txt .= "Category: $category[$i]\n";
	self::$html .= "<tr><td colspan=2 class=category><b>$category[$i]</b></li></td></tr>\n";
      }
      $evaluation = self::shortToLong($row[$i]);
      if (preg_match("/suggested level for 2011/i", $header[$i])) {
	print "$studentId, $row[$i]\n"; // Dispaly it once, to move to file
      } else {
	self::$txt .= "\n" . $header[$i] . "," . $evaluation  . "\n";
	Self::$html .= "<tr><td class=left> $header[$i] </td><td> <i>$evaluation</i>   </td></tr>\n";
      }
    }
		
    self::$html .= "</table>\n";
    self::$html .= "</body>\n</html>\n";
		
		
    self::PrintThreeFiles($studentId);
    //		if ($studentId != 1452) return;
    Mail::mailEvaluation($studentId, self::$html, 1);

  }

  private static function ProcessOneExcelFile($filename, $enrolledStudent, $previous, $next, $previousEnrollment) {
    $array_data = array(); 
    $rowcount =0;

    $objReader = preg_match("/xls$/i", $filename) ? PHPExcel_IOFactory::createReader('Excel5') : PHPExcel_IOFactory::createReader('Excel2007');
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($filename);
    $worksheet=$objPHPExcel->getSheet(0);
    $a = "Recommended Language Level for 2012-13";
    $header = array(); $nextYear=0; $current="unknown";


    foreach ($worksheet->getRowIterator() as $row) {
      $cellIterator = $row->getCellIterator(); 
      $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set 
      $rowIndex = $row->getRowIndex (); 
      $cells = array();
      if ($rowIndex==1) {
	foreach ($cellIterator as $cellno => $cell) { 
	  $s = $cell->getValue();
	  if ($cellno == 1) {
	    $previousShort=preg_replace('/\s+/', '', $s);
	    if (array_key_exists($previousShort, $previous)) {
	      $previousId = $previous[$previousShort];
	    } else {
	      $previousId = 0;
	      print "Error: $previousShort class could not be found in previous array\n";
	    }
	  }
	}
      } else if ($rowIndex==2) {
	foreach ($cellIterator as $cellno => $cell) { 
	  $s = $cell->getValue();
	  $header[] = $s;
	  if ($s == $a) $nextYear=$cellno;
	}
	if ($nextYear == 0) print "Error: next year column missing, row is $rowIndex\n";
      } else if ($rowIndex>2){
	foreach ($cellIterator as $cellno => $cell) { 
	  $cells[] = $cell->getValue();
	}
	$studentId = intval($cells[1]);
	if (empty($studentId)) continue;
	$student = Student::GetItemById($studentId);
	if (empty($student)) print "Error: no student found for $studentId\n";
	if (!array_key_exists($studentId, $enrolledStudent)) continue;
	$nextShort = strtoupper($cells[$nextYear]);
	$change = $nextShort == $previousShort ? "same" : "change";
	if (array_key_exists($nextShort, $next)) {
	  $nextId = $next[$nextShort];
	} else {
	  $nextId = 0;
	  print "Error: $nextShort class could not be found in next array\n";
	}

	// final checking before update

	if ($previousEnrollment[$student->id] != $previousShort) print "Error: previous enrollment and current file does not match\n";
	//	if ($enrolledStudent[$student->id] != $previousShort) print "Error: current enrollment " .
	//							     $enrolledStudent[$student->id] . " and current file $previousShort does not match\n";

	$sql = sprintf("update Enrollment set availableClass = %s where student = %s and availableClass = %s",
		       $nextId, $studentId,$enrolledStudent[$studentId] );

	if ($nextId != $enrolledStudent[$studentId] 
	    && $enrolledStudent[$studentId] != 135
	    && $enrolledStudent[$studentId] != 136
	    ) print "$change $sql\n";

	//	print "$studentId, $previousShort, $nextShort, $change, $rowIndex \n";
	$rowcount++;
      }
    } 
    //    print " $filename, row count = $rowcount";
    return $rowcount;
    print_r($array_data);
  }

  public  static function ProcessAllExcelFiles() {
    $directory = self::ReadDir; // figure out a way for the caller to override it
    $i = 1; $grandTotal = 0;

    $enrolledStudent = array();
    foreach(Enrollment::GetAllEnrollmentForFacilitySession(Facility::Brooklawn, Calendar::CurrentYear()) as $item) {
      if (Department::IsLanguage($item->class->course->department)) {
	$enrolledStudent[$item->student->id] = $item->class->id;
      }
    }

    $previousEnrollment = array();
    foreach(Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS, Calendar::PreviousYear()) as $item) {
      if (Department::IsLanguage($item->class->course->department)) {
	$previousEnrollment[$item->student->id] = $item->class->short();
      }
    }

    $previousClassMap = array();
    foreach (AvailableClass::GetAllYear(Calendar::PreviousYear()) as $id => $item) {
      $previousClassMap[$item->short()]=$id;
    }
    $classMap= array();
    foreach (AvailableClass::GetAllYear(Calendar::CurrentYear()) as $id => $item) {
      $classMap[$item->short()]=$id;
    }

    if ($handle = opendir($directory)) {
      while (false !== ($file = readdir($handle))) {
	if ($file != "." && $file != ".." && preg_match("/xls/i", $file)) {
	  echo "$file\n";
	  $grandTotal += self::ProcessOneExcelFile("$directory/$file", $enrolledStudent, $previousClassMap, $classMap, $previousEnrollment);
	  //	  print ", $i \n"; $i++;
	}
      }
      print "Grand total is $grandTotal\n";
      closedir($handle);
    }
  }

  public static function ProcessOneFile($directory, $file) {
    echo "$directory/$file\n";
    if (($handle = fopen("$directory/$file", "r")) !== FALSE) {
      $evalCategory = fgetcsv($handle, 0, ",");
      $evalHeaading = fgetcsv($handle, 0, ",");
      while (($list = fgetcsv($handle, 0, ",")) !== FALSE) {
	self::WriteStudentAssessment($evalCategory, $evalHeaading, $list);
      }
    }
  }


  public  static function ProcessAllFiles() {
    $directory = self::ReadDir; // figure out a way for the caller to override it
    if ($handle = opendir($directory)) {
      while (false !== ($file = readdir($handle))) {
	if ($file != "." && $file != ".." && preg_match("/csv$/i", $file)) {
	  self::ProcessOneFile($directory, $file);
	}
      }
      closedir($handle);
    }
  }

  public static function TeacherEmailAttendanceAssessment($year) {
    foreach (Teachers::TeacherListYear($year) as $item) {
      $person=$item->person;
      $classshort=$item->class->short();
      if ($item->class->course->department > 3) continue;
      $room=$item->class->room->roomNumber;
      $body = <<<TEACHEREMAILATTENDANCEASSESSMENT

<p>Thank you for your volunteering work at Vidyalaya in 2011-12. We have you teaching class $classshort class in room number $room. Attached please find the progress report template for your students this year.  Review the names of the students on your list, and please let us know if there are any discrepancies. An updated attendance sheet is also attached.

<p> 
Please print out a copy of this template for yourselves and use it to collect the relevant data from now until June 2012.  You should plan to administer the final exam in May, so that you will have sufficient time to fill out the progress report for each student. You will be asked to enter the progress directly onto this template and submit it electronically to us during the first week of June. 

<p>
Please feel free to ask questions or make comments at any time (asmita@vidyalaya.us or reply to this email).  It is best if you do not wait until the last minute to ask questions. 

 <p>Regards,

 <p>Asmita<br />Language and Curriculum Team<br />Vidyalya Inc.

TEACHEREMAILATTENDANCEASSESSMENT;
      $footer = "";
      $subject="Attendance/Assessment Sheet";
      $production=1;

      //      if ($person->id() != "F227") continue;
      if ($production == 0) $subject = "[Test] $subject";
      print "Trying to send email to id " . $person->id() . ", Class - $classshort, subject: $subject\n";
      $mail = Mail::SetupMailUmesh();
      Mail::SetPersonAddress($mail, $person, $production);
      $mail->Subject = $subject;
      $salutation = "<p>Dear " . $person->fullName() . ",";
      $mail->Body = $salutation . $body . $footer;
      $mail->AltBody = "This is the body when user views in plain text format"; //Text Body

      $department=Department::NameFromId($item->class->course->department);
      $filename="/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/attendance/$department/excel/$classshort.xlsx";
      $mail->AddAttachment($filename); // attachment

      $filename="/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/assessment/excel/$classshort.xlsx";
      $mail->AddAttachment($filename); // attachment

      //      continue;

      if(!$mail->Send()) {
	echo "Mailer Error: Person: " . $person->id(). ": " . $mail->ErrorInfo . "\n";
      }  else {
	echo "Message has been sent, Person: " .$person->id() . ":\n";
      }

      // die ("I die\n");
    }
  }


}


?>
