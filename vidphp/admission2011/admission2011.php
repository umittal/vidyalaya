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


$students = GetAllData();

// ExistingFamilies($students);
//NewFamiliesOrientation();

PostOrientation(Family::GetItemById(344));

?>
