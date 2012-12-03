<?php
require_once "vidyalaya.inc";
require_once "HtmlFactory.inc";
require_once "TwoYear.inc";
/** Error reporting */
error_reporting(E_ALL | E_STRICT | E_NOTICE);

class VidBook {
  private $objPHPExcel = null;
	
  public function fillData($sheet) {


    $sheet->getColumnDimension('B')->setAutoSize(true);
    $love = "I love you";
    $sheet->setCellValue('A1', 'Terms and conditions');
    $sheet->setCellValue('A3', $love);
  }
	
  public function setActiveSheet($id, $title, $header) {
    $this->objPHPExcel->createSheet($id);
    $this->objPHPExcel->setActiveSheetIndex($id);
    $this->objPHPExcel->getActiveSheet()->setTitle($title);
    $sheet = $this->objPHPExcel->getActiveSheet();
    $sheet->getHeaderFooter()->setOddHeader('&L&B'.$header.'&RPrinted on &D');
    $sheet->getHeaderFooter()->setOddFooter('&L&B' . $this->objPHPExcel->getProperties()->getTitle() . '&RPage &P of &N');
    $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
    $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    return $sheet;
  }
	
  public function __construct() {
    require_once "PHPExcel/PHPExcel.php";
    $this->objPHPExcel = new PHPExcel();
    $this->objPHPExcel->getProperties()->setCreator("Umesh Mittal")
      ->setLastModifiedBy("Umesh Mittal")
      ->setTitle("Vidyalaya Report")
      ->setSubject("Office 2007 XLSX Test Document")
      ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
      ->setKeywords("office 2007 openxml php")
      ->setCategory("Test result file");
  }
	
  public function SaveWorkbook($fileName) {
    $objWriter = new PHPExcel_Writer_Excel2007($this->objPHPExcel);
    $objWriter->save($fileName);
  }


}

class Reports {

  public static function FeeCheckFamily($template, $items) {
    if (empty($items)) return;
    $template->addBlockFile('RESULT', 'F_RESULT', 'FeeCheck.tpl');
    $template->touchBlock('F_RESULT');
    foreach ($items as $item) {
      $templateName="BLOCK";
      $template->setCurrentBlock($templateName);
      $template->setVariable("FAMILYID", $item->family->id);
      $template->setVariable("SESSION",Calendar::SessionFromYear($item->year));
      $template->setVariable("PARENTS", $item->family->parentsName());
      $template->setVariable("REGISTRATIONDATE", $item->registrationDate);
      $template->setVariable("TUITION", $item->tuition);
      $template->setVariable("NEWENROLLMENT", $item->newEnrollment);
      $template->setVariable("ADJUSTMENT", $item->adjustment);
      $template->setVariable("REQUIRED", $item->required);
      $template->setVariable("PAID", $item->paid);
      $template->setVariable("DIFF", $item->diff);
      $status = $item->diff == 0 ? "OK" : "NOT OK";
      $template->setVariable("STATUS", $status);

      $template->parseCurrentBlock();
    }
  }
	
  public static function FeeCheckReena($template, $items2011, $items2012) {
    $template->addBlockFile('RESULT', 'F_RESULT', 'FeeCheckReena.tpl');
    $template->touchBlock('F_RESULT');

    $familyTotal = array();
    foreach ($items2011 as $item) {
      // show only registered families for now. add check box later
      if ($item->status != EnumFamilyTracker::registered) continue;
      $familyTotal[$item->family->id] =$item->diff;
    }
    foreach ($items2012 as $item) {
      // show only registered families for now. add check box later
      if ($item->status != EnumFamilyTracker::registered) continue;
      if (array_key_exists($item->family->id, $familyTotal)) {
	$familyTotal[$item->family->id] +=$item->diff;
      } else {
	$familyTotal[$item->family->id] =$item->diff;
      }
    }

    foreach($familyTotal as $familyId => $diff) {
      $templateName="BLOCK";
      $template->setCurrentBlock($templateName);
      $template->setVariable("FAMILYID", $familyId);
      $template->setVariable("DIFF", $diff);
      $key= $familyId . ":" . "1";
      $foo = array_key_exists($key, $items2011) ? $items2011[$key]->diff : 0;
      $template->setVariable("YEAR2011", $foo);

      $key= $familyId . ":" . "2";
      $foo = array_key_exists($key, $items2012) ? $items2012[$key]->diff : 0;
      $template->setVariable("YEAR2012", $foo);

      //      $template->setVariable("REQUIRED", $item->required);
      //      $template->setVariable("PAID", $item->paid);
      //      $template->setVariable("SESSION",Calendar::SessionFromYear($item->year));
      $template->setVariable("PARENTS", Family::GetItemById($familyId)->parentsName());
      //      $status = $item->diff == 0 ? "OK" : "NOT OK";
      //      $template->setVariable("STATUS", $status);

      $template->parseCurrentBlock();
    }
  }
	
  public static function TwoYearSummary($template) {
    $continuing = array(); $leaving=array(); $newstudent=array();
    $t_l = $t_c = $t_n = 0;
    foreach(TwoYearLayout::GetAll() as $two) {
      switch ($two->status) {
      case TwoYearLayout::Leaving:
	$course=Department::CharFromId($two->previousYear->language) . $two->previousYear->languageLevel;
	$courslist[$course]=1;
	if (!isset($leaving[$course])) {$leaving[$course]=1;}else{ $leaving[$course]++;}
	$t_l++;
	if (empty($two->previousYear->cultureLevel)) continue;
	$course="C". $two->previousYear->cultureLevel;
	$courslist[$course]=1;
	if (!isset($leaving[$course])) {$leaving[$course]=1;}else{ $leaving[$course]++;}
	break;
      case TwoYearLayout::Continuing:
      case TwoYearLayout::Change:
	$course=Department::CharFromId($two->previousYear->language) . $two->previousYear->languageLevel;
	$courslist[$course]=1;
	$t_c++;
	if (!isset($continuing[$course])) {$continuing[$course]=1;}else{ $continuing[$course]++;}
	if (empty($two->previousYear->cultureLevel)) continue;
	$course="C". $two->previousYear->cultureLevel;
	$courslist[$course]=1;
	if (!isset($continuing[$course])) {$continuing[$course]=1;}else{ $continuing[$course]++;}
	break;
      case TwoYearLayout::NewStudent:
      case TwoYearLayout::Orientation:
	$course=Department::CharFromId($two->thisYear->language) . $two->thisYear->languageLevel;
	$courslist[$course]=1;
	$t_n++;
	if (!isset($newstudent[$course])) {$newstudent[$course]=1;}else{ $newstudent[$course]++;}
	if (empty($two->thisYear->cultureLevel)) continue;
	$course="C". $two->thisYear->cultureLevel;
	$courslist[$course]=1;
	if (!isset($newstudent[$course])) {$newstudent[$course]=1;}else{ $newstudent[$course]++;}
	break;
      default:
	print "Error: invalid status $two->$status found\n";
      }
    }
    $template->addBlockFile('RESULT', 'F_RESULT', 'TwoYearSummary.tpl');
    $template->touchBlock('F_RESULT');
    foreach ($courslist as $key => $value) {
      $templateName="SUMMARY";
      $template->setCurrentBlock($templateName);

      //      $continuing = isset($continuing[$key]) ? $continuing[$key] : 0;
      $cont = isset($continuing[$key]) ? $continuing[$key] : "";
      $leave = isset($leaving[$key]) ? $leaving[$key] : "";
      $newstu = isset($newstudent[$key]) ? $newstudent[$key] : "";
      $previous = $cont+$leave;

      $retension = sprintf ("%.2f%%", $cont/$previous*100);
      $template->setVariable("PREVIOUS", $previous);
      $template->setVariable("LEAVING", $leave);
      $template->setVariable("CONTINUING", $cont);
      $template->setVariable("RETENTION", $retension);

      $template->setVariable("NEW", $newstu);



      if ($key == "KG0") $key="KG";
      $template->setVariable("COURSE", $key);
      $template->parseCurrentBlock();
    }
    $templateName="TOTAL";
    $template->setCurrentBlock($templateName);
    $template->setVariable("PREVIOUS", $t_l + $t_c);
    $template->setVariable("LEAVING", $t_l);
    $template->setVariable("CONTINUING", $t_c);
    $template->setVariable("RETENTION", sprintf("%.2f%%", $t_c*100/($t_c+$t_l)));
    $template->setVariable("NEW", $t_n);
    $template->parseCurrentBlock();


  }

  public static function VolunteerCodes($template) {
    $template->addBlockFile('RESULT', 'F_RESULT', 'VolunteerCodes.tpl');
    $template->touchBlock('F_RESULT');
    $saved = -1; //it will never be -1 in the database
    foreach(Codes::GetAllVolunteerCodes() as $code) {
      if ($code->category ==1 ) continue;
      // header
      if ($saved > 0 and $saved != $code->category) {
	$templateName="CATEGORYBLOCK";
	$template->setCurrentBlock($templateName);
	$template->parseCurrentBlock();
      }
      if ($saved != $code->category) {
	$templateName="CATEGORY";
	$template->setCurrentBlock($templateName);
	$template->setVariable("CATEGORYID", $code->category);
	$template->setVariable("DESC", EnumCodesCategory::NameFromId($code->category));
	$template->parseCurrentBlock();
	$saved=$code->category;
      }

      // table
      $templateName="VOLUNTEERCODE";
      $template->setCurrentBlock($templateName);
      $template->setVariable("ID", $code->id);
      $template->setVariable("CODE", $code->code);
      $template->setVariable("DEPARTMENT", $code->department);
      $template->setVariable("ROLE", $code->role);
      $template->setVariable("HOURS", $code->hours);
      $template->setVariable("REQUIREMENT", $code->requirement);
      $template->setVariable("DESCRIPTION", $code->description);
      $template->parseCurrentBlock();
    }
    
  }

  private  static function RegistrationPacketHtml($family) {  
    $students = Student::AllStudents();
    $templateDir = realpath(dirname(__FILE__)."/../templates");
    $pageheader = '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg" width="700" height="70" alt="php5 logo"/></a>';
    $timestamp = date('d M Y h:i A');
    $footer = '<div id="footer"> <table><tr><td>Copyright &#169; 2012 Vidyalya Inc., Printed at: ' 
      . $timestamp 
      . '</td><td style="text-align:right;"><div class="page-number"></div></td></tr></table></div>';

    // Header 
    $template = new HTML_Template_ITX($templateDir);
    $template->loadTemplatefile("Layout.tpl", true, true);
    $template->addBlockFile('TOP', 'F_TOP', 'LayoutTop.tpl');
    $template->touchBlock('F_TOP');
    $html = $template->get();

    // Family Detail Form - one per family
    $template = new HTML_Template_ITX($templateDir);
    $template->loadTemplatefile("Layout.tpl", true, true);

    $template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
    $template->touchBlock('F_CONTENT');
    $template->setCurrentBlock('HEADER');
    $template->setVariable("HEADER", $pageheader);
    $template->parseCurrentBlock();

    $template->addBlockFile('RESULT', 'F_RESULT', 'FamilyDetail.tpl');
    $template->touchBlock('F_RESULT');
    DisplayFamilyTemplateV3($template, $family);

    $html = $html . $template->get();	

    $html = $html . $footer . '<DIV style="page-break-after:always"></DIV>';

    // Registration Form - one per family
    $template = new HTML_Template_ITX($templateDir);
    $template->loadTemplatefile("Layout.tpl", true, true);

    $template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
    $template->touchBlock('F_CONTENT');

    $template->setCurrentBlock('HEADER');
    $template->setVariable("HEADER", $pageheader);
    $template->parseCurrentBlock();

    $template->addBlockFile('RESULT', 'F_RESULT', 'Registration.tpl');
    $template->touchBlock('F_RESULT');
    DisplayRegistrationTemplate($template, $family, $students);

    $html = $html . $template->get();	
	
    // Medical Information Form - one per student
    foreach ($family->EligibleChildren() as $student) {
      $html = $html . $footer . '<DIV style="page-break-after:always"></DIV>';
	  	  
      $template = new HTML_Template_ITX($templateDir);
      $template->loadTemplatefile("Layout.tpl", true, true);
      $template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
      $template->touchBlock('F_CONTENT');
      $template->setCurrentBlock('HEADER');
      $template->setVariable("HEADER", $pageheader);
      $template->parseCurrentBlock();
	  
      $template->addBlockFile('RESULT', 'F_RESULT', 'MedicalInformation.tpl');
      $template->touchBlock('F_RESULT');
      DisplayStudentMedicatlInformationTemplate($template, $student);	
      $html = $html . $template->get();			
    }
	
    // Footer
    $template = new HTML_Template_ITX($templateDir);
    $template->loadTemplatefile("Layout.tpl", true, true);
    $template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
    $template->setCurrentBlock('FOOTER');
    $template->setVariable("FOOTER", $footer);
    $template->parseCurrentBlock();
    $template->touchBlock('F_BOTTOM');
    $html = $html . $template->get();

    return $html;
  }

  public static function RegistrationPacketFamilyDownload($family) {
    if (is_null($family)) return;
    $html = Reports::RegistrationPacketHtml($family);
    $fileName = "Family-" . $family->id . ".pdf";
    PrintFactory::HtmlToPdfToWeb($html, $fileName);
  }

  public static function RegistrationPacketFamily($family) {
    if (is_null($family)) return;
    $html = Reports::RegistrationPacketHtml($family);
    $pdf = PrintFactory::HtmlToPdf($html);
    
    $printDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission";
    $fileName = $printDir . "/pdf/Family-" . $family->id . ".pdf";
    file_put_contents("$fileName", $pdf);
    echo "printed $fileName\n";
    return;
    
    $fileName = $printDir . "/html/Family-" . $family->id . ".html";
    file_put_contents("$fileName", $html);
    echo "printed $fileName\n";
    return;
  }

  public static function VolunteerListV2($year, $carryLinks) {
    $html = "<p>Vidyalaya appreciates the following volunteers for their service during " . Calendar::SessionFromYear($year) . " session.\n";
    $html .= '<table id="maintable" cellspacing="0" class="tablesorter" width="600px">' . "\n";
    $html .= "<thead><tr><th>Name</th><th>Home</th><th>CELL</th><th>Role</th><th>Email</th></tr></thead><tbody>\n";
    foreach(Volunteers::GetAllYear($year) as $item) {

      if (VidSession::IsLoggedIn()) {
	$phone = $item->person->home->phone;
	$cell = $item->person->cellPhone;
	$email = $item->person->email;
      } else {
	$phone = "";
	$cell = "";
	$email = ""; 
      }

      $html .= "<tr><td>" .  $item->person->fullName() . "</td><td>";
      $html .= $phone . "</td><td>" . $cell . "</td><td>";
      $html.=  VolunteerRole::IdToString($item->role) . "</td><td>" . $email . "</td></tr>\n"; 
    }
    $html .= "</tbody></table>\n";
    return $html;
  }

  private static function TeacherDetail($teacher) {
    $name = $teacher->IsLead ? "<b>" . $teacher->person->fullName() ."</b>"  :  $teacher->person->fullName();
    $url=$_SERVER['PHP_SELF'] . "?command=person&MFS=$teacher->MFS&id=$teacher->mfsId";
    $foo = "<a href='$url'>$name</a>";
    $foo .= "<br />" . $teacher->person->home->phone;
    $foo .= "<br />" . $teacher->person->email;
    return $foo;
  }

  public static function TeacherListV2($year, $carryLinks) {
    $teachercount=0; $done = array();
    foreach (Teachers::TeacherListYear($year) as $teacher) {
      $key = "$teacher->MFS:$teacher->mfsId";
      if (!array_key_exists($key, $done)) {
	$done[$key]=1;
	$teachercount++;
      }
    }


    $html="<p>The classes at Vidyalaya are made possible by the volunteerism of following $teachercount teachers.\n";

    foreach (Department::GetAll() as $dept) {
      $classCount = array(); $total=0;
      foreach (Enrollment::GetAllEnrollmentForDeptSession($dept, $year) as $item) {
	if (empty($classCount[$item->class->id])) $classCount[$item->class->id]=0;
	$classCount[$item->class->id]++;
	$total++;
      }

      $deptTeachers = Teachers::TeacherListDepartment($dept, $year);

      $avgStudentPerClass = count($classCount) !=0 ? $total/count($classCount) : 0; 
      $avgStudentPerTeacher = count($deptTeachers) !=0 ? $total/count($deptTeachers) : 0;
      $html .= "<h3>" . Department::NameFromId($dept) . "</h3> (" . count($classCount) .  " classes, $total students, " . count($deptTeachers) . " Teachers";
      $html .= sprintf(", Student Count (Average): Per Class: %.2f , Per Teacher: %.2f)\n",$avgStudentPerClass,  $avgStudentPerTeacher);
      $html .= '<table id="table' . $dept .'" cellspacing="0" class="tablesorter">' . "\n";
      $html .= "<thead><tr><th>Class</th><th>#</th><th width=30px>Room</th>";
      $html .= "<th colspan=3>Teachers</th>";

      $html .= "<th width=300px>Description</th></tr></thead><tbody>\n";
      foreach (AvailableClass::GetAllYearDepartment($dept, $year) as $class) {
	
	$i=0;$teacherlist = array(); 

	foreach ($deptTeachers as $teacher)  {
	  if ($teacher->class->id == $class->id) 
	    $teacherlist[$i++] = $teacher;
	}

	$rowcount=intval(count($teacherlist)/3);
	if (count($teacherlist) %3 > 0 || $rowcount==0) $rowcount++;

	$html .= "<tr><td rowspan=$rowcount valign='top'>" .  $class->short() . "</td>";

	$classCount1 = isset($classCount[$class->id]) ? $classCount[$class->id] : 0;
	$url=$_SERVER['PHP_SELF'] . "?command=ClassRoster&classId=$class->id";
	$html .= "<td rowspan=$rowcount valign='top'><a href='$url'>" . $classCount1  . "</a></td>";

	$url=$_SERVER['PHP_SELF'] . "?command=Room&id=" . $class->room->id;
	$html .= "<td rowspan=$rowcount valign='top'><a href='$url'>" . $class->room->roomNumber . "</a></td>";
	
	for ($i=0; $i < 3; $i++) {
	  if ($i < count($teacherlist)) {$html .= "<td width='150px'>" . self::TeacherDetail($teacherlist[$i]) . "</td>";}
	  else {$html .= "<td width='150px'>&nbsp;</td>";}
	}
	
	$html .= "<td rowspan=$rowcount valign='top'>" .  $class->course->full .  "</td></tr>\n";
	for ($j=1; $j < $rowcount; $j++) {
	  $html .= "<tr>";
	  for ($k=0; $k < 3; $k++) {
	    if ($i < count($teacherlist)) {$html .= "<td width='150px'>" . self::TeacherDetail($teacherlist[$i]) . "</td>";}
	    else {$html .= "<td width='150px'>&nbsp;</td>";}
	    $i++;
	  }
	  $html .= "</tr>\n";
	}

      }
      $html .= "</tbody></table>\n";
    }

    return $html;
  }

  private static function EventRSVPTable ($items, &$html, $lastUpdate, $heading, $amount) {

    usort ($items, "Family::CompareFatherLast");
    $html .= "<h3>$heading</h3>\n";
    $html .= '<table id="' . $heading . '" cellspacing="0" class="tablesorter" width="600px">' . "\n";
    $html .= "<thead><tr><th>#</th><th>ID</th><th>Time</th><th>Parents</th><th>Paid</th></tr></thead><tbody>\n";
    $i=1;
    foreach($items as $family) {
      $time = $lastUpdate[$family->id];
      $money = $amount[$family->id] == 0 ? "" : $amount[$family->id];
      //      if ($family->id == 441) print "<p> time for 441 is " . $lastUpdate[441];
      $html .= "<tr> <td align=right>$i</td><td align=right>$family->id</td><td>$time</td> <td>" .  $family->parentsName();
      $html .= "</td><td>$money</td></tr>";
      $i++;
    }
    $html .= "</tbody></table>\n";
  }

  public static function EventRSVP($eventId) {
    $interest = array(); $registered = array(); 
    $cancel=array(); $decline=array(); $lastUpdate=array(); $amount= array();
    $event=Items::GetItemById($eventId);
    if (is_null($event)) return "<h3>No such event found</h3>";


    foreach(ItemRegistration::EventRegistration($eventId) as $registration) {
      $person = Person::PersonFromId($registration->MFS, $registration->mfsId);
      if (!array_key_exists($person->home->id, $lastUpdate)) {
	$lastUpdate[$person->home->id] = $registration->lastUpdate;
	$amount[$person->home->id] = $registration->amountPaid;

	if ($registration->statusId & ItemRegistrationStatus::Decline) {
	  $decline[] = $person->home;
	} else if ($registration->statusId & ItemRegistrationStatus::CancelRequest 
		   || $registration->statusId & ItemRegistrationStatus::Cancelled) {
	  $cancel[] = $person->home;
	} else if ($registration->statusId & ItemRegistrationStatus::Interested) {
	  $interest[] = $person->home;
	  //	  print "<p>I was here for " . $person->home->id;
	} else if ($registration->statusId & ItemRegistrationStatus::Registered) {
	  $registered[] = $person->home;
	  //	  print "<p>$registration->lastUpdate, I was here for " . $person->home->id . ", " . $lastUpdate[$person->home->id];
	} else {
	  print "weird status $registration->statusId for family " . $person->home->id . "\n";
	}
      }

    }
    


    date_default_timezone_set ("America/New_York");
    $html = "<h3>Event Description</h3>";

    $html .= "<table class='tablesorter' cellspacing=0 >\n";
    $html .= "<tr><td>Event ID:</td><td>$event->id</td></tr>\n";
    $html .= "<tr><td>Description:</td><td><a href='$event->url'>$event->description</a></td></tr>\n";
    $html .= "<tr><td>When:</td><td>$event->when</td></tr>\n";
    $html .= "<tr><td>Type:</td><td>$event->itemType</td></tr>\n";
    $html .= "<tr><td>Cost:</td><td>$event->cost</td></tr>\n";
    $html .= "<tr><td>Curent Time:</td><td>" . strftime("%c", time()) . "</td></tr>\n";
    $html .= "<tr><td>Registration:</td><td>" . strftime("%c",$event->start) . " - "  . strftime("%c",$event->end) . "</td></tr>\n";
    $html .= " </table>\n";
    $html .= "<h3>Event Registration</h3>";
    if (!empty($interest) ) {
      self::EventRSVPTable($interest, $html, $lastUpdate, "Interested", $amount);
    }
    if (!empty($registered) ) {
      self::EventRSVPTable($registered, $html, $lastUpdate, "Registered", $amount);
    }
    if (!empty($decline) ) {
      self::EventRSVPTable($decline, $html, $lastUpdate, "Declined", $amount);
    }
    if (!empty($cancel) ) {
      self::EventRSVPTable($cancel, $html, $lastUpdate, "Cancelled", $amount);
    }
    return $html;
  }

  public static function DisplayStudentLookup($template, $list) {
    global $htmlDir;
    if (empty($list)) return;

    $template->addBlockFile('RESULT', 'F_RESULT', 'StudentLookup.tpl');
    $template->touchBlock('F_RESULT');
    
    $template->setCurrentBlock("HIDDENFORMS");
    $template->setVariable("STUDENTFORM", file_get_contents("$htmlDir/StudentForm.inc"));
    $template->parseCurrentBlock();

    $count = count($list);
    $maxDisplay = 20;
    if ($count > $maxDisplay) {
      $templateName="MORETHAN20";
      $template->setCurrentBlock($templateName);
      $template->setVariable("COUNT", $count);
      $template->setVariable("MAXDISPLAY", $maxDisplay);
      $template->parseCurrentBlock();
    }

    $templateName="STUDENTLOOKUP";
    $i=0;
    foreach ($list as $s) {
      $template->setCurrentBlock($templateName);
      $template->setVariable("ID", $s->id);
      $template->setVariable("FIRST", $s->firstName);
      $template->setVariable("LAST", $s->lastName);
      $template->setVariable("PARENT", $s->parentsName());
      $template->parseCurrentBlock();
      $i++;
      if ($i == $maxDisplay) break;
    }
  }

  public static function DisplayPersonLookup($template, $list) {
    global $htmlDir;
    if (empty($list)) return;

    $template->addBlockFile('RESULT', 'F_RESULT', 'PersonLookup.tpl');
    $template->touchBlock('F_RESULT');
    
    $template->setCurrentBlock("HIDDENFORMS");
    $template->setVariable("STUDENTFORM", file_get_contents("$htmlDir/FamilyForm.inc"));
    $template->parseCurrentBlock();

    $count = count($list);
    $maxDisplay = 20;
    if ($count > $maxDisplay) {
      $templateName="MORETHAN20";
      $template->setCurrentBlock($templateName);
      $template->setVariable("COUNT", $count);
      $template->setVariable("MAXDISPLAY", $maxDisplay);
      $template->parseCurrentBlock();
    }

    $templateName="LOOKUP";
    $i=0;
    foreach ($list as $s) {
      $template->setCurrentBlock($templateName);
      $template->setVariable("ID", $s->home->id);
      $template->setVariable("FIRST", $s->firstName);
      $template->setVariable("LAST", $s->lastName);
      $template->setVariable("GENDER", $s->gender);
      $template->setVariable("HOME", $s->home->phone);
      $template->setVariable("CELL", $s->cellPhone);
      $template->setVariable("EMAIL", $s->email);
      $template->parseCurrentBlock();
      $i++;
      if ($i == $maxDisplay) break;
    }
  }

  public static function DisplayEventCalendarDetail($template, $params) {
    $id = isset($_POST['EventCalendarId']) ?  $_POST['EventCalendarId'] : null;
    if (empty($id)) return;

    $event = EventCalendar::GetItemById($id);
    if (empty($event)) return;

    $template->addBlockFile('RESULT', 'F_RESULT', 'EventCalendarDetail.tpl');
    $template->touchBlock('F_RESULT');
    
    $templateName="EVENTCALENDARDETAIL";
    $template->setCurrentBlock($templateName);
    $template->setVariable("ID", $event->id);
    $template->setVariable("TYPE", EventCalendarType::StringFromId($event->eventType));
    $template->setVariable("DATE", $event->date);
    $template->setVariable("WEEKNUMBER", $event->weekNumber);
    $template->setVariable("PORTAL", $event->portalId);
    $template->setVariable("START", $event->startTime);
    $template->setVariable("END", $event->endTime);
    $template->setVariable("CLASS", $event->class);
    $template->setVariable("DESCRIPTION", $event->description);
    $template->parseCurrentBlock();

    if (VidSession::IsSuperUser()) {
      $templateName="BUTTONS";
      $template->setCurrentBlock($templateName);
      $template->setVariable("EVENTID", $event->id);
      $template->parseCurrentBlock();
    }

  }

  public static function DisplayEventCalendarShort($template, $interestingDates = array("foo" => "bar")) {
    //    foreach($interestingDates as $key => $value) print "interesting dates are $key\n";
    $start = Calendar::StartDate();
    $schoolday=array(); $yoga=array(); $presentation=array(); $activity=array(); $holiday=array(); $sunday=array();
    foreach (EventCalendar::ListScheduledEvents(1, $start, $count) as $event) {
      list($year, $month, $day) = explode("-", $event->date);
      $weekno = intval(($day-1)/7);
      $sunday["$year-$month"][$weekno]=$day;
      if ($event->eventType & EventCalendarType::SchoolDay) {
	$schoolday["$year-$month"][$weekno]=$day;
      }

      if ($event->eventType & EventCalendarType::Activity) {
	$activity[$event->date]=$event->description;
      }
      if ($event->eventType & EventCalendarType::Holiday) {
	$holiday[$event->date]=$event->description;
      }
    }

    $missing = array();
    foreach ($schoolday as $month=>$weeks) {
      $template->setCurrentBlock("MONTHLY");
      list($y, $m) = explode("-", $month);
      $timestamp = mktime(0, 0, 0, $m, 1, 2005);
      $template->setVariable("MONTH", date("F", $timestamp));

      for ($i=0; $i<5; $i++) {
	if (isset($weeks[$i])) {
	  $totalweeks++;$monthlytotal++;
	  $checkdate=$month . "-" . $weeks[$i];
          $datestring = $weeks[$i];
	  if (array_key_exists($checkdate, $interestingDates)) $datestring = "<b>$datestring</b>";
	  $template->setVariable("SCHOOLDAY" . $i, $datestring);
	}
      }
      $template->parseCurrentBlock();
    }


    if (isset($holiday)) {
      foreach($holiday as $date=>$description) {
      $template->setCurrentBlock("HOLIDAY");
      $template->setVariable("HOLIDAYDATE", formatDate($date));
	$template->setVariable("HOLIDAYDESC", $description);
      $template->parseCurrentBlock();
      }
    }

    if (isset($activity)) {
      foreach($activity as $date=>$description) {
      $template->setCurrentBlock("ACTIVITY");
      $template->setVariable("ACTIVITYDATE", formatDate($date));
	$template->setVariable("ACTIVITYDESC", $description);
      $template->parseCurrentBlock();
      }

    }

  }

  public static function DisplayEventCalendarYear($template, $params) {
    $count=0;

    $template->addBlockFile('RESULT', 'F_RESULT', 'EventCalendarYear.tpl');
    $template->touchBlock('F_RESULT');

    $year=Calendar::CurrentYear();
    $template->setCurrentBlock("SESSIONBLK");
    $template->setVariable("SESSION", Calendar::SessionFromYear($year));
    $template->parseCurrentBlock();

    $start = isset($_POST['startDate']) ?  $_POST['startDate'] : null;
    $schoolday=array(); $yoga=array(); $presentation=array(); $activity=array(); $holiday=array(); $sunday=array();
    foreach (EventCalendar::ListScheduledEvents(1, $start, $count) as $event) {
      if ($event->eventType & EventCalendarType::Calendar) continue;
      list($year, $month, $day) = explode("-", $event->date);
      $weekno = intval(($day-1)/7);
      $sunday["$year-$month"][$weekno]=$day;
      if ($event->eventType & EventCalendarType::SchoolDay) {
	$schoolday["$year-$month"][$weekno]=$day;
      }
      if ($event->eventType & EventCalendarType::Yoga) {
	$yoga["$year-$month"][$weekno]=$event->class;
      }
      if ($event->eventType & EventCalendarType::Presentation) {
	$presentation["$year-$month"][$weekno]=$event->class;
      }
      if ($event->eventType & EventCalendarType::Activity) {
	$activity["$year-$month"][$weekno]=$event->description;
	//	print "adding $event->description <br />\n";
      }
      if ($event->eventType & EventCalendarType::Holiday) {
	$holiday["$year-$month"][$weekno]=$event->description;
      }
    }
    $sudo = VidSession::IsSuperUser();

    $totalweeks =0; $totalWholeDay=0;
    foreach ($schoolday as $month=>$weeks) {
      $template->setCurrentBlock("MONTHLY");
      list($y, $m) = explode("-", $month);
      $timestamp = mktime(0, 0, 0, $m, 1, 2005);
      $template->setVariable("MONTH", date("F", $timestamp));

      $monthlytotal=0;
      for ($i=0; $i<5; $i++) {
	if (isset($weeks[$i])) {
	  $totalweeks++;$monthlytotal++;
	  $template->setVariable("SCHOOLDAY" . $i, $weeks[$i]);

	  if (isset($yoga[$month][$i])) {
	    $foo = $sudo ? $yoga[$month][$i]->urlRoster() : $yoga[$month][$i]->short();
	    $template->setVariable("YOGA" . $i, $foo);
	  }

	  if (isset($presentation[$month][$i])) {
	    $foo = $sudo ? $presentation[$month][$i]->urlRoster() : $presentation[$month][$i]->short();
	    $template->setVariable("PRESENTATION" . $i, $foo);
	  }

	  if (isset($activity[$month][$i])) {
	    $template->setVariable("ACTIVITY" . $i, $activity[$month][$i]);
	    //	    print "going to unset " . $activity[$month][$i] . "<br />\n";
	    unset ($activity[$month][$i]);
	  }
	}
      }

      $missing = array();
      // put missing pieces here, max capacity 3. assume no overlap between activity and holiday
      if (isset($activity[$month] )) {
      foreach ($activity[$month] as $weekno=>$description) {
	$missing[$weekno] = $description;
	//	print "$month, $weekno, $description <br />\n";
      }
      }

      if (isset($holiday[$month] )) {
      foreach ($holiday[$month] as $weekno=>$description) {
	if (isset($missing[$weekno])) print "Error: weekno occupied at " .  __FUNCTION__." in ".__FILE__." at ".__LINE__."<br \>\n";;
	$missing[$weekno] = $description;
	//	print "$month, $weekno, $description <br />\n";
      }
      }


      $count = count($missing);
      if ($count > 3) print "Error: too many ==$count==  items at " .  __FUNCTION__." in ".__FILE__." at ".__LINE__."<br \>\n";
      $currentpos=1;
      if (isset($missing)) {
      foreach($missing as $weekno=>$description) {
	$template->setVariable("WHOLEDAY" . $currentpos++, $sunday[$month][$weekno] . ": " . $description);
	$totalWholeDay++;
      }
      }


      $template->setVariable("MONTHLYTOTAL", $monthlytotal);
      $template->parseCurrentBlock();
    }

    $template->setCurrentBlock("TOTALWEEKS");
    $template->setVariable("TOTAL", $totalweeks);
    $template->setVariable("TOTALWHOLEDAY", $totalWholeDay);
    $template->setVariable("SUMMER", 52 - $totalWholeDay - $totalweeks);
    $template->parseCurrentBlock();
    
  }

  public static function DisplayYogaEvents($template, $params) {
    $count=0;

    $start = isset($_POST['startDate']) ?  $_POST['startDate'] : null;
    $template->addBlockFile('RESULT', 'F_RESULT', 'YogaEvent.tpl');
    $template->touchBlock('F_RESULT');

    $template->setCurrentBlock("HIDDENFORMS");
    $template->setVariable("EVENTCALENDARFORM", file_get_contents("../html/EventCalendarForm.inc"));
    $template->parseCurrentBlock();
    
    if (VidSession::IsSuperUser()) {
      $templateName="AUTHORIZED";
      $template->setCurrentBlock($templateName);
      $template->setVariable("EVENTID", "");
      $template->parseCurrentBlock();
    }

    $templateName="EVENTS";
    foreach (EventCalendar::ListYogaEvents(1, $start, $count) as $event) {
      $template->setCurrentBlock($templateName);
      $template->setVariable("DATE", $event->date);
      $template->setVariable("START", $event->startTime);
      $template->setVariable("END", $event->endTime);
      $template->setVariable("CLASS", $event->class->short());
      $room = isset($event->class) ? $event->class->room->roomNumber : "";
      $template->setVariable("ROOM", $room);
      $template->parseCurrentBlock();
    }
  }

  public static function DisplayEventCalendar($template, $params) {
    $count=0;

    $start = isset($_POST['startDate']) ?  $_POST['startDate'] : null;
    $template->addBlockFile('RESULT', 'F_RESULT', 'EventCalendar.tpl');
    $template->touchBlock('F_RESULT');

    $template->setCurrentBlock("HIDDENFORMS");
    $template->setVariable("EVENTCALENDARFORM", file_get_contents("../html/EventCalendarForm.inc"));
    $template->parseCurrentBlock();
    
    if (VidSession::IsSuperUser()) {
      $templateName="AUTHORIZED";
      $template->setCurrentBlock($templateName);
      $template->setVariable("EVENTID", "");
      $template->parseCurrentBlock();
    }

    $templateName="EVENTS";
    foreach (EventCalendar::ListScheduledEvents(1, $start, $count) as $event) {
      $template->setCurrentBlock($templateName);
      $template->setVariable("WEEK", $event->weekNumber);
      $template->setVariable("ID", $event->id);
      $template->setVariable("TYPE", EventCalendarType::StringFromId($event->eventType));
      $template->setVariable("DATE", $event->date);
      $template->setVariable("START", $event->startTime);
      $template->setVariable("END", $event->endTime);
      $template->setVariable("DESCRIPTION", $event->LongDescription());
      $template->parseCurrentBlock();
    }
    
  }

  public static function lcmatrix($year) {
    $language = array(); $culture=array();
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::Brooklawn, $year) as $item) {
      //      $done[$item->student->id] = $item->student;
      if ($item->class->course->department != Department::Culture) {
	if (array_key_exists($item->student->id, $language) )
	  print "oh oh , i am going to overwrite language for $item->student->id\n";
	$language[$item->student->id] = $item->class;
      } else {
	if (array_key_exists($item->student->id, $culture) )
	  print "oh oh , i am going to overwrite culture for $item->student->id\n";
	$culture[$item->student->id] = $item->class;
      }
    }

    // Create LC map
    $lcmap=array(); $ccarray=array(); $langarray=array();
    foreach ($culture as $studentId => $class) {
      $ccarray[$class->short()] =$class->id;
      $key = $language[$studentId]->short() . ':' .$class->short();
      $langarray[$language[$studentId]->short()] = $language[$studentId]->id;
      array_key_exists($key, $lcmap) ? $lcmap[$key]++ : $lcmap[$key]=1;
    }

    $kg=0;
    foreach ($language as $studentId => $class) {
      if (array_key_exists($studentId, $culture)) continue;
      if ($class->course->department == Department::Kindergarten) {
	$kg++;
	$kgclassid=$class->id;
      } else {
	print "Error- student $studentId is not in culture and not in kindergarten either, enrolled in $class->id\n";
      }
    }

    ksort($ccarray);
    $i=0;

    $html="<table id='table1' cellspacing='0' class='tablesorter'>\n";
    $html .= "<thead><tr><th>LANG</th>";
    $vt=array();
    foreach ($ccarray as $key=>$item) {
      $vt[$i]=0;
      $ccarray[$key]=$i++; 
      $html .= "<th><a href='/dakhila/php/dataViewer2.php?command=ClassRoster&classId=$item'>$key</a></th>";
    }
    $html .= "<th>". count($ccarray) . "</th></tr></thead><tbody>\n";
    $vt[$i]=0;

    $lang2d=array();
    $total=0; ksort($lcmap);
    foreach ($lcmap as $key=>$count) {
      $exp = explode(':', $key);
      $j = $ccarray[$exp[1]];
      $lang2d[$exp[0]][$j] = $count;
      $total += $count;
    }

    
    foreach($lang2d as $key=>$v1) {
      $langid = $langarray[$key];
      $html .= "<tr><td><a href='/dakhila/php/dataViewer2.php?command=ClassRoster&classId=$langid'>$key</a></td>";
      $t =0;
      for ($i=0; $i< count($ccarray); $i++) {
	if (isset($v1[$i])) {
	  $html .= "<td style='padding-left: 15px; text-align: right;padding-top: 5px;'> $v1[$i]</td>";
	  
	  $t += $v1[$i]; $vt[$i]+=$v1[$i];
	} else { 
	  $html .= "<td></td>";
	}
      }
      $vt[$i]+=$t; 
      $html .= "<td style='padding-left: 15px; text-align: right;padding-top: 5px;font-weight:bold; background-color:grey;'>$t</td></tr>\n";
    }
    $html .= "</tbody><tfoot><tr><td>" . count($lang2d) ."</td>";
    foreach ($vt as $c) {
      $html .= "<td style='padding-left: 15px; text-align: right;padding-top: 5px;font-weight:bold; background-color:grey;'>$c</td>";
    }
    $html .= "</tr></tfoot></table>\n";

    $gt = $c+$kg;
    $html .= "<p><a href='/dakhila/php/dataViewer2.php?command=ClassRoster&classId=$kgclassid'>KG</a> - $kg<br <br />Grand Total - $gt</p>\n";
    return $html;
  }


  
  public static function VolunteerListForHandbookHtml($year, $carryLinks, $fh) {
    fwrite($fh,  "<p>Vidyalaya appreciates the following volunteers for their service.\n");
    fwrite($fh, "<div id='teacherlist'>\n<table>\n");
    fwrite($fh, "<tr><th class='rowhead' width='200px'>Name</th><th>Role</th></tr>\n");
    foreach(Volunteers::GetAllYear($year) as $item) {
      fwrite($fh, "<tr><td>" .  $item->person->fullName() . "</td><td>" . VolunteerRole::IdToString($item->role) . "</td></tr>\n"); 
    }
    fwrite($fh, "</table>\n</div>\n");
  }
}

class RoomUtilization {
  private static $objArray = Array();

  private static function firstTimeCall($facility, $year) {
    if (!empty(self::$objArray)) return;
    $enrollment = Enrollment::GetAllEnrollmentForFacilitySession($facility, $year);
    foreach ($enrollment as $item) {
      $room = $item->class->room;
      self::$objArray[$room->id][$item->class->id][] = $item;
    }
    return self::$objArray;
  }


  public static function utilization($facility, $year) {
    self::firstTimeCall ($facility, $year);
    $i = 1;
    foreach (self::$objArray as  $roomid => $availableClass) {
      $room = Rooms::GetItemById($roomid);
      print "$i. Room Number: $room->roomNumber, capacity: $room->capacity\n";
      foreach ($availableClass as $classid => $students) {
	$classa = AvailableClass::GetItemById($classid);
	$cc = CourseCatalog::GetItemById($classa->course->id);
	print "$classa->startTime - $classa->endTime: Course: " . $classa->short().
	  ", student = " . count($students) . "\n";	
      }
      $i++;
      print "\n";
    }
    return Array();
  }

  public static function utilizationDept($facility, $year, $dept) {
    self::firstTimeCall ($facility, $year);
    $i = 1;
    $workbook = new VidBook();
    foreach (self::$objArray as  $roomid => $availableClass) {
      $room = Rooms::GetItemById($roomid);
      foreach ($availableClass as $classid => $enrollments) {
	$classa = AvailableClass::GetItemById($classid);
	$cc = CourseCatalog::GetItemById($classa->course->id);

	if ($cc->department == $dept) {
	  print "$i. Room Number: $room->roomNumber, Course: " . $classa->short(). "\n";
	  $sheet = $workbook->setActiveSheet($i-1	, $classa->short(), $classa->short());
	  $sheet->setCellValue('A1', "Room Number: $room->roomNumber, Course: " . $classa->short());
	  $row = 2;
	  foreach ($enrollments as $enrollment) {
	    $student = $enrollment->student;
	    $sheet->getColumnDimension('C')->setAutoSize(true);
	    $sheet->setCellValue('B'.$row, $student->id);
	    $sheet->setCellValue('C'.$row, $student->fullName());
	    //print "$student->id, ". $student->fullName() . "\n";
	    $row++;
	  }
	  //					$workbook->fillData($sheet); // this needs to go in loop
	  $i++;
	}
      }
				
      print "\n";
    }
    $workbook->SaveWorkbook("/tmp/cultureRoster.xlsx");
  }


  // List Students in any department
  public static function listStudentsDept($facility, $year, $dept) {
    self::firstTimeCall ($facility, $year);
    $i = 1;
    foreach (self::$objArray as  $roomid => $availableClass) {
      $room = Rooms::GetItemById($roomid);
      foreach ($availableClass as $classid => $enrollments) {
	$classa = AvailableClass::GetItemById($classid);
	$cc = CourseCatalog::GetItemById($classa->course->id);

	if ($cc->department == $dept) {
	  foreach ($enrollments as $enrollment) {
	    $student = $enrollment->student;
	    print $i .  ", " .  $student->id . ", " .$student->fullName();
	    print ",Room: $room->roomNumber, Course: " . $classa->short();
	    print "\n";
	    $i++;
	  }
	}

      }
    }
  }
}



class VidUtil {
  private function AddEmailArray (&$emailArray, $email, $id) {
    if (!empty($emailArray[$email])) {
      print "Error: email $email already assigned to id $emailArray[$email], trying to assign it to $id\n";
    } else {
      $emailArray[$email] = $id;
    }
  }

  private function HandlePraveenStyleEmail(&$emailArray, $praveenStyle, $id) {
    foreach (explode(";", $praveenStyle) as $email) {
      if (!empty($email)) {
	self::AddEmailArray($emailArray, $email, $id);
      }
    }
  }

  public static function EmailCheck($email) {
    $emailArray = array();

    foreach (Family::GetAllFamilies() as $family) {
      self::HandlePraveenStyleEmail($emailArray, $family->mother->email, $family->id);
      self::HandlePraveenStyleEmail($emailArray, $family->father->email, $family->id);

      foreach ($family->Children() as $student) {
	self::HandlePraveenStyleEmail($emailArray, $student->email, $family->id);
      }
    }

    $matchingArray = preg_grep_keys($email, $emailArray);
    if (empty($matchingArray)) {
      //	if (empty($emailArray[$email])) {
      print "email **$email** does not exist in our database\n";
    } else {
      foreach ($matchingArray as $email => $familyId) {
	print "Family id for **$email** is $familyId\n";
      }
    }
  }

  public static function WaitingList() {
    $waiting = Array(); $i=1;
    print "#, student, Family, name, gender, age, grade, language\n";
    foreach (Student::AllStudents() as $student) {
      if ($student->studentStatus->id != StudentStatus::Waiting) continue;
      $waiting[]=$student;
      print "$i, $student->id"
	. ", " . $student->family->id
	. ", " . $student->fullName() 
	. ", " . $student->GenderName() 
	. ", " . sprintf("%2d", $student->AgeAt(Calendar::RegistrationSession))
	. ", " . sprintf("%2d", $student->GradeAt(Calendar::RegistrationSession))
	. ", " . Department::NameFromId($student->languagePreference)
			
	. "\n";
      $i++;
    }
  }

  private static function printParentContact($parent, $family) {

    if (!empty($parent->email)) {
      print $parent->firstName . ", " . $parent->lastName . ", " . $parent->email .
	", \"" . $family->address->OneLineAddress() . "\", " . $parent->cellPhone . "\n";
    }
  }

  public static function printMemberList() {
    print "First Name, Last Name, E-mail Address, Home Address, Mobile Phone\n";
    foreach (Family::GetAllFamilies() as $family) {
      self::printParentContact($family->mother, $family);
      self::printParentContact($family->father, $family);
    }
  }

}


?>
