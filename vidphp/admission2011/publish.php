#!/usr/bin/php -q
<?php
@ob_end_clean();
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
  const MAILINGLISTDIR="/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/mailinglist/";
  const rosterDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/roster/";
  const volunteerDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/volunteer/";

  // call people for meeting with Trustee based on Praveen's date assignment
  public static function FamilyMarshalling() {
    $nonteachingfile = self::volunteerDir . "familyVolunteerList.csv";

    $production=1;
    $subject = "Volunteering Meeting:  Family ";
    $mail =   Mail::SetupMailSPA();
    $draft="";
    if ($production != 1) {
      $subject = "[Test] $subject";
      $draft = "<p>This is a draft <br />";
    }
    //    $content = file_get_contents("volunteerMarshall.html");
        $content = file_get_contents("volunteerMarshallNoshow.html");
	//    $content = file_get_contents("volunteerMarshallReminder.html");
    $count=0;

    if (($fh = fopen($nonteachingfile, "r")) !== FALSE) {
      fgets($fh);
      while ((list($familyid, $name, $start, $rolevalue, $date )= fgetcsv($fh, 0, ",")) !== FALSE) {
	$id = trim($familyid);
	//	if (empty($id) || $rolevalue != "Available") continue;
	if (empty($id) || $rolevalue != "noshow") continue;

	if ($date != "Sunday, January 13, 2013") continue;
	$count++;
	print "$count. Family $id, $date\n";
	$body = str_replace("==DATE==", $date, $content);
	$family=Family::GetItemById($id);
	Mail::SetFamilyAddress($mail, $family, $production);
	$s = "$subject $id";
	$mail->Subject = $s;
	$a = $family->parentsName(); $csv[] = $a;
	$salutation = "<p>Dear " . $a . ",</p>";
	$mail->Body = $draft . $salutation . $body;
	$mail->AltBody = "Family: $family->id"; //Text Body

	print "foo, $family->id, " . $family->mother->fullName() . ", " .	  $family->father->fullName() . "\n";
	//	continue;
	  //die ("i die here");

	  //      return;
	  if(!$mail->Send()) {
	    echo "Mailer Error: $family->id:  " . $mail->ErrorInfo . "\n";
	    return;
	  }  else {
	    echo "Message has been sent, Family $family->id\n";
	  }

	  // die("hello\n");
	  sleep(1);
      
	  $mail->ClearAllRecipients(); 
	  $mail->ClearAttachments(); 
	  $mail->ClearCustomHeaders(); 

      } 
    }
  }

  public static function FamilyVolunteerList($year=null) {
    if (is_null($year)) $year=Calendar::CurrentYear();
    if ($year >= 2010) $year -= 2010;

    $startYear = Enrollment::familyStartingYear();
    $role = array();
    foreach(Teachers::TeacherListYear($year) as $teacher) {
      $role[$teacher->person->home->id] = "Teacher";
    }

    $nonteachingfile = self::rosterDir . "nonTeachingVolunteers.csv";
    if (($fh = fopen($nonteachingfile, "r")) !== FALSE) {
      fgets($fh);
      while ((list($familyid, $name, $start, $rolevalue )= fgetcsv($fh, 0, ",")) !== FALSE) {
	$id = trim($familyid);
	if (!empty($id)) {
	  if (isset($role[$id])) {
	    $role[$id] .= " $rolevalue";
	  } else {
	    $role[$id] = $rolevalue;
	  }
	  print "$role[$id]\n";
	}
      } 
    }
    
    $fh = tmpfile();
    if (!$fh) die ("could not open temporary file for writing");
    fwrite ($fh, "Family, Parents, Starting, Role\n");
    foreach (Enrollment::GetEnrolledFamilesForFacilitySession(Facility::Brooklawn, $year) as $family) {
      $csv=array();
      $csv[] = $family->id;
      $csv[] = $family->parentsName();
      $csv[] = $startYear[$family->id] + 2010;
      $csv[] = isset($role[$family->id]) ? $role[$family->id] : "Available";
      fputcsv($fh, $csv);
     }

    $filename = self::rosterDir . "familyVolunteerList.csv";
    fseek($fh, 0);
    file_put_contents("$filename", stream_get_contents($fh));
    print "saved $filename\n";
    fclose($fh);
   
  }

  public static function NotComingBack($year=null) {
    if (is_null($year)) $year=Calendar::CurrentYear();
    if ($year >= 2010) $year -= 2010;

    $fh = tmpfile();
    if (!$fh) die ("could not open temporary file for writing");
    fwrite ($fh, "ID, First, Last, Address, City, Fee Check\n");
    foreach (FamilyTracker::NotComingBack($year) as $tracker) {
      $csv=array();
      $family = Family::GetItemById($tracker->family);
      $csv[] = $family->id;
      $csv[] = $family->mother->firstName;
      $csv[] = $family->mother->lastName;
      $csv[] = $family->address->addr1;
      $csv[] = $family->address->city . ", " . $family->address->state . " " . $family->address->zipcode;
      $csv[] = 0;
    
      fputcsv($fh, $csv);
     }
    $filename = self::rosterDir . "notcomingback.csv";
    fseek($fh, 0);
    file_put_contents("$filename", stream_get_contents($fh));
    print "saved $filename\n";
    fclose($fh);
   
  }


  public static function NewLanguageStudents($year) {
    $fh = tmpfile();
    if (!$fh) die ("could not open temporary file for writing");
    fwrite ($fh, "Student, Family, Name, Age, Parents, Class\n");

    $production=1;
    $subject = "Vidyalaya Free Gift for ";
    $mail =   Mail::SetupMailSPA();
    $draft="";
    if ($production != 1) {
      $subject = "[Test] $subject";
      $draft = "<p>This is a draft <br />";
    }
    $content = file_get_contents("freeGiftemail.html");


    foreach(Department::GetAll() as $dept) {
      if (Department::IsLanguage($dept)) {
	foreach (Enrollment::GetAllEnrollmentForDeptSession($dept, $year) as $e) {
	  if ($e->student->WasEverEnrolled()) continue;


	  $csv = array(); $body = $content;

	  $a = $e->student->id; $csv[] = $a;
	  $body = str_replace("==STUDENTID==", $a, $body);

	  $a = $e->student->family->id; $csv[] = $a;
	  $family=Family::GetItemById($a);
	  Mail::SetFamilyAddress($mail, $family, $production);
	  $body = str_replace("==FAMILYID==", $a, $body);

	  $a = $e->student->fullName(); $csv[] = $a;
	  $s = "$subject $a";
	  $mail->Subject = $s;
	  $body = str_replace("==STUDENTNAME==", $a, $body);

	  $a = intval($e->student->Age()); $csv[] = $a;
	  $body = str_replace("==AGE==", $a, $body);

	  $a = $e->student->family->parentsName(); $csv[] = $a;
	  $salutation = "<p>Dear " . $a . ",</p>";
	  $body = str_replace("==PARENTNAME==", $a, $body);

	  $csv[] = $e->class->short();
       	  fputcsv($fh, $csv);

	  $mail->Body = $draft . $salutation . $body;
	  $mail->AltBody = "Family: $family->id"; //Text Body

	  //continue;
	  //die ("i die here");

	  //      return;
	  if(!$mail->Send()) {
	    echo "Mailer Error: $family->id:  " . $mail->ErrorInfo . "\n";
	    return;
	  }  else {
	    echo "Message has been sent, Family $family->id\n";
	  }

	  //	  die("hello\n");
      
	  $mail->ClearAllRecipients(); 
	  $mail->ClearAttachments(); 
	  $mail->ClearCustomHeaders(); 

	}
      }
    }

    $filename = self::rosterDir . "newLanguageStudents.csv";
    fseek($fh, 0);
    file_put_contents("$filename", stream_get_contents($fh));
    print "saved $filename\n";
    fclose($fh);

  }

  public static function NewStudents($year=null) {
    if (is_null($year)) $year=Calendar::CurrentYear();
    if ($year >= 2010) $year -= 2010;

    $fh = tmpfile();
    if (!$fh) die ("could not open temporary file for writing");
    fwrite ($fh, "ID, First, Last, Age, Class 1, room 1, class 2, room2\n");
    
    $i=0; $j=0;
    foreach (Enrollment::GetStudents($year) as $studentId => $value) {
      $csv=array();
      if (!Student::WasEverEnrolledId($studentId)) {
	$s = Student::GetItemById($studentId);
	$csv[] = $studentId;
	$csv[] = $s->firstName;
	$csv[] = $s->lastName;
	$csv[] = intval($s->Age()); 
	foreach (Enrollment::GetEnrollmentStudent($s->id, $year) as $e) {
	  $csv[] = $e->class->short();
	  $csv[] = $e->class->room->roomNumber;
	}
	$i++;
	fputcsv($fh, $csv);
      } else {
	$j++;
      }
    }

    print "new = $i, old=$j\n";
    $filename = self::rosterDir . "newStudents.csv";
    fseek($fh, 0);
    file_put_contents("$filename", stream_get_contents($fh));
    fclose($fh);
  }

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
    $enrollment = Enrollment::GetAllEnrollmentForFacilitySession(Facility::Brooklawn, $year);

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

    $directory="/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/roster/word";
    $document->filename = "$directory/2012.docx";
    print "saving file $directory/2012.docx\n";
    $document->SaveDocument();
  }

  public static function FamilyListForHandbookDistribution($year) {
    $fh = tmpfile();
    if (!$fh) die ("could not open temporary file for writing");
    fwrite ($fh, "ID, mother, father, kid1, kid2, kid3\n");
    
    $enrollment = Enrollment::GetAllEnrollmentForFacilitySession(Facility::Brooklawn, $year);
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
    $directory = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/roster/word/";

    foreach (AvailableClass::GetAllYear($year) as $class) {
      $short= $class->short();

      $document = new WordTable();
      $document->SetLandscape();

      $table = $document->table;
      self::ClassDirectoryTable($table, $class, true);
      $text = "Teachers: " . Teachers::TeacherListClassCsv($class->id);
      $document->SetFooter($text);
      $text = "Vidyalaya Inc. 2012-13 $short Roster";
      $document->SetHeader($text);
      $document->filename = "$directory/ClassWide/$short.docx";
      $document->SaveDocument();

      $document = new WordTable(false);
      $table = $document->table;
      self::ClassDirectoryTable($table, $class, false);
      $text = "Teachers: " . Teachers::TeacherListClassCsv($class->id);
      $document->SetFooter($text);
      $text = "Vidyalaya Inc. 2012-13 $short Directory";
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
    foreach(Volunteers::GetAllYear(2012) as $item) {
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
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::Brooklawn, $year) as $item) {
      $list[$item->student->id] = $item->student;
      if ($item->class->course->department == Department::Culture ) {
	$culture[$item->student->id] = $item->class;
      } else {
	$language[$item->student->id] = $item->class;
      }
    }

    fwrite($fh, "first, last, lc, lr, cc, cr,id,lang, family \n");
    foreach ($list as $item) {
      $csv = array();
      $csv[] = $item->firstName;
      $csv[] = $item->lastName;
      $csv[] = $language[$item->id]->short();
      $csv[] = $language[$item->id]->room->roomNumber;
      if (array_key_exists($item->id, $culture)) {
	$csv[] = $culture[$item->id]->short();
	$csv[] = $culture[$item->id]->room->roomNumber;
      } else {
	$csv[]="";
	$csv[]="";
      }
      $csv[] = $item->id;
      $csv[] = Department::NameFromId($item->languagePreference);
      $csv[] = $item->family->id;
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
    $filename = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/roster/txt/" .  $class->short() . ".txt";
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
    $printDir = "/home/umesh/student2012";
    $html = "<html><head><style type='text/css'>td {padding-left:10px;}</style></head><body>\n";
    $html .= "<img src='/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/Layouts/PHHSLayout.jpg' width='632' height='700' alt='layout'>\n";

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
    fwrite ($fh, "ID, Family, First, Last, Language, , Culture, ,Parents,\n");


    $language = array(); $culture=array();
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::Brooklawn, $year) as $item) {
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
      $csv[] =  $student->family->id;
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

      $csv[] = $student->family->parentsName();
      
      fputcsv($fh, $csv);
      //      self::printOneStudent($student, $lc, $cc);
    }

    $filename = self::rosterDir . "StudentsSpa.csv";
    fseek($fh, 0);
    file_put_contents("$filename", stream_get_contents($fh));
    fclose($fh);
  }

  private static function RosterSpaTeachers($year) {

    // todo: if somoene teaches multiple classes, it will print multiple pages. it should be fixed
    $printDir = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/OpeningDay/pdf";
    foreach (Teachers::TeacherListYear($year) as $item) {
      print "Printing Teacher " .$item->person->fullName() . "\n";
      $html = "<html><head><style type='text/css'>td {padding-left:10px;}</style></head><body>\n";
      $html .= "<img src='/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/Layout/umesh.jpg' width='683' height='461' alt='layout'>\n";

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

  private static function RosterAdults($year) {
    $fh = tmpfile();
    if (!$fh) die ("could not open temporary file for writing");

    fwrite ($fh, "ID, MFS, First, Last, Description\n");
    foreach (Enrollment::GetEnrolledFamilesForFacilitySession(Facility::Brooklawn, $year) as $item) {
      $csv = array();
      $csv[] = $item->id;
      $csv[] = "Mother";
      $csv[] = $item->mother->firstName;
      $csv[] = $item->mother->lastName;
      $csv[] = "Enrolled";
      fputcsv($fh, $csv);

      $csv=array();
      $csv[] = $item->id;
      $csv[] = "Father";
      $csv[] = $item->father->firstName;
      $csv[] = $item->father->lastName;
      $csv[] = "Enrolled";
      fputcsv($fh, $csv);
    }

    foreach (Volunteers::GetAllYear($year) as $item) { // volunteers
      $csv = array();
      $csv[] = $item->person->mfsId;
      $csv[] = MFS::StringFromId($item->person->MFS);
      $csv[] = $item->person->firstName;
      $csv[] = $item->person->lastName;
      $csv[] = "Volunteer";
      fputcsv($fh, $csv);
    }

    $filename = self::rosterDir . "adults.csv";
    fseek($fh, 0);
    file_put_contents("$filename", stream_get_contents($fh));
    fclose($fh);
  }


  public static function RosterSpa($year) {
     self::RosterSpaStudents($year);
     self::RosterSpaTeachers($year);
     self::RosterAdults($year);
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
    $text = "Vidyalaya Inc. 2012-13";
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
    $text = "Vidyalaya Inc. 2012-13";
    $document->SetHeader($text);
    $document->filename = self::rosterDir ."volunteers.docx";
    $document->SaveDocument();
  }


  private static function AttendanceSheetFill($class) {
    $inputFileName = "/home/umesh/Dropbox/Vidyalaya-Management/Admission/attendance2012.xlsx";
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

    $count = 0;
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
      $count++;
    }
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setVisible(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setVisible(TRUE);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setVisible(TRUE);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setVisible(TRUE);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setVisible(TRUE);

    $timestamp = date('d M Y h:i A');
    $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter ("&L$timestamp&CTeachers: " . Teachers::TeacherListClassCsv($class->id) . ", Count: " . $count);


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
      //      $pdfFile=$excelDir . $class->short() . ".pdf";

      $objPHPExcel = self::AttendanceSheetFill($class);
      
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save($excelFile);
      //      return;
      //      $objWriter = new PHPExcel_Writer_PDF($objPHPExcel);
      //      $objWriter->save("/tmp/umesh.pdf"); // did not look good
      //      die("check /tmp/umesh.pdf\n");

      //      echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MBrn\no";
    }
  }

  private static function LanguageAssessmentFill($class) {
    $templateDirectory = "/home/umesh/Dropbox/Vidyalaya-Management/templates/LanguageAssessment";
    $filename = $class->course->department == Department::Kindergarten ? "KG" :  $class->course->level;
    $inputFileName = "$templateDirectory/". $filename . ".xlsx";

    $activeSheetIndex=0;
    $row =3;

    $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
    $objPHPExcel->setActiveSheetIndex($activeSheetIndex);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    $objPHPExcel->getActiveSheet()->setShowGridlines(false);
    $objPHPExcel->getActiveSheet()->setRightToLeft(FALSE);


    $objPHPExcel->getActiveSheet()->setTitle($class->short());
    //    $shortValue = $class->course->department == Department::Culture ? $class->short() . " Language Asessment" : $class->short();
    //    $objPHPExcel->getActiveSheet()->getCell("B2")->setValue($shortValue);
    $objPHPExcel->getActiveSheet()->getCell("B1")->setValue($class->course->short . " " . $class->section);
    //    $objPHPExcel->getActiveSheet()->getCell("B3")->setValue("Room: " . $class->room->roomNumber);
			
    $objPHPExcel->getActiveSheet()->getRowDimension("1")->setVisible(TRUE);
    $objPHPExcel->getActiveSheet()->getRowDimension("2")->setVisible(TRUE);
    $objPHPExcel->getActiveSheet()->getRowDimension("3")->setVisible(TRUE);

    $count = 0;
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
      $count++;
    }
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setVisible(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setVisible(TRUE);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setVisible(TRUE);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setVisible(TRUE);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setVisible(TRUE);

    $timestamp = date('d M Y h:i A');
    $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter ("&L$timestamp&CTeachers: " . Teachers::TeacherListClassCsv($class->id) . ", Count: " . $count);

    return $objPHPExcel;


  }

  public static function LanguageAssessment($year) {
    foreach (AvailableClass::GetAllYear($year) as $class) {
      if ($class->course->department == Department::Culture) continue;
      $excelDir = self::BaseDir . "/" . $class->session . "/assessment/" . 
	"/excel/";
      $pdfDir=str_replace("excel", "pdf", $excelDir);
      if (!file_exists($excelDir) && !mkdir($excelDir, 0777, true)) die ("error creating directory $excelDir");
      if (!file_exists($pdfDir) && !mkdir($pdfDir, 0777, true)) die ("error creating directory $pdfDir");
      $excelFile=$excelDir . $class->short() . ".xlsx";
      //      $pdfFile=$excelDir . $class->short() . ".pdf";

      $objPHPExcel = self::LanguageAssessmentFill($class);
      
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save($excelFile);
    }
  }


}	

class NewsletterHtml {
  public static function Publish() {
    $year="2012"; // todo: get year from date
    $fh = tmpfile();
    if (!$fh) die ("could not open temporary file for writing");

    $previous = EventCalendar::PreviousSchoolDay();
    $next =  EventCalendar::NextSchoolDay();

    print "previous is $previous->date, next is $next->date\n";

    $date = $previous->date;
    $expiration = $next->date;
    $week = $previous->weekNumber;


    $directory= "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/Newsletter/$date/";

    fwrite($fh, "<div id='newsletter'>\n");

    // Step 1: publish dates
    fwrite ($fh, "<p class='newsgate'> Week: $week <br />Expiration Date: $expiration <br />Last Class: $date\n");
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
  $content = file_get_contents($classfile);
  $content = preg_replace('/__NEWLINE__/', '</td></tr><tr><td valign="top">&nbsp;</td><td>', $content); 


  fwrite($fh, $content);
  fwrite ($fh, $classfooter . "\n");
}

    }


    fwrite($fh, "</div>\n");
    fseek($fh, 0);

    $filename=$directory . "final.html";
    file_put_contents("$filename", stream_get_contents($fh));
    print "check $filename\n";
    fclose($fh);

  }
}

class EventManager {

  private static function EventMail($family, $body) {
    if ($family->id != 273) return;
    $footer="<p>Regards,<p>Vidyalaya Event Management<br />(sent by: Umesh Mittal)</p>";
    $production=1;
    $subject = "AVG Visit Event, Family- $family->id";
    print "Trying to send email to id " . $family->id . "\n";
    if ($production == 0) $subject = "[Test] $subject";
    $mail = Mail::SetupMailSpa();
    //    Mail::SetFamilyAddress(&$mail, $family, $production);
    $mail->Subject = $subject;
    $salutation = "<p>Dear " . $family->parentsName() . ",";
    $mail->Body = $salutation . $body . $footer;
    $mail->AltBody = "This is the body when user views in plain text format, opening day $family->id"; //Text Body
    

    if(!$mail->Send()) {
      echo "Mailer Error: Family: $family->id: " . $mail->ErrorInfo . "\n";
    }  else {
      echo "Message has been sent, Family: $family->id:\n";
    }

    //die ("only one");

  }

  // let us do some workflow here
  private static function PaymentConfirmation($registration, $familystudent, &$age, &$adult) {
    $person = Person::PersonFromId($registration->MFS, $registration->mfsId);

    if ($registration->amountPaid == 0 ) return;
    if ($registration->statusId & ItemRegistrationStatus::PaymentAcknowledged) return;
    print "\nFamily: " . $person->home->id . "\n\n";

    $body = <<<BODY
<p>Thank you for registration for <a href="http://www.vidyalaya.us/shiksha/avg2012.html"
target="_blank">AVG Visit</a> event on Sunday November 13, 2011. This email is your confirmation that we have received \$$registration->amountPaid from you.

BODY;

    if (array_key_exists($person->home->id, $familystudent))  {
      $body .= "<p>For Enrolled families, the registration fee is $10 per family. For your family, we know following persons \n";
      $family = Family::GetItemById($person->home->id);
      $parents = $family->parentsName();
      $adult +=2;
      $body .= "<ul><li>Adults: $parents<li>Children: "; 
      $kids = "";

      foreach(explode(" ", $familystudent[$person->home->id]) as $id) {
	$student = Student::GetItemById($id);
	$ages = intval($student->Age());
	$kids .= $student->fullName() . "(" . $ages  . ") " ;
	
	if (array_key_exists($ages, $age)) {$age[$ages]++;} else {$age[$ages]=1;}
      }

      $body .= "$kids </ul><p>Please bring a printout of this page with you for ease of on-site registration.\n";

      if ($registration->amountPaid > 10) {
	$count = intval(($registration->amountPaid - 10)/5);
	$adult +=$count;
	$body .= "<p>We are also expecting additional $count guests based on your fee. \n";
      }

    } else {
      $count = intval($registration->amountPaid/5);
	$adult +=$count;
      $body .= "<p>For volunteer families, the registration cost is $5 per person. We expect to see $count adults. Please do let us know if there is any change\n";
    }
    
    //    echo $body;
  self::EventMail($person->home, $body);
    ItemRegistration::UpdateStatusAdmin($registration, ItemRegistrationStatus::PaymentAcknowledged);
    //    die ("only one");
    return;
  }

  private static function workflow($registration) {
    if ($registration->statusId & ItemRegistrationStatus::Decline ) {
      if ($registration->statusId & ItemRegistrationStatus::DeclineAcknowledged) return;
      $body = file_get_contents("event1.decline.html");
      self::EventMail($person->home, $body);
      ItemRegistration::UpdateStatusAdmin($registration, ItemRegistrationStatus::DeclineAcknowledged);
      return;
    }


    if ($registration->statusId & ItemRegistrationStatus::CancelRequest ) {
      print "do not know how to handle cancel\n";
      return;
    }

    if ($registration->statusId & ItemRegistrationStatus::Interested ) {
      if ($registration->amountPaid != 0 ) return;
      if ($registration->statusId & ItemRegistrationStatus::Cancelled) return;
      $body = file_get_contents("event1.interested.html");
      self::EventMail($person->home, $body);
      ItemRegistration::UpdateStatus($registration, ItemRegistrationStatus::Cancelled | ItemRegistrationStatus::CancelAcknowledged);
      return;
    }
  }

  private static function UnknownReminder($status) {
    // create unknown list
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::Brooklawn, 2012) as $enrollment) { // registered students
      $familyId = $enrollment->student->family->id;
      if (!array_key_exists($familyId, $status)) {
	$status[$familyId] = 0;
	$body = file_get_contents("event1.announce.html");
	self::EventMail($enrollment->student->family, $body);
      }
    }

    foreach (Volunteers::GetAllYear(2012) as $item) { // volunteers
      $familyId = $item->person->home->id;
      if (!array_key_exists($familyId, $status)) {
	$status[$familyId] = 0;
	$body = file_get_contents("event1.announce.html");
	self::EventMail($item->person->home, $body);
      }
    }
  }

  public static function ReportParticipation($eventId) {

    $familyReg=array();
    foreach(ItemRegistration::EventRegistration($eventId) as $registration) {
      $person = Person::PersonFromId($registration->MFS, $registration->mfsId);
      if (!array_key_exists($person->home->id, $familyReg)) {
	$familyReg[$person->home->id] = $registration;
      }
    }

    // store enrollment data
    $enrollment = Enrollment::GetAllEnrollmentForFacilitySession(Facility::Brooklawn, 2012);
    $done=array(); 
    foreach($enrollment as $e) {
      if (array_key_exists($e->student->family->id, $familyReg)) continue;
      if (array_key_exists($e->student->family->id, $done)) continue;
      $done[$e->student->family->id] = 1;
      $body = file_get_contents("event1.announce.html");
      self::EventMail($e->student->family, $body);
    }

    return;

    print "Total Paid: $total\nAdults:  $adult\n";
    $kids=0;
    foreach ($age as $ages => $count) {
      print "$ages     $count\n";
      $kids += $count;
    }

    print "total kids $kids\n";
  }

  public static function PostPayment($eventId, $amount, $date, $familyId) {
    $time=time();
    $interest = array(); $cancel=array(); $decline=array(); $familyReg=array();
    foreach(ItemRegistration::EventRegistration($eventId) as $registration) {
      $person = Person::PersonFromId($registration->MFS, $registration->mfsId);
      if (!array_key_exists($person->home->id, $familyReg)) {
	$familyReg[$person->home->id] = $registration;
      } else {
	  print "wierd status $registration->statusId for family " . $person->home->id . "\n";
      }
    }


    if (array_key_exists($familyId, $familyReg)) { // person paying money registered, update record
      // set registered, paymentacknowledged,  clear cancelrequest,cancelled,declined,
      $registration = $familyReg[$familyId];
      $status = $registration->statusId;
      $status |=  ItemRegistrationStatus::Registered ;
      $flagsToRemove = ItemRegistrationStatus::CancelRequest | ItemRegistrationStatus::Cancelled | ItemRegistrationStatus::Decline;
      $status &= ~$flagsToRemove;

      $amount = $registration->amountPaid + $amount;
      $query = "
	update itemRegistration set Status = $status, amountPaid = $amount 
	where itemId = $registration->itemId and MFS = $registration->MFS and mfsId = $registration->mfsId 
              and Status = $registration->statusId
	";
      // send an email about payment being received.
      
    } else { // preson paying money did not register, insert record
      $status = ItemRegistrationStatus::Registered;
      $amount = $amount;
      $query="insert into itemRegistration values ($eventId, 1, $familyId, 0, $amount, $status, $time)";
    }
    $result = VidDb::query($query); 
    echo "$query\n";
  }

  public static function PostPaymentFile() {
    if (($handle = fopen("/tmp/nov6.csv", "r")) != FALSE) {
      while (($data=fgetcsv($handle, 1000, ",")) != FALSE) {
	self::PostPayment(1, $data[1], '2012-11-06', $data[0]);
      }
    }
  }


}

//print Codes::VolunteerCodeHtml();  exit(); // print volunteer codes for shiksha portal
//EventManager::ReportParticipation(1); exit();
//EventManager::PostPaymentFile(); exit();
//Publications::LanguageAssessment(2012); exit();
//NewsletterHtml::Publish();
//Publications::FamilyListForHandbookDistribution(2012); exit();
//Publications::AttendanceSheet(2012); exit();
//Publications::RosterFromFile("/tmp/aa"); exit();
//Publications::Roster(2012); exit();

//Publications::RosterSpa(2012); exit();
//Publications::NotComingBack(2012); exit();
//Publications::NewLanguageStudents(2012); exit();
//Publications::FamilyVolunteerList(2012); exit();
//Publications::NewStudents(2012); exit();

//Publications::FullDumpFamilies();

//Publications::BadgeFile(2012); exit();
//Publications::CreateMailingLists(2012);exit();
//Publications::VolunteerListForHandbook(2012); exit();
Publications::FamilyMarshalling(); exit();
//Publications::TeacherListForHandbook(2012);exit();

//Publications::SchoolDirectory(2012); exit();
//Publications::TeacherDirectory(2012); exit (); // Directory of all Teachers
//Publications::VolunteerDirectory(2012); exit (); // Directory of all Volunteers
//Publications::ClassDirectory(2012); exit (); // Directory of all classes, with and without email


?>
