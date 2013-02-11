<?php

$libDir="../../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";
require_once "HTML/Template/ITX.php";
require_once "$libDir/HtmlFactory.inc";
require_once "$libDir/reports.inc";
require_once "$libDir/TwoYear.inc";
require_once "$libDir/Evaluation.inc";
require_once "$libDir/FeeCheck.inc";
require_once "$libDir/OpeningDay.inc";
require_once "../../MPDF53/mpdf.php";


EventCalendar::UpdateWeekNumber(2012);exit();

//FeeCheck::email1112(); exit ();


//OpeningDay::DisplayBoard();exit();
//OpeningDay::PrintDistributionMaterial();exit();
//OpeningDay::PrintDistributionMaterialFamily(Family::GetItemById(546), 2012); exit();


//TwoYearLayout::updateRegistrationDate();exit();


foreach (FeeCheck::CreateForYear(2012) as $item) {
  print $item . "\n\n";
}
exit();


//EventCalendar::AddSundays(2012);exit();

?>
