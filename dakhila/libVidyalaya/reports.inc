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
	$footer = '<div id="footer"> <table><tr><td>Copyright &#169; 2011 Vidyalya Inc., Printed at: ' 
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
    $html = "<p>Vidyalaya appreciates the following volunteers for their service.\n";
    $html .= '<table id="maintable" cellspacing="0" class="tablesorter" width="600px">' . "\n";
    $html .= "<thead><tr><th>Name</th><th>Home</th><th>CELL</th><th>Role</th><th>Email</th></tr></thead><tbody>\n";
    foreach(Volunteers::GetAllYear($year) as $item) {
      $html .= "<tr><td>" .  $item->person->fullName() . "</td><td>";
      $html .= $item->person->home->phone . "</td><td>" . $item->person->cellPhone . "</td><td>";
      $html.=  VolunteerRole::IdToString($item->role) . "</td><td>" . $item->person->email . "</td></tr>\n"; 
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

      $avgStudentPerClass = $total/count($classCount); $avgStudentPerTeacher = $total/count($deptTeachers);
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

	$url=$_SERVER['PHP_SELF'] . "?command=ClassRoster&classId=$class->id";
	$html .= "<td rowspan=$rowcount valign='top'><a href='$url'>" . $classCount[$class->id] . "</a></td>";

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

  public static function DisplayEventCalendar($template, $count, $params) {
    $template->addBlockFile('RESULT', 'F_RESULT', 'EventCalendar.tpl');
    $template->touchBlock('F_RESULT');
    
    $templateName="EVENTS";
    foreach (EventCalendar::ListScheduledEvents(1, 0, $count) as $event) {
      $template->setCurrentBlock($templateName);
      $template->setVariable("WEEK", $event->weekNumber);
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
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS, $year) as $item) {
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
