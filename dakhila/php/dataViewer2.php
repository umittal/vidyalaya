<?php
$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
require_once "HTML/Template/ITX.php";
require_once "$rootDir/libVidyalaya/db.inc";
require_once "$rootDir/libVidyalaya/vidyalaya.inc";
require_once "$rootDir/libVidyalaya/HtmlFactory.inc";
require_once "$rootDir/libVidyalaya/reports.inc";


// login is special since it is not authenticated
$command=isset($_GET["command"]) ? $_GET["command"] : "login";
if (empty($command)) $command="login";
$dataviewer = new DataViewer("../templates");
switch ($command) {
case "login":
  $dataviewer->login();
  break;
default:
 $dataviewer->DoIt($command);

}



class DataViewer {

  private $template = null;
  private $thispage = null;

  private function SetMenu() {
    /*
    $table = "<table width='100%'><tr>";
    $table .= "<td><a href=\"$this->thispage?command=home\">Home</a></td>";
    $table .= "<td><a href=\"$this->thispage?command=Teachers\">Teachers</a></td>";

    $logout = "<a href='" . $_SERVER['PHP_SELF'] . "?command='logout'>Logout</a>";
    $count=$_SESSION['count'];
    $rightside = isset($_SESSION['loginUsername']) ? "$logout" : "Please Login $count";
    $table .= "<td align='right'>$rightside</td>";

    $table .= "</tr></table>\n";
    */

    $dojomenu = VidSession::Menu();

    $this->template->setCurrentBlock('MENU');
    $this->template->setVariable('MENU', $dojomenu);
    $this->template->parseCurrentBlock();

    $this->template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
    $this->template->touchBlock('F_BOTTOM');
	
    $this->template->setCurrentBlock('FOOTER');
    $this->template->setVariable("FOOTER", VidSession::FooterWeb());
    $this->template->parseCurrentBlock();
  }

  private function Layout() {
    $this->template->loadTemplatefile("Layout.tpl", true, true);

    $this->template->addBlockFile('TOP', 'F_TOP', 'LayoutTop2.tpl');
    $this->template->touchBlock('F_TOP');

    $this->template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
    $this->template->touchBlock('F_CONTENT');
	
    $this->template->setCurrentBlock('HEADER');
    $this->template->setVariable("HEADER", '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
		width="700" height="70" 
		alt="php5 logo"/></a>');
    $this->template->parseCurrentBlock();
  }

  private static function FamilyTrackerChoices($default) {
    if ($default == null) $default=0;
    $choices = array_merge(array(0 => "All"),  EnumFamilyTracker::$choices);
    //    $choices = EnumFamilyTracker::$choices;
    //    $choices[0] = "All";
    $familyTrackerChoices = "";
    //    $familyTrackerChoices = '<option value="0">All</option>\n';
    foreach ( $choices as $value => $choice) {
      $selected = $value == $default ? "selected='selected'" : "";
      $familyTrackerChoices .= "<option value=$value $selected>$choice</option>\n"; 
    }
    return $familyTrackerChoices;
  }

  // ************************************************************
  public function login() {
    $this->SetMenu();

    $html = file_get_contents("../html/login.inc");
    $this->template->setCurrentBlock('RESULT');
    $this->template->setVariable("RESULT", $html);
    $this->template->parseCurrentBlock();
    print $this->template->get();
  }



  // ************************************************************
  public function DoIt($command) {

    // ************************************************************
    if ($command == "logout") {
      VidSession::startSession();
      $message = "<p>";
      // An authenticated user has logged out -- be polite and thank them for using your application.
      if (isset($_SESSION["loginUsername"]))
	$message .= "Thanks {$_SESSION["loginUsername"]} for using the Application.";
      // Some script, possibly the setup script, may have set up a  logout message
      if (isset($_SESSION["message"])) {
	$message .= "<p>Message: " . $_SESSION["message"];
	unset($_SESSION["message"]);
      }
      
      // Unset all of the session variables.
      $_SESSION = array();
      // If it's desired to kill the session, also delete the session cookie.
      // Note: This will destroy the session, and not just the session data!
      if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		  $params["path"], $params["domain"],
		  $params["secure"], $params["httponly"]
		  );
      }
      session_destroy(); // Destroy the session.

      $this->SetMenu();
      $message .= "<p> To login again, please click <a href=\"$this->thispage?command=login\">here</a>\n";
      $this->template->setCurrentBlock('RESULT');
      $this->template->setVariable("RESULT", $message);
      $this->template->parseCurrentBlock();
      print $this->template->get();
      return;
    }


    VidSession::sessionAuthenticate();
    $this->SetMenu();
    if ($command!= "Family" && !VidSession::IsSuperUser()) {
      $html = "<p>Sorry, only administrators are permitted access to this page, please click the back button on your browser</p>";
      $this->template->setCurrentBlock('RESULT');
      $this->template->setVariable('RESULT', $html);
      $this->template->parseCurrentBlock();
      print $this->template->get();
      return;
    }
    switch ($command) {

    // ************************************************************
    case "home":
      $html = file_get_contents("../html/dakhila.inc");
      $this->template->setCurrentBlock('RESULT');
      $this->template->setVariable("RESULT", $html);
      $this->template->parseCurrentBlock();
      print $this->template->get();
      break;

    // ************************************************************
    case "FamilyOld":
      $familyId=$_GET["familyId"];
      if ($familyId =="") $familyId="47";
      $family = Family::GetItemById($familyId);
      DisplayFamilyV3($this->template, $family);
      print $this->template->get();
      break;

    // ************************************************************
    case "newFamily":
      $this->template->setCurrentBlock('RESULT');
      $html = file_get_contents("../html/formNewFamily.inc");
      $this->template->setVariable("RESULT", $html);
      $this->template->parseCurrentBlock();
      $this->template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
      $this->template->touchBlock('F_BOTTOM');
      
      print $this->template->get();
      break;

    // ************************************************************
    case "newEvent":
      $this->template->setCurrentBlock('RESULT');
      $html = file_get_contents("../html/formNewEvent.inc");
      $this->template->setVariable("RESULT", $html);
      $this->template->parseCurrentBlock();
      $this->template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
      $this->template->touchBlock('F_BOTTOM');
      
      print $this->template->get();
      break;

    case "newRegistration":
      $url = htmlentities($_SERVER['PHP_SELF']) . "?command=newRegistration";
      $this->template->setCurrentBlock('QUERY');
      $html = str_replace("URL", $url, file_get_contents("../html/formNewRegistration.inc"));
      $this->template->setVariable("QUERY", $html);
      $this->template->parseCurrentBlock();

      $html="";
      $this->template->setCurrentBlock('RESULT');
      if($_POST['update']) $html = "<p>update</p>";
      if($_POST['register']) $html = "<p>register</p>";
      $this->template->setVariable("RESULT", $html);
      $this->template->parseCurrentBlock();


      $this->template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
      $this->template->touchBlock('F_BOTTOM');
      
      print $this->template->get();
      break;

    // ************************************************************
    case "newClass":
      $this->template->setCurrentBlock('RESULT');
      $html = file_get_contents("../html/formNewClass.inc");
      $this->template->setVariable("RESULT", $html);
      $this->template->parseCurrentBlock();
      $this->template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
      $this->template->touchBlock('F_BOTTOM');
      
      print $this->template->get();
      break;

    // ************************************************************
    case "Family":
      $url = htmlentities($_SERVER['PHP_SELF']) . "?command=Family";
      $familyId = isset($_POST['ID']) ?  $_POST['ID'] : null;

      if (VidSession::IsSuperUser()) {
	$form = file_get_contents("../html/formLookupFamily.inc");
	$form=preg_replace("/==FAMILYID==/", $familyId, $form);
	$form=preg_replace("/==URL==/", $url, $form);

	$this->template->setCurrentBlock('QUERY');
	$this->template->setVariable('QUERY', $form);
	$this->template->parseCurrentBlock();
      }

      if (isset($familyId)) {
	if (VidSession::IsSuperUser()) {
	  $family = Family::GetItemById($familyId);
	} else {
	  $email = $_SESSION["loginUsername"];
	  $person = Person::PersonFromEmail($email); 
	  $family=is_null($person) ? null : $person->home;
	}
	DisplayFamilyV3($this->template, $family);
      }

      print $this->template->get();
      break;
      
    // ************************************************************
    case "OtherContactsList":
      $url = htmlentities($_SERVER['PHP_SELF']) . "?command=OtherContactsList";
      $this->template->addBlockFile('RESULT', 'F_RESULT', 'OtherContactList.tpl');
      $this->template->touchBlock('F_RESULT');
      $this->template->setCurrentBlock("HIDDENFORMS");
      $this->template->setVariable("CONTACTFORM", file_get_contents("../html/ContactForm.inc"));
      $this->template->parseCurrentBlock();
  
      
      $templateName="CONTACTS";

      $query ="SELECT 
  phone,
  OtherContacts.`name`,
  COUNT(DISTINCT ID) 
FROM
  OtherContacts 
  LEFT OUTER JOIN `Students2003` 
    ON (
      phone = Students2003.`EmergencyContact` 
      OR phone = Students2003.`Hospital` 
      OR phone = `Students2003`.`Dentist` 
      OR phone = Students2003.`PrimaryDoctor`
    ) 

GROUP BY phone,
  OtherContacts.`name` 
  ORDER BY COUNT(DISTINCT id) DESC";

      $result = VidDb::query($query);
      while ($row = mysql_fetch_array($result)) {
	$phone = $row[0];
	$name = $row[1];
	$count = $row[2];
	$this->template->setCurrentBlock($templateName);
	$this->template->setVariable("PHONE", formatPhone($phone));
	$this->template->setVariable("NAME", $name);
	$this->template->setVariable("COUNT", $count);
	$this->template->parseCurrentBlock();
      }
      
      print $this->template->get();
      break;
	
    // ************************************************************
    case "OtherContacts":
      $url = htmlentities($_SERVER['PHP_SELF']) . "?command=OtherContacts";
      $phone = isset($_POST['phone']) ?  $_POST['phone'] : null;

      $form = <<<EOT
<script>
  dojo.require("dijit.form.Button"); 
</script>
<a href="/dakhila/php/dataViewer2.php?command=OtherContactsList">Contact List</a>
        <form  dojoType="dijit.form.Form" id="phonesearch" name="phonesearch" method="post" action="$url">
   <script type="dojo/method" data-dojo-event="onReset">
        return confirm('Press OK to reset widget values');
    </script>

    <script type="dojo/method" data-dojo-event="onSubmit">
        if(this.validate()){
            return confirm('Form is valid, press OK to submit');
        }else{
            alert('Form contains invalid data.  Please correct first');
            return false;
        }
        return true;
    </script>
	Phone number:     <input type="text" id="phone" name="phone"
    dojoType="dijit.form.ValidationTextBox"
    promptMessage="Enter Contact  Phone(xxx-xxx-xxxx)." 
    size="12"
    maxLength="12"
	value="$phone"
    style="width: 120px;" 
    required="true"
     regExp="^[0-9]\d{2}-\d{3}-\d{4}$"
    invalidmessage="Telephone number format: xxx-xxx-xxxx"
    />

<button dojoType="dijit.form.Button" type="submit" >Search</button>
</form>

EOT;
      $this->template->setCurrentBlock('QUERY');
      $this->template->setVariable('QUERY', $form);
      $this->template->parseCurrentBlock();

      if (isset($phone)) {
	$phone = preg_replace('/[^0-9]/', '', $phone);
	//	print "<p>looking up $phone</p>\n";
	$contact = OtherContacts::ObjectFromKey($phone);
	DisplayContact($this->template, $contact);
      }
      //      }
      print $this->template->get();
      break;

    // ************************************************************
    case "Student":
      $url = htmlentities($_SERVER['PHP_SELF']) . "?command=Student";
      $studentId = isset($_POST['ID']) ?  $_POST['ID'] : null;

      $form = <<<EOT
	<form method="post" action="$url">
	Student ID: <input type="text" name="ID" value="$studentId"> 
	<input type="submit" name="submit" value="GO"><br>
</form>

EOT;
      $this->template->setCurrentBlock('QUERY');
      $this->template->setVariable('QUERY', $form);
      $this->template->parseCurrentBlock();


	if (isset($studentId)) {
	    $student = Student::GetItemById($studentId);
	    DisplayStudent($this->template, $student);
	  }

      print $this->template->get();
      break;

		
    // ************************************************************
    case "Registration":
      $familyId=$_GET["familyId"];
      if ($familyId =="") $familyId="47";
      $family = Family::GetItemById($familyId);
      DisplayRegistrationV2($this->template, $family);
      print $this->template->get();
      break;
		
		
    // ************************************************************
    case "MedicalForm":
      $studentId=$_GET["studentId"];
      if ($studentId =="") $studentId="1446";
      $item = Student::GetItemById($studentId);
      DisplayStudentMedicalInformationV3($this->template, $item);
      print $this->template->get();
      break;
		
    case "RegistrationSummary":
	  $sql = <<< SQLREGISTRATIONSUMMAY
	    select previousYear, currentYear, count(*) 
	    from FamilyTracker where year = 2 
	    group by previousYear, currentYear 
	    order by previousYear, currentYear

SQLREGISTRATIONSUMMAY;
	  $result = VidDb::query($sql);
	  $current = null; 
	  $subTotal = 0; $subTotal1 = 0; $subTotal2 = 0; $subTotal3 = 0;
	  $total = 0; $total1 = 0; $total2 = 0; $total3 = 0;
	  $html =  "<html> ";
	  $html .= "<head><style type=\"text/css\">
.ou { font-style:oblique;text-decoration:underline; }
</style>
<script>
  function showTrackerDetails(year, previous, current) {
  var form=dijit.byId(\"trackerForm\");
  var idfield=dijit.byId(\"YEAR\"); idfield.attr(\"value\", year);
  var idfield=dijit.byId(\"PREVIOUS\"); idfield.attr(\"value\", previous);
  var idfield=dijit.byId(\"CURRENT\"); idfield.attr(\"value\", current);
  if (form) {form.submit();} else {alert (\"form not found\");}
  }
</script>

</head><body>
<form method=\"post\" action=\"/dakhila/php/dataViewer2.php?command=FamilyTracker\" style=\"display:none\" id=\"trackerForm\" dojoType=\"dijit.form.Form\">
Year: <input type=\"text\" dojoType=\"dijit.form.TextBox\" name=\"YEAR\" id=\"YEAR\"> 
Previous: <input type=\"text\" dojoType=\"dijit.form.TextBox\" name=\"PREVIOUS\"  id=\"PREVIOUS\"> 
Current: <input type=\"text\" dojoType=\"dijit.form.TextBox\" name=\"CURRENT\" id=\"CURRENT\"> 
<input type=\"submit\" name=\"go\" value=\"GO\"><br>
</form>

";
	  $html .= "<h4>Registration Status (2012-13)</h4><table width='400'  class='tablesorter'>\n";
	  $html .= "<thead><tr><th>2011-12</th><th>2012-13</th><th>Yes</th><th>No</th><th>Maybe</th></tr></thead><tbody>\n";
	  while ($row = mysql_fetch_array($result)) {
	    $currentYear = $row[0];
	    if ($current == null) $current = $currentYear;
	    if ($current !=  $currentYear) {
	      //show subtotal

	      $html .=  "<tr><td>";
	      $html .=  "Subtotal </td><td style='font-weight:bold;'> " . $subTotal . "</td>";
	      $html .=  "<td align=right> " . $subTotal1 . "</td><td align=right>" . $subTotal2 . "</td><td align=right>" . $subTotal3 . "</td></tr>\n";
	      $html .= "<tr><td colspan=5>&nbsp;</td></tr>\n";
	      $current = $currentYear;
	      $subTotal = 0;  $subTotal1 = 0; $subTotal2 = 0; $subTotal3 = 0;
	    }
	    $html .=  "<tr><td>";
	    $html .=  EnumFamilyTracker::NameFromId($row[0]) . "</td><td> " . EnumFamilyTracker::NameFromId($row[1]) ."</td>";

	    $number = "<td class=\"ou\" onclick=\"showTrackerDetails(2, $row[0], $row[1])\" align=\"right\" onmouseover=\"this.style.cursor='pointer'\">$row[2]</td>";

	    switch ($row[1]) {
	    case EnumFamilyTracker::registered:
	      //	      $html .= "<td align=right> " . $row[2] . "</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
	      $html .= "$number<td>&nbsp;</td><td>&nbsp;</td></tr>\n";
	      $subTotal1 += $row[2];
	      $total1 += $row[2];
	      break;
	    case EnumFamilyTracker::pendingInvitation:
	    case EnumFamilyTracker::pendingRegistration:
	      $html .= "<td>&nbsp;</td><td>&nbsp;</td>$number</tr>\n";
	      $subTotal3 += $row[2];
	      $total3 += $row[2];
	      break;
	    default:
	      $html .= "<td>&nbsp;</td>$number<td>&nbsp;</td></tr>\n";
	      $subTotal2 += $row[2];
	      $total2 += $row[2];
	      break;
	
	    }
	    $subTotal += $row[2];
	    $total += $row[2];
	  }
	  $html .=  "</tbody><tfooter>";
	  $html .=  "<tr><td>Subtotal </td><td style='font-weight:bold;'> " . $subTotal . "</td>";
	  $html .=  "<td align=right> " . $subTotal1 . "</td><td align=right>" . $subTotal2 . "</td><td align=right>" . $subTotal3 . "</td></tr>\n";
	  $html .=  "<tr><td colspan=5>&nbsp;</td></tr>\n";
	  $html .=  "<tr><td>Total </td><td style='font-weight:bold;'> " . $total."</td>\n";
	  $html .=  "<td align=right> " . $total1 . "</td><td align=right>" . $total2 . "</td><td align=right>" . $total3 . "</td></tr>\n";

	  $html .=  "</tfooter></table>\n";

	  $html .= "<p>Total Tuition Collected: " . FamilyTracker::TuitionCollected() . "</p>\n";
	  $html .= "</body></html>";
	  $this->template->setCurrentBlock('RESULT');
	  $this->template->setVariable("RESULT", $html);
	  $this->template->parseCurrentBlock();
	  print $this->template->get();
	  break;

    // ************************************************************
    case "CourseCatalog":
      DisplayCourseCatalog($this->template);
      print $this->template->get();
      break;		

    case "FamilyTracker":
      $url = htmlentities($_SERVER['PHP_SELF']) . "?command=FamilyTracker";
      $year = isset($_POST['YEAR']) ?  $_POST['YEAR'] : null;
      $previous = isset($_POST['PREVIOUS']) ?  $_POST['PREVIOUS'] : null;
      $current = isset($_POST['CURRENT']) ?  $_POST['CURRENT'] : null;

      $previousChoices = self::FamilyTrackerChoices($previous);
      $currentChoices = self::FamilyTrackerChoices($current);

      $form = <<<EOT
	<script>
	dojo.require("dijit.form.Select");
        </script>
	<form method="post" action="$url">
	Year: 
	<select id="YEAR" dojoType="dijit.form.Select" name="YEAR">
	<option value=0>2010</option>
	<option value=1>2011</option>
	<option value=2 selected="selected">2012</option>
	</select>
	Previous: 
	<select id="previous" dojoType="dijit.form.Select" name="PREVIOUS">
	$previousChoices
	</select>
	Current: 
	<select id="current" dojoType="dijit.form.Select" name="CURRENT">
	$currentChoices
	</select>
   <input type="submit" name="submit" value="GO"><br>
</form>

EOT;

      $this->template->setCurrentBlock('QUERY');
      $this->template->setVariable('QUERY', $form);
      $this->template->parseCurrentBlock();
      
      $sql = "select * from FamilyTracker  where ";
      $csv = array();
      if (isset($_POST['YEAR']) && $_POST['YEAR'] != "") {
	$year = $_POST['YEAR'];
	if ($year >= 2010) $year -= 2010;
	$csv[] = "year  = " . $year;
      }
      if (isset($_POST['PREVIOUS']) && $_POST['PREVIOUS'] != 0) $csv[] = "previousYear  = " . $_POST['PREVIOUS'];
      if (isset($_POST['CURRENT']) && $_POST['CURRENT'] != 0) $csv[] = "currentYear  = " . $_POST['CURRENT'];

      if (count($csv) != 0) {
	$sql .= implode (" and  ", $csv);
	$result = VidDb::query($sql);

	$this->template->addBlockFile('RESULT', 'F_RESULT', 'FamilyTrackerDetails.tpl');
	$this->template->touchBlock('F_RESULT');

	$count=0;
	while ($row = mysql_fetch_alias_array($result)) {
	  $ryear=$row["FamilyTracker.year"];
	  $family = Family::GetItemById($row["FamilyTracker.family"]);

	  $this->template->setCurrentBlock("TRACKER");
	  $this->template->setVariable("FAMILYID",$family->id);
	  $this->template->setVariable("HOMEPHONE",$family->phone);
	  $this->template->setVariable("PARENT",$family->parentsName());
	  if ($row["FamilyTracker.previousYear"] == EnumFamilyTracker::waitlist) {
	    $this->template->setVariable("PREVIOUS",$family->priority_date );
	  } else {
	    $this->template->setVariable("PREVIOUS",EnumFamilyTracker::NameFromId($row["FamilyTracker.previousYear"]));
	  }
	  $this->template->setVariable("CURRENT",EnumFamilyTracker::NameFromId($row["FamilyTracker.currentYear"]));
	  $this->template->setVariable("YEAR",$ryear+2010);
	  $this->template->parseCurrentBlock();
	  $count++;
	}
	$this->template->parseCurrentBlock();
	$this->template->setCurrentBlock("COUNT");
	$this->template->setVariable("COUNT",$count);
	$this->template->parseCurrentBlock();
      }
      print $this->template->get();
      break;

    // ************************************************************
    case "WaitlistStudents":
      $this->template->addBlockFile('RESULT', 'F_RESULT', 'WaitlistStudents.tpl');
      $this->template->touchBlock('F_RESULT');

      $this->template->setCurrentBlock("HIDDENFORMS");
      $this->template->setVariable("STUDENTFORM", file_get_contents("../html/StudentForm.inc"));
      $this->template->parseCurrentBlock();

      $count=0;
      foreach (FamilyTracker::GetAll() as $tracker) {
	$family = Family::GetItemById($tracker->family);
	if ($tracker->previousYear == EnumFamilyTracker::registered) continue;
	if ($tracker->previousYear != EnumFamilyTracker::waitlist) die("famiy $family->id is neither registered nor waitlist");
	if (!($tracker->currentYear == EnumFamilyTracker::registered || 
	      $tracker->currentYear == EnumFamilyTracker::pendingRegistration || 
	      $tracker->currentYear == EnumFamilyTracker::pendingInvitation )) continue;


	foreach ($family->EligibleChildren() as $child) {
	  $this->template->setCurrentBlock("WAITLIST");
	  $this->template->setVariable("ID",$child->id);
	  $this->template->setVariable("LANGUAGE",$child->LanguageInterest());
	  $this->template->setVariable("FIRST",$child->firstName);
	  $this->template->setVariable("LAST",$child->lastName);
	  $this->template->setVariable("PARENTS",$family->parentsName());
	  $this->template->setVariable("STATUS",EnumFamilyTracker::NameFromId($tracker->currentYear));
	  $this->template->parseCurrentBlock();
	  $count++;
	}
      }
      
      $this->template->setCurrentBlock("COUNT");
      $this->template->setVariable("COUNT",$count);
      $this->template->parseCurrentBlock();
      
      print $this->template->get();
      break;

    // ************************************************************
    case "AvailableCourse":
      //      $year = isset($_GET['year']) ?  $_GET['year'] : null;
      $year = isset($_POST['year']) ?  $_POST['year'] : null;
      if ($year == null) $year=Calendar::CurrentYear();
      //      $facility = isset($_GET['facility']) ?  $_GET['facility'] : null;
      $facility = isset($_POST['facility']) ?  $_POST['facility'] : null;
      if ($facility =="") $facility=Facility::PHHS;

      $url = htmlentities($_SERVER['PHP_SELF']) . "?command=AvailableCourse";


      $form = <<<EOT
    <script>
      dojo.require("dijit.form.ComboBox");    
      dojo.require("dijit.form.Form");    
      dojo.require("dijit.form.ValidationTextBox");    
    </script>

<div class="formContainer">
	<form method="post" action="$url"
	dojoType = "dijit.form.Form">

	<div class="formRow"> 
	Year: <input type="text" name="year" id="year" value="$year" size=6
           dojoType="dijit.form.ValidationTextBox" 
           required="true"  
	regExp="\b201\d\b"
           promptMessage="Enter School Start Year."
           invalidMessage="Invalid School Start Year." 
           trim="true"

	> 
      <label for="facility">Facility:</label> 
<!--
	  <select id="facility1" title="facility1" name="facility1" 
	    dojoType="dijit.form.ComboBox"
        autoComplete="false"
        forceValidOption="true"
			      >
		<option value="1">Eastlake Elementary School</option>
		<option selected  value="2">Parsippany Hills High School</option>
      </select>
-->
	  <select id="facility" title="facility" name="facility">
		<option value="1">Eastlake Elementary School</option>
		<option selected="selected"  value="2">Parsippany Hills High School</option>
      </select>
   <input type="submit" name="submit" value="GO"><br>
    </div>




</form>
</div>

EOT;
      $this->template->setCurrentBlock('QUERY');
      $this->template->setVariable('QUERY', $form);
      $this->template->parseCurrentBlock();
		
      DisplayAvailableClass($this->template, $year, $facility);
      print $this->template->get();
      break;		
      
    // ************************************************************
      case "ClassRoster":
		$classId=$_GET["classId"];
		if ($classId =="") $classId=75;
		DisplayClassRoster($this->template, $classId);
		print $this->template->get();
		break;	


    // ************************************************************
    case "Rooms":
      $year = isset($_GET['year']) ?  $_GET['year'] : null;
      if ($year == null) $year=Calendar::CurrentYear();
      $facility = isset($_GET['facility']) ?  $_GET['facility'] : null;
      if ($facility =="") $facility=Facility::PHHS;

      $url = htmlentities($_SERVER['PHP_SELF']) . "?command=$command";

      DisplayRooms($this->template, $year, $facility);
      print $this->template->get();
      break;		


    // ************************************************************
    case "Room":
      $url = htmlentities($_SERVER['PHP_SELF']) . "?command=$command";
      $roomId = isset($_POST['ID']) ?  $_POST['ID'] : null;
      if (empty($roomId) && isset($_GET["id"])) {
	$roomId=$_GET["id"];
      }

      $form = <<<EOT
	<form method="post" action="$url">
	Room ID: <input type="text" name="ID" value="$roomId"> 
	<input type="submit" name="submit" value="GO"><br>
</form>

EOT;
      $this->template->setCurrentBlock('QUERY');
      $this->template->setVariable('QUERY', $form);
      $this->template->parseCurrentBlock();


	if (isset($roomId)) {
	    $room = Rooms::GetItemById($roomId);
	    DisplayRoom($this->template, $room);
	  }

      print $this->template->get();
      break;

		
    // ************************************************************
    case "Volunteers":
      $year=2011;
      $js = <<< NAMAKOOL
<script type="text/javascript">
$(document).ready( function() {
    \$table = $("#maintable").tablesorter({widthFixed: true, widgets: ['zebra']
	});

});
</script>
NAMAKOOL;

      $this->template->setCurrentBlock('RESULT');
      $this->template->setVariable('RESULT', $js . Reports::VolunteerListV2($year, true));
      $this->template->parseCurrentBlock();
      print $this->template->get();
      break;


    // ************************************************************
    case "VolunteerCodes":
      $this->template->setCurrentBlock('RESULT');
      $this->template->setVariable('RESULT', Reports::VolunteerCodes($this->template));
      $this->template->parseCurrentBlock();
      print $this->template->get();
      break;
    // ************************************************************
    case "EventRSVP":
      $url = htmlentities($_SERVER['PHP_SELF']) . "?command=EventRSVP";
      $itemId = isset($_POST['ID']) ?  $_POST['ID'] : null;

      $form = <<<EOT
	<form method="post" action="$url">
	Event ID: <input type="text" name="ID" value="$itemId"> 
   <input type="submit" name="submit" value="GO"><br>
</form>

EOT;
      $this->template->setCurrentBlock('QUERY');
      $this->template->setVariable('QUERY', $form);
      $this->template->parseCurrentBlock();


      $js = <<< NAMAKOOL
<script type="text/javascript">
$(document).ready( function() {
    \$table = $("#Interested").tablesorter({widthFixed: true, widgets: ['zebra']
	});
    \$table = $("#Declined").tablesorter({widthFixed: true, widgets: ['zebra']
	});
    \$table = $("#Registered").tablesorter({widthFixed: true, widgets: ['zebra']
	});

});
</script>
NAMAKOOL;

      if (isset($itemId)) {
	$this->template->setCurrentBlock('RESULT');
	$this->template->setVariable('RESULT', $js . Reports::EventRSVP($itemId));
	$this->template->parseCurrentBlock();
      }
      print $this->template->get();
      break;


    // ************************************************************
    case "Teachers":
      $year=2011;
      $js = <<< NAMAKOOL
<script type="text/javascript">
$(document).ready( function() {
    \$table = $("#table1").tablesorter({widthFixed: true, widgets: ['zebra'],
	  headers:{0:{sorter: false},1:{sorter: false},2:{sorter: false},3:{sorter: false}, 4:{sorter: false}}
      });
    \$table = $("#table2").tablesorter({widthFixed: true, widgets: ['zebra'],
	  headers:{0:{sorter: false},1:{sorter: false},2:{sorter: false},3:{sorter: false}, 4:{sorter: false}}
});
    \$table = $("#table3").tablesorter({widthFixed: true, widgets: ['zebra'],
	  headers:{0:{sorter: false},1:{sorter: false},2:{sorter: false},3:{sorter: false}, 4:{sorter: false}}
});
    \$table = $("#table4").tablesorter({widthFixed: true, widgets: ['zebra'],
	  headers:{0:{sorter: false},1:{sorter: false},2:{sorter: false},3:{sorter: false}, 4:{sorter: false}}
 });
    \$table = $("#table5").tablesorter({widthFixed: true, widgets: ['zebra'],
	  headers:{0:{sorter: false},1:{sorter: false},2:{sorter: false},3:{sorter: false}, 4:{sorter: false}}
 });

});
</script>
NAMAKOOL;

      $this->template->setCurrentBlock('RESULT');
      $this->template->setVariable('RESULT', $js . Reports::TeacherListV2($year, true));
      $this->template->parseCurrentBlock();
      print $this->template->get();
      break;

    // ************************************************************
    case "lcmatrix":
      $url = htmlentities($_SERVER['PHP_SELF']) . "?command=lcmatrix";
	$year = isset($_POST['year']) ?  $_POST['year'] : 2012;
	$form = <<<LCMATRIXFORM
<script type="text/javascript">
$(document).ready( function() {
    \$table = $("#table1").tablesorter({widthFixed: true, widgets: ['zebra'],
	  headers:{0:{sorter: false},1:{sorter: false},2:{sorter: false},3:{sorter: false}, 4:{sorter: false},
                   5:{sorter: false},6:{sorter: false},7:{sorter: false}, 8:{sorter: false},9:{sorter: false},
         10:{sorter: false},11:{sorter: false},12:{sorter: false}, 13:{sorter: false},14:{sorter: false},15:{sorter: false}

	}

 });

});
</script>
<div class="formContainer">
	<form method="post" action="$url"
	dojoType = "dijit.form.Form">

	<div class="formRow"> 
	Year: <input type="text" name="year" id="year" value="$year" size=6
           dojoType="dijit.form.ValidationTextBox" 
           required="true"  
	regExp="\b201\d\b"
           promptMessage="Enter School Start Year."
           invalidMessage="Invalid School Start Year." 
           trim="true"

	> 
   <input type="submit" name="submit" value="GO"><br>
    </div>
</form>
</div>
LCMATRIXFORM;

      $this->template->setCurrentBlock('QUERY');
      $this->template->setVariable('QUERY', $form);
      $this->template->parseCurrentBlock();
		

      $this->template->setCurrentBlock('RESULT');
      $this->template->setVariable('RESULT',  Reports::lcmatrix($year));
      $this->template->parseCurrentBlock();
      print $this->template->get();
      break;		

    // ************************************************************
    case "person":
      $email = isset($_POST['email']) ?  $_POST['email'] : null;
      $form = <<<EOT
    <script>
      dojo.require("dojo.parser");  
       dojo.require("dijit.form.Button"); 
dojo.require("dijit.form.Form");
      dojo.require("dijit.form.ValidationTextBox");    
    </script>
<div class="formContainer">
      <form method="POST" action="$this->thispage?command=person" dojoType="dijit.form.Form" >
      <div class="formRow">
      <label for="email">Email Address:</label>
<input type="text" size="35" name="email" id="email"  value="$email"
           dojoType="dijit.form.ValidationTextBox" 
           required="true"  
	regExp="\b[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}\b"
           promptMessage="Enter email address."
           invalidMessage="Invalid Email Address." 
           trim="true"
/>
	      <button dojoType="dijit.form.Button" type="submit" >
	Go
	      </button>
	</div>
</div>
</form>
EOT;
      $this->template->setCurrentBlock('QUERY');
      $this->template->setVariable('QUERY', $form);
      $this->template->parseCurrentBlock();

      if (!is_null($email)) {
      $person = Person::PersonFromEmail($email);
      //      $person = Person::PersonFromId($mfs, $id); 
      if (!is_null($person))
	DisplayPerson($this->template, $person);
      }
      print $this->template->get();
      break;		


    // ************************************************************
    case "help":
      $query="select 1";
      $result  = VidDb::query($query);

      $html = "<p> I want to help you my son";
      $a = $_SERVER['SERVER_NAME'];
      $html .= "<p> server name is $a";
      $a=$_SERVER['PHP_SELF'];
      $html .= "<p> porgram name  $a";
      $a=VidDb::$dbserver;
      $html .= "<p> database server $a";
      print $html;
      break;

    // ************************************************************
    default:
      $html = "<p>Please specify a valid command for the data you want to see";
      print $html;
    }
  }

  public function __construct($templateDir) {
    $this->template = new HTML_Template_ITX($templateDir);
    $this->thispage = $_SERVER['PHP_SELF'];
    $this->Layout();
  }
}

?>
</body>
</html>
