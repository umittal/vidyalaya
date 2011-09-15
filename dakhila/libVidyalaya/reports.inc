<?php
require_once "vidyalaya.inc";
/** Error reporting */
error_reporting(E_ALL);

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
