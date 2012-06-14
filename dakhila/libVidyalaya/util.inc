<?php

require_once "vidyalaya.inc";
$dompdfDir = realpath(dirname(__FILE__)."/../../dompdf");
require_once("$dompdfDir/dompdf_config.inc.php");
//$phpMailerDir = realpath(dirname(__FILE__)."/../../Classes/PHPMailer_v5.1");
$phpMailerDir = realpath(dirname(__FILE__)."/../../Classes/PHPMailer_5.2.1");

require_once("$phpMailerDir/class.phpmailer.php");

// returns true if $str begins with $sub
function beginsWith( $str, $sub ) {
    return ( substr( $str, 0, strlen( $sub ) ) == $sub );
}

// return tru if $str ends with $sub
function endsWith( $str, $sub ) {
    return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
}

// trims off x chars from the front of a string
// or the matching string in $off is trimmed off
function trimOffFront( $off, $str ) {
    if( is_numeric( $off ) )
        return substr( $str, $off );
    else
        return substr( $str, strlen( $off ) );
}

// trims off x chars from the end of a string
// or the matching string in $off is trimmed off
function trimOffEnd( $off, $str ) {
    if( is_numeric( $off ) )
        return substr( $str, 0, strlen( $str ) - $off );
    else
        return substr( $str, 0, strlen( $str ) - strlen( $off ) );
}

function preg_grep_keys ($pattern, $input, $flags=0) {

  $keys = preg_grep("/{$pattern}/i", array_keys($input), $flags);
  $vals = array();

  foreach ($keys as $key) {
    $vals[$key] = $input[$key];
  }
  return $vals;
}

function parseArgs($argv){
    array_shift($argv); $o = array();
    foreach ($argv as $a){
        if (substr($a,0,2) == '--'){ $eq = strpos($a,'=');
            if ($eq !== false){ $o[substr($a,2,$eq-2)] = substr($a,$eq+1); }
            else { $k = substr($a,2); if (!isset($o[$k])){ $o[$k] = true; } } }
        else if (substr($a,0,1) == '-'){
            if (substr($a,2,1) == '='){ $o[substr($a,1,1)] = substr($a,3); }
            else { foreach (str_split(substr($a,1)) as $k){ if (!isset($o[$k])){ $o[$k] = true; } } } }
        else { $o[] = $a; } }
    return $o;
}

function GetSingleIntArgument() {
	$sapi = php_sapi_name();
	switch ( $sapi ) {

		case "cli":

				
			if( $_SERVER["argc"] != 2) die ("sorry, i can only handle one argument right now\n");
			$line = trim($_SERVER["argv"][1]);
			if ($line == "q") break;
			$entry =intval($line);
			if ($entry == 0 ) {
				die( "found: $line, expecting an integer\n\n");
				break;
			}
			return $entry;
			break;

		default:
			die ("do not know how to handle $sapi yet\n");
	}

}

function formatSqlTime($h, $m, $ampm) {
  if (empty($h)) $h=0;
  if (empty($m)) $m=0;
if (strval(intval($h)) != strval($h)) return  "Error: Bad Hour ==$h==";
if (strval(intval($m)) != strval($m)) return  "Error: Bad Minute ==$m==";
  if  ($h < 0 || $h > 12) return "Error: Bad Hour $h";
  if ($m < 0 || $m > 59) return "Error: Bad Minute $m";
  switch ($ampm) {
  case "AM": break;
  case "PM": 
    $h +=12;
    break;
  default:
    return "Error: Bad ampm $ampm";
  }
  return sprintf("%02d:%02d:00", $h, $m);
}

function formatTime($timestring) {
  if (empty($timestring)) return "";
  list ($h, $m, $s) = explode(":", $timestring);
  if ($h==0 && $m==0) return "";
  $ampm = "AM";
  $ampm = $h > 12 ? "PM" : "AM";
  $hh = $h > 12 ? $h - 12 : $h;
  return sprintf("%02d:%02d $ampm", $hh, $m);
}


function formatPhone($num) 
{ 
#    $num = ereg_replace('[^0-9]', '', $num); 
    $num = preg_replace('/[^0-9]/', '', $num); 

    $len = strlen($num); 
    if($len == 7) 
        $num = preg_replace('/([0-9]{3})([0-9]{4})/', '$1-$2', $num); 
    elseif($len == 10) 
        $num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '$1-$2-$3', $num); 

    return $num; 
}

function saveTempFp ($fp, $filename) {
  fseek($fp, 0);
  file_put_contents("$filename", stream_get_contents($fp));
  fclose ($fp);
}

class PrintFactory {
  function GetHtmlForFamilyDetail($family) {
    // Header 
    $template = new HTML_Template_ITX("/var/www/dakhila/templates");
    $template->loadTemplatefile("Layout.tpl", true, true);
    $template->addBlockFile('TOP', 'F_TOP', 'LayoutTop.tpl');
    $template->touchBlock('F_TOP');
    $html = $template->get();

    // Family Detail Form - one per family
    $template = new HTML_Template_ITX("/var/www/dakhila/templates");
    $template->loadTemplatefile("Layout.tpl", true, true);
    $template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
    $template->touchBlock('F_CONTENT');
    $template->setCurrentBlock('HEADER');
    $template->setVariable("HEADER", '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
		width="600" height="60" 
		alt="php5 logo"/></a>');
    $template->parseCurrentBlock();
    $template->setCurrentBlock('FOOTER');
    $template->setVariable("FOOTER", "Copyright (c) 2011 Vidyalya Inc.");
    $template->parseCurrentBlock();

    $template->addBlockFile('RESULT', 'F_RESULT', 'FamilyDetail.tpl');
    $template->touchBlock('F_RESULT');
    DisplayFamilyTemplateV3($template, $family);
    $html = $html . $template->get();	
    return $html;
  }

  function GetHtmlForPersonDetail($person) {
    // Header 
    $template = new HTML_Template_ITX("/var/www/dakhila/templates");
    $template->loadTemplatefile("Layout.tpl", true, true);
    $template->addBlockFile('TOP', 'F_TOP', 'LayoutTop.tpl');
    $template->touchBlock('F_TOP');
    $html = $template->get();

    // Family Detail Form - one per family
    $template = new HTML_Template_ITX("/var/www/dakhila/templates");
    $template->loadTemplatefile("Layout.tpl", true, true);
    $template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
    $template->touchBlock('F_CONTENT');
    $template->setCurrentBlock('HEADER');
    $template->setVariable("HEADER", '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
		width="600" height="60" 
		alt="php5 logo"/></a>');
    $template->parseCurrentBlock();
    $template->setCurrentBlock('FOOTER');
    $template->setVariable("FOOTER", "Copyright (c) 2011 Vidyalya Inc.");
    $template->parseCurrentBlock();

    $template->addBlockFile('RESULT', 'F_RESULT', 'PersonDetail.tpl');
    $template->touchBlock('F_RESULT');
    DisplayPersonTemplate($template, $person);
    $html = $html . $template->get();	
    return $html;
  }

  function HtmlToPdf($html) {
    $html = str_replace('&nbsp;', '<span style="color:#fff;">x</span>',$html);
    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->render();
    return $dompdf->output();
  }

}

Class Calendar {
	const BaseYear = 2010
	, Today = 1
	, CurrentSession = 2
	, RegistrationSession = 3
	;
	
	public static function RegistrationYear() {
		return date("Y", time());
	}
	
	public static function CurrentYear() {
		$year = date("Y", time());
		$month = date("n", time());
		if ($month < 7) $year--;
		return $year;
	}
	
	public static function NextYear() {
		return CurrentYear() + 1;
	}
	
	public static function PreviousYear() {
		return CurrentYear() - 1;
	}
	
	public static function Years($from, $to) {
		return ($to - $from)/60/60/24/365;
	}
	
	public static function AgeAt($dob, $rule) {
		if (empty($dob)) return "Unknown";
		switch ($rule) {
			case Calendar::Today:
				return Calendar::Years(strtotime($dob), time());
			case Calendar::CurrentSession:
				return Calendar::Years(strtotime($dob), strtotime(Calendar::CurrentYear() . "-09-01"));
			case Calendar::RegistrationSession:
				return Calendar::Years(strtotime($dob), strtotime(Calendar::RegistrationYear() . "-09-01"));
				
			default:
				die ("calling AgeAt() is not implemented for $rule\n");
		}
	}
	
	public static function Age($dob) {
		return self::AgeAt($dob, Calendar::Today);
	}
	
	public static function GradeAt($first, $rule) {
		if (empty($first)) return "Unknown";
		switch ($rule) {
			case Calendar::CurrentSession:
				$yeardiff =  Calendar::CurrentYear() - $first + 1;
				break;
			case Calendar::RegistrationSession:
				$yeardiff =  Calendar::RegistrationYear() - $first +1;
				break;
			default:
				die ("calling GradeAt() is not implemented for $rule\n");
		}
		
		if ($yeardiff == 0) return "KG";
		if ($yeardiff > 12) return "College";
		
		return $yeardiff;
	}
	
}

final class EnumFamilyTracker {
  const registered = 1;
  const waitlist = 2;
  const pendingRegistration = 3;
  const movingAway = 4;
  const notInterested = 5;
  const nextYear=6;
  const tooExpensive=7;
  const sportsConflict=8;
  const graduating=9;
  const pendingInvitation=10;
  const cancelled=11;
  

  public static $choices = Array(1 => "Registered", "Waitlist", "Pending", "Moved Out", "Not Interested", "Next Year",
				 "Too Expensive", "Sports Conflict", "All Graduating", "To Be Invited", "Cancelled");

    public function enum($string){
    	
    		$tocheck = 'EnumFamilyTracker::'.$string;
    		$value =constant($tocheck);
    		if (empty($value)) throw new Exception("value $value for $string, $tocheck not found in EnumFamilyTracker"); 
        return $value;
    }
    
    public static function NameFromId($id) {
    	switch ($id) {
    		case self::registered: return "Registered";
    		case self::waitlist: return "Waitlist";
    		case self::pendingRegistration: return "Pending";
    		case self::movingAway: return "Moved Out";
    		case self::notInterested: return "Not Interested";
    		case self::nextYear: return "Next Year";
    		case self::tooExpensive: return "Too Expensive";
    		case self::sportsConflict: return "Sports Conflict";
    		case self::graduating: return "All Graduating";
    		case self::pendingInvitation: return "To be invited";
    		case self::cancelled: return "Cancelled";
    		
    		default: return "Unknown $id";
    	}
    }
}

class Mail {
  private static function SetupMailCommon($email, $name, $password, $replyEmail, $replyName) {
    $mail = new PHPMailer(false);
    $mail->IsSMTP(); // send via SMTP

    
    $server = $command=isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME']  : "";

    $sapi = php_sapi_name();
     if (preg_match('/^www.vidyalaya.us/',$server ) && $sapi != "cli")  {
      $mail->SMTPAuth = false; // turn on SMTP authentication
      $mail->Host = "relay-hosting.secureserver.net";
      $mail->Port = "25";
      $mail->SMTPSecure = "";
    } else {
      $mail->SMTPAuth = true; // turn on SMTP authentication
      $mail->Username = $email; // SMTP username
      $mail->Password = $password; // SMTP password
      $mail->Host = "smtp.gmail.com";
      $mail->Port = "465";
      $mail->SMTPSecure = "ssl";
    }
    $mail->From = $email;
    $mail->FromName = $name;
    //		$mail->AddReplyTo($email,$name);
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->IsHTML(true); // send as HTML
    $mail->WordWrap = 50; // set word wrap

    return $mail;
  }

  public static function SetupMailAdmissions() {
    return self::SetupMailCommon("Admission2012@vidyalaya.us", "Vidyalaya Admissions", "Praveen38", 
				 "Admission2012@vidyalaya.us", "Vidyalaya Admissions");
  }
	
  public static function SetupMailSPA() {
    return self::SetupMailCommon("spa@vidyalaya.us", "Student and Parent Affairs", "vasudha", 
				 "umesh@vidyalaya.us", "Umesh Mittal");
  }

  public static function SetupMailSPAReplyAsmita() {
    return self::SetupMailCommon("spa@vidyalaya.us", "Student and Parent Affairs", "vasudha", 
				 "asmita@vidyalaya.us", "Asmita Mistry");
  }

  public static function SetupMailInfo() {
    return self::SetupMailCommon("info@vidyalaya.us", "Vidyalaya Information", "praveen", 
				 "umesh@vidyalaya.us", "Umesh Mittal");
  }

  public static function SetupMailUmesh() {
    return self::SetupMailCommon("umesh@vidyalaya.us", "Vidyalaya Administration", "Praveen38", 
				 "asmita@vidyalaya.us", "Language Curriculum Team");
  }

	
  private static function SetupMail() {

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


  private static function SetEmailAddress(&$mail, $email, $name, $production) {
    if ($production == 0 ) {
      $mail->AddAddress("voting@vidyalaya.us", "Testing email");
      return;
    }

    foreach (explode(";", $email) as $toAddress) {
      if (!empty($toAddress))
	$mail->AddAddress($toAddress, $name);
    }
  }

  public static function SetPersonAddress(&$mail, $person, $production) {
    //    print "I will send to ". $person->id() . ": Person: " . $person->fullName() . ": " .  $person->email . "\n";
    self::SetEmailAddress($mail, $person->email, $person->fullName(), $production);
  }

  public  static function SetFamilyAddress(&$mail, $family, $production) {
    print "I will send to ". $family->id . ": Mother: " . $family->mother->fullName() . ": " .  $family->mother->email . "\n";
    self::SetEmailAddress($mail, $family->mother->email, $family->mother->fullName(), $production);
    print "I will send to ". $family->id . ": Father: " . $family->father->fullName() . ": " .  $family->father->email . "\n";
    self::SetEmailAddress($mail, $family->father->email, $family->father->fullName(), $production);
  }

  public static function  mailFamilyFromAdmission($family, $subject, $body, $production) {
    $footer = "<p>Vidyalaya Management Team<br>Umesh Mittal</p>";
    self::mailFamily($family, $subject, $body, $production, $footer);
  }


  public static function  mailFamily($family, $subject, $body, $production, $footer) {
    if ($production == 0) $subject = "[Test] $subject";

    $mail = self::SetupMailSPA();
    self::SetFamilyAddress($mail, $family, $production);

    $mail->Subject = $subject;
    $salutation = "<p>Dear " . $family->parentsName() . ",";
    $mail->Body = $salutation . $body . $footer;

    $mail->AltBody = "This is the body when user views in plain text format"; //Text Body
		
    if(!$mail->Send()) {
      echo "Mailer Error: Family: $family->id: " . $mail->ErrorInfo . "\n";
    }  else {
      echo "Message has been sent, Family: $family->id:\n";
    }
  }

  public static function mailResetPasswordCode($email, $person, $url, $ip, $production) {
    $subject = "Password Reset for Dakhila Portal";
    if ($production == 0) $subject = "[Test] $subject";
    $mail = self::SetupMailUmesh();
    self::SetPersonAddress(&$mail, $person, $production);

    $server=  $_SERVER['SERVER_NAME'];
    $mail->Subject = $subject;
    $salutation = "<p>Dear " . $person->fullName() . ",\n";
    $body = "<p>Request received from $ip </p>\n";
    $body .= "<p>Please click <a href='$url'>here</a> to set your password for Dakhila Portal at Vidyalaya. Follow the instructions to reset your password from the same machine. <p> Please <a href='$server/dakhila/php/dataViewer2.php?command=login'>login</a> again  with the new password\n";
    $footer = "<p>Sincerely,<p>info@vidyalaya.us<p>\n";

    $mail->Body = $salutation . $body . $footer;
    $mail->AltBody = "Password reset code for email , " . $email; //Text Body
    if(!$mail->Send()) {
      echo "Mailer Error: Id: $person->mfsId: " . $mail->ErrorInfo . "\n";
    }  else {
      //      echo "Message has been sent, Family: $person->mfsId:\n";
    }

  }
	
  public static function  mailEvaluation($studentId, $html, $production) {
    $student = Student::GetItemById($studentId);
    $family = $student->family;
    
    $footer = "<p>Asmita Mistry<br>On behalf of the Language Curriculum Team</p>";	
    $subject = "Language Evaluation 2010-11: " . $student->fullName();
    if ($production == 0) $subject = "[Test] $subject";
    
    $mail = self::SetupMailSPA();
    self::SetFamilyAddress($mail, $family, $production);
    $mail->Subject = $subject;
    $salutation = "<p>Dear " . $family->parentsName() . ",";
    
    $name = $student->fullName();
    
    $body = <<<EOT
		
		<p>Attached please find the language evaluation report for $name for the 2010-11 school year. 
		Also please find the class assignment for the upcoming school year if you have already registered. 
		We also want to take this opportunity to thank the teachers for their voluntary contribution.

		<p>Should you have any questions or concerns, please respond to this email at the 
		earliest. 
		Please forward any questions or comments to spa@vidyalaya.us.  The SPA team will 
		coordinate with the Language Curriculum Team to address your concerns.

<p>Have a great summer!

<p>Regards,
EOT;
		
    $mail->Body = $salutation . $body . $footer;
    
    
    $filename = "/tmp/evalution-" . $studentId. ".pdf";
    file_put_contents($filename, HtmlToPdf($html));
    $mail->AddAttachment($filename); // attachment
    
    $mail->AltBody = "This is the body when user views in plain text format"; //Text Body
    
    if(!$mail->Send()) {
      echo "Mailer Error: Family: $family->id: " . $mail->ErrorInfo . "\n";
    }  else {
      echo "Message has been sent, Family: $family->id:\n";
    }
    
    unlink($filename);
  }
}




?>
