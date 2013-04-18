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

adolescent(); exit();
//bodySubject(); exit ();


//EventCalendar::UpdateWeekNumber(2012);exit();

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

function adolescent() {
    $query = "select distinct student from Enrollment 
              left join Students2003 on Enrollment.student = Students2003.ID
	      where Students2003.YearFirstGrade > 2001 and  Students2003.YearFirstGrade < 2006
             ";
    $result = VidDb::query($query);
    
    $fh = tmpfile();
    if (!$fh) die ("could not open temporary file for writing");
    fwrite ($fh, "Student, Parents, Grade\n");
    while ($row = mysql_fetch_array($result)) {
      $s = Student::GetItemById($row[0]);
      $csv=array();
      $csv[] = $s->fullName();
      $csv[] = $s->parentsName();
      $csv[] = Calendar::GradeAt($s->firstGradeYear, Calendar::RegistrationSession);
      fputcsv($fh, $csv);
    }

    $filename = "/home/umesh/Dropbox/Vidyalaya-Roster/2013-14/roster/adolescent.csv";
    fseek($fh, 0);
    file_put_contents("$filename", stream_get_contents($fh));
    print "saved $filename\n";
    fclose($fh);
}

function bodySubject() {
  EmailMessageText::findSubjectBody("001", $body, $subject);
  print "body is \n$body\n\n";
  print "subject is \n$subject\n\n";
}


?>
