<?php
require("phpmailer/class.phpmailer.php");

function SendPhpMail($email, $name, $date, $familyId) {
	 $mail = new PHPMailer(false); 
	 $mail->IsSMTP(); // send via SMTP
	 //IsSMTP(); // send via SMTP
	 $mail->SMTPAuth = true; // turn on SMTP authentication
	 $mail->Username = "waitlist@vidyalaya.us"; // SMTP username
	 $mail->Password = "praveen"; // SMTP password
	 $webmaster_email = "umesh@vidyalaya.us"; //Reply to this email ID
//	 $email="umesh@qvt.com"; // Recipients email ID
//	 $name="Parent Name"; // Recipient's name
	 $mail->From = $webmaster_email;
	 $mail->FromName = "Vidyalaya Admissions";
	 $mail->AddAddress($email,$name);
	 $mail->AddReplyTo("umesh@vidyalaya.us","Admission Coordinator");
	 $mail->AddReplyTo("praveen@vidyalaya.us","Executive Director");
	 $mail->WordWrap = 50; // set word wrap
//	 $mail->AddAttachment("/home/umesh/Downloads/volunteer.pdf"); // attachment
//	 $mail->AddAttachment("/home/umesh/Downloads/registration.pdf"); // attachment
//	 $mail->AddAttachment("/home/umesh/Downloads/medical.pdf"); // attachment
//	 $mail->AddAttachment("/home/umesh/Downloads/Flyer.pdf"); // attachment
	 //$mail->AddAttachment("/tmp/image.jpg", "new.jpg"); // attachment
	 $mail->IsHTML(true); // send as HTML
	 $mail->Subject = "Vidyalaya: Last chance to reserve a spot next year";
	 $mail->Body = "
	 <p>Dear Parent,
	 <p>

Hope you are enjoying a great summer. We wanted to take a minute of
your time to talk about admission to Sunday School for the year
2010-11.

<p>This email is being sent to all the parents who were registered in
2009-10 but whose application has yet not been recorded for
2010-11. If you have already sent it to us and we have not recorded it
yet, we apologize. We will check and see what we missed. Regardless,
please let us know your plans by return email so we can finalize the
roster. If you are unable to come back, we would love to know the
reason and wish you all the best for future. If you are planning to
come back, please include the full name of the student and the parents
so it is easy for us to compile the data.

<p>Due to time constraints, this is the last email that will be sent
to the parents registered in 2009-10. Please respond this weekend, if
possible.

<p>
Regards,

	 <p>
	 Umesh Mittal<br>for Admissions

	 "; //HTML Body
	 $mail->AltBody = "This is the body when user views in plain text format"; //Text Body
	 if(!$mail->Send())
	 {
	 echo "Mailer Error: " . $mail->ErrorInfo;
	 }
	 else
	 {
	 echo "Message has been sent";
	 }
}

$handle = @fopen("/tmp/unregistered.csv", "r");
if ($handle) {
    while (!feof($handle)) {
        $buffer = trim(fgets($handle, 4096));

	list($email) = split (",", $buffer);
//	if ($familyid > 328) {
//	   $email="umittal@gmail.com";
	  $command = "SendPhpMail($email, '', '', '')";
	  SendPhpMail($email, "", "", "");
	   echo "$command\n";
//	   die();
//	}
//        echo $buffer;
    }
    fclose($handle);
}

//SendPhpMail("umesh@qvt.com", "Proof Reader 1", "2010-01-01", 400);
//SendPhpMail("mehrotra@optonline.net", "Proof Reader 2", "2010-01-02", 401);
//SendPhpMail("veena@glocon.net", "Proof Reader 3", "2010-01-03", 402);

?>