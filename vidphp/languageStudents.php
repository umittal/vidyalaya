<?php
/** Error reporting */
error_reporting(E_ALL);

/** Include path **/
ini_set('include_path', ini_get('include_path').':../../Classes:../../dakhila/libVidyalaya');

include_once  'db.inc';
include_once 'vidyalaya.inc';

/** PHPExcel */
include 'PHPExcel/PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
include 'PHPExcel/Writer/Excel2007.php';

// Create new PHPExcel object
echo date('H:i:s') . " Create new PHPExcel object\n";
$objPHPExcel = new PHPExcel();

// Set properties
echo date('H:i:s') . " Set properties\n";
$objPHPExcel->getProperties()->setCreator("Umesh Mittal");
$objPHPExcel->getProperties()->setLastModifiedBy("Umesh Mittal");
$objPHPExcel->getProperties()->setTitle("Students by Language");
$objPHPExcel->getProperties()->setSubject("Vidyalaya Students");
$objPHPExcel->getProperties()->setDescription("List of Vidyalaya Students by Classes");


// Add some data
echo date('H:i:s') . " Add some data\n";
$sheetCount=0;

$students = GetAllData();

foreach (Course::$objArray as $course) {
	if ($course->lc == 'l') {
		$count=3;
		foreach ($students as $id => $student) {
			if($student->IsEnrolled  && $student->registration->language->id == $course->id) {
				if (!isset($initialized[$course->id])) {
					$initialized[$course->id]=1;
					$sheetCount++;
					$objPHPExcel->createSheet();
					$objPHPExcel->setActiveSheetIndex($sheetCount);
					$objPHPExcel->getActiveSheet()->setTitle($course->symbol);
				}
				$cellValue=sprintf("B%d", $count);
				$objPHPExcel->getActiveSheet()->SetCellValue($cellValue, $student->id);
				$cellValue=sprintf("C%d", $count);
				$objPHPExcel->getActiveSheet()->SetCellValue($cellValue, $student->fullName());
				$count++;
			}
		}
	} 

	
}




		
// Save Excel 2007 file
echo date('H:i:s') . " Write to Excel2007 format\n";
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$objWriter->save("/tmp/languageStudents.xlsx");

// Echo done
echo date('H:i:s') . " Done writing file.\r\n";
?>