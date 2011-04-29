<?php
require("phpmailer/class.phpmailer.php");
require 'db.inc';


function SendPhpMail($email, $name, $lang, $cult, $memail, $femail, $lteacher, $cteacher, $lroom, $croom) {
	 $mail = new PHPMailer(false); 
	 $mail->IsSMTP(); // send via SMTP
	 //IsSMTP(); // send via SMTP
	 $mail->SMTPAuth = true; // turn on SMTP authentication
	 $mail->Username = "announcement@vidyalaya.us"; // SMTP username
	 $mail->Password = "praveen"; // SMTP password
	 $webmaster_email = "umesh@vidyalaya.us"; //Reply to this email ID
//	 $email="umesh@qvt.com"; // Recipients email ID
//	 $name="Parent Name"; // Recipient's name
	 $mail->From = $webmaster_email;
	 $mail->FromName = "Vidyalaya Announcement";

	 $mail->AddAddress($email,$email);
//	 if ("" != $memail) {$mail->AddAddress($memail,$memail);}
//	 if ("" != $femail) {$mail->AddAddress($femail,$femail);}
	 $mail->AddReplyTo("info@vidyalaya.us","Vidyalaya Information");
	 $mail->WordWrap = 50; // set word wrap
	 $mail->AddAttachment("/home/umesh/Vidyalaya-0918.pdf"); // attachment
//	 $mail->AddAttachment("/home/umesh/eastlakelayout2010.pdf"); // attachment
	 //$mail->AddAttachment("/tmp/image.jpg", "new.jpg"); // attachment
	 $mail->IsHTML(true); // send as HTML
	 $mail->Subject = "Vidylaya: Opening Day";


	 $mail->Body = "


<div style=\"font: 14px Verdana, Arial, Helvetica, sans-serif\; border-collapse; collapse\;\">
	 <p>

Welcome to Vidyalaya Opening Day this Sunday, September 19, 2010 at Eastlake Elementary School, 40 Eba Road, Parsippany, NJ.


<p>
Please take a moment to read our opening day newsletter attached  here.  We will follow a modified schedule this Sunday.  
The online version is available <a href=\"http://www.vidyalaya.us/modx/101.html\">here</a>. The following topics are covered.
<ul>
<li> Letter from Praveen Mehrotra
<li> Message from Student and Parent Affairs (SPA) team  regarding opening day
<li> Parking notes from Administration
<li> Couple of upcoming competitive events from Shikha Saxena
</ul>




	 <p>
	 Thank you for volunteering at Vidyalaya. 
	 <p>
	 Umesh Mittal<br>Vidyalaya
</div>

	 "; //HTML Body
	 $mail->AltBody = "This is the body when user views in plain text format"; //Text Body
	 if(!$mail->Send())
	 {
	 echo "Mailer Error: " . $mail->ErrorInfo;
	 }
	 else
	 {
	 echo "Message has been sent for $name  to $memail, $femail\n";
	 }
}


if (!$connection = @ mysql_connect($hostname, $username, $password))
  die("Cannot connect");
if (!mysql_selectdb($databasename, $connection))
  showerror();
session_start();

$query = "SELECT Parents2003.MLAST_NAME, Parents2003.MFIRST_NAME,   Parents2003.M_EMAIL as memail, Parents2003.F_EMAIL as femail


FROM Parents2003 LEFT JOIN Students2003 ON Parents2003.ID=Students2003.PARENT_ID
WHERE Parents2003.TYPE_CODE=2
GROUP BY Parents2003.ID, Parents2003.MLAST_NAME, Parents2003.MFIRST_NAME, Parents2003.FLAST_NAME, Parents2003.FFIRST_NAME, Parents2003.MH_PHONE, Parents2003.MW_PHONE, Parents2003.MC_PHONE, Parents2003.M_EMAIL, Parents2003.FH_PHONE, Parents2003.FW_PHONE, Parents2003.FC_PHONE, Parents2003.F_EMAIL, Parents2003.M_ADDRESS, Parents2003.M_CITY, Parents2003.M_STATE, Parents2003.M_ZIP_CODE, Parents2003.F_ADDRESS, Parents2003.F_CITY, Parents2003.F_STATE, Parents2003.F_ZIP_CODE, Parents2003.PRIMARY_PHONE_FLAG, Parents2003.PRIMARY_EMAIL_FLAG, Parents2003.COMMUNITY_EMAIL_FLAG, Parents2003.DIRECTORY_FLAG, Parents2003.TYPE_CODE, Parents2003.M_ACTIVITY_CHOICE_1, Parents2003.M_ACTIVITY_CHOICE_2, Parents2003.M_ACTIVITY_CHOICE_3, Parents2003.F_ACTIVITY_CHOICE_1, Parents2003.F_ACTIVITY_CHOICE_2, Parents2003.F_ACTIVITY_CHOICE_3, Parents2003.AMOUNT_PAID
";
if (!($result = mysql_query($query, $connection)))
   showerror();
   $i=0;
while ($row = mysql_fetch_assoc($result)) {
      $i++;
      $name = $row["FIRST_NAME"] . " " .  $row["LAST_NAME"];
      $memail = $row["memail"]; $femail = $row["femail"];


      echo "$i, $name, $lang, $cult, $memail, $femail\n";

//   id we are testing are 249 ankita, 142 kevin 131 prachi
//      if ($i == 2  ) {
            SendPhpMail("niravlad81@gmail.com", $name, $lang, $cult, $memail, $femail, $lteacher, $cteacher, $lroom, $croom);
	    die();

//      }
 
}




?>