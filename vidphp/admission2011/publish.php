<?php

$libDir="../../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";

require_once "../../Classes/PHPWord.php";

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
      $document->SetLandscape();
      $text = $class->short() . "- " . Teachers::TeacherList($class->id);
      $document->SetFooter($text);
      $text = "Vidyalaya Inc. 2011-12 Roster";
      $document->SetHeader($text);

      $table = $document->table;
      self::ClassDirectoryTable($table, $class, true);
      $document->filename = "$directory/ClassWide/$short.docx";
      $document->SaveDocument();

      $document = new WordTable(false);
      $table = $document->table;
      self::ClassDirectoryTable($table, $class, false);
      $document->filename = "$directory/ClassShort/$short.docx";
      $document->SaveDocument();

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
    self::MailingListsVolunteers($year);
    self::MailingListsAll($year);
  }

  public static function VolunteerListForHandbook($year) {
    $filename = self::rosterDir . "volunteer.html";
    $fh = fopen("$filename", "w");
    fwrite($fh,  "<p>Vidyalaya appreciates the following volunteers for their service.\n");
    fwrite($fh, "<div id='teacherlist'>\n<table>\n");
    fwrite($fh, "<tr><th class='rowhead' width='200px'>Name</th><th>Role</th></tr>\n");
    foreach(Volunteers::GetAllYear($year) as $item) {
      fwrite($fh, "<tr><td>" .  $item->person->fullName() . "</td><td>" . VolunteerRole::IdToString($item->role) . "</td></tr>\n"); 
    }
    fwrite($fh, "</table>\n</div>\n");
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


  const rosterDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/roster/";

  public static function BadgeFile($year) {
    $filename = self::rosterDir . "studentbadge.csv";
    $fh = fopen("$filename", "w");

    $list = array(); $language= array(); $culture= array();
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::Eastlake, $year) as $item) {
      $list[$item->student->id] = $item->student;
      if ($item->class->course->department == Department::Culture ) {
	$culture[$item->student->id] = $item->class;
      } else {
	$language[$item->student->id] = $item->class;
      }
    }
    foreach ($list as $item) {
      $csv = array();
      $csv[] = $item->id;
      $csv[] = $item->firstName;
      $csv[] = $item->lastName;
      $csv[] = $language[$item->id]->short();
      $csv[] = $language[$item->id]->room->roomNumber;
      if (array_key_exists($item->id, $culture)) {
	$csv[] = $culture[$item->id]->short();
	$csv[] = $culture[$item->id]->room->roomNumber;
      }
      fputcsv($fh, $csv);
    }
    fclose($fh);

    $filename = self::rosterDir . "teacherbadge.csv";
    $fh = fopen("$filename", "w");

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

}	



//Publications::RosterFromFile("/tmp/aa"); exit();
//Publications::Roster(2011); exit();

//Publications::FullDumpFamilies();

//Publications::BadgeFile(2011); exit();
//Publications::CreateMailingLists(2011);exit();

//Publications::VolunteerListForHandbook(2011); exit();
Publications::TeacherListForHandbook(2011);exit();

//Publications::SchoolDirectory(); exit();
//Publications::TeacherDirectory(2011); exit (); // Directory of all Teachers
//Publications::VolunteerDirectory(2011); exit (); // Directory of all Volunteers
//Publications::ClassDirectory(2011); exit (); // Directory of all classes, with and without email



?>


