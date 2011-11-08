<?php

$libDir="../../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";
require_once "$libDir/reports.inc";

require_once "../../Classes/PHPWord.php";
require_once  "PHPExcel/PHPExcel/IOFactory.php";

class WordTable {
  public $table = null;
  public $filename = null;
  private $PHPWord=null;
  private $sectionStyle=null;
  private $footer=null;
  private $header=null;

  public function SetLandscape() {
    $this->sectionStyle->setLandscape();
  }

  public function SetFooter($text) {
    $this->footer->addText($text);
  }

  public function SetHeader($text) {
    $this->header->addText($text);
  }

  public function __construct() {

    // New Word Document
    $this->PHPWord = new PHPWord();

    // New portrait section
    $section = $this->PHPWord->createSection();
    $this->sectionStyle = $section->getSettings();
    $this->footer = $section->createFooter();
    $this->header = $section->createHeader();
     
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
  const BaseDir = "/home/umesh/Dropbox/Vidyalaya-Roster";
  const MAILINGLISTDIR="/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/mailinglist/";
  const rosterDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/roster/";

  private static function InvolvedFamilies($year) {
    // Enrolled Families + Volunteers
    $families = array();

    // Add more rows / cells
    foreach (FamilyTracker::RegisteredFamilies() as $item) {
      $families[$item->family] = Family::GetItemById($item->family);
    }      
    foreach(Volunteers::GetAllYear($year) as $item) {
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
    return $families;
  }

  public static function SchoolDirectory($year) {
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

    $families = self::InvolvedFamilies($year);
    $enrollment = Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS, $year);

    foreach ($families as $family) {
      $table->addRow();
      $table->addCell(2000)->addText($family->mother->fullName());
      $table->addCell(2000)->addText($family->father->fullName());
      $cell = $table->addCell(2000);
      if ($family->directory == 1) {
	$cell->addText($family->address->addr1);
	$cell->addText($family->address->city . ", ". $family->address->state . " " . $family->address->zipcode);
	$cell->addText($family->phone);
      }
      
      // Write Student First name in last column
      $cell = $table->addCell(2000);
      $done=array();
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

  public static function FamilyListForHandbookDistribution($year) {
    $fh = tmpfile();
    if (!$fh) die ("could not open temporary file for writing");
    fwrite ($fh, "ID, mother, father, kid1, kid2, kid3\n");
    
    $enrollment = Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS, $year);
    foreach (self::InvolvedFamilies($year) as $family) {
      $csv=array();
      $csv[] = $family->id;
      $csv[] = $family->mother->fullName();
      $csv[] = $family->father->fullName();
      $done=array();
      foreach($enrollment as $e) {
	if (array_key_exists($e->student->id, $done)) continue;
	if ($e->student->family->id == $family->id) {
	  $csv[] = $e->student->firstName;
	  $done[$e->student->id] = 1;
	}
      }
      fputcsv($fh, $csv);
    }
    $filename = self::rosterDir . "FamiliesForHandbookDistribution.csv";
    fseek($fh, 0);
    file_put_contents("$filename", stream_get_contents($fh));
    fclose($fh);
    
  }

  private static function ClassDirectoryTable($table, $class, $email) {
    $fontStyle = array('bold'=>true, 'align'=>'center');
    $styleCell = array('valign'=>'center');
    $styleCellBTLR = array('valign'=>'center', 'textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR);


    $table->addRow(200);
    $table->addCell(500, $styleCell)->addText('First', $fontStyle);
    $table->addCell(500, $styleCell)->addText('Last', $fontStyle);
    if ($class->course->department != Department::Culture) 
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
      if ($class->course->department != Department::Culture) 
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
      $document->SetLandscape();

      $table = $document->table;
      self::ClassDirectoryTable($table, $class, true);
      $text = "Teachers: " . Teachers::TeacherListClassCsv($class->id);
      $document->SetFooter($text);
      $text = "Vidyalaya Inc. 2011-12 $short Roster";
      $document->SetHeader($text);
      $document->filename = "$directory/ClassWide/$short.docx";
      $document->SaveDocument();

      $document = new WordTable(false);
      $table = $document->table;
      self::ClassDirectoryTable($table, $class, false);
      $text = "Teachers: " . Teachers::TeacherListClassCsv($class->id);
      $document->SetFooter($text);
      $text = "Vidyalaya Inc. 2011-12 $short Directory";
      $document->SetHeader($text);
      $document->filename = "$directory/ClassShort/$short.docx";
      $document->SaveDocument();

    }
  }



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

  private static function MailingListsTeachers($year) {
    $fparray=array();
    foreach (Teachers::TeacherListYear($year) as $item) {
      $dept=$item->class->course->department;
      if (empty($fparray[$dept])) $fparray[$dept]=tmpfile();
      foreach(Emails::GetEmailArray($item->MFS, $item->mfsId) as $email) fwrite($fparray[$dept], $email . "\n");
    }

    foreach ($fparray as $dept => $fp) {
      $filename=self::MAILINGLISTDIR . Department::NameFromId($dept) . ".txt";
      saveTempFp($fp, $filename);
      print "saved file $filename\n";
    }

    // copy files to real locations
  }

  private static function MailingListsVolunteers($year) {
    $filename=self::MAILINGLISTDIR . "/volunteers.txt";

    $fp=popen("sort -u --output=$filename", "w");
    if (!$fp) die ("could not open $filename for writing\n");
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
    $filename=self::MAILINGLISTDIR . "/allEmails.csv";
    $fp=fopen($filename, "w");
    if (!$fp) die("Could not open $filemae for writing\n");
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
    self::MailingListsTeachers($year);
    self::MailingListsClass($year);
    self::MailingListsVolunteers($year);
    self::MailingListsAll($year);
  }


  public static function VolunteerListForHandbook($year) {
    $fh = tmpfile();
    if (!$fh) die ("could not open temporary file for writing");

    Reports::VolunteerListForHandbookHtml($year, false, $fh);

    $filename = self::rosterDir . "volunteer.html";
    fseek($fh, 0);
    file_put_contents("$filename", stream_get_contents($fh));
    print "wrote file $filename\n";
    fclose ($fh);

  }

  public static function TeacherListForHandbook($year) {
    $filename = self::rosterDir . "teachers.html";
    $fh = fopen("$filename", "w");

    $teachercount=0; $done = array();
    foreach (Teachers::TeacherListYear($year) as $teacher) {
      $key = "$teacher->MFS:$teacher->mfsId";
      if (!array_key_exists($key, $done)) {
	$done[$key]=1;
	$teachercount++;
      }
    }
    
    fwrite($fh,  "<p>The classes at Vidyalaya are made possible by the volunteerism of following $teachercount teachers.\n");
    fwrite($fh, "<div id='teacherlist'>\n<table>\n");
    foreach (Department::GetAll() as $dept) {
      fwrite($fh, "<tr><th colspan=3 class='rowhead'>" . Department::NameFromId($dept) . "</th></tr>\n");
      foreach (AvailableClass::GetAllYearDepartment($dept, $year) as $class) {
	fwrite($fh, "<tr><td>" .  $class->short() 
	       . "</td><td nowrap='true'>" . $class->course->full . "</td><td>" 
	       . Teachers::TeacherListClassHtml($class->id) . "</td></tr>\n");
      }
    }
    fwrite($fh, "</table>\n</div>\n");
    fclose ($fh);
    print "Wrote file $filename\n";
  }


  private static function printParentContact($parent, $family) {
    print $parent->firstName . ", " . $parent->lastName . ", " . $parent->email .
      ", \"" . $family->address->OneLineAddress() . "\", " . $parent->cellPhone . "\n";
  }
  public static function FullDumpFamilies() {
    print "ID, MF, First Name, Last Name, E-mail Address, Home Address, Mobile Phone\n";
    foreach (Family::GetAll() as $family) {
      print "$family->id, m, ";
      self::printParentContact($family->mother, $family);
      print "$family->id, f, ";
      self::printParentContact($family->father, $family);
    }
  }

  public static function BadgeFile($year) {
    $filename = self::rosterDir . "studentbadge.csv";
    $fh = fopen("$filename", "w");

    $list = array(); $language= array(); $culture= array();
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS, $year) as $item) {
      $list[$item->student->id] = $item->student;
      if ($item->class->course->department == Department::Culture ) {
	$culture[$item->student->id] = $item->class;
      } else {
	$language[$item->student->id] = $item->class;
      }
    }

    fwrite($fh, "first, last, lc, lr, cc, cr,id,lang \n");
    foreach ($list as $item) {
      $csv = array();
      $csv[] = $item->firstName;
      $csv[] = $item->lastName;
      $csv[] = $language[$item->id]->short();
      $csv[] = $language[$item->id]->room->roomNumber;
      if (array_key_exists($item->id, $culture)) {
	$csv[] = $culture[$item->id]->short();
	$csv[] = $culture[$item->id]->room->roomNumber;
      }
      $csv[] = $item->id;
      $csv[] = Department::NameFromId($item->languagePreference);
      fputcsv($fh, $csv);
    }
    fclose($fh);

    $filename = self::rosterDir . "teacherbadge.csv";
    $fh = fopen("$filename", "w");

    fwrite($fh, "id, mfs, class, room, name\n");
    foreach (Teachers::TeacherListYear($year) as $item) {
      $csv = array();
      $csv[] = $item->mfsId;
      $csv[] = MFS::CodeFromId($item->MFS);
      $csv[] = $item->class->short();
      $csv[] = $item->class->room->roomNumber;
      $csv[] = $item->person->fullName();
      fputcsv($fh, $csv);
    }
    fclose ($fh);
  }


  private static $rosterid = null;
  private static $rosterfh = null;

  private static function RosterStudent($student) {
    fwrite(self::$rosterfh,  "\n" . self::$rosterid++ . ",  $student->id, " .  $student->fullName() . ", " . $student->GenderName() );
    fwrite(self::$rosterfh, "(Age: " . (int)$student->AgeAt(Calendar::CurrentSession) . ", Grade: ". $student->Grade() . "), " . $student->CellEmail() . "\n");
    fwrite(self::$rosterfh, "   Home, " . $student->family->phone . ", " .  $student->family->address->city . "\n");
		
    fwrite(self::$rosterfh, "   Mother, " . $student->family->mother->fullName() . ", " . $student->family->mother->WorkCellEmail() . "\n");
    fwrite(self::$rosterfh, "   Father, " . $student->family->father->fullName() . ", " . $student->family->father->WorkCellEmail() . "\n");
  }
	
  private static function RosterClass($class) {
    $filename = "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/roster/txt/" .  $class->short() . ".txt";
    self::$rosterfh = fopen($filename, "w");
    echo "$filename\n";
    //fwrite(self::$rosterfh,  "\n**********************\n");
    fwrite(self::$rosterfh, "Class: " . $class->short() . "\n");
    fwrite(self::$rosterfh, "Room: " . "Facility: " . "\n");
    fwrite(self::$rosterfh, "Teachers: " . Teachers::TeacherListClassCsv($class->id) . "\n"); 
    foreach (Enrollment::GetEnrollmentForClass($class->id)  as $item) {
      self::RosterStudent ($item->student);
    }
    fclose(self::$rosterfh);
  }
	
  public static function Roster($year) {
    foreach (AvailableClass::GetAllYear($year) as $class) {
      self::$rosterid = 1;
      self::$rosterfh = null;
      self::RosterClass($class);
    }

    echo "Run the program to convert text file to pdf file from command line\n";
  }

  private static function printOneStudent($student, $lc, $cc) {
    $printDir = "/home/umesh/student2011";
    $html = "<html><head><style type='text/css'>td {padding-left:10px;}</style></head><body>\n";
    $html .= "<img src='/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/Layouts/PHHSLayout.jpg' width='632' height='700' alt='layout'>\n";

    //    $html .= "<h3>Student</h3>";
    $html .=  "<table>\n";
    $html .= "<tr><td>ID</td><td>$student->id (Family: ". $student->family->id . ", Home Phone: " . $student->family->phone .  ")</td>\n";
    $html .= "<tr><td>Name</td><td>" . $student->fullName() . " (Parents: " . $student->parentsName() .  ")</td>\n";
    $html .=  "</table>\n";


    //    $html .= "<h3>Enrollment</h3>";
    $html .= "<p>&nbsp;</p>";
    $html .=  "<table>\n";
    $html .= "<tr><th>Time</th><th>Class</th><th>Location</th><th>Teachers</th></tr>\n";
    $html .= "<tr><td>09:30 - 10:00</td><td>Prayers</td><td>Cafeteria</td><td>Mukesh Dave et. al.</td>\n";
    if (!is_null($lc)) {
      $html .= "<tr><td>$lc->startTime - $lc->endTime</td><td>". $lc->short() . "</td><td>" . $lc->room->roomNumber . "</td>";
      $html .= "<td>" . Teachers::TeacherListClassHtml($lc->id) .  "\n";
    }
    if (!is_null($cc)) {
      $html .= "<tr><td>$cc->startTime - $cc->endTime</td><td>". $cc->short() . "</td><td>" . $cc->room->roomNumber . "</td>";
      $html .= "<td>" . Teachers::TeacherListClassHtml($cc->id) .  "\n";
    }

    $html .= "</table></body></html>";
    $pdf = PrintFactory::HtmlToPdf($html);
    $fileName = $printDir . "/Student-" . $student->id . ".pdf";
    file_put_contents("$fileName", $pdf);
  }

  private static function RosterSpaStudents($year) {
    $fh = tmpfile();
    if (!$fh) die ("could not open temporary file for writing");
    fwrite ($fh, "ID, First, Last, Language, , Culture, ,\n");


    $language = array(); $culture=array();
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS, $year) as $item) {
      $done[$item->student->id] = $item->student;
      //      print "Printing Student " . $item->student->fullName() . "\n";
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

    foreach ($done as $student) {
      $csv = array();
      $csv[] =  $student->id;
      $csv[] =  $student->firstName;
      $csv[] =  $student->lastName;
      $lc = null; $cc = null; 
      if (array_key_exists($student->id, $language)) {
	$lc = $language[$student->id];
	$csv[] = $language[$student->id]->short();
	$csv[] = $language[$student->id]->room->roomNumber;
      } else {
	$csv[] = "";
	$csv[] = "";
      }
      
      if (array_key_exists($student->id, $culture)) {
	$cc = $culture[$student->id];
	$csv[] = $culture[$student->id]->short();
	$csv[] = $culture[$student->id]->room->roomNumber;
      } else {
	$csv[] = "";
	$csv[] = "";
      }
      
      fputcsv($fh, $csv);
      self::printOneStudent($student, $lc, $cc);
    }

    $filename = self::rosterDir . "StudentsSpa.csv";
    fseek($fh, 0);
    file_put_contents("$filename", stream_get_contents($fh));
    fclose($fh);
  }

  private static function RosterSpaTeachers($year) {

    // todo: if somoene teaches multiple classes, it will print multiple pages. it should be fixed
    $printDir = "/home/umesh/student2011";
    foreach (Teachers::TeacherListYear($year) as $item) {
      print "Printing Teacher " .$item->person->fullName() . "\n";
      $html = "<html><head><style type='text/css'>td {padding-left:10px;}</style></head><body>\n";
      $html .= "<img src='/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/Layouts/PHHSLayout.jpg' width='632' height='700' alt='layout'>\n";

      $html .=  "<table>\n";
      $mfskey=MFS::CodeFromId($item->MFS) . $item->mfsId;
      $html .= "<tr><td>ID</td><td>" . $mfskey . "</td>\n";
      $html .= "<tr><td>Teacher Name</td><td>" . $item->person->fullName() ."</td>\n";
      $html .=  "</table>\n";

      $cc = $item->class;
      $html .=  "<p><table>\n";
      $html .= "<tr><th>Time</th><th>Class</th><th>Location</th><th>Teachers</th></tr>\n";
      $html .= "<tr><td>$cc->startTime - $cc->endTime</td><td>". $cc->short() . "</td><td>" . $cc->room->roomNumber . "</td>";
      $html .= "<td>" . Teachers::TeacherListClassHtml($cc->id) .  "\n";
      $html .= "</table></body></html>";
      $fileName = $printDir . "/Teacher-" . $mfskey . ".pdf";
      file_put_contents("$fileName", PrintFactory::HtmlToPdf($html));
    }    
  }


  public static function RosterSpa($year) {
     self::RosterSpaStudents($year);
     self::RosterSpaTeachers($year);
  }


  public static function RosterFromFile ($filename) {
    self::$rosterid = 1;
    self::$rosterfh = fopen("$filename.out", "w");
    if (($handle = fopen($filename, "r")) !== FALSE) {
      while ((list($studentid, $rest )= fgetcsv($handle, 0, "\t")) !== FALSE) {
	if (empty($studentid)) continue;
	$student = Student::GetItemById($studentid);
	if (empty($student)) {
	  print "student not found for $studentid\n";
	} else {
	  self::RosterStudent ($student);
	}
				
      }
    }
  }

  public static function TeacherDirectory($year) {
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
    $table->addCell(2000, $styleCell)->addText('First', $fontStyle);
    $table->addCell(2000, $styleCell)->addText('Last', $fontStyle);
    $table->addCell(2000, $styleCell)->addText('Phone', $fontStyle);
    $table->addCell(2000, $styleCell)->addText('Class', $fontStyle);
    $table->addCell(2000, $styleCell)->addText('Role', $fontStyle);

    $volunteerTeacher = array();
    $classes = array();
    foreach (Volunteers::GetAllYear($year) as $item) {
      $key = "$item->MFS:$item->mfsId";
      if ($item->role & VolunteerRole::Teacher)
	$volunteerTeacher[$key] = 1;
    }

    foreach (Teachers::TeacherListYear($year) as $item) {
      $key = "$item->MFS:$item->mfsId";
      $uniquTeachers[$key] = $item;
      $class = array_key_exists($key, $classes) ? $classes[$key] . " " : "";
      $class .= $item->class->short();
      $classes[$key] = $class;
    }
    foreach ($uniquTeachers as $item) {
      $key = "$item->MFS:$item->mfsId";
      $table->addRow();
      $table->addCell(2000)->addText($item->person->firstName);
      $table->addCell(2000)->addText($item->person->lastName);
      $table->addCell(2000)->addText($item->person->home->phone);
      $table->addCell(2000)->addText($classes[$key]);
      $key = "$item->MFS:$item->mfsId";
      $role = array_key_exists($key, $volunteerTeacher) ? "Volunteer" : "Participant";
      $table->addCell(2000)->addText($role);
    }
    $text = "Teacher Directory";
    $document->SetFooter($text);
    $text = "Vidyalaya Inc. 2011-12";
    $document->SetHeader($text);
    $document->filename = self::rosterDir ."teachers.docx";
    $document->SaveDocument();
  }

  public static function VolunteerDirectory($year) {
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
    $table->addCell(2000, $styleCell)->addText('First', $fontStyle);
    $table->addCell(2000, $styleCell)->addText('Last', $fontStyle);
    $table->addCell(2000, $styleCell)->addText('Phone', $fontStyle);
    $table->addCell(2000, $styleCell)->addText('Role', $fontStyle);

    $volunteerTeacher = array();
    $classes = array();
    foreach (Volunteers::GetAllYear($year) as $item) {
      $role = $item->role;
      if ($role & VolunteerRole::Teacher) $role = $role ^ VolunteerRole::Teacher;
      if ($role & VolunteerRole::Trustee) $role = $role ^ VolunteerRole::Trustee;
      if ($role == 0) continue;

      $table->addRow();
      $table->addCell(2000)->addText($item->person->firstName);
      $table->addCell(2000)->addText($item->person->lastName);
      $table->addCell(2000)->addText($item->person->home->phone);
      $table->addCell(2000)->addText(VolunteerRole::IdToString($role));
    }
    $text = "Volunteer Directory";
    $document->SetFooter($text);
    $text = "Vidyalaya Inc. 2011-12";
    $document->SetHeader($text);
    $document->filename = self::rosterDir ."volunteers.docx";
    $document->SaveDocument();
  }


  private static function AttendanceSheetFill($class) {
    $inputFileName = "/home/umesh/Dropbox/Vidyalaya-Management/Admission/attendance2011.xlsx";
    $activeSheetIndex=0;
    $row =4;

    /**  Identify the type of $inputFileName  **/
    //		$inputFileType =PHPExcel_IOFactory::identify($inputFileName);
    /**  Create a new Reader of the type that has been identified  **/
    //		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
    /**  Load $inputFileName to a PHPExcel Object  **/
    //		$objPHPExcel = $objReader->load($inputFileName);
    $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
    $objPHPExcel->setActiveSheetIndex($activeSheetIndex);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    $objPHPExcel->getActiveSheet()->setShowGridlines(false);
    $objPHPExcel->getActiveSheet()->setRightToLeft(FALSE);
		
    //		$objPHPExcel->getProperties()->setCreator("Umesh Mittal");
    //		$objPHPExcel->getProperties()->setLastModifiedBy("Umesh Mittal");
    //		$objPHPExcel->getProperties()->setTitle("Students");
    //		$objPHPExcel->getProperties()->setSubject("Vidyalaya Students");
    //		$objPHPExcel->getProperties()->setDescription("List of Vidyalaya Students by Classes");
		
		

    $objPHPExcel->getActiveSheet()->setTitle($class->short());
    $shortValue = $class->course->department == Department::Culture ? $class->short() . " Attendance Sheet" : $class->short();
    $objPHPExcel->getActiveSheet()->getCell("B2")->setValue($shortValue);
    $objPHPExcel->getActiveSheet()->getCell("B3")->setValue("Room: " . $class->room->roomNumber);
			
    $objPHPExcel->getActiveSheet()->getRowDimension("1")->setVisible(TRUE);
    $objPHPExcel->getActiveSheet()->getRowDimension("2")->setVisible(TRUE);
    $objPHPExcel->getActiveSheet()->getRowDimension("3")->setVisible(TRUE);
    foreach(Enrollment::GetEnrollmentForClass ($class->id) as $item) {
      $cellValue=sprintf("B%d", $row);
      $objPHPExcel->getActiveSheet()->getCell($cellValue)->setValue($item->student->id);
      $cellValue=sprintf("C%d", $row);
      $fullName=$item->student->fullName();
      if ($class->course->department == Department::Kindergarten) {
	$fullName = substr(Department::NameFromId($item->student->languagePreference), 0, 1) . " " . $fullName;
      }
      $objPHPExcel->getActiveSheet()->getCell($cellValue)->setValue($fullName);
      $objPHPExcel->getActiveSheet()->getRowDimension($row)->setVisible(TRUE);
      $row++;
    }
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setVisible(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setVisible(TRUE);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setVisible(TRUE);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setVisible(TRUE);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setVisible(TRUE);

    return $objPHPExcel;
  }
	
  public static function AttendanceSheet($year) {
    foreach (AvailableClass::GetAllYear($year) as $class) {
      $excelDir = self::BaseDir . "/" . $class->session . "/attendance/" . 
	Department::NameFromId($class->course->department) . "/excel/";
      $pdfDir=str_replace("excel", "pdf", $excelDir);
      if (!file_exists($excelDir) && !mkdir($excelDir, 0777, true)) die ("error creating directory $excelDir");
      if (!file_exists($pdfDir) && !mkdir($pdfDir, 0777, true)) die ("error creating directory $pdfDir");
      $excelFile=$excelDir . $class->short() . ".xlsx";
      $pdfFile=$excelDir . $class->short() . ".pdf";

      $objPHPExcel = self::AttendanceSheetFill($class);
      
      //PDF Writer is horrible, do not use it.
      //      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
      //      $objWriter->save($pdfFile);

      $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter ("Teachers: " . Teachers::TeacherListClassCsv($class->id));
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save($excelFile);
      //      echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MBrn\no";
    }
  }


}	

class NewsletterHtml {
  public static function Publish($date) {
    $year="2011"; // todo: get year from date
    $fh = tmpfile();
    if (!$fh) die ("could not open temporary file for writing");

    $directory= "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/Newsletter/$date/";

    fwrite($fh, "<div id='newsletter'>\n");

    // Step 1: publish dates
    $expiration = "2011-10-30";
    fwrite ($fh, "<p class='newsgate'> Expiration Date: $expiration\n");
    fwrite ($fh, "<p class='newsgate'> Last Class: $date\n");
    fwrite ($fh, "  <a name='top'>&nbsp;</a>\n");
    fwrite ($fh, "\n");

    $classfile=$directory . "summary.html";
    if (file_exists($classfile)) {
      fwrite ($fh, "<h3>Summary</h3>" . "\n");
      fwrite($fh, file_get_contents($classfile));
    }

    // Step 2: availble table
    $available = array(); $unavailable = "";
    foreach (AvailableClass::GetAllYear($year) as $class) {
      $short = $class->short();
      $classfile=$directory . $short . ".html";
      if (file_exists($classfile)) {
	$available[] =  "<a href='[~[*id*]~]#$short'>$short</a>";
      } else {
	$unavailable .=  " $short";
      }
    }

    $table = "<table>\n"; 
    if (!empty($available)) $table .= "<tr><td>Available</td><td>" . implode(",", $available) . "</td></tr>\n";
    if (!empty($unavailable)) $table .= "<tr><td>Pending</td><td>$unavailable</td></tr>\n";

    $table .= "</table>\n";

    fwrite($fh, $table);

    // Step 3: Print Classes
    foreach (AvailableClass::GetAllYear($year) as $class) {
      $short = $class->short();
      $description = htmlspecialchars($class->course->full);
      $teachers = Teachers::TeacherListClassHtml($class->id);
      $dept = strtolower(Department::NameFromId($class->course->department));
      $color = Department::$colors[$class->course->department];



      $classheader = <<<CLASSHEADER
 <!--            *******************************  SECTION $short ******************** -->
  <a name="$short">&nbsp;</a>
  <div style="border: 1px $color dotted;">
    <table>
      <caption class="$dept">$short - $description - $teachers</caption>
      <COLGROUP><COL width="10%"><COL width="90%">
      <tr><td valign="top">Comments</td><td>

CLASSHEADER;

$classfooter = <<<CLASSFOOTER
	</td></tr><tr><td valign="top">&nbsp;</td><td>
    </td></tr>
    </table>
  </div>
  <a href="[~[*id*]~]#top">top</a>
<p>      


CLASSFOOTER;


      //insert class file
$classfile=$directory . $short . ".html";
if (file_exists($classfile)) {
  fwrite ($fh, $classheader . "\n");
  fwrite($fh, file_get_contents($classfile));
  fwrite ($fh, $classfooter . "\n");
}

    }


    fwrite($fh, "</div>\n");
    fseek($fh, 0);

    $filename = "/tmp/foo";
    file_put_contents("$filename", stream_get_contents($fh));
    print "check $filename\n";
    fclose($fh);

  }
}

NewsletterHtml::Publish("2011-10-23"); exit();
//Publications::FamilyListForHandbookDistribution(2011); exit();
//Publications::AttendanceSheet(2011); exit();
//Publications::RosterFromFile("/tmp/aa"); exit();
//Publications::Roster(2011); exit();

//Publications::RosterSpa(2011); exit();

//Publications::FullDumpFamilies();

//Publications::BadgeFile(2011); exit();
//Publications::CreateMailingLists(2011);exit();

//Publications::VolunteerListForHandbook(2011); exit();
//Publications::TeacherListForHandbook(2011);exit();

//Publications::SchoolDirectory(2011); exit();
//Publications::TeacherDirectory(2011); exit (); // Directory of all Teachers
//Publications::VolunteerDirectory(2011); exit (); // Directory of all Volunteers
//Publications::ClassDirectory(2011); exit (); // Directory of all classes, with and without email



?>


