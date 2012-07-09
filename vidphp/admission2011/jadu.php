<?php

$libDir="../../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";
require_once "HTML/Template/ITX.php";
require_once "$libDir/HtmlFactory.inc";
require_once "$libDir/reports.inc";
require_once "$libDir/TwoYear.inc";
require_once "$libDir/Evaluation.inc";
require_once "../../MPDF53/mpdf.php";


//EventCalendar::AddSundays(2012);exit();
EventCalendar::UpdateWeekNumber(2012);exit();

?>
