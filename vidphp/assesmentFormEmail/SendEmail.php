<?php
require("libphp-phpmailer/class.phpmailer.php");
require "../db.inc";

class student {
  public $fullName = 'fullname';
  public $emailArray = array("umesh@vidyalaya.us", "praveen.vidyalaya.us");
}

function CreateDatabaseQuery() {
  $query = <<< DATABASE_QUERY_UMESH

    SELECT Students2003.LAST_NAME, Students2003.FIRST_NAME, Students2003.ID,
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


DATABASE_QUERY_UMESH;
  return $query;
}

function CreateDictionary($connection) {
  $query = CreateDatabaseQuery();
  if (!($result = mysql_query($query, $connection)))
    showerror();
  $i=0;

  while ($row = mysql_fetch_assoc($result)) {
    $id = $row["ID"];
    $name = $row["FIRST_NAME"] . " " .  $row["LAST_NAME"];
    $emailArray = array_merge(explode(";", $row["memail"]), explode(";", $row["femail"]));
    $instance = new student();
    $instance->fullName = $name;
    $instance->emailArray = $emailArray;
    $dictStudents[$id] = $instance;
  }
  return $dictStudents;
}

function CreateBody($full, $emails) {
$sendToList = implode(",", $emails);
  $body = <<<UMESH_HTML_BODY
<div style="font: 14px Verdana, Arial, Helvetica, sans-serif; border-collapse; collapse;">
	 <p>
Dear Parents,
<p>
During the last academic year we instituted an across&ndash;the&ndash;board assessment procedure for our language curricula. The assessment was designed to evaluate a broad range of language skills for each student.
<p>
As you are probably aware, our curriculum is aligned with the guidelines provided by the American Council on the Teaching of Foreign Languages (ACTFL) and several of our current teachers have taken extensive training in assessing and evaluating students in their language proficiency.
<p>
Depending on the level of the class, students may have been assessed for listening comprehension, reading comprehension, writing, oral communications and synthesizing thoughts in the language. The final assessment that was provided by the teachers was based on cumulative progress of the student over the school year and his/her performance in various benchmark assignments and tests.
<p>
Attached you will find the assessment form for $full from last year. Each level of language used a form that was designed to provide specific indicators for the level. You will notice that teachers choose one of the three markers to indicate each student&#39;s progress: N&ndash; needs improvement, S&ndash; satisfactory, E&ndash; excellent.  Additionally there were categories to indicate class participations and attendance.
<p>
Our language curriculum is divided into two broad levels &ndash; Novice and Intermediate. Each of these levels is further divided into three sub&ndash;levels: low, mid and high. Language learning is a cumulative process and encompasses a number of skills. The levels of the language classes are not in any way related to the &#39;grade&#39; level of the student; instead these are simply classifications determined by learning objectives for student progress.

It is important that students meet the objectives of a level before proceeding to the next level.  Language learning is a repetitive process and our students attend classes at Vidyalaya for less than thirty hours each year. For these reason it is expected that often times students will be in one level for multiple years.
<p>
Assessment and placement of students in appropriate levels is key to a productive and successful environment. We continue to improve our procedures and update our policies as we strive to provide a prolific language learning environment for all of our students.

<p> 
<p>
 

Sincerely,
<p>
Kiron Sharma and Reena Shah<br>
Assessment Coordinators, 2009&ndash;10

UMESH_HTML_BODY;

  return $body;

}

function SendPhpMail($name,  $sendToArray, $body, $file) {
  $mail = new PHPMailer(false); 
  $mail->Host = "smtp.gmail.com";
  $mail->Port = 465;
  $mail->SMTPSecure = "ssl";
  $mail->IsSMTP(); // send via SMTP
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

$mail->AddAttachment($file); // attachment
  //  $mail->AddAttachment("/home/umesh/eastlakelayout2010.pdf"); // attachment

  $mail->IsHTML(true); // send as HTML

  $mail->Subject = "Vidylaya: 2009-10 Language Assessment for $name ";


  $mail->Body = $body;
  $mail->AltBody = "This is the body when user views in plain text format"; //Text Body
  if(!$mail->Send()) 	 {
    echo "Mailer Error sending mail for $name: " . $mail->ErrorInfo . "\n";  return; 
  }

  echo "Message has been sent for $name ".  implode(", ", $sendToArray). "\n";
}


function mailFile($file, $id, $dict) {
  $full = $dict[$id]->fullName;
  $emails = $dict[$id]->emailArray;

  if ($id == 1445) {
    $emails = array("umesh@qvt.com");
    echo "will mail file $file to $id, $full, " .implode(", ", $emails) . "\n";
    $body = CreateBody($full, $emails);

    SendPhpMail($full, $emails,  $body, $file);
    die();
  }
}

function main($connection) {
  echo "I made it to main\n";

  # read files in the directory
  $dir = "/home/umesh/assessmentforms/2009-10/";

  $dictStudents = CreateDictionary($connection);
  $c = count($dictStudents);
  echo "made $c objects\n";

  if (is_dir($dir)) {
    if ($dh=opendir($dir)) {
      while(($file=readdir($dh)) !== false) {
	if (preg_match("/.pdf$/", $file ) != 0) {
	  mailFile($dir . $file, basename($file, ".pdf"), $dictStudents);
	  }
	}
	closedir($dh);
    }
  }
}



if (!$connection = @ mysql_connect($hostname, $username, $password))
  die("Cannot connect using hostname=$hostname, user=$username, password=$password\n");
if (!mysql_selectdb($databasename, $connection))
  showerror();
session_start();


main($connection);


?>

