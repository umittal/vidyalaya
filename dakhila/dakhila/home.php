<?php
//require "authentication.inc"; 
require_once "HTML/Template/ITX.php";
require 'libVidyalaya/db.inc';

// Connect to an authenticated session or relocate to logout.php
VidSession::sessionAuthenticate();
$template = new HTML_Template_ITX("./templates");
$template->loadTemplatefile("home.tpl", true, true);

$template->setVariable("USERNAME", $_SESSION["loginUsername"]);
$template->parseCurrentBlock();
$template->show();


print '

<table>
<tr><td><a href="php/classsize.php">Class Size</tr>
<tr><td><a href="php/studentListByLanguage.php">Student By Language Class</tr>
<tr><td><a href="php/studentListByCulture.php">Student By Culture Class</tr>
<tr><td><a href="php/familyList.php">Families</tr>
<tr><td><a href="php/dataViewer.php?command=Family&familyId=47">Family Detail</tr>
<tr><td><a href="php/dataViewer.php?command=Registration&familyId=47">Registration Form</tr>
<tr><td><a href="php/dataViewer.php?command=MedicalForm&studentId=1446">Medical Form</tr>
<tr><td><a href="php/dataViewer.php?command=RegistrationSummary">2011 Registration Summary</tr>
<tr><td><a href="php/dataViewer.php?command=CourseCatalog">Course Catalog</tr>
<tr><td><a href="php/dataViewer.php?command=AvailableCourse&facility=2&year=2011">Available Courses</tr>
<tr><td><a href="php/dataViewer.php?command=ClassRoster&classId="75">Available Courses</tr>
</table>
';

?>
