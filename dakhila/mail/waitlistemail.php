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
	 $mail->WordWrap = 50; // set word wrap
	 $mail->AddAttachment("/home/umesh/Downloads/volunteer.pdf"); // attachment
	 $mail->AddAttachment("/home/umesh/Downloads/registration.pdf"); // attachment
	 $mail->AddAttachment("/home/umesh/Downloads/medical.pdf"); // attachment
	 $mail->AddAttachment("/home/umesh/Downloads/Flyer.pdf"); // attachment
	 //$mail->AddAttachment("/tmp/image.jpg", "new.jpg"); // attachment
	 $mail->IsHTML(true); // send as HTML
	 $mail->Subject = "Admission for year 2010-11";
	 $mail->Body = "
	 <p>Dear $name,
	 <p>

	 <p>It gives us great pleasure to announce that we are now accepting
	 admission forms from all families for school year 2010-11. Our current
	 school year was a big success, we increased our enrollment and
	 improvement in our curriculum was appreciated by all the parents. We
	 are also proud to add Telugu to our offering in addition to Hindi and
	 Gujrati. We are very excited about the new year and we hope it will be
	 better than the current year.

	 <p>We have already completed the registration of existing
	 families. The siblings of existing students get higher
	 priority for obvious reasons. The remaining spots are filled
	 on first come first served basis. We have all the emails we
	 ever received at waitlist@vidyalaya.us and it will provide us
	 guidance should we have a space limitation. We will hold a
	 mandatory orientation session for new parents and students on
	 June 6, 2010 from 9:30am to 12 noon at Eastlake School (40
	 Eba Road, Parsippany, NJ 07054). Plese complete the attached
	 forms and mail it to us (PO Box 775, Morris Plains, NJ 07950)
	 or bring it in.

	 <p>If you have a child going to kindergarten or higher grade
	 in Fall of 2010, please register by filling one registration
	 and one volunteer form. Also complete one medical form for
	 student.  Forms can also be downloaded from from our website
	 (click <a
	 href=\"http://www.vidyalaya.us/modx/93.html&#35;form\">here</a>). We
	 would appreciate if the form is completely filled up and
	 registration fee is attached with it. We are not able to
	 accept any incomplete forms and all parents are required to
	 contribute volunteer hours to the school.


	 <p>We expect registration to close on June 6. We encourage you to let
	 your friends know about it so they can all benefit from it. It is
	 vital for everyone to send email to waitlist@vidyalaya.us to register
	 their interest. We invite you to visit our website at
	 http://www.vidyalaya.us and learn more about us.
	 
	 <p>Attached please also find flyer for our annual event on
	 Saturday May 15, 2010. Do let us know if you are planning to
	 buy tickets so we can keep them ready at the gate.

	 <p>
	 Looking forward to meeting you all,
	 <p>
	 Umesh Mittal<br>for Admissions

	 <p>Original Request Date: $date<br>Family Id: $familyId

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

$handle = @fopen("/tmp/wait.csv", "r");
if ($handle) {
    while (!feof($handle)) {
        $buffer = trim(fgets($handle, 4096));

	list($email, $name, $date, $familyid) = split (",", $buffer);
	if ($familyid > 328) {
//	   $email="umesh@qvt.com";
	  $command = "SendPhpMail($email, $name, $date, $familyid)";
	  SendPhpMail($email, $name, $date, $familyid);
	   echo "$command\n";
//	   die();
	}
//        echo $buffer;
    }
    fclose($handle);
}

//SendPhpMail("umesh@qvt.com", "Proof Reader 1", "2010-01-01", 400);
//SendPhpMail("mehrotra@optonline.net", "Proof Reader 2", "2010-01-02", 401);
//SendPhpMail("veena@glocon.net", "Proof Reader 3", "2010-01-03", 402);

?>