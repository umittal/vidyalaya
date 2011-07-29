<?php
require_once "HTML/QuickForm2.php";
require "../authentication.inc"; 
require_once "HTML/Template/ITX.php";
require_once "$rootDir/libVidyalaya/db.inc";
require_once "$rootDir/libVidyalaya/vidyalaya.inc";
$familyDetailLink="/dakhila/php/dataViewer.php?command=Family";
// Bring in the new QuickForm2 & create the appropriate object:


    // Create a GET form with the name QfDemo:
    $form = new HTML_QuickForm2('QfDemo', 'get');

if (!$connection = @ mysql_connect($hostname, $username, $password))
  die("Cannot connect");
if (!mysql_selectdb($databasename, $connection))
  showerror();

session_start();
// Connect to an authenticated session or relocate to logout.php
sessionAuthenticate();
$students = GetAllData();

$template = new HTML_Template_ITX("../templates");
$template->loadTemplatefile("ClassDetails.tpl", true, true);

$templateName="STUDENT";
$template->setCurrentBlock($templateName);

$className=$_GET["className"];
if ($className =="") $className="H4";

	// Create a GET form with the name QfDemo:
	$form = new HTML_QuickForm2('QfDemo', 'get');
	$form->addElement('text', 'Age', null, array('label' => 'Age'));
    if ($form->validate()) {
        # Get here when the form has been filled in
        // $form->freeze();	// If we want the form redisplayed in the way that the user entered it
				// but you need to do another display()
	// $res = $form->exportValues();
        $res = $form->getValue();
	//	echo "Title = " . htmlspecialchars($res['Title']) . " = '" . $Titles[$res['Title']] . "'<br>\n";
	//	echo "FirstName = " . htmlspecialchars($res['FirstName']) . "<br>\n";
	// echo "LastName = " . htmlspecialchars($res['LastName']) . "<br>\n";
	echo "Age = " . htmlspecialchars($res['Age']) . "<br>\n";
	//	echo "Telephone = " . htmlspecialchars($res['Telephone']) . "<br>\n";
    } else {
    	// First time display the form
    // $form->display();
	echo $form;
    }
    
  $count=0;
  foreach ($students as $id => $student) {
    $matched = $student->IsEnrolled;
    if (strncmp($className, "C", strlen("C")) == 0) {
      $matched = $matched && $student->registration->culture->description == $className;
      $teachers = $student->registration->culture->teachers;
      $room = $student->registration->culture->room;
    } else {
      $matched = $matched && $student->registration->language->symbol == $className;
      $teachers = $student->registration->language->teachers;
      $room = $student->registration->language->room;
    }
    if ($matched) {
      $count++;
      $template->setVariable("COUNT", $count);
      $familyId=$student->family->id;
      $familyLink = sprintf("<a href=\"$familyDetailLink&familyId=%s\">%s</a>", $familyId, $id);
      $template->setVariable("ID", $familyLink);
      $template->setVariable("FullName", $student->fullName());
      $template->setVariable("DOB",  date("Y-m-d", strtotime($student->dateOfBirth)));
      $template->setVariable("EMAIL", implode(", ", $student->mailingListArray()));
    }
    $template->parseCurrentBlock();
  }
  
$templateName="CLASSHEAD";
$template->setCurrentBlock($templateName);
$template->setVariable("SIZE", $count);
$template->setVariable("CLASS", $className);
$template->setVariable("TEACHERS", $teachers);
$template->setVariable("ROOM", $room);

$template->parseCurrentBlock();


//Output the web page
$template->show();
?>
