<?php

$libDir="../../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";

require_once "../../Classes/PHPWord.php";

class WordTable {
  public $table = null;
  public $filename = null;
  private $PHPWord=null;

  public function __construct() {

    // New Word Document
    $this->PHPWord = new PHPWord();

    // New portrait section
    $section = $this->PHPWord->createSection();

    // Define table style arrays
    $styleTable = array('borderBottomSize'=>6, 'borderBottomColor'=>'006699', 'cellMargin'=>20);
    $styleFirstRow = array('borderBottomSize'=>18, 'borderBottomColor'=>'0000FF', 'bgColor'=>'66BBFF');

    // Add table style
    $this->PHPWord->addTableStyle('myOwnTableStyle', $styleTable, $styleFirstRow);

    // Add table
    $this->table = $section->addTable('myOwnTableStyle');
  }

  public function SaveDocument() {
    if (is_null($this->filename)) die ("Trying to save file without a name");
    // Save File
    $objWriter = PHPWord_IOFactory::createWriter($this->PHPWord, 'Word2007');
    $objWriter->save("$this->filename");
  }
}


class Publications {

  public static function SchoolDirectory() {
    $document = new WordTable();
    $table = $document->table;
    // Define font style for first row
    $fontStyle = array('bold'=>true, 'align'=>'center');
    // Define cell style arrays
    $styleCell = array('valign'=>'center');
    $styleCellBTLR = array('valign'=>'center', 'textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR);


    // Add row
    $table->addRow(200);
    // Add cells
    $table->addCell(2000, $styleCell)->addText('Mother', $fontStyle);
    $table->addCell(2000, $styleCell)->addText('Father', $fontStyle);
    $table->addCell(2000, $styleCell)->addText('Address', $fontStyle);
    $table->addCell(2000, $styleCell)->addText('Students', $fontStyle);
    //$table->addCell(500, $styleCellBTLR)->addText('Row 5', $fontStyle);

    $families = array();
    $enrollment = Enrollment::GetAllEnrollmentForFacilitySession(Facility::Eastlake, 2011);

    // Add more rows / cells
    foreach (FamilyTracker::RegisteredFamilies() as $item) {
      $families[$item->family] = Family::GetItemById($item->family);
    }      
    foreach(Volunteers::GetAllYear(2011) as $item) {
      switch ($item->MFS) {
      case MFS::Mother:
	$families[$item->mfsId] =  Family::GetItemById($item->mfsId);
	break;
      case MFS::Father:
	$families[$item->mfsId] =  Family::GetItemById($item->mfsId);
	break;
      case MFS::Student:
	$student = Student::GetItemById($item->mfsId);
	$families[$student->family->id] = $student->family;
	break;
      default: 
	die ("unexpected type of item found in volunteers\n");
      }
    }

    usort ($families, "Family::CompareFatherLast");

    foreach ($families as $family) {
      $table->addRow();
      $table->addCell(2000)->addText($family->mother->fullName());
      $table->addCell(2000)->addText($family->father->fullName());
      $cell = $table->addCell(2000);
      $cell->addText($family->address->addr1);
      $cell->addText($family->address->city . ", ". $family->address->state . " " . $family->address->zipcode);
      $cell->addText($family->phone);
      
      // Write Student First name in last column
      $cell = $table->addCell(2000);
      $list = null; $done=array();
      foreach($enrollment as $e) {
	if (array_key_exists($e->student->id, $done)) continue;
	if ($e->student->family->id == $family->id) {
	  $cell->addText($e->student->firstName);
	  $done[$e->student->id] = 1;
	}
      }


    } // All Registered Familes

    $directory="/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/roster/word";
    $document->filename = "$directory/2011.docx";
    $document->SaveDocument();
  }

  private static function ClassDirectoryTable($table, $class, $email) {
    $fontStyle = array('bold'=>true, 'align'=>'center');
    $styleCell = array('valign'=>'center');
    $styleCellBTLR = array('valign'=>'center', 'textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR);


    $table->addRow(200);
    $table->addCell(500, $styleCell)->addText('First', $fontStyle);
    $table->addCell(500, $styleCell)->addText('Last', $fontStyle);
    $table->addCell(500, $styleCell)->addText('Age', $fontStyle);
    $table->addCell(500, $styleCell)->addText('Grade', $fontStyle);
    if ($email)
      $table->addCell(500, $styleCell)->addText('Email', $fontStyle);
    $table->addCell(500, $styleCell)->addText('First', $fontStyle);
    $table->addCell(500, $styleCell)->addText('Last', $fontStyle);
    $table->addCell(500, $styleCell)->addText('Phone', $fontStyle);
    if ($email)
      $table->addCell(500, $styleCell)->addText('Email', $fontStyle);

    foreach (Enrollment::GetEnrollmentForClass($class->id)  as $item) {
      $student = $item->student;
      $table->addRow();
      $table->addCell(500)->addText($student->firstName);
      $table->addCell(500)->addText($student->lastName);
      $table->addCell(500)->addText((int)$student->AgeAt(Calendar::CurrentSession));
      $table->addCell(500)->addText($student->Grade());
      if ($email)
	$table->addCell(500)->addText($student->email);

      $cell = $table->addCell(500);
      $cell->addText($student->family->mother->firstName);
      $cell->addText($student->family->father->firstName);
      $cell = $table->addCell(500);
      $cell->addText($student->family->mother->lastName);
      $cell->addText($student->family->father->lastName);
      $table->addCell(500)->addText($student->family->phone);
      if ($email) {
	$cell = $table->addCell(500);
	$cell->addText($student->family->mother->email);
	$cell->addText($student->family->father->email);
      }

    }

  }

  public static function ClassDirectory($year) {
    $directory = "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/roster/word/";

    foreach (AvailableClass::GetAllYear($year) as $class) {
      $short= $class->short();

      $document = new WordTable();
      $table = $document->table;
      self::ClassDirectoryTable($table, $class, true);
      $document->filename = "$directory/ClassWide/$short.docx";
      $document->SaveDocument();

      $document = new WordTable();
      $table = $document->table;
      self::ClassDirectoryTable($table, $class, false);
      $document->filename = "$directory/ClassShort/$short.docx";
      $document->SaveDocument();

    }
  }

  public static function TeacherList($year) {
    $directory = "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/roster/word/";
    //    foreach (Teachers::TeacherListDepartment(Department::Hindi, $year) as $teacher) {
    foreach (Teachers::TeacherListYear($year) as $teacher) {
      //      print $teacher->class->short() . ", " . $teacher->person->fullName() . ", " . $teacher->person->email .  "\n";
      print $teacher->class->short() . ", " . $teacher->person->fullName() . ", " . $teacher->person->home->phone .  "\n";
    }

  }

  const MAILINGLISTDIR="/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/mailinglist/";

  private static function  MailingListsClass($year) {
    foreach (AvailableClass::GetAllYear($year) as $item) {
      $filename = self::MAILINGLISTDIR . $item->short() . ".txt";
	    
      $fp=popen("sort -u --output=$filename", "w");
      foreach (Enrollment::GetFamilies($item->id) as $family) {
	fwrite($fp, str_replace(";", "\n", $family->mother->email) . "\n");
	fwrite($fp, str_replace(";", "\n", $family->father->email) . "\n");
      }

      foreach (Teachers::TeacherListClass($item->id) as $teacher) {
	fwrite($fp, str_replace(";", "\n", $teacher->person->email) . "\n");
      }

      pclose($fp);
    }
  }

  private static function MailingListsVolunteers($year) {
    $filename="volunteers.txt";

    $fp=popen("sort -u --output=self::MAILINGLISTDIR/$filename", "w");
    foreach(Volunteers::GetAllYear(2011) as $item) {
      switch ($item->MFS) {
      case MFS::Mother:
	$family =  Family::GetItemById($item->mfsId);
	fwrite($fp, str_replace(";", "\n", $family->mother->email) . "\n");
	break;
      case MFS::Father:
	$family =  Family::GetItemById($item->mfsId);
	fwrite($fp, str_replace(";", "\n", $family->father->email) . "\n");
	break;
      case MFS::Student:
	$student = Student::GetItemById($item->mfsId);

	fwrite($fp, str_replace(";", "\n", $student->email) . "\n");
	break;
      default: 
	die ("unexpected type of item found in volunteers\n");
      }
    }
  }

  private static function MailingListsAll($year) {
    $filename="allEmails.csv";
    $fp=fopen("self::MAILINGLISTDIR/$filename", "w");
    foreach(Emails::GetAll() as $item) {
      $csv = array();
      $csv[] = $item->email;
      switch ($item->MFS) {
      case MFS::Mother:
	$family =  Family::GetItemById($item->mfsId);
	$csv[] = $family->mother->fullName();
	break;
      case MFS::Father:
	$family =  Family::GetItemById($item->mfsId);
	$csv[] = $family->father->fullName();
	break;
      case MFS::Student:
	$student = Student::GetItemById($item->mfsId);
	$csv[] = $student->fullName();
	break;
      default: 
	die ("unexpected type of item found in volunteers\n");
      }
      fputcsv($fp, $csv);
    }

  }

  public static function CreateMailingLists($year) {
    self::MailingListsClass($year);
    return;
    self::MailingListsVolunteers($year);
    self::MailingListsAll($year);
  }


}	

Publications::CreateMailingLists(2011);exit();
//Publications::TeacherList(2011);  exit();
//Publications::SchoolDirectory(); exit();
//Publications::ClassDirectory(2011); exit (); // Directory of all classes, with and without email

?>
