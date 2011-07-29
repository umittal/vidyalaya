<?php
$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
require_once "HTML/Template/ITX.php";
require_once "$rootDir/libVidyalaya/db.inc";
require_once "$rootDir/libVidyalaya/vidyalaya.inc";
require_once "$rootDir/libVidyalaya/HtmlFactory.inc";

$command=$_GET["command"];
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

  private function Layout() {
    $this->template->loadTemplatefile("Layout.tpl", true, true);

    $this->template->addBlockFile('TOP', 'F_TOP', 'LayoutTop2.tpl');
    $this->template->touchBlock('F_TOP');

    $this->template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
    $this->template->touchBlock('F_CONTENT');
	
    $this->template->setCurrentBlock('HEADER');
    $this->template->setVariable("HEADER", '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
		width="800" height="80" 
		alt="php5 logo"/></a>');
    $this->template->parseCurrentBlock();

    $this->template->setCurrentBlock('MENU');
    $table = "<table width='100%'><tr>";
    $table .= "<td><a href=\"$this->thispage?command=home\">Home</a></td><td align='right'><a href=\"$this->thispage?command=logout\">Logout</a></td>";
    $table .= "</tr></table>\n";
    $this->template->setVariable('MENU', $table);
    $this->template->parseCurrentBlock();

	
    $this->template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
    $this->template->touchBlock('F_BOTTOM');
	
    $this->template->setCurrentBlock('FOOTER');
    $this->template->setVariable("FOOTER", "Copyright (c) 2011 Vidyalya Inc.");
    $this->template->parseCurrentBlock();

  }

  public function login() {
    $html = <<<LOGIN
<div style="position:absolute; top:50%; left:25%; right:25%; border:1px solid silver; overflow:auto; text-align:left">
      <form method="POST" action="/dakhila/logincheck.php">
      <table>
      <tr><td style="padding-right:10px;">email address:</td><td><input type="text" size="50" name="loginUsername"></td></tr>
      <tr><td style="padding-right:10px;">password:</td><td><input type="password" size="10" name="loginPassword"></td></tr>
      </table>
      <p><input type="submit" value="Log in">
      </form>

      <p><i>The access to this system is restricted to authorized users. If you are not an authorized user, please exit immediately. 
      To request a login to this system, please send an email to info@vidyalaya.us</i>
</div>
LOGIN;
    $this->template->setCurrentBlock('RESULT');
    $this->template->setVariable("RESULT", $html);
    $this->template->parseCurrentBlock();
    print $this->template->get();
  }


  public function DoIt($command) {

    VidSession::sessionAuthenticate();
    switch ($command) {

    case "home":
      $html = <<<HOMEPLATE

<ul>
<li style="padding:10px;">Legacy Programs</li>
<table>
<tr><td><a href="classsize.php">Class Size</td></tr>
<tr><td><a href="studentListByLanguage.php">Student By Language Class</tr>
<tr><td><a href="studentListByCulture.php">Student By Culture Class</tr>
<tr><td><a href="familyList.php">Families</tr>
</table>
<li style="padding:10px;">Admissions</li>
<table>
<tr><td><a href="dataViewer2.php?command=Family&familyId=47">Family Detail</tr>
<tr><td><a href="dataViewer2.php?command=Registration&familyId=47">Registration Form</tr>
<tr><td><a href="dataViewer2.php?command=MedicalForm&studentId=1446">Medical Form</tr>
</table>
<li style="padding:10px;">New Data Structure</li>
<table>
<tr><td><a href="dataViewer2.php?command=RegistrationSummary">2011 Registration Summary</tr>
<tr><td><a href="dataViewer2.php?command=CourseCatalog">Course Catalog</tr>
<tr><td><a href="dataViewer2.php?command=AvailableCourse&facility=2&year=2011">Available Courses</tr>
<tr><td><a href="dataViewer2.php?command=ClassRoster&classId=75">Class Roster</tr>
<tr><td><a href="/dakhila/wow/grid.php">Available Courses V2</tr>
</table>

<li style="padding:10px;">Housekeeping</li>
<table>
<tr><td><a href="../password.php">Change Password</td></tr>
</table>


HOMEPLATE;

      $this->template->setCurrentBlock('RESULT');
      $this->template->setVariable("RESULT", $html);
      $this->template->parseCurrentBlock();
      print $this->template->get();
      break;
    case "logout":
      $message = "<p>";

      // An authenticated user has logged out -- be polite and thank them for using your application.
      if (isset($_SESSION["loginUsername"]))
	$message .= "Thanks {$_SESSION["loginUsername"]} for using the Application.";

      // Some script, possibly the setup script, may have set up a  logout message
      if (isset($_SESSION["message"])) {
	  $message .= "<p>Message: " . $_SESSION["message"];
	  unset($_SESSION["message"]);
	}

      $message .= "<p> To login again, please click <a href=\"$this->thispage?command=login\">here</a>\n";

      session_destroy(); // Destroy the session.
      $this->template->setCurrentBlock('RESULT');
      $this->template->setVariable("RESULT", $message);
      $this->template->parseCurrentBlock();
    print $this->template->get();

      break;

    case "FamilyOld":
      $familyId=$_GET["familyId"];
      if ($familyId =="") $familyId="47";
      $family = Family::GetItemById($familyId);
      DisplayFamilyV3($this->template, $family);
      print $this->template->get();
      break;

    case "Family":
      $url = htmlentities($_SERVER['PHP_SELF']) . "?command=Family";
      $familyId = isset($_POST['ID']) ?  $_POST['ID'] : null;

      $form = <<<EOT
	<form method="post" action="$url">
	Family ID: <input type="text" name="ID" value="$familyId"> 
   <input type="submit" name="submit" value="GO"><br>
</form>

EOT;
      $this->template->setCurrentBlock('QUERY');
      $this->template->setVariable('QUERY', $form);
      $this->template->parseCurrentBlock();


      //      if(isset($_POST['submit'])) {
	if (isset($familyId)) {
	    $family = Family::GetItemById($familyId);
	    DisplayFamilyV3($this->template, $family);
	  }
	//      }
      print $this->template->get();
      break;

		
    case "Registration":
      $familyId=$_GET["familyId"];
      if ($familyId =="") $familyId="47";
      $family = Family::GetItemById($familyId);
      DisplayRegistrationV2($this->template, $family);
      print $this->template->get();
      break;
		
		
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
		  from FamilyTracker where year = 1 
		  group by previousYear, currentYear 
		  order by previousYear, currentYear

SQLREGISTRATIONSUMMAY;
		$result = VidDb::query($sql);
		$subTotal = 0; $total = 0; $current = null;
		$html =  "<html><body><table width='400'>\n";
		$html .= "<tr><th>Current Year</th><th>Next Year</th><th>Count</th></tr>\n";
		while ($row = mysql_fetch_array($result)) {
		  $currentYear = $row[0];
		  if ($current == null) $current = $currentYear;
		  if ($current !=  $currentYear) {
		    //show subtotal
			$html .=  "<tr><td colspan=2>";
			$html .=  "Subtotal </td><td align=right> " . $subTotal;
			$html .=  "</td></tr>\n<tr><td>&nbsp;</td></tr>\n";
			$current = $currentYear;
			$subTotal = 0;
		  }
			$html .=  "<tr><td>";
			$html .=  EnumFamilyTracker::NameFromId($row[0]) . "</td><td> " . EnumFamilyTracker::NameFromId($row[1]) .
			"</td><td align=right> " . $row[2] ;
			$html .=  "</td></tr>\n";
			$subTotal += $row[2];
			$total += $row[2];
		}
			$html .=  "<tr><td colspan=2>";
			$html .=  "Subtotal </td><td align=right> " . $subTotal;
			$html .=  "</td></tr>\n<tr><td>&nbsp;</td></tr>\n";
			$html .=  "<tr><td colspan=2>";
			$html .=  "Total </td><td align=right> " . $total;
			$html .=  "</td></tr>\n";
		$html .=  "\n</table></body></html>";
      $this->template->setCurrentBlock('RESULT');
      $this->template->setVariable("RESULT", $html);
      $this->template->parseCurrentBlock();
    print $this->template->get();
		break;

    case "CourseCatalog":
      DisplayCourseCatalog($this->template);
      print $this->template->get();
      break;		

    case "AvailableCourse":
      $year=$_GET["year"];
      if ($year =="") $year=Calendar::CurrentYear();
      $facility=$_GET["facility"];
      if ($facility =="") $facility=Facility::PHHS;
		
      DisplayAvailableClass($this->template, $year, $facility);
      print $this->template->get();
      break;		
      
      case "ClassRoster":
		$classId=$_GET["classId"];
		if ($classId =="") $classId=75;
		DisplayClassRoster($this->template, $classId);
		print $this->template->get();
		break;	

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
