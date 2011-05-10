<?php
require_once "vidyalaya.inc";

class ExcelUtils {

private function sample() {

/** Error reporting */
error_reporting(E_ALL);

/** PHPExcel */
require_once "PHPExcel/PHPExcel.php";


// Create new PHPExcel object
echo date('H:i:s') . " Create new PHPExcel object\n";
$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Umesh Mittal")
							 ->setLastModifiedBy("Umesh Mittal")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");


$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objPHPExcel->getProperties()->getTitle() . '&RPage &P of &N');

$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

$objPHPExcel->getActiveSheet()->setTitle('Invoice');
$objPHPExcel->createSheet();

$love = "I love you";

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Terms and conditions');
$objPHPExcel->getActiveSheet()->setCellValue('A3', $love);

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save("/data/ntshare/tmp/test.xlsx");

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
		foreach (self::$objArray as  $roomid => $availableClass) {
			$room = Rooms::GetItemById($roomid);
			foreach ($availableClass as $classid => $enrollments) {
				$classa = AvailableClass::GetItemById($classid);
				$cc = CourseCatalog::GetItemById($classa->course->id);

				if ($cc->department == $dept) {
				print "$i. Room Number: $room->roomNumber, Course: " . $classa->short(). "\n";	
				foreach ($enrollments as $enrollment) {
					$student = $enrollment->student;
					print "$student->id, ". $student->fullName() . "\n";
				}
				}
			}
			$i++;
			print "\n";
		}
	}



}


//doit();

?>
