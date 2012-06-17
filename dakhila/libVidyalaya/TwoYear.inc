<?php

$libDir="../../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";
require_once "$libDir/Admission.inc";


class TwoYearEnrollment {
	public $language=null;
	public $languageLevel=null;
	public $languageSection=null;

	public $cultureLevel=null;
	public $cultureSection=null;
	
	public function updateFromEnrollment($enrollment) {
		$department = $enrollment->class->course->department;

		if ($department == Department::Culture) {
			$this->cultureLevel = $enrollment->class->course->level;
			$this->cultureSection = $enrollment->class->section;
				
		} else {
			$this->language =  $enrollment->class->course->department;
			$this->languageLevel  = $enrollment->class->course->level;
			$this->languageSection = $enrollment->class->section;
				
		}
	}

	public function updateFromStudent($student) {
		$this->language = $student->languagePreference;
		$this->cultureLevel = $student->GradeAt(Calendar::RegistrationSession);
		if ($this->cultureLevel > 9) $this->cultureLevel = 9;
	}
	
	public function csv($fields) {
//		$fields = Array();
		$fields[] = Department::NameFromId($this->language);
		$fields[] = $this->languageLevel;
		$fields[] = $this->languageSection;
		$fields[] = $this->cultureLevel;
		$fields[] = $this->cultureSection;
//		return implode (", ", $fields);
	}
}


class TwoYearLayout {
  public $previousYear = null;
  public $thisYear = null;
  public $status=null;
  public $assessment=null;

  const Leaving = "Leaving";
  const NewStudent = "New";
  const Orientation = "Orientation"; // Family attended Orientation
  const Continuing = "Continuing";
  const Change = "Change";
	
  const LeavingStudentsFile = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission/leaving.csv";
  const EnrolledStudentsFile = "/home/umesh/Dropbox/Vidyalaya-Roster/2012-13/admission/enrolled.csv";
	
  private static $objArray = Array ();
  private static $orientation = Array();
	


  private static function firstTimeCall() {
    if (!empty(self::$objArray)) return;
    self::currentYearFromDatabase();
    self::currentYearFromFile();
    self::prevYearFromDatabase();
    self::orientationList();
    self::loadAssessment();
    self::updateStatus();
  }

  public static function GetItemById($key) {
    self::firstTimeCall();
    return self::$objArray[$key];
  }
	
  public static function GetAll() {
    self::firstTimeCall();
    return self::$objArray;
  }
	
  private static function updateStatus() {
    foreach (self::$objArray as $studentid => $twoyear) {
      if ($twoyear->previousYear->language == null) {
	if ($twoyear->thisYear->language != null) {
	  $twoyear->status = self::NewStudent;
	  $studnet = Student::GetItemById($studentid);
	  $familyId = $studnet->family->id;
	  if (!empty(self::$orientation[$familyId])) {
	    $twoyear->status = self::Orientation;
	  }
	}
      } else {
	if ($twoyear->thisYear->language == null) {
	  $twoyear->status = self::Leaving;
	  continue;
	} 
	if ($twoyear->previousYear->language == Department::Kindergarten) {
	  $twoyear->status =self::Continuing;
	} else {
	  $twoyear->status = $twoyear->previousYear->language == $twoyear->thisYear->language ? 
	    self::Continuing : self::Change;
	}
      }
    }
		
  }

  private static function prevYearFromDatabase () {
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS,2011) as $enrollment) {
      if (empty(self::$objArray[$enrollment->student->id])) self::$objArray[$enrollment->student->id] = new TwoYearLayout();
      $twoyear = self::GetItemById($enrollment->student->id);
      $twoyear->previousYear->updateFromEnrollment($enrollment);
    }
  }
	
  private static function currentYearFromDatabase () {
    foreach (Enrollment::GetAllEnrollmentForFacilitySession(Facility::PHHS,2012) as $enrollment) {
      if (empty(self::$objArray[$enrollment->student->id])) self::$objArray[$enrollment->student->id] = new TwoYearLayout();
      $twoyear = self::GetItemById($enrollment->student->id);
      //print "setting this year value for $enrollment->student->id\n";
      $twoyear->thisYear->updateFromEnrollment($enrollment);
    }
  }
	

  private static function updateNewStudent($studentId) {
    $student = Student::GetItemById($studentId);
    if (empty($student)) print "student not found for id ==$studentId==";
    if (empty(self::$objArray[$studentId])) {
      //      print "I am here for student $studentId, found in current year from file\n";
      self::$objArray[$studentId] = new TwoYearLayout();
      $twoyear = self::GetItemById($studentId);
      $twoyear->thisYear->updateFromStudent($student);
    }
  }

  private static function currentYearFromFile() {
    //    $filename = "/home/umesh/Dropbox/Vidyalaya-Management/Administration/2011.csv";
    $filename = Admission::DataFile;
    if (is_readable($filename)) {
      $handle = fopen($filename, "r");
      $header = fgetcsv($handle, 0, ",");
      $header = fgetcsv($handle, 0, ",");
      $i=1;
      $totalTuition=0;
      $done=array();
      $fileTuition = array();
      while ((list($familyId,$Check , $base, $new , $adj , $CD , $PB , $Bag , $date , $total ,$foo, $ch1 , $ch2 , $ch3 )
	      = fgetcsv($handle, 0, ",")) !== FALSE) {
	if (!empty($familyId)) {
	  if (!empty($ch1)) self::updateNewStudent($ch1);
	  if (!empty($ch2)) self::updateNewStudent($ch2);
	  if (!empty($ch3)) self::updateNewStudent($ch3);
	}
      }
    }
  }
	
  private static function loadAssessment() {
    return;
    $filename = Admission::assesssmentFile;
    $count = array();
    if (($handle = fopen($filename, "r")) !== FALSE) {
      while ((list($studentId,$recommendation)=
	      fgetcsv($handle, 0, ",")) !== FALSE) {
	if (empty(self::$objArray[$studentId])) {
	  print "Studnet $studentId not found in twoyear array, look into it\n";
	}
	$twoyear = self::GetItemById($studentId);
	$twoyear->assessment = $recommendation;
      }
    }
  }

  private static function orientationList() {
    return;
    $filename = Admission::OrientationFile;
    $count = array();
    if (($handle = fopen($filename, "r")) !== FALSE) {
      while ((list($familyid,$Check)=
	      fgetcsv($handle, 0, ",")) !== FALSE) {
	self::$orientation[$familyid] = 1;
      }
    }

    // update the static array
  }
	
  public static function twoYearCsv () {
    $enrolledHandle = fopen(self::EnrolledStudentsFile, "w") or die ("cannot open file " . self::EnrolledStudentsFile);
    $leavingHandle = fopen(self::LeavingStudentsFile, "w") or die ("cannot open file " . self::LeavingStudentsFile);
    self::firstTimeCall();
    foreach (self::$objArray as $studentid => $twoYear) {
       $student = Student::GetItemById($studentid);
      $familyid = $student->family->id;
      $currFamilyStatus = EnumFamilyTracker::NameFromId(FamilyTracker::CurrentYearStatus($familyid));
      $fileHandle = null;
				
      $fields = Array();
      $fields[] = $studentid;
      $fields[] = $familyid;
      //			$fields[] = $twoYear->previousYear->csv();
				
      if ($twoYear->status != self::Leaving) {
	$fileHandle = $enrolledHandle;

	//				$fields[] = $twoYear->thisYear->csv();
	$twoYear->thisYear->csv(&$fields);
	$fields[] = $twoYear->status;
	$fields[] = $twoYear->assessment;
      } else {

	$fileHandle = $leavingHandle;
	$twoYear->previousYear->csv(&$fields);
	$fields[] = $currFamilyStatus;
      }
      $fields[] = $student->fullName();
				
      if ($twoYear->status == self::Continuing) $twoYear->previousYear->csv(&$fields);
				
      fputcsv($fileHandle, $fields);
    }

    fclose($enrolledHandle); fclose($leavingHandle);
  }

  public static function assignClass() {
    AvailableClass::CreateClassCourseCatalog();
    self::firstTimeCall();
    $count = 0;
    foreach (self::$objArray as $studentid => $twoYear) {
      $student = Student::GetItemById($studentid);
				
      if ($twoYear->thisYear->languageLevel != null) continue;
      if ($twoYear->status == self::Leaving) continue;
				
      $count++;
				
      if($student->GradeAt(Calendar::RegistrationSession) == "KG") {
	$class = AvailableClass::findAvailableClass(Calendar::RegistrationYear(), Department::Kindergarten, 0, null);
	if ($class ==null) {
	  print "Error:KG not found for year " .Calendar::RegistrationYear() . "\n";
	} else {
	  print "insert into Enrollment set student = $student->id, availableClass = $class->id;\n";
	}
	continue;
      }
				
      // all others require culture and language. Let us do culture first
      $level = $twoYear->thisYear->cultureLevel;
      $class = AvailableClass::findAvailableClass(Calendar::RegistrationYear(), Department::Culture, $level, null);
      if ($class ==null) {
	print "Error:Culture level $level not found for year " .Calendar::RegistrationYear() . "\n";
      } else {
	print "insert into Enrollment set student = $student->id, availableClass = $class->id;\n";
      }
			
      // for language, we have new, change and continuing
			
      $department = $twoYear->thisYear->language;
      if ($twoYear->status == self::Continuing) {
	//assign same level as last year
	$level = $twoYear->previousYear->languageLevel;;
	if ($level == 0) $level=1;
      } else {
	$level = 1; 
      }
      $class = AvailableClass::findAvailableClass(Calendar::RegistrationYear(), $department, $level, null);
      if ($class ==null) {
	print "Error:Department $department,  level $level not found for year " .Calendar::RegistrationYear() . "\n";
      } else {
	print "insert into Enrollment set student = $student->id, availableClass = $class->id;\n";
      }
			
				
				
      print "$count: $studentid needs to be enrolled";
      print "\n";
    }
  }

  public static  function checkFeePaid() {
    self::firstTimeCall();
		
    $feeRequired = Array();
    $newRegFee = Array();
		
    foreach (self::$objArray as $studentid => $twoYear) {
      $student = Student::GetItemById($studentid);
      $familyid = $student->family->id;
			
      if ($twoYear->status != self::Leaving) {
	if(empty($feeRequired[$familyid])) {
	  $feeRequired[$familyid] = 450;
	  $newRegFee[$familyid] = 0;
	} else {
	  $feeRequired[$familyid] = 550;
	}
				
	if ($twoYear->status == self::NewStudent || $twoYear->status == self::Orientation ) $newRegFee[$familyid] += 50; 
      }
    }
		
		
    // check if fee is paid fully
    foreach ($feeRequired as $familyid=>$require) {
      $require += $newRegFee[$familyid];
      $tracker = FamilyTracker::GetItemById($familyid);
      $family = Family::GetItemById($familyid);
      if ($require != $tracker->tuition) {
	print "Family: $familyid, Require: $require, Paid: $tracker->tuition, " . $family->parentsName() . "\n";
      }
    }
		
  }
	
  private function __construct() { 
    $this->previousYear = new TwoYearEnrollment();
    $this->thisYear = new TwoYearEnrollment();
    $this->status = "unknown";
  }
}

?>
