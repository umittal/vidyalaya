<?php
$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
require_once "HTML/Template/ITX.php";
require_once "$rootDir/libVidyalaya/db.inc";
require_once "$rootDir/libVidyalaya/vidyalaya.inc";
require_once "$rootDir/libVidyalaya/HtmlFactory.inc";
require_once "$rootDir/libVidyalaya/reports.inc";



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
    $this->template->setCurrentBlock('MENU');
    $table = "<table width='100%'><tr>";
    $table .= "<td><a href=\"$this->thispage?command=home\">Home</a></td>";
    $table .= "<td><a href=\"$this->thispage?command=Teachers\">Teachers</a></td>";

    $logout = "<a href='" . $_SERVER['PHP_SELF'] . "?command='logout'>Logout</a>";
    $count=$_SESSION['count'];
    $rightside = isset($_SESSION['loginUsername']) ? "$logout" : "Please Login $count";
    $table .= "<td align='right'>$rightside</td>";

    $table .= "</tr></table>\n";
    $this->template->setVariable('MENU', $table);
    $this->template->parseCurrentBlock();

    $this->template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
    $this->template->touchBlock('F_BOTTOM');
	
    $username=$_SESSION["loginUsername"];
    $dbserver=$_SESSION["dbserver"];
    $count=$_SESSION['count'];

    $this->template->setCurrentBlock('FOOTER');
    $this->template->setVariable("FOOTER", "Copyright (c) 2011 Vidyalaya Inc., ($username,$dbserver, $count )");
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
    $html = <<<LOGIN
      <style type="text/css">
       @import "/dakhila/css/form.css"; 
       </style>
    <script>
      dojo.require("dojo.parser");  
       dojo.require("dijit.form.Button"); 
dojo.require("dijit.form.Form");
      dojo.require("dijit.form.ValidationTextBox");    
    </script>

<div style="position:absolute; top:50%; left:25%; right:25%; overflow:auto; text-align:left">
<div class="formContainer">
      <form method="POST" action="/dakhila/logincheck.php" dojoType="dijit.form.Form" >
    <div class="formTitle">Dakhila Portal Login</div>

      <div class="formRow">
      <label for="email">Email Address:</label>
<input type="text" size="35" name="email" id="email" 
           dojoType="dijit.form.ValidationTextBox" 
           required="true"  
	regExp="\b[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}\b"
           promptMessage="Enter email address."
           invalidMessage="Invalid Email Address." 
           trim="true"
/>
	</div>

      <div class="formRow">
      <label for="password">Password:</label>
       <input type="password" size="10" name="password" id="password" 
           dojoType="dijit.form.ValidationTextBox" 
           required="true"  
           promptMessage="Enter password."
           trim="true"

/>
	</div>
	      <button dojoType="dijit.form.Button" type="submit" >
	Login
	      </button>

</form>
</div>
	      

      <p><i>The access to this system is restricted to authorized users. If you are not an authorized user, please exit immediately. 
      To request a login to this system, please send an email to info@vidyalaya.us</i>
</div>
LOGIN;
    $this->template->setCurrentBlock('RESULT');
    $this->template->setVariable("RESULT", $html);
    $this->template->parseCurrentBlock();
    print $this->template->get();
  }



  // ************************************************************
  public function DoIt($command) {
    VidSession::sessionAuthenticate();
    $this->SetMenu();
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

    // ************************************************************
    case "FamilyOld":
      $familyId=$_GET["familyId"];
      if ($familyId =="") $familyId="47";
      $family = Family::GetItemById($familyId);
      DisplayFamilyV3($this->template, $family);
      print $this->template->get();
      break;

    // ************************************************************
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
	Year: <input type="text" name="YEAR" value="$year"> 
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
      
      $sql = "select * from FamilyTracker inner join Students2003 on FamilyTracker.family = Students2003.PARENT_ID where ";
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
	$html = "";
	$result = VidDb::query($sql);
	$html .= "<table>\n";

	$this->template->addBlockFile('RESULT', 'F_RESULT', 'FamilyTrackerDetails.tpl');
	$this->template->touchBlock('F_RESULT');

	while ($row = mysql_fetch_alias_array($result)) {
	  $ryear=$row["FamilyTracker.year"];
	  $family = Family::GetItemById($row["FamilyTracker.family"]);
	  $student = Student::GetItemById($row["Students2003.ID"]);

	  $this->template->setCurrentBlock("TRACKER");
	  $this->template->setVariable("FAMILYID",$family->id);
	  $this->template->setVariable("STUDENTID",$student->id);
	  $this->template->setVariable("PARENT",$family->parentsName());
	  $this->template->setVariable("STUDENT",$student->fullName());
	  $this->template->setVariable("AGE",intval($student->Age()));
	  $this->template->setVariable("GRADE",$student->Grade());
	  $this->template->setVariable("LANGUAGE",$student->LanguageInterest());
	  $this->template->setVariable("PREVIOUS",EnumFamilyTracker::NameFromId($row["FamilyTracker.previousYear"]));
	  $this->template->setVariable("CURRENT",EnumFamilyTracker::NameFromId($row["FamilyTracker.currentYear"]));
	  $this->template->setVariable("YEAR",$ryear+2010);
	  $this->template->parseCurrentBlock();


	  $html .= "<tr><td>" . $family->parentsName() . "</td><td>" . $student->fullName() . "</td><td>" . intval($student->Age()) . "</td><td>" . $student->Grade(). "</td><td>". $student->LanguageInterest() . "</td></tr>\n";
	}
	$html .= "</table>\n";

	//	$this->template->setCurrentBlock('RESULT');
	//	$this->template->setVariable("RESULT", $html);
	$this->template->parseCurrentBlock();
      }
      print $this->template->get();
      break;


    // ************************************************************
    case "AvailableCourse":
      $year = isset($_GET['year']) ?  $_GET['year'] : null;
      if ($year == null) $year=Calendar::CurrentYear();
      $facility = isset($_GET['facility']) ?  $_GET['facility'] : null;
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
	  <select id="facility" title="facility" name="facility" 
	    dojoType="dijit.form.ComboBox"
        autoComplete="false"
        forceValidOption="true"
			      >
		<option value="1">Eastlake Elementary School</option>
		<option selected  value="2">Parsippany Hills High School</option>
      </select>
   <input type="submit" name="submit" value="Under Construction"><br>
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
      $style = <<<LOCALSTYLE
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
      <style type="text/css">
	td {padding-left: 15px; text-align: right;padding-top: 5px;}
	tr.odd {background-color: #F0F0F6;}
</style>

LOCALSTYLE;

      $year=2011;
      $this->template->setCurrentBlock('RESULT');
      $this->template->setVariable('RESULT', $style . Reports::lcmatrix($year));
      $this->template->parseCurrentBlock();
      print $this->template->get();
      break;		

    // ************************************************************
    case "person":
      $mfs = isset($_GET['MFS']) ?  $_GET['MFS'] : null;
      $id = isset($_GET['id']) ?  $_GET['id'] : null;
      $person = Person::PersonFromId($mfs, $id);
      DisplayPerson($this->template, $person);
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
