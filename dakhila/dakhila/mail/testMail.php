<?php
require("/var/www/Classes/PHPMailer_v5.1/class.phpmailer.php");
$mail = new PHPMailer(false); 
$mail->IsSMTP(); // send via SMTP
//IsSMTP(); // send via SMTP
$mail->SMTPAuth = true; // turn on SMTP authentication


$mail->Username = "umesh@vidyalaya.us"; // SMTP username
$mail->Password = "umesh2"; // SMTP password
$webmaster_email = "umesh@vidyalaya.us"; //Reply to this email ID
$email="Admission2011@vidyalaya.us"; // Recipients email ID
$name="Daddy Mittal"; // Recipient's name

$mail->From = $webmaster_email;
$mail->FromName = "Daddy From";
$mail->AddAddress($email,$name);
$mail->AddReplyTo($webmaster_email,"Daddy From");


$mail->WordWrap = 50; // set word wrap
//$mail->AddAttachment("/var/tmp/file.tar.gz"); // attachment
//$mail->AddAttachment("/tmp/image.jpg", "new.jpg"); // attachment

$mail->IsHTML(true); // send as HTML

$mail->Subject = "This is the subject";

$mail->Body = "Hi,
This is the HTML BODY "; //HTML Body
$mail->AltBody = "This is the body when user views in plain text format"; //Text Body

if(!$mail->Send())
{
echo "Mailer Error: " . $mail->ErrorInfo;
}
else
{
echo "Message has been sent";
}
?>