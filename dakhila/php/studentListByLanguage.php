<?php
$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
require_once "HTML/Template/ITX.php";
require "$rootDir/libVidyalaya/db.inc";

VidSession::sessionAuthenticate();

$template = new HTML_Template_ITX("$rootDir/templates");
$template->loadTemplatefile("StudentListByLanguage.tpl", true, true);

$query = "select  LanguageGrades.id, last_name, first_name 
from Students2003,LanguageGrades 
where status=1 and  
      ss_lang_next = LanguageGrades.id
order by last_name, first_name
";
$result = VidDb::query($query);

while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      $languageList[$row[0]] .= $row[2] . " " . $row[1]. ", ";
}

$query = "select Description, Room, count(*), Teachers, LanguageGrades.id, LanguageGrades.Value
 from LanguageGrades, Students2003 
where ss_lang_next=LanguageGrades.id and continuing <>2 and status=1 
group by LanguageGrades.id
order by Description
";

$templateName="LANGUAGE";

$result = VidDb::query($query);
$item=1;
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      // Work with CLASSSIZE block
          $template->setCurrentBlock($templateName);

      $template->setVariable("ITEM", $item++); 
      // Assign the row data to the template placeholders
      $i=0;
      $template->setVariable("NAME", $row[$i]);  $i++;
      $template->setVariable("ROOM", $row[$i]);  $i++;
      $template->setVariable("SIZE", $row[$i]);  $i++;
      $template->setVariable("TEACHERS", $row[$i]); $i++;
      $template->setVariable("VALUE", $languageList[$row[$i]]); $i++;
      $template->setVariable("PARTICIPANTS", "<a href=\"classDetails.php\?className=$row[$i]\">Participants</a>");

      // Parse the current block
$template->parseCurrentBlock();
}


//Output the web page
$template->show();
?>
