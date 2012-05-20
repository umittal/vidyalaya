<?php

$libDir="../../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";
require_once "HTML/Template/ITX.php";
require_once "$libDir/HtmlFactory.inc";
require_once "$libDir/reports.inc";
require_once "../../MPDF53/mpdf.php";

//require("../../Classes/PHPMailer_v5.1/class.phpmailer.php");


function SetupMail() {

  $email = "Admission2012@vidyalaya.us";
  $name = "Vidyalaya Admissions";

  $mail = new PHPMailer(false); 
  $mail->IsSMTP(); // send via SMTP
  $mail->SMTPAuth = true; // turn on SMTP authentication
  $mail->Username = $email; // SMTP username
  $mail->Password = "Praveen38"; // SMTP password
  $mail->From = $email;
  $mail->FromName = $name;
  $mail->AddReplyTo($email,$name);
  $mail->IsHTML(true); // send as HTML
  $mail->WordWrap = 50; // set word wrap

  return $mail;
}

// set the TO address field of mail to family's email address
function SetFamilyAddress(&$mail, $family) {
  //	$mail->AddAddress("umesh@vidyalaya.us", "Testing post orientation"); 	return;
	
  foreach (explode(";", $family->mother->email) as $toAddress) {
    if (!empty($toAddress)) {
      print "I will send to ". $family->id . ": Mother: " . $family->mother->fullName() . ": " .  $toAddress . "\n";
      $mail->AddAddress($toAddress, $family->mother->fullName());
    }
    
  };

  foreach (explode(";", $family->father->email) as $toAddress) {
    if (!empty($toAddress)) {
      print "I will send to ". $family->id . ": Father: " . $family->father->fullName() . ": " .  $toAddress . "\n";
      $mail->AddAddress($toAddress, $family->father->fullName());
    }
    
  }
	
}


function GetEnrolledStudentCountForFamily($id, $students) {
	$count=0;
	
	foreach ($students as $id2 => $student) {
		if($student->IsEnrolled && $student->family->id == $id) {
			$count++;
		}
	}
return $count;
}

function ExistingFamiliesOld($students) {
  foreach (Family::$objArray as $family) {
    $count = GetEnrolledStudentCountForFamily($family->id, $students);
    if ($count > 0) {
      AnnounceExisting($family);
    }

  }
}

function NewFamilyOrientation($family) {
  $mail = SetupMail();
  SetFamilyAddress($mail, $family);

  $mail->Subject = "Vidyalaya Admission 2011-12 - Invitation to Mandtory Orientation";
  
  // attachments
  $attachDir = "/home/umesh/admissions";
  $mail->AddAttachment("$attachDir/Volunteer2011.pdf"); // attachment
  $mail->AddAttachment("$attachDir/ParticipationAgreement.pdf"); // attachment

  $customizedPdf = "/home/umesh/package2011/Family-". $family->id . ".pdf";
  if (!file_exists($customizedPdf)) die ("customized file $customizedPdf not found, aborting\n");
  $mail->AddAttachment("$customizedPdf"); // attachment
  
//  $draft = "<p>This is a <u>draft</u> message being sent for review. Please send all comments, trivial/substantial. The real mail will come later.";
  $draft="";
  $salutation = "<p>Dear " . $family->parentsName() . ",";
  $mail->Body = $draft . $salutation . file_get_contents("../../vidphp/admission2011/neworientation.html");
  $mail->AltBody = "This is the body when user views in plain text format"; //Text Body

  	print "Family id: $family->id, Name: " . $family->parentsName() . "\n";
  
  //if ($family->id != 402) return;

  if(!$mail->Send()) {
    echo "Mailer Error: " . $mail->ErrorInfo . "\n";
  }  else {
    echo "Message has been sent\n";
  }
	
}

function PostOrientation($family) {
  $production=1;
  $mail =   Mail::SetupMailAdmissions();
  Mail::SetFamilyAddress(&$mail, $family, $production);

  $subject = "Vidyalaya Admission (late) 2011-12 - Family $family->id";
  if ($production == 0) $subject = "[Test] $subject";

  $mail->Subject = $subject;
  
  $salutation = "<p>Dear " . $family->parentsName() . ",";
  $mail->Body = $salutation . file_get_contents("../../vidphp/admission2011/lateAdmission.html");
  $mail->AltBody = "Family: $family->id"; //Text Body
  $mail->AddReplyTo("umesh@vidyalaya.us", "Umesh Mittal");


  // attachments
  $customizedPdf = "/home/umesh/package2011/Family-". $family->id . ".pdf";
  if (!file_exists($customizedPdf)) die ("customized file $customizedPdf not found, aborting\n");
  $mail->AddAttachment("$customizedPdf"); // attachment
  
  $mail->AddAttachment("/home/umesh/Dropbox/Vidyalaya-Management/Admission/Volunteer2011.pdf"); // attachment
  $mail->AddAttachment("/home/umesh/Dropbox/Vidyalaya-Management/Admission/ParticipationAgreement.pdf"); // attachment
  //  $mail->AddAttachment("/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/Layouts/phhslayout2011-12.pdf"); // attachment


  print "Family id: $family->id, Name: " . $family->parentsName() . "\n";
  
  //if ($family->id != 402) return;

  if(!$mail->Send()) {
    echo "Mailer Error: " . $mail->ErrorInfo . "\n";
  }  else {
    echo "Message has been sent\n";
  }
	
}

function NewFamiliesOrientation() {

	foreach (Family::$objArray as $family) {
		if ($family->category->id != FamilyCategory::Waiting) continue;
		NewFamilyOrientation($family);
	}
}

function sendReminders() {
	foreach (FamilyTracker::GetAll() as $tracker) {
		if ($tracker->currentYear != EnumFamilyTracker::enum('pendingRegistration')) continue;

//		if ($tracker->previousYear != EnumFamilyTracker::enum('waitlist')) {
if ($tracker->family==472)
			Reminder($tracker->family, $tracker->previousYear);
//			die ("no reason to live\n");
	//}
	}
}

function Reminder($familyId, $prev) {
	$family = Family::GetItemById($familyId);
	$mail = SetupMail();
	
	SetFamilyAddress($mail, $family);
	//	$mail->AddAddress("voting@vidyalaya.us", $family->father->fullName());

	$mail->Subject = "Admission Reminder, Family- $family->id";
  $salutation = "<p>Dear " . $family->parentsName() . ",";
  
  if ($prev == EnumFamilyTracker::enum('waitlist')) {
    //  	$salutation .= "<p>Priority Date " . $family->priority_date;
  	$body = <<<BODY_WAITING
	  <p>We are sending you this reminder because you had sent a request to join Vidyalaya on $family->priority_date. 
	  We have sent you the registration material but have not heard back from you. 
	  
	  <p> 
	  We will clear our wait list today and any request received after that will be assigned a new priority date. 
	  Please let us know today if you are mailing your forms soon. 

BODY_WAITING;
  } else {
  	$body = <<<BODY_REGD

	  <p>We had sent you an email on April 26 with the registration material. The deadline was May 14 but we have 
	  not received your registration papers. 
	  <p> 
	  We would like to close the registration process now. If for some reason, you are unable to join, please 
	  drop us a line and we will stop sending you reminders. Please also let us know if you are mailing your forms.  

BODY_REGD;
  }
  
  $checklist = <<<CHECKLIST
    <p>Before you submit your registration papers, please make sure that the amount on the check is correct and the 
    family ID is written on it. We request Registration Form (one per family) and Medical Forms (one per student) back. 
    All pages must be signed and dated at the bottom. Please do not forget to put volunteering codes in the Registration Form.

  <p>The completed paperwork can be mailed to PO BOX 775, Morris Plain, NJ 07950.
  <p>Umesh Mittal <br>
  Admissions

CHECKLIST;

  $mail->Body = $salutation . $body. $checklist;
  $mail->AltBody = "This is the body when user views in plain text format"; //Text Body

  	print "Family id: $family->id, Name: " . $family->parentsName() . "\n";
  

  if(!$mail->Send()) {
    echo "Mailer Error: " . $mail->ErrorInfo . "\n";
  }  else {
    echo "Message has been sent\n";
  }
}

function OrientationCheck() {
	$filename = "orientation1.txt";
	$count = array();
	if (($handle = fopen($filename, "r")) !== FALSE) {
		while ((list($familyid,$Check)=
		fgetcsv($handle, 0, ",")) !== FALSE) {
			if (!empty($Check)) continue;
			$family = Family::GetItemById($familyid);
			$currentYear = FamilyTracker::CurrentYearStatus ($family->id);
			empty($count[$currentYear]) ? $count[$currentYear]=1 : $count[$currentYear]++;
			if ($currentYear == 3){
				$mail = SetupMail();
				print "$family->id, " .  $family->parentsName() . "\n";
				continue;
				SetFamilyAddress($mail, $family);
				//$mail->AddAddress("voting@vidyalaya.us", $family->father->fullName());
				$mail->Subject = "Vidyalaya Orientation Followup, Family- $family->id";
				$salutation = "<p>Dear " . $family->parentsName() . ",";
				$body = <<<BODY_WAITING
	  <p>It was nice meeting you at our Orientation on May 17, 2011. Our records indicate that we have not yet received 
	  your registration form. <p> If you have decided 
	  not to enroll, please let us know so we can stop following up with you.
	  <p>
	  Thank you,
	  <p>
	  Umesh MIttal<br>
	  Admissions

BODY_WAITING;

				
				$mail->Body = $salutation . $body;
				$mail->AltBody = "This is the body when user views in plain text format"; //Text Body

				 
				print "$family->id, " .  $family->parentsName() . "\n";
				continue;
				if(!$mail->Send()) {
					echo "Mailer Error: " . $mail->ErrorInfo . "\n";
				}  else {
					echo "Message has been sent\n";
					
				}
			}
		}
		foreach ($count as $key => $value) {
			print EnumFamilyTracker::NameFromId($key) . ", $value\n";
		}

	}
}

class Evaluation {
	const TopDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2010-11/evaluation";
	const ReadDir  = "/home/umesh/Dropbox/Vidyalaya-Roster/2010-11/evaluation/input";
	const WriteDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2010-11/evaluation/report";
	
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
}

class Admission {
  //  const DataFile = "/home/umesh/Dropbox/Vidyalaya-Management/Administration/2011.csv";
  const DataFile = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission/Admission.csv";
  const OrientationFile = "/home/umesh/workspace/vidphp/admission2011/orientation1.txt";
  //  const assesssmentFile = "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/data/assessment.csv";
  const assesssmentFile = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission/assessment.csv";
  const rosterDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-12/roster/";
  public static $students = Array ();

  private static function sendItemEmail($familyId, $cd, $pb, $bag) {
		
    $family = Family::GetItemById($familyId);
    $body = <<<ITEMEMAIL
      <p>As part of the registration process, you ordered the following items. 
      Please collect it from us at Picnic tomorrow, Sunday June 19, 2011. Please also bring a printout of this email, 
      if possible.
	<p>
ITEMEMAIL;

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
      //			self::sendItemEmail($familyId, $totalCD[$familyId], $totalPB[$familyId], $totalBag[$familyId]);
      $grandTotal += $total;
    }
    print "Grand Total = $grandTotal\n";
  }

  // ***********************

  private static function FamilyOpeningDay($familyId, $enrollment) {
    $family = Family::GetItemById($familyId);

    $body = <<<FAMILYOPENINGDAY
      <p> (Please print the attachments in this email and bring it with you tomorrow)
<p>
It gives us immense pleasure to inform you that we are on track to have another great school year. First day of school is Sunday, September 18, 2011 at Parsippany Hills High School, 20 Rita Drive, Parsippany. (attached map)
<p>
ALL parents must come on this day for a mandatory meeting (auditorium). New parents please arrive at 8:30 AM (cafeteria, we will hand out material - print attachment) and continuing families please arrive at 9:00 AM (auditorium). Attached also you will find your room assignment. Your timeliness and patience will be greatly appreciated.
<p>
ALL students must come prepared to school with the following:
<ul>
<li> Prayer books (new kids will receive their free copy)
<li> One inch 3-ring binder
<li> Pencils with erasers
</ul>

<p>
Next week, parents of KG and 1st Grade students will be getting a separate e-mail from their respective teachers about the additional arts/crafts supplies needed. All other course materials are provided to the students throughout the year.
<p>
ALL parents, We will send you a weekly Newsletter every Tuesday night, this will have messages from your language and culture teachers and will help you get prepared for any upcoming events. Also, please check our website www.vidyalaya.us for the latest calendar of events, newsletter, policies, curriculum, contact names, etc
<p>
Safe and Supporting environment: For our kids safety - we strongly recommend that no cars be parked for drop off or pickup in front of the school. Drive very carefully when around the school. As a volunteer organization we support eachother and make it a fun learning experience for our kids. We welcome new volunteers and have a variety of teams to choose from, contact us at SPA@vidyalaya.us and also bring the completed participation agreement.
<p>
Regards,
<p>

SPA (Student and Parent Affairs management team)<br />(sent by: Vasudha Sharma)

FAMILYOPENINGDAY;

    $footer="";
      $production=1;
      $subject = "Vidyalaya Opening Day, Family- $family->id";

      print "Trying to send email to id " . $family->id . "\n";
      //      if ($family->id != 436) return;
      if ($production == 0) $subject = "[Test] $subject";
      $mail = Mail::SetupMailSpa();
      Mail::SetFamilyAddress(&$mail, $family, $production);
      $mail->Subject = $subject;
      $salutation = "<p>Dear " . $family->parentsName() . ",";
      $mail->Body = $salutation . $body . $footer;
      $mail->AltBody = "This is the body when user views in plain text format, opening day $family->id"; //Text Body

      $filename="/home/umesh/student2011/Family-" . $family->id . ".pdf";
      $mail->AddAttachment($filename); // attachment


    $list = null; $done=array();
    foreach($enrollment as $item) {
      if (array_key_exists($item->student->id, $done)) continue;
      if ($item->student->family->id == $familyId) {
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


  private static function admissionConfirmationEmailFamily($familyId, $tuition, $enrollment) {
		
    $family = Family::GetItemById($familyId);

    $body = <<<ADMISSIONEMAIL
<p>
We are writing this email to let you know that the start of Vidyalaya 2011-12 session has been delayed by one week. Our first day now will be September 18, 2011.
<p>
We were planning to start the school at Eastlake Elementary School.  However, earlier this week, concerned authorities advised us that because of the size of our enrollment, we were at serious risk of violations not only for parking but also for crowding in the hallways and gym.  In order to not jeopardize the safety of our families and to minimize the disruption that a potential fire code violation would have on Vidyalaya, we decided to find an alternate venue for the school. 

<p>
We are pleased to inform you that pending some contractual paperwork, we will be moving to Parsippany Hills High School for the current session.
<p>
We apologize for the late notice due to events out of our control and look forward to seeing you all on Sunday September 18, 2011. More emails will be coming with further details. For the record, following students are enrolled from your family

<ol>

ADMISSIONEMAIL;

    $list = null; $done=array();
    foreach($enrollment as $item) {
      if (array_key_exists($item->student->id, $done)) continue;
      if ($item->student->family->id == $familyId) {
	$list .= "<li> " . $item->student->fullName() . "</li>\n";
	$done[$item->student->id] = 1;
      }
    }
    if (is_null($list)) die ("Houston, we have a problem, $familyId\n");

    $closing = <<<CLOSING
      </ol>

      <p>Regards,<p>

CLOSING;
    $subject = "Opening Day 2011-12 notification, Family- $family->id";

    if ($familyId > 472)
      Mail::mailFamilyFromAdmission($family, $subject, $body . $list . $closing, 1);
  }

  public static function admissionConfirmationEmail($year) {
    $enrollment = Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS, $year);
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
      //      self::admissionConfirmationEmailFamily($item->family, $item->tuition, $enrollment);
      //      self::FamilyOpeningDay($item->family, $enrollment);
    }
  }

  // if a family wants to receive email regarding class assignment
  public static function FamilyClassAssignment($year) {
    $enrollment = Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS, $year);
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
      $body = <<<FAMILYUPDATE
	<p>Attached please find confirmation of update in the enrollment of your family. Please let me know if there is any update to the family detail form.
 <p>Please print the student sheet and put it in the student\'s bag for easy reference.
<p>
Regards,
<p>
Vidyalaya Administration<br /> (sent by: Umesh Mittal)

FAMILYUPDATE;
      $footer="";
      $production=1;
      $subject = "Vidyalaya Update, Family- $family->id";
      print "Trying to send email to id " . $family->id . "\n";
      if ($production == 0) $subject = "[Test] $subject";
      $mail = Mail::SetupMailSpa();
      Mail::SetFamilyAddress(&$mail, $family, $production);
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

  public static function TeacherEmail($year) {
    foreach (Teachers::TeacherListYear($year) as $item) {
      $person=$item->person;
      $classshort=$item->class->short();
      $room=$item->class->room->roomNumber;
      $body = <<<TEACHEREMAIL
<p>(please print attachments as you consider necessary)
<p>Thank you for volunteering to teach  $classshort class in room number $room at Parsippany Hills High School, 20 Rita Drive, Morris Plains (<a href="http://maps.google.com/maps?daddr=Parsippany-Troy+Hills+Township,+New+Jersey+(Parsippany+Hills+High+School)&hl=en&ll=40.861323,-74.456422&spn=0.002836,0.005681&sll=40.861371,-74.456422&sspn=0.002739,0.005681&geocode=CYyExFx_jj4jFXV_bwIdj-GP-yEx9DwpNb5Kzg&vpsrc=0&mra=mift&t=h&z=18">Directions</a>) starting September 18, 2011.  

Please print attached  layout of school with your class information and bring it with you Sunday. Please be advised that the schedule for tomorrow will be as follows:  
<ul>
<li>8:30 New Parents Arrive
<li>9:00 All Continuing Parents/Teachers Arrive
<li>9:25 Prayer/Assembly Session
<li>10:00 Language Class
<li>11:00 Culture Class
<li>11:30 KG Dismissal
<li>11:45 All Other Classes Dismissal
</ul>

<p>
Please arrive at school as early as possible so you have the opportunity to find your classroom and become familiar with the school layout prior to the Prayer/Assembly session.  New parents will arrive at 8:30 and Prayer/Assembly will start at 9:25am sharp.
<ul>
<li>Language teachers:</li> At the conclusion of Assembly/Prayer class, please escort your students from the Auditorium to your classroom.  At the end of the language class, please stay in your room until the Culture teacher arrives.  Students should never be left unattended in the classroom at any time.
<p>
<li>Culture teachers:</li> Please reach your designated classroom 5 minutes before the class begins to monitor the students, and stay in the classroom until all students have vacated at the end of class. 
</ul>
<p>
The coordinators for specific teams may send additional communications as needed. We have attached the attendance sheet and a roster of students for your class. Please print these attachments and bring them with you.  
<p>Looking forward to another great year,

<p>(The above  message is sent by Umesh Mittal  on behalf of various team leaders for Teaching Volunteers.)


TEACHEREMAIL;

      $footer = "";
      $subject="Welcome to Vidyalaya 2011";
      $production=1;

     print "Trying to send email to id " . $person->id() . "\n";
      if ($person->id() != "F227") continue;
      if ($production == 0) $subject = "[Test] $subject";
      $mail = Mail::SetupMailUmesh();
      Mail::SetPersonAddress($mail, $person, $production);
      $mail->Subject = $subject;
      $salutation = "<p>Dear " . $person->fullName() . ",";
      $mail->Body = $salutation . $body . $footer;
      $mail->AltBody = "This is the body when user views in plain text format"; //Text Body

      $filename="/home/umesh/student2011/Teacher-" . $person->id() . ".pdf";
      $mail->AddAttachment($filename); // attachment

      $department=Department::NameFromId($item->class->course->department);
      $filename="/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/attendance/$department/excel/$classshort.xlsx";
      $mail->AddAttachment($filename); // attachment
      $filename="/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/roster/word/ClassWide/$classshort.docx";
      $mail->AddAttachment($filename); // attachment

      if(!$mail->Send()) {
	echo "Mailer Error: Person: " . $person->id(). ": " . $mail->ErrorInfo . "\n";
      }  else {
	echo "Message has been sent, Person: " .$person->id() . ":\n";
      }
    }
  }

  public static function VolunteerEmail($year) {
    foreach (Volunteers::GetAllYear($year) as $item) {
      $person=$item->person;
      $body = <<<VOLUNTEEREMAIL
<p>
	<p>On behalf of Board of Trustees, Vidyalaya would like to thank you for volunteering at Vidyalaya. This year over 200 families have registered their kids to come to school. We appreciate your efforts to help us manage the school.
<p>Attached please find your personal information that we have in our database along with the Participation Agreement signed by all participants. We request you to read the participation agreement,  mark any changes in the personal information sign and date  it at the bottom and bring it with you when you come to our facilities.

<p>Regards,

<p>Vidyalaya Inc.<br />(sent by: Umesh Mittal)<p>

<p>ps: Please see the Google satellite image of the school below

<img src="http://www.vidyalaya.us/modx/assets/images/phhs-maps.jpg"></img>

VOLUNTEEREMAIL;
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
	
	
  public static function Validation($year) {
    print "1. Validate Registered Parents between enrollment and familytracker\n";
    // get list of families from enrollment
    $enrolledFamily = array();
    foreach(Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS, $year) as $item) {
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

    print "todo 5. validate student language preference and language assignment are aligned\n";
  }

  public static function PrintVolunteers($year) {
    $pdfDir = "/home/umesh/student2011";
    foreach (Volunteers::GetAllYear($year) as $item) {
      $full = $item->person->fullName();
      $openingHtml = <<< AGREEMENT
	<p>As part of providing Voluntary Services to Vidyalaya, I have read the Family Participation Agreement of Vidyalaya dated April 2011. 
	I hereby agree to the Terms of the agreement and the Student Handbook.
<table>
<thead>
<tr><th class="name">Name</th><th class="phone">Date</th><th class="name">Signature</th></tr>
</thead>
<tbody>
	<tr><td>$full</td><td style="text-align: right;" class=input>&nbsp;/2011</td><td class=input>&nbsp;</td></tr>
</tbody>
</table> 
<div style='font-size:50%'><p>

Note: Please print, sign, date and bring this form to opening day, Septmber 18, 2011
 Parsippany Hills High School. Review and mark any update to the personal information.</div>

AGREEMENT;
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

    $students = array(); $done = array();
    foreach(Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS, $year) as $item) {
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

    $cashfile="/home/umesh/Dropbox/Vidyalaya-Management/Administration/2011.csv";
    if (($handle = fopen($cashfile, "r")) !== FALSE) {
      $header = fgetcsv($handle, 0, ",");
      $header = fgetcsv($handle, 0, ",");
      $i=1;
      $totalTuition=0;

      while ((list($family,$Check , $base, $new , $DVD , $CD , $PB , $Bag , $Ann , $Total ,$foo, $ch1 , $ch2 , $ch3 )
	      = fgetcsv($handle, 0, ",")) !== FALSE) {
	if (!empty($family)) {

	  $new = str_replace('$', "",$new);
	  $DVD = str_replace('$', "",$DVD);
	  $CD = str_replace('$', "",$CD);
	  $PB = str_replace('$', "",$PB);
	  $Bag = str_replace('$', "",$Bag);

	  //	  if ($family==473) print "473:  DVD\n";
	  if (empty($totalFamily[$family])) {
	    $totalFamily[$family] =0;
	    $totalnew[$family] = 0;
	    $totalDVD[$family] = 0;
	    $totalCD[$family] = 0;
	    $totalPB[$family] = 0;
	    $totalBag[$family] = 0;
	  }
	  $totalFamily[$family] +=$new+$DVD+$CD+$PB+$Bag;
	  $totalnew[$family] += $new/50;
	  $totalDVD[$family] += $DVD/10;
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



    fwrite($fp, "Fam, #, Stud, Teach, New, DVD, CD, PB, Bag, Parents\n");

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

      $value=$totalDVD[$item] == 0 ?  "": $totalDVD[$item];
      $csv[]=$value;
      $openingHtml .= "<tr><td>DVD</td><td>$value</td></tr>\n";

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

            $pdf = PrintFactory::HtmlToPdf(PrintFactory::GetHtmlForFamilyDetail($familyobj) . $openingHtml);
            file_put_contents("$fileName", $pdf);
    }
    
    fflush($fp);
    fseek($fp, 0);
    $filename = self::rosterDir . "families.csv";
    file_put_contents("$filename", stream_get_contents($fp));
    fclose($fp);

    print "Total Families = " . count($familyarray) . ", Teaching families = " . count($teachers) . "\n";
  }

  private static function AdultLanguageMail($family) {
    //    if ($family->id != 43) return;
    $footer="<p>Regards,<p>Language Curriculum Team<br />(sent on behalf of : Asmita Mistry)</p>";
    $production=1;
    $subject = "Survey: Language Class for Adults";
    print "Trying to send email to id " . $family->id . "\n";
    $body = file_get_contents("adult.inc");
    if ($production == 0) $subject = "[Test] $subject";
    $mail = Mail::SetupMailSpaReplyAsmita();
    Mail::SetFamilyAddress(&$mail, $family, $production);
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

    //die ("only one");

  }

  public static function AdultLanguage() {
    $status = array();
    // registered families
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS, 2011) as $enrollment) { 
      $familyId = $enrollment->student->family->id;
      if (!array_key_exists($familyId, $status)) {
	$status[$familyId] = 0;
	self::AdultLanguageMail($enrollment->student->family);
      }
    }

    foreach (Volunteers::GetAllYear(2011) as $item) { // volunteers
      $familyId = $item->person->home->id;
      if (!array_key_exists($familyId, $status)) {
	$status[$familyId] = 0;
	self::AdultLanguageMail($item->person->home);
      }
    }
  }

  private static function AnnounceExisting($family) {

    $production=1;
    $mail =   Mail::SetupMailAdmissions();
    Mail::SetFamilyAddress(&$mail, $family, $production);

    $subject = "Vidyalaya Admission 2012-13, Family $family->id";
    if ($production == 0) $subject = "[Test] $subject";
    $mail->Subject = $subject;

    // attachments
    //    $customizedPdf = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission/pdf/Family-". $family->id . ".pdf";
    //    $mail->AddAttachment("$customizedPdf"); // attachment
    //   $mail->AddAttachment("/home/umesh/Dropbox/Vidyalaya-Management/Admission/Volunteer2011.pdf"); // attachment
    //   $mail->AddAttachment("/home/umesh/Dropbox/Vidyalaya-Management/Admission/ParticipationAgreement.pdf"); // attachment
  
    print "Family id: $family->id, Name: " . $family->parentsName() . " $subject\n";

    $salutation = "<p>Dear " . $family->parentsName() . ",";
    $mail->Body = $draft . $salutation . file_get_contents("../../vidphp/admission2011/reminder-all.html");
    $mail->AltBody = "Family: $family->id"; //Text Body

    //    return;
    if(!$mail->Send()) {
      echo "Mailer Error: " . $mail->ErrorInfo . "\n";
    }  else {
      echo "Message has been sent\n";
    }

  }

  public static function ExistingFamilies() {
    $i=1;
    foreach (FamilyTracker::GetAll() as $tracker) {
      if ($tracker->family <  461) continue;
      //      if ($tracker->previousYear != EnumFamilyTracker::registered) continue;
      if ($tracker->currentYear != EnumFamilyTracker::pendingRegistration) continue;
      print $tracker->family . ", previous: " . EnumFamilyTracker::NameFromId($tracker->previousYear) . ", current: " 
	. EnumFamilyTracker::NameFromId($tracker->currentYear) . "\n";
      $family = Family::GetItemById($tracker->family);
      print "-->$i. Family id: $family->id, Name: " . $family->parentsName() . "\n";
      $i++;
      self::AnnounceExisting($family);
      
    }
    return;
  }

  private static function AnnounceOrientation($family) {
    $production=1;
    $mail =   Mail::SetupMailAdmissions();
    Mail::SetFamilyAddress(&$mail, $family, $production);

    $subject = "Vidyalaya Admission 2012-13, Family $family->id";
    if ($production == 0) $subject = "[Test] $subject";
    $mail->Subject = $subject;
  

    // attachments
    $customizedPdf = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission/pdf/Family-". $family->id . ".pdf";
    $mail->AddAttachment("$customizedPdf"); // attachment
    $mail->AddAttachment("/home/umesh/Dropbox/Vidyalaya-Management/Admission/Volunteer2011.pdf"); // attachment
    $mail->AddAttachment("/home/umesh/Dropbox/Vidyalaya-Management/Admission/ParticipationAgreement.pdf"); // attachment
  
    print "Family id: $family->id, $subject; Name: " . $family->parentsName() . "\n";

    //    return;
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



}


class TwoYearEnrollment {
	public $language=null;
	public $languageLevel=null;
	public $languageSection=null;

	public $cultureLevel=null;
	public $cultureSection=null;
	
	public function updateFromEnrollment($enrollment) {
		$department = $enrollment->class->course->department;

		if ($department == Department::Culture) {
			$this->cultureLevel = $enrollment->class->course->level;
			$this->cultureSection = $enrollment->class->section;
				
		} else {
			$this->language =  $enrollment->class->course->department;
			$this->languageLevel  = $enrollment->class->course->level;
			$this->languageSection = $enrollment->class->section;
				
		}
	}

	public function updateFromStudent($student) {
		$this->language = $student->languagePreference;
		$this->cultureLevel = $student->GradeAt(Calendar::RegistrationSession);
		if ($this->cultureLevel > 9) $this->cultureLevel = 9;
	}
	
	public function csv($fields) {
//		$fields = Array();
		$fields[] = Department::NameFromId($this->language);
		$fields[] = $this->languageLevel;
		$fields[] = $this->languageSection;
		$fields[] = $this->cultureLevel;
		$fields[] = $this->cultureSection;
//		return implode (", ", $fields);
	}
}


class TwoYearLayout {
  public $previousYear = null;
  public $thisYear = null;
  public $status=null;
  public $assessment=null;

  const Leaving = "Leaving";
  const NewStudent = "New";
  const Orientation = "Orientation"; // Family attended Orientation
  const Continuing = "Continuing";
  const Change = "Change";
	
  const LeavingStudentsFile = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission/leaving.csv";
  const EnrolledStudentsFile = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission/enrolled.csv";
	
  private static $objArray = Array ();
  private static $orientation = Array();
	


  private static function firstTimeCall() {
    if (!empty(self::$objArray)) return;
    self::currentYearFromDatabase();
    self::currentYearFromFile();
    self::prevYearFromDatabase();
    self::orientationList();
    self::loadAssessment();
    self::updateStatus();
  }

  public static function GetItemById($key) {
    self::firstTimeCall();
    return self::$objArray[$key];
  }
	
  public static function GetAll() {
    self::firstTimeCall();
    return self::$objArray;
  }
	
  private static function updateStatus() {
    foreach (self::$objArray as $studentid => $twoyear) {
      if ($twoyear->previousYear->language == null) {
	if ($twoyear->thisYear->language != null) {
	  $twoyear->status = self::NewStudent;
	  $studnet = Student::GetItemById($studentid);
	  $familyId = $studnet->family->id;
	  if (!empty(self::$orientation[$familyId])) {
	    $twoyear->status = self::Orientation;
	  }
	}
      } else {
	if ($twoyear->thisYear->language == null) {
	  $twoyear->status = self::Leaving;
	  continue;
	} 
	if ($twoyear->previousYear->language == Department::Kindergarten) {
	  $twoyear->status =self::Continuing;
	} else {
	  $twoyear->status = $twoyear->previousYear->language == $twoyear->thisYear->language ? 
	    self::Continuing : self::Change;
	}
      }
    }
		
  }

  private static function prevYearFromDatabase () {
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS,2011) as $enrollment) {
      if (empty(self::$objArray[$enrollment->student->id])) self::$objArray[$enrollment->student->id] = new TwoYearLayout();
      $twoyear = self::GetItemById($enrollment->student->id);
      $twoyear->previousYear->updateFromEnrollment($enrollment);
    }
  }
	
  private static function currentYearFromDatabase () {
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS,2012) as $enrollment) {
      if (empty(self::$objArray[$enrollment->student->id])) self::$objArray[$enrollment->student->id] = new TwoYearLayout();
      $twoyear = self::GetItemById($enrollment->student->id);
      //print "setting this year value for $enrollment->student->id\n";
      $twoyear->thisYear->updateFromEnrollment($enrollment);
    }
  }
	

  private static function updateNewStudent($studentId) {
    $student = Student::GetItemById($studentId);
    if (empty($student)) print "student not found for id ==$studentId==";
    if (empty(self::$objArray[$studentId])) {
      //      print "I am here for student $studentId, found in current year from file\n";
      self::$objArray[$studentId] = new TwoYearLayout();
      $twoyear = self::GetItemById($studentId);
      $twoyear->thisYear->updateFromStudent($student);
    }
  }

  private static function currentYearFromFile() {
    //    $filename = "/home/umesh/Dropbox/Vidyalaya-Management/Administration/2011.csv";
    $filename = Admission::DataFile;
    if (($handle = fopen($filename, "r")) !== FALSE) {
      $header = fgetcsv($handle, 0, ",");
      $header = fgetcsv($handle, 0, ",");
      $i=1;
      $totalTuition=0;
      $done=array();
      $fileTuition = array();
      while ((list($familyId,$Check , $base, $new , $adj , $CD , $PB , $Bag , $date , $total ,$foo, $ch1 , $ch2 , $ch3 )
	      = fgetcsv($handle, 0, ",")) !== FALSE) {
	if (!empty($familyId)) {
	  if (!empty($ch1)) self::updateNewStudent($ch1);
	  if (!empty($ch2)) self::updateNewStudent($ch2);
	  if (!empty($ch3)) self::updateNewStudent($ch3);
	}
      }
    }
  }
	
  private static function loadAssessment() {
    return;
    $filename = Admission::assesssmentFile;
    $count = array();
    if (($handle = fopen($filename, "r")) !== FALSE) {
      while ((list($studentId,$recommendation)=
	      fgetcsv($handle, 0, ",")) !== FALSE) {
	if (empty(self::$objArray[$studentId])) {
	  print "Studnet $studentId not found in twoyear array, look into it\n";
	}
	$twoyear = self::GetItemById($studentId);
	$twoyear->assessment = $recommendation;
      }
    }
  }

  private static function orientationList() {
    return;
    $filename = Admission::OrientationFile;
    $count = array();
    if (($handle = fopen($filename, "r")) !== FALSE) {
      while ((list($familyid,$Check)=
	      fgetcsv($handle, 0, ",")) !== FALSE) {
	self::$orientation[$familyid] = 1;
      }
    }

    // update the static array
  }
	
  public static function twoYearCsv () {
    $enrolledHandle = fopen(self::EnrolledStudentsFile, "w") or die ("cannot open file " . self::EnrolledStudentsFile);
    $leavingHandle = fopen(self::LeavingStudentsFile, "w") or die ("cannot open file " . self::LeavingStudentsFile);
    self::firstTimeCall();
    foreach (self::$objArray as $studentid => $twoYear) {
      $student = Student::GetItemById($studentid);
      $familyid = $student->family->id;
      $currFamilyStatus = EnumFamilyTracker::NameFromId(FamilyTracker::CurrentYearStatus($familyid));
      $fileHandle = null;
				
      $fields = Array();
      $fields[] = $studentid;
      $fields[] = $familyid;
      //			$fields[] = $twoYear->previousYear->csv();
				
      if ($twoYear->status != self::Leaving) {
	$fileHandle = $enrolledHandle;

	//				$fields[] = $twoYear->thisYear->csv();
	$twoYear->thisYear->csv(&$fields);
	$fields[] = $twoYear->status;
	$fields[] = $twoYear->assessment;
      } else {

	$fileHandle = $leavingHandle;
	$twoYear->previousYear->csv(&$fields);
	$fields[] = $currFamilyStatus;
      }
      $fields[] = $student->fullName();
				
      if ($twoYear->status == self::Continuing) $twoYear->previousYear->csv(&$fields);
				
      fputcsv($fileHandle, $fields);
    }

    fclose($enrolledHandle); fclose($leavingHandle);
  }

  public static function assignClass() {
    AvailableClass::CreateClassCourseCatalog();
    self::firstTimeCall();
    $count = 0;
    foreach (self::$objArray as $studentid => $twoYear) {
      $student = Student::GetItemById($studentid);
				
      if ($twoYear->thisYear->languageLevel != null) continue;
      if ($twoYear->status == self::Leaving) continue;
				
      $count++;
				
      if($student->GradeAt(Calendar::RegistrationSession) == "KG") {
	$class = AvailableClass::findAvailableClass(Calendar::RegistrationYear(), Department::Kindergarten, 0, null);
	if ($class ==null) {
	  print "Error:KG not found for year " .Calendar::RegistrationYear() . "\n";
	} else {
	  print "insert into Enrollment set student = $student->id, availableClass = $class->id;\n";
	}
	continue;
      }
				
      // all others require culture and language. Let us do culture first
      $level = $twoYear->thisYear->cultureLevel;
      $class = AvailableClass::findAvailableClass(Calendar::RegistrationYear(), Department::Culture, $level, null);
      if ($class ==null) {
	print "Error:Culture level $level not found for year " .Calendar::RegistrationYear() . "\n";
      } else {
	print "insert into Enrollment set student = $student->id, availableClass = $class->id;\n";
      }
			
      // for language, we have new, change and continuing
			
      $department = $twoYear->thisYear->language;
      if ($twoYear->status == self::Continuing) {
	//assign same level as last year
	$level = $twoYear->previousYear->languageLevel;;
	if ($level == 0) $level=1;
      } else {
	$level = 1; 
      }
      $class = AvailableClass::findAvailableClass(Calendar::RegistrationYear(), $department, $level, null);
      if ($class ==null) {
	print "Error:Department $department,  level $level not found for year " .Calendar::RegistrationYear() . "\n";
      } else {
	print "insert into Enrollment set student = $student->id, availableClass = $class->id;\n";
      }
			
				
				
      print "$count: $studentid needs to be enrolled";
      print "\n";
    }
  }

  public static  function checkFeePaid() {
    self::firstTimeCall();
		
    $feeRequired = Array();
    $newRegFee = Array();
		
    foreach (self::$objArray as $studentid => $twoYear) {
      $student = Student::GetItemById($studentid);
      $familyid = $student->family->id;
			
      if ($twoYear->status != self::Leaving) {
	if(empty($feeRequired[$familyid])) {
	  $feeRequired[$familyid] = 450;
	  $newRegFee[$familyid] = 0;
	} else {
	  $feeRequired[$familyid] = 550;
	}
				
	if ($twoYear->status == self::NewStudent || $twoYear->status == self::Orientation ) $newRegFee[$familyid] += 50; 
      }
    }
		
		
    // check if fee is paid fully
    foreach ($feeRequired as $familyid=>$require) {
      $require += $newRegFee[$familyid];
      $tracker = FamilyTracker::GetItemById($familyid);
      $family = Family::GetItemById($familyid);
      if ($require != $tracker->tuition) {
	print "Family: $familyid, Require: $require, Paid: $tracker->tuition, " . $family->parentsName() . "\n";
      }
    }
		
  }
	
  private function __construct() { 
    $this->previousYear = new TwoYearEnrollment();
    $this->thisYear = new TwoYearEnrollment();
    $this->status = "unknown";
  }
}

//Admission::Payment2012(); exit();
//Admission::InviteNew(); exit();
//Admission::ExistingFamilies(); exit();
//Admission::AdultLanguage(); exit();

//Teachers::AddTeacher(79, "hetalapurva@gmail.com", 0) ; exit();
//Admission::VolunteerEmail(2011);exit();
//Admission::TeacherEmail(2011);exit();
//Admission::TeacherEmailAttendanceAssessment(2011);exit();


//Admission::OpeningDay(2011); exit();
//Admission::PrintVolunteers(2011); exit();

//Admission::admissionConfirmationEmail(2011);exit(); // has thing in there to decide which email to send
//Admission::FamilyClassAssignment(2011); exit(); //to resend class assignment email to parents
//Admission::itemDelivery(); exit();


//Evaluation::ProcessAllFiles(); exit();


//FamilyTracker::loadPayments();exit();
//FamilyTracker::UpdateFamilyTracker(); exit();
TwoYearLayout::assignClass(); exit();
///TwoYearLayout::checkFeePaid(); exit();
//TwoYearLayout::twoYearCsv(); exit();
//Admission::Validation(2011); exit();



//OrientationCheck(); exit();

//sendReminders();

if (php_sapi_name() != "cli") die ("only cli allowed here\n");
if( $_SERVER["argc"] < 2) die ("no arguments specified\n");
$out=parseArgs($_SERVER["argv"]);
if (array_key_exists('f', $out)) 
   die("you want me to execute " . $out['f'] . "\n");
if (array_key_exists("function", $out)) 
  die("you want me to execute " . $out['function'] . "\n");
//die("I have nothing to do\n");


//$students = GetAllData();

//NewFamiliesOrientation();
$entry = GetSingleIntArgument();
PostOrientation(Family::GetItemById($entry));

?>
