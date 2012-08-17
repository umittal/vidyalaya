<?php

$libDir="../../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";
require_once "HTML/Template/ITX.php";
require_once "$libDir/HtmlFactory.inc";
require_once "$libDir/reports.inc";
require_once "$libDir/TwoYear.inc";
require_once "$libDir/Evaluation.inc";
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


//Admission::Payment2012(); exit();
//Admission::Mela2012(); exit();
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


Evaluation::ProcessAllExcelFiles(); exit();

//FamilyTracker::loadPayments();exit();
//FamilyTracker::UpdateFamilyTracker(); exit();
//TwoYearLayout::assignClass(); exit();
///TwoYearLayout::checkFeePaid(); exit();
TwoYearLayout::twoYearCsv(); exit();
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
