<?php

$libDir="../../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";

require("../../Classes/PHPMailer_v5.1/class.phpmailer.php");

/** PHPExcel */
//require_once  "PHPExcel/PHPExcel.php";
require_once  "PHPExcel/PHPExcel/IOFactory.php";
//require_once  'PHPExcel/PHPExcel/Writer/Excel2007.php';


function SetupMail() {

  $email = "Admission2011@vidyalaya.us";
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

function AnnounceExisting($family) {
  $mail = SetupMail();
  SetFamilyAddress($mail, $family);

  $mail->Subject = "Vidyalaya Admission 2011-12";
  
  // attachments
  $attachDir = "/home/umesh/admissions";
  $mail->AddAttachment("$attachDir/Volunteer2011.pdf"); // attachment
  $mail->AddAttachment("$attachDir/ParticipationAgreement.pdf"); // attachment
  
  $customizedPdf = "/home/umesh/package2011/Family-". $family->id . ".pdf";
  $mail->AddAttachment("$customizedPdf"); // attachment
  
//  $draft = "<p>This is a <u>draft</u> message being sent for review. Please send all comments, trivial/substantial. The real mail will come later.";
	$draft="";
  $salutation = "<p>Dear " . $family->parentsName() . ",";
  $mail->Body = $draft . $salutation . file_get_contents("../../vidphp/admission2011/announce-existing.html");
  $mail->AltBody = "This is the body when user views in plain text format"; //Text Body

//  if ($family->id != 43) return;

  if(!$mail->Send()) {
    echo "Mailer Error: " . $mail->ErrorInfo . "\n";
  }  else {
    echo "Message has been sent\n";
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

function ExistingFamilies($students) {
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
  $mail = SetupMail();
  SetFamilyAddress($mail, $family);

  $mail->Subject = "Vidyalaya Admission 2011-12 - Family $family->id";
  
  // attachments
  $customizedPdf = "/home/umesh/package2011/Family-". $family->id . ".pdf";
  if (!file_exists($customizedPdf)) die ("customized file $customizedPdf not found, aborting\n");
  $mail->AddAttachment("$customizedPdf"); // attachment
  
  $attachDir = "/home/umesh/admissions";
  $mail->AddAttachment("$attachDir/Volunteer2011.pdf"); // attachment
  $mail->AddAttachment("$attachDir/ParticipationAgreement.pdf"); // attachment

  //  $draft = "<p>This is a <u>draft</u> message being sent for review. Please send all comments, trivial/substantial. The real mail will come later.";
  $draft="";
  $salutation = "<p>Dear " . $family->parentsName() . ",";
  $mail->Body = $draft . $salutation . file_get_contents("../../vidphp/admission2011/postorientation.html");
  $mail->AltBody = "Family: $family->id"; //Text Body

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

class Mail {
	private static function SetupMailCommon($email, $name, $password) {
		$mail = new PHPMailer(false);
		$mail->IsSMTP(); // send via SMTP
		$mail->SMTPAuth = true; // turn on SMTP authentication
		$mail->Username = $email; // SMTP username
		$mail->Password = $password; // SMTP password
		$mail->From = $email;
		$mail->FromName = $name;
		$mail->AddReplyTo($email,$name);
		$mail->IsHTML(true); // send as HTML
		$mail->WordWrap = 50; // set word wrap

		return $mail;
		
	}
	private static function SetupMailAdmissions() {
		return self::SetupMailCommon("Admission2011@vidyalaya.us", "Vidyalaya Admissions", "Praveen38");
	}
	
	private static function SetupMailSPA() {
		return self::SetupMailCommon("spa@vidyalaya.us", "Student and Parent Affairs", "vasudha");
	}
	
	private static function SetupMail() {

		$email = "Admission2011@vidyalaya.us";
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
	private static function SetFamilyAddress(&$mail, $family, $production) {
		if ($production == 0 ) {
			$mail->AddAddress("voting@vidyalaya.us", "Testing email");
			return;
		}


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

	public static function  mailFamilyFromAdmission($family, $subject, $body, $production) {
		$footer = "<p>Umesh Mittal<br>Admissions</p>";
		self::mailFamily($family, $subject, $body, $production, $footer);
	}

	public static function  mailFamily($family, $subject, $body, $production, $footer) {
		if ($production == 0) $subject = "[Test] $subject";

		$mail = self::SetupMail();
		self::SetFamilyAddress($mail, $family, $production);

		$mail->Subject = $subject;
		$salutation = "<p>Dear " . $family->parentsName() . ",";
		$mail->Body = $salutation . $body . $footer;

		$mail->AltBody = "This is the body when user views in plain text format"; //Text Body
		
		if(!$mail->Send()) {
			echo "Mailer Error: Family: $family->id: " . $mail->ErrorInfo . "\n";
		}  else {
			echo "Message has been sent, Family: $family->id:\n";
		}
	}
	
	public static function  mailEvaluation($studentId, $html, $production) {
		$student = Student::GetItemById($studentId);
		$family = $student->family;
		
		$footer = "<p>Asmita Mistry<br>On behalf of the Language Curriculum Team</p>";	
		$subject = "Language Evaluation 2010-11: " . $student->fullName();
		if ($production == 0) $subject = "[Test] $subject";

		$mail = self::SetupMailSPA();
		self::SetFamilyAddress($mail, $family, $production);
		$mail->Subject = $subject;
		$salutation = "<p>Dear " . $family->parentsName() . ",";
		
		$name = $student->fullName();
		
		$body = <<<EOT
		
		<p>Attached please find the language evaluation report for $name for the 2010-11 school year. 
		Also please find the class assignment for the upcoming school year if you have already registered. 
		We also want to take this opportunity to thank the teachers for their voluntary contribution.

		<p>Should you have any questions or concerns, please respond to this email at the 
		earliest. All placement issues will be addressed before the school opens in September. 
		Please forward any questions or comments to spa@vidyalaya.us.  The SPA team will 
		coordinate with the Language Curriculum Team to address your concerns.

<p>Have a great summer!

<p>Regards,
EOT;
		
		$mail->Body = $salutation . $body . $footer;
		

		$filename = "/tmp/evalution-" . $studentId. ".pdf";
		file_put_contents($filename, HtmlToPdf($html));
		  $mail->AddAttachment($filename); // attachment
		
		$mail->AltBody = "This is the body when user views in plain text format"; //Text Body
		
		if(!$mail->Send()) {
			echo "Mailer Error: Family: $family->id: " . $mail->ErrorInfo . "\n";
		}  else {
			echo "Message has been sent, Family: $family->id:\n";
		}
		
		unlink($filename);
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
				self::$html .= "<tr><td class=left> $header[$i] </td><td> <i>$evaluation</i>   </td></tr>\n";
			}
		}
		
		self::$html .= "</table>\n";
		self::$html .= "</body>\n</html>\n";
		
		
		self::PrintThreeFiles($studentId);
		if ($studentId != 1452) return;
		Mail::mailEvaluation($studentId, self::$html, 0);

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
	const DataFile = "/home/umesh/Dropbox/Vidyalaya-Management/Administration/2011.csv";
	const OrientationFile = "/home/umesh/workspace/vidphp/admission2011/orientation1.txt";
	const assesssmentFile = "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/data/assessment.csv";

	const BaseDir = "/home/umesh/Dropbox/Vidyalaya-Roster";

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

	public static function itemDelivery() {
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
	
	public static function classParentsEmail($class) {
	  foreach (Enrollment::GetFamilies($class) as $family) {
	    print "$family->id, " . $family->mother->email. ", " . $family->father->email . "\n";
#	    print "$family->id, ". "$family->mother->email," . " $family->father->email" . " \n";
	  }

	}
	
	private static $rosterid = null;
	private static $rosterfh = null;

	private static function RosterStudent($student) {
		fwrite(self::$rosterfh,  "\n" . self::$rosterid++ . ",  $student->id, " .  $student->fullName() . ", " . $student->GenderName() );
		fwrite(self::$rosterfh, "(Age: " . (int)$student->AgeAt(Calendar::CurrentSession) . ", Grade: ". $student->Grade() . "), " . $student->CellEmail() . "\n");
		fwrite(self::$rosterfh, "   Home, " . $student->family->phone . ", " .  $student->family->address->city . "\n");
		
		fwrite(self::$rosterfh, "   Mother, " . $student->family->mother->fullName() . ", " . $student->family->mother->WorkCellEmail() . "\n");
		fwrite(self::$rosterfh, "   Father, " . $student->family->father->fullName() . ", " . $student->family->father->WorkCellEmail() . "\n");
	}
	
	private static function RosterClass($class) {
		$filename = "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/roster/txt/" .  $class->short() . ".txt";
		self::$rosterfh = fopen($filename, "w");
		echo "$filename\n";
		//fwrite(self::$rosterfh,  "\n**********************\n");
		fwrite(self::$rosterfh, "Class: " . $class->short() . "\n");
		fwrite(self::$rosterfh, "Room: " . "Facility: " . "\n");
		fwrite(self::$rosterfh, "Teachers: " . "\n"); 
		foreach (Enrollment::GetEnrollmentForClass($class->id)  as $item) {
			self::RosterStudent ($item->student);
		}
		fclose(self::$rosterfh);
	}
	
	public static function Roster($year) {
		foreach (AvailableClass::GetAllYear($year) as $class) {
			self::$rosterid = 1;
			self::$rosterfh = null;
			self::RosterClass($class);
		}
	}
	
	public static function RosterFromFile ($filename) {
		self::$rosterid = 1;
		self::$rosterfh = fopen("$filename.out", "w");
		if (($handle = fopen($filename, "r")) !== FALSE) {
			while ((list($studentid, $rest )= fgetcsv($handle, 0, "\t")) !== FALSE) {
				if (empty($studentid)) continue;
				$student = Student::GetItemById($studentid);
				if (empty($student)) {
					print "student not found for $studentid\n";
				} else {
					self::RosterStudent ($student);
				}
				
			}
		}
	}
	
	private static function AttendanceSheetFill($class) {
		$inputFileName = "/tmp/attendance2011.xlsx";
		$activeSheetIndex=0;
		$row =4;

		/**  Identify the type of $inputFileName  **/
//		$inputFileType =PHPExcel_IOFactory::identify($inputFileName);
		/**  Create a new Reader of the type that has been identified  **/
//		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		/**  Load $inputFileName to a PHPExcel Object  **/
//		$objPHPExcel = $objReader->load($inputFileName);
		$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
		$objPHPExcel->setActiveSheetIndex($activeSheetIndex);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->setShowGridlines(false);
		$objPHPExcel->getActiveSheet()->setRightToLeft(FALSE);
		
//		$objPHPExcel->getProperties()->setCreator("Umesh Mittal");
//		$objPHPExcel->getProperties()->setLastModifiedBy("Umesh Mittal");
//		$objPHPExcel->getProperties()->setTitle("Students");
//		$objPHPExcel->getProperties()->setSubject("Vidyalaya Students");
//		$objPHPExcel->getProperties()->setDescription("List of Vidyalaya Students by Classes");
		
		

		$objPHPExcel->getActiveSheet()->setTitle($class->short());
		$objPHPExcel->getActiveSheet()->getCell("B2")->setValue($class->short());
			
		$objPHPExcel->getActiveSheet()->getRowDimension("1")->setVisible(TRUE);
		$objPHPExcel->getActiveSheet()->getRowDimension("2")->setVisible(TRUE);
		$objPHPExcel->getActiveSheet()->getRowDimension("3")->setVisible(TRUE);
		foreach(Enrollment::GetEnrollmentForClass ($class->id) as $item) {
			$cellValue=sprintf("B%d", $row);
			$objPHPExcel->getActiveSheet()->getCell($cellValue)->setValue($item->student->id);
			$cellValue=sprintf("C%d", $row);
			$objPHPExcel->getActiveSheet()->getCell($cellValue)->setValue($item->student->fullName());
			$objPHPExcel->getActiveSheet()->getRowDimension($row)->setVisible(TRUE);
			$row++;
		}
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setVisible(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setVisible(TRUE);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setVisible(TRUE);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setVisible(TRUE);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setVisible(TRUE);

		return $objPHPExcel;
	}
	
	public static function AttendanceSheet($year) {
		foreach (AvailableClass::GetAllYear($year) as $class) {
			$excelDir = self::BaseDir . "/" . $class->session . "/attendance/" . 
			Department::NameFromId($class->course->department) . "/excel/";
			$pdfDir=str_replace("excel", "pdf", $excelDir);
			if (!file_exists($excelDir) && !mkdir($excelDir, 0777, true)) die ("error creating directory $excelDir");
			if (!file_exists($pdfDir) && !mkdir($pdfDir, 0777, true)) die ("error creating directory $pdfDir");
			
			$excelFile=$excelDir . $class->short() . ".xlsx";
			$pdfFile=$excelDir . $class->short() . ".pdf";
			print "$excelFile, $pdfFile \n";
			
//			continue;
			$objPHPExcel = self::AttendanceSheetFill($class);

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
//			$objWriter->save($pdfFile);

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save($excelFile);
			echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MBrn\no";
		}
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
	
	const LeavingStudentsFile = "/tmp/leaving.csv";
	const EnrolledStudentsFile = "/tmp/enrolled.csv";
	
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
		foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::Eastlake,2010) as $enrollment) {
			if (empty(self::$objArray[$enrollment->student->id])) self::$objArray[$enrollment->student->id] = new TwoYearLayout();
			$twoyear = self::GetItemById($enrollment->student->id);
			$twoyear->previousYear->updateFromEnrollment($enrollment);
		}
	}
	
	private static function currentYearFromDatabase () {
		foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS,2011) as $enrollment) {
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
			print "I am here for student $studentId, found in current year from file\n";
			self::$objArray[$studentId] = new TwoYearLayout();
			$twoyear = self::GetItemById($studentId);
			$twoyear->thisYear->updateFromStudent($student);
		}
	}

	private static function currentYearFromFile() {
		$filename = "/home/umesh/Dropbox/Vidyalaya-Management/Administration/2011.csv";
		if (($handle = fopen($filename, "r")) !== FALSE) {
			$header = fgetcsv($handle, 0, ",");
			$header = fgetcsv($handle, 0, ",");
			$i=1;
			$totalTuition=0;
			$done=array();
			$fileTuition = array();
			while ((list($family,$Check , $base, $new , $DVD , $CD , $PB , $Bag , $Ann , $Total ,$foo, $ch1 , $ch2 , $ch3 )
			= fgetcsv($handle, 0, ",")) !== FALSE) {
				if (!empty($family)) {
					if (!empty($ch1)) self::updateNewStudent($ch1);
					if (!empty($ch2)) self::updateNewStudent($ch2);
					if (!empty($ch3)) self::updateNewStudent($ch3);
				}
			}
		}
	}
	
	private static function loadAssessment() {
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
		self::firstTimeCall();
		$count = 0;
		foreach (self::$objArray as $studentid => $twoYear) {
			$student = Student::GetItemById($studentid);
				
			if ($twoYear->thisYear->languageLevel != null) continue;
			if ($twoYear->status == self::Leaving) continue;
				
			$count++;
				
			if($student->GradeAt(Calendar::RegistrationSession) == "KG") {
				$class = AvailableClass::findAvailableClass(2011, Department::Kindergarten, 0, null);
				if ($class ==null) {
					print "Error:KG not found for year 2011\n";
				} else {
					print "insert into Enrollment set student = $student->id, availableClass = $class->id;\n";
				}
				continue;
			}
				
			// all others require culture and language. Let us do culture first
			$level = $twoYear->thisYear->cultureLevel;
			$class = AvailableClass::findAvailableClass(2011, Department::Culture, $level, null);
			if ($class ==null) {
				print "Error:Culture level $level not found for year 2011\n";
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
			$class = AvailableClass::findAvailableClass(2011, $department, $level, null);
			if ($class ==null) {
				print "Error:Department $department,  level $level not found for year 2011\n";
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


//Admission::AttendanceSheet(2011); exit();
//Evaluation::ProcessAllFiles(); exit();
//Admission::RosterFromFile("/tmp/aa"); exit();
Admission::Roster(2011); exit();
//Admission::itemDelivery(); exit();
//Admission::classParentsEmail(67); Admission::classParentsEmail(65); exit();
//TwoYearLayout::checkFeePaid(); exit();
//TwoYearLayout::assignClass(); exit();
//TwoYearLayout::twoYearCsv(); exit();


//OrientationCheck(); exit();

//sendReminders();
//exit;

$students = GetAllData();

// ExistingFamilies($students);
//NewFamiliesOrientation();
$entry = GetSingleIntArgument();
PostOrientation(Family::GetItemById($entry));

?>
