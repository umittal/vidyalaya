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
		foreach (Student::AllStudents() as $student) {
			if ($student->studentStatus->id != StudentStatus::Waiting) continue;
			$waiting[]=$student;
			print "$i, $student->id, "
			.  ", " .$student->family->id
			.  ", " .$student->fullName() 
			. ", " . $student->GenderName() 
			. ", " . sprintf("%2d", $student->AgeAt(Calendar::RegistrationSession))
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
