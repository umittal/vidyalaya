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
//	 $mail->AddAttachment("/home/umesh/Downloads/volunteer.pdf"); // attachment
//	 $mail->AddAttachment("/home/umesh/Downloads/registration.pdf"); // attachment
//	 $mail->AddAttachment("/home/umesh/Downloads/medical.pdf"); // attachment
//	 $mail->AddAttachment("/home/umesh/Downloads/Flyer.pdf"); // attachment
	 //$mail->AddAttachment("/tmp/image.jpg", "new.jpg"); // attachment
	 $mail->IsHTML(true); // send as HTML
	 $mail->Subject = "Vidylaya: Mandatory New Parent Meeting: June 6, 2010";
	 $mail->Body = "
	 <p>Dear $name,
	 <p>

We would like to update you on our registration process for
2010-11. You can find updated version of this email <a
href=\"http://www.vidyalaya.us/modx/93.html&#35;june6\">here</a>.

<p><u>Waitlist Update:</u> We have sent emails to 90 families on the waiting
list and we have already received forms from many families. We have
deposited all the checks we have received as of last week and all
cancellation requests received until July 31 will be processed
promptly. Submission of forms or check deposit is merely an
administrative convenience. The school will be formed once we are able
to secure the facilities and the admission list is announced which we
expect to happen at the end of the August.

<p><u>Orientation:</u> We request mandatory participation in a new parent orientation we
have scheduled for coming sunday June 6, 2010. In this session, we
explain how our 100% volunteer organization works, seek your
participation in the process to make it happen and we speak to all our
prospective students to judge their language proficiency so we can
place them in the appropriate class. It is our intention to find ways
to include all families in the school and we will do our best with the
active participation of all families. The assessment requirement
applies to all new students who will go to 1st grade or older in their
regular school year 2010-11 (Kindgergarten students are excluded).

<p><u>Time Planning:</u> Our school starts with prayers at 9:30 am. We meet at Easlake
Elementary School located at 40 Eba Road, Parsippany, NJ. We invite
everyone to attend the prayers after which the existing students will
go to their classrooms for their final exam. It should take us a few
minutes to organize the room for the presenation followed by a
question answer session. We will try to organize three rooms for
Hindi, Gujrati and Telugu assessments and while the children are being
evaluated, we will be available to collect forms and answer any other
question you may have. Prospective volunteers will have an opportunity
to meet with the various coordinators. We hope to conclude it all by
11:30 am. Our school normally ends at 11:45 am. Snacks will be
available for all prospective students on the way out.

<p><u>Special Situations:</u> A lot of parents are going to make their time available to make
this event happen and we expect everyone to be present latest by
10:15am.  If you are unable to attend due to circumstances beyond our
control, please let us know your availability at
umesh@vidyalaya.us. We will try to arrange a special language
assessment on a case by case basis. It is not possible to place a
student without an assessemnt of the language proficiency.


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
//	if ($familyid > 328) {
//	   $email="umesh@qvt.com";
	  $command = "SendPhpMail($email, $name, $date, $familyid)";
	  SendPhpMail($email, $name, $date, $familyid);
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