<?php
require("phpmailer/class.phpmailer.php");
require 'db.inc';

function SendPhpMail($name,  $sendToArray, $body) {
  $mail = new PHPMailer(false); 
  $mail->IsSMTP(); // send via SMTP
  //IsSMTP(); // send via SMTP
  $mail->SMTPAuth = true; // turn on SMTP authentication
  $mail->Username = "announcement@vidyalaya.us"; // SMTP username
  $mail->Password = "praveen"; // SMTP password
  $webmaster_email = "umesh@vidyalaya.us"; //Reply to this email ID
  $mail->From = $webmaster_email;
  $mail->FromName = "Vidyalaya Announcement";

  foreach ($sendToArray as $value) {
    if ("" != $value) {
      echo "DEBUG: Adding email address for $name -  $value \n";
      $mail->AddAddress($value,$value);
    }
  }

  $mail->AddReplyTo("info@vidyalaya.us","Vidyalaya Information");
  $mail->WordWrap = 50; // set word wrap

  //  $mail->AddAttachment("/home/umesh/Vidyalaya-0918.pdf"); // attachment
  //  $mail->AddAttachment("/home/umesh/eastlakelayout2010.pdf"); // attachment

  $mail->IsHTML(true); // send as HTML

  $mail->Subject = "Vidylaya: Class Assignment and Opening Day";


  $mail->Body = $body;
  $mail->AltBody = "This is the body when user views in plain text format"; //Text Body
  if(!$mail->Send()) 	 {
    echo "Mailer Error sending mail for $name: " . $mail->ErrorInfo . "\n";  return; 
  }

  echo "Message has been sent for $name ".  implode(", ", $sendToArray). "\n";
}


function main ($connection) {


  $body = "I am the best\n";

      SendPhpMail($name, "Admission2011@vidyalaya.us",  $body);
  }



$connection=null;
main($connection);


?>