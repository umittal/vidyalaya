<?php
require("phpmailer/class.phpmailer.php");
require 'db.inc';


function CreateBody($name, $lang, $cult, $sendToArray, $lteacher, $cteacher, $lroom, $croom) {
$sendToList = implode(",", $sendToArray);
  $body = <<<UMESH_HTML_BODY
<div style="font: 14px Verdana, Arial, Helvetica, sans-serif; border-collapse; collapse;">
	 <p>

Welcome to Vidyalaya Opening Day this Sunday, September 19, 2010 at Eastlake Elementary School, 40 Eba Road, Parsippany, NJ.

<hr />
<table border=1 style="padding:5px; border-width: 0 0 1px 1px; border-collapse; collapse; ">
<tr><td colspan=2 style=" text-align: left;  "><b>Student Information</b></td></tr>
<tr><td>Student</td><td > $name</td></tr>
<tr><td>Email</td><td >$sendToList</td></tr>
</table>

<p>&nbsp;</p>

<table border=1 style="padding:5px ; border-width: 1px 1px 1px 1px; border-collapse; collapse; ">
<tr><td colspan=5 style="font-weight: bold; text-align: left;  "><b>Class Assignment</b> </td></tr>
<tr><th>Class</th><th>Time</th><th>Room</th><th>Class</th><th>Teacher</th></tr>
<tr><td>Prayer</td><td>9:30</td><td colspan=3>Gymnasium</td></tr>
<tr><td>Language</td><td>10:00</td><td>$lroom</td><td> $lang</td><td>$lteacher</td></tr>
<tr><td>Culture</td><td>11:00</td><td>$croom</td><td> $cult</td><td>$cteacher</td></tr>
<tr><td>Dismissal</td><td colspan=4>11:45</td></tr>

</table>
<p>This Sunday we will follow a modified schedule. New students arrive by 8:30am, continuing students arrive by 9:10am.

<hr />
<p>
Please print the above class assignment information and bring it with you on Sunday. Familiarize yourself with the 
layout of the Eastlake School. Rooms 1 through 12 are in the Primary Hallway, which is
 accessed by making a right turn as you exit the Gymnasium towards the main entrance of the
 school. Rooms 14 through 25 are in the Intermediate Hallway, accessed by making a left as you exit the Gymnasium.

<p>
Please take a moment to read our opening day newsletter attached  here.  We will follow a modified schedule this Sunday.  
The online version is available <a href="http://www.vidyalaya.us/modx/101.html">here</a>. The following topics are covered.
<ul>
<li> Letter from Praveen Mehrotra
<li> Message from Student and Parent Affairs (SPA) team  regarding opening day
<li> Parking notes from Administration
<li> Couple of upcoming competitive events from Shikha Saxena
</ul>




	 <p>
	 We look forward to another great year. Please arrive on time on Sunday. We have a lot to cover in a compressed schedule.
	 <p>
	 Umesh Mittal<br>Vidyalaya
UMESH_HTML_BODY;

  return $body;

}

function CreateDatabaseQuery() {
  $query = <<< DATABASE_QUERY_UMESH

SELECT Students2003.LAST_NAME, Students2003.FIRST_NAME, LanguageGrades.Description as ldes, 
       LanguageGrades.Room as lroom,  CultureGrades.Description as cdes, CultureGrades.Room as croom,
       LanguageGrades.Teachers as lteacher, CultureGrades.Teachers as cteacher,
       Parents2003.M_EMAIL as memail, Parents2003.F_EMAIL as femail
FROM ((
 (
  (
   (Students2003 INNER JOIN Parents2003 ON Students2003.PARENT_ID=Parents2003.ID) 
    INNER JOIN LanguageGrades ON Students2003.SS_LANG_NEXT=LanguageGrades.ID)  

  INNER JOIN CultureGrades ON Students2003.SS_CULT_NEXT=CultureGrades.ID) 
  INNER JOIN SchoolGrade ON Students2003.NEXT_YEAR_GRADE=SchoolGrade.ID) 
  INNER JOIN Email_Preferences ON Parents2003.PRIMARY_EMAIL_FLAG=Email_Preferences.ID) 
  INNER JOIN MusicGrades ON Students2003.SS_MUSIC_NEXT=MusicGrades.ID 


WHERE (((Students2003.CONTINUING)<>2) 
      And ((Students2003.STATUS) Is Null 
      Or (Students2003.STATUS)<>3)) 
      Or (((Students2003.STATUS)=1)) 
ORDER BY Students2003.LAST_NAME, Students2003.FIRST_NAME, SchoolGrade.Description; 
DATABASE_QUERY_UMESH;
  return $query;
}

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


  $query = CreateDatabaseQuery();
  if (!($result = mysql_query($query, $connection)))
    showerror();
  $i=0;

  while ($row = mysql_fetch_assoc($result)) {
    $i++;
    $name = $row["FIRST_NAME"] . " " .  $row["LAST_NAME"];
    $lroom=$row["lroom"]; $croom=$row["croom"];
    $lang = $row["ldes"];
    $cult = $row["cdes"];
    $memail = $row["memail"]; $femail = $row["femail"];
    $lteacher = $row["lteacher"]; $cteacher = $row["cteacher"];
    $sendToArray = array_merge(explode(";", $row["memail"]), explode(";", $row["femail"]));
#    $sendToArray = array("umesh@vidyalaya.us");
#    $sendToArray = array("umesh@qvt.com");



    $body = CreateBody($name, $lang, $cult, $sendToArray, $lteacher, $cteacher, $lroom, $croom);

    //   id we are testing are 249 ankita, 142 kevin 131 prachi
    //      if (strpos($memail, ",") || strpos($femail, "," )) {
#    if ($i == 141) {
      echo "\n" . "$i, $name, $lang, $cult,".  implode(", ", $sendToArray). "\n";
#      SendPhpMail($name, $sendToArray,  $body);
#    }
  }
}

  if (!$connection = @ mysql_connect($hostname, $username, $password))
    die("Cannot connect using hostname=$hostname, user=$username, password=$password\n");
  if (!mysql_selectdb($databasename, $connection))
    showerror();
  session_start();


main($connection);


?>