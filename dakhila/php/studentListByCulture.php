<?php
require "../authentication.inc"; 
require_once "HTML/Template/ITX.php";
require "$rootDir/libVidyalaya/db.inc";

if (!$connection = @ mysql_connect($hostname, $username, $password))
  die("Cannot connect");
if (!mysql_selectdb($databasename, $connection))
  showerror();

session_start();
// Connect to an authenticated session or relocate to logout.php
sessionAuthenticate();

$template = new HTML_Template_ITX("$rootDir/templates");
$template->loadTemplatefile("StudentListByLanguage.tpl", true, true);

$query = "select  CultureGrades.id, last_name, first_name 
from Students2003,CultureGrades 
where status=1 and  
      ss_cult_next = CultureGrades.id
order by last_name, first_name
";

if (!($result = mysql_query($query, $connection)))
   showerror();

while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $languageList[$row[0]] .= $row[2] . " " . $row[1]. ", ";
}

$query = "select Description, Room, count(*), Teachers, CultureGrades.id
 from CultureGrades, Students2003 
where ss_cult_next=CultureGrades.id and continuing <>2 and status=1 
group by CultureGrades.id
order by Description
";

$templateName="LANGUAGE";

if (!($result = mysql_query($query, $connection)))
   showerror();

$item=1;
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      // Work with CLASSSIZE block
          $template->setCurrentBlock($templateName);

      $template->setVariable("ITEM", $item++); 
      // Assign the row data to the template placeholders
      $i=0;
      $hyperlink= "<a href=\"classDetails.php\?className=$row[0]\">Participants</a>";
      $template->setVariable("PARTICIPANTS", "$hyperlink");
      $template->setVariable("NAME", $row[$i]);  $i++;
      $template->setVariable("ROOM", $row[$i]);  $i++;
      $template->setVariable("SIZE", $row[$i]);  $i++;
      $template->setVariable("TEACHERS", $row[$i]); $i++;
      $template->setVariable("VALUE", $languageList[$row[$i]]); $i++;

      // Parse the current block
$template->parseCurrentBlock();
}


//Output the web page
$template->show();
?>
