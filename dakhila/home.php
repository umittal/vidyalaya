<?php
require "authentication.inc"; 
require_once "HTML/Template/ITX.php";
require 'libVidyalaya/db.inc';

function PrintResult($result) {

	while ($row = mysql_fetch_array($result, MYSQL_NUM))
	{
		foreach ($row as $attribute)
			print "{$attribute} ";
		print "\n";
	}
}

session_start();

// Connect to an authenticated session or relocate to logout.php
sessionAuthenticate();

$template = new HTML_Template_ITX("./templates");
$template->loadTemplatefile("home.tpl", true, true);

$template->setVariable("USERNAME", $_SESSION["loginUsername"]);
$template->parseCurrentBlock();
$template->show();

if (!$connection = @ mysql_connect($hostname, $username, $password))
  die("Cannot connect");
if (!mysql_selectdb($databasename, $connection))
  showerror();

//    $connection=vidyalaya();

print '

<table>
<tr><td><a href="php/classsize.php">Class Size</tr>
<tr><td><a href="php/studentListByLanguage.php">Student By Language Class</tr>
<tr><td><a href="php/studentListByCulture.php">Student By Culture Class</tr>
<tr><td><a href="php/familyList.php">Families</tr>
</table>
';

?>
