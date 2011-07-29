<?php
$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
require_once "HTML/Template/ITX.php";
require "$rootDir/libVidyalaya/db.inc";

VidSession::sessionAuthenticate();

$template = new HTML_Template_ITX("../templates");
$template->loadTemplatefile("ClassSize.tpl", true, true);


$query = "select Description, Value, Room, count(*), Teachers 

FROM    Students2003 INNER JOIN LanguageGrades ON Students2003.SS_LANG_NEXT=LanguageGrades.ID

WHERE ( (
	 (Students2003.CONTINUING)<>2) And ((Students2003.STATUS) Is Null Or (Students2003.STATUS)<>3)
        ) 
        Or (((Students2003.STATUS)=1)
      ) 


group by Room
order by Description
";

$result = VidDb::query($query);

$templateName="LANGUAGE";
$total=0;

while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      // Work with CLASSSIZE block
          $template->setCurrentBlock($templateName);

      // Assign the row data to the template placeholders
      $i=0;
//$template->setVariable("NAME", $row[$i]);  $i++;
				  $i++;
      $template->setVariable("NAME", $row[$i]); $i++;
      $template->setVariable("ROOM", $row[$i]);  $i++;
      $template->setVariable("SIZE", $row[$i]); $total+= $row[$i];  $i++;
      $template->setVariable("TEACHERS", $row[$i]); $i++;

      // Parse the current block
$template->parseCurrentBlock();
}

$template->setCurrentBlock("LTOTAL");
$template->setVariable("TOTAL", $total);
$template->parseCurrentBlock();


$query = "select Description, Room, count(*), Teachers from CultureGrades, Students2003 
where ss_cult_next=CultureGrades.id and continuing <>2 and status=1 
group by Room
order by Description";

$result = VidDb::query($query);
$templateName="CULTURE";

while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      // Work with CLASSSIZE block
          $template->setCurrentBlock($templateName);

      // Assign the row data to the template placeholders
      $i=0;
      $template->setVariable("NAME", $row[$i]);  $i++;
      $template->setVariable("ROOM", $row[$i]);  $i++;
      $template->setVariable("SIZE", $row[$i]);  $i++;
      $template->setVariable("TEACHERS", $row[$i]); $i++;

      // Parse the current block
$template->parseCurrentBlock();
}

$template->setCurrentBlock("CTOTAL");
$template->setVariable("TOTAL", $total);
$template->parseCurrentBlock();



//Output the web page
$template->show();
?>
