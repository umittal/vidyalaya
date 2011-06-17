<?php

$libDir="../../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";

require("../../Classes/PHPMailer_v5.1/class.phpmailer.php");


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
	public static function SetupMail() {

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
	public static function SetFamilyAddress(&$mail, $family, $test) {
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

}

class Admission {
	const DataFile = "/tmp/2011.csv";
	
	public static function  mailFamily($family, $subject, $body, $test) {
		$mail = Mail::SetupMail();
		Mail::SetFamilyAddress($mail, $family, $test);
		$mail->Subject = $subject;

		$salutation = "<p>Dear " . $family->parentsName() . ",";
		$footer = "<p>Umesh Mittal<br>Admissions</p>";

		$mail->Body = $salutation . $body . $footer;
		$mail->AltBody = "This is the body when user views in plain text format"; //Text Body
		
		if(!$mail->Send()) {
			echo "Mailer Error: Family: $family->id: " . $mail->ErrorInfo . "\n";
		}  else {
			echo "Message has been sent, Family: $family->id:\n";	
		}
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
		foreach ($totalFamily as $family => $total) {
			if ($total == 0 ) continue;
			print "$family, $totalCD[$family], $totalPB[$family], $totalBag[$family], $totalFamily[$family]\n";
			$grandTotal += $total;
		}
		print "Grand Total = $grandTotal\n";
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
	}
	
	public function csv() {
		$fields = Array();
		$fields[] = Department::NameFromId($this->language);
		$fields[] = $this->languageLevel;
		$fields[] = $this->languageSection;
		$fields[] = $this->cultureLevel;
		$fields[] = $this->cultureSection;
		return implode (", ", $fields);
	}
}


class TwoYearLayout {
	public $previousYear = null;
	public $thisYear = null;
	public $status=null;

	const Leaving = "Leaving";
	const NewStudent = "New";
	
	private static $objArray = Array ();

	private static function firstTimeCall() {
		if (!empty(self::$objArray)) return;
		self::prevYearFromDatabase();
		self::currentYearFromFile();
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
		foreach (self::$objArray as $twoyear) {
			if ($twoyear->previousYear->language == null) {
				if ($twoyear->thisYear->language != null) $twoyear->status = self::NewStudent;
			} else {
				if ($twoyear->thisYear->language == null) {
					$twoyear->status = self::Leaving;
					continue;
				} 
				if ($twoyear->previousYear->language == Department::Kindergarten) {
					$twoyear->status ="Continuing";
				} else {
					$twoyear->status = $twoyear->previousYear->language == $twoyear->thisYear->language ? "Continuing" : "Change";
				}
			}
		}
		
	}

	private static function prevYearFromDatabase () {
		foreach (Enrollment::GetAllEnrollmentForFacilitySession(1,0) as $enrollment) {
			if (empty(self::$objArray[$enrollment->student->id])) self::$objArray[$enrollment->student->id] = new TwoYearLayout();
			$twoyear = self::GetItemById($enrollment->student->id);
			$twoyear->previousYear->updateFromEnrollment($enrollment);
		}
	}

	private static function updateNewStudent($studentId) {
		$student = Student::GetItemById($studentId);
		if (empty($student)) print "student not found for id ==$studentId==";
		if (empty(self::$objArray[$studentId])) self::$objArray[$studentId] = new TwoYearLayout();
		$twoyear = self::GetItemById($studentId);
		$twoyear->thisYear->updateFromStudent($student); 
	}

	private static function currentYearFromFile() {
		$filename = "/tmp/2011.csv";
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

	public static  function twoyearstudents() {
		self::firstTimeCall();
		
		$feeRequired = Array();
		$newRegFee = Array();
		
		foreach (self::$objArray as $studentid => $twoYear) {
			$student = Student::GetItemById($studentid);
			$familyid = $student->family->id;
			
			$fields = Array();
			$fields[] = $studentid;
			$fields[] = $familyid;
			$fields[] = $twoYear->previousYear->csv();
			$fields[] = $twoYear->thisYear->csv();
			$fields[] = $twoYear->status;
			$fields[] = EnumFamilyTracker::NameFromId(FamilyTracker::CurrentYearStatus($familyid));
			$fields[] = $student->fullName();
			
//			print implode (", ", $fields) . "\n";
			
			if ($twoYear->status != self::Leaving) {
				if(empty($feeRequired[$familyid])) {
					$feeRequired[$familyid] = 450;
					$newRegFee[$familyid] = 0;
				} else {
					$feeRequired[$familyid] = 550;
				}
				
				if ($twoYear->status == self::NewStudent) $newRegFee[$familyid] += 50; 
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

Admission::itemDelivery(); exit();
//TwoYearLayout::twoyearstudents(); exit();

//OrientationCheck(); exit();

//sendReminders();
//exit;

$students = GetAllData();

// ExistingFamilies($students);
//NewFamiliesOrientation();
$entry = GetSingleIntArgument();
PostOrientation(Family::GetItemById($entry));

?>
