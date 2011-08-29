<?php
require_once "util.inc";

class Gender {
      const Girl = 1
      , Boy = 2
      , Unknown = 3
      ;

public static function NameFromId($number) {
	switch ($number) {
		case 1: return "Girl";
		case 2: return "Boy";
		default: return "Unknown";
	}
	}

}

class Department {
	public $id = null;

	const Hindi = 1
	, Gujarati = 2
	, Telugu = 3
	, Culture = 4
	, Kindergarten = 5
	;

	public function Name() {
		return Department::NameFromId($this->id);
	}

	public static function IsLanguage($id) {
	  switch ($id) {
	  case 1: case 2: case 3: return true;
	  default: return false;
	  }
	}

	public static function NameFromId($number) {
		switch ($number) {
			case 1: return "Hindi";
			case 2: return "Gujarati";
			case 3: return "Telugu";
			case 4: return "Culture";
			case 5: return "Kindergarten";
			default: return "Unknown";
		}
	}

	public static function NameFromChar($char) {
		switch ($char) {
			case "H": return Department::NameFromId(1);
			case "G": return Department::NameFromId(2);
			case "T": return Department::NameFromId(3);
			case "C": return Department::NameFromId(4);
			case "C": return Department::NameFromId(5);
			default: return "unknown";
		}
	}

	public static function IdFromChar($char) {
		switch ($char) {
			case "H": return 1;
			case "G": return 2;
			case "T": return 3;
			case "C": return 4;
			case "K": return 5;
			default: return null;
		}
	}


	public static function NameFromPraveenSymbol($symbol) {
		if (beginsWith($symbol, "KG")) {
			$langChar = $symbol[count($symbol)-1];
		} else {
			$langChar = $symbol[0];
		}
		return Department::NameFromChar($langChar);
	}
	
	public static function IdFromPraveenSymbol($symbol) {
		if (beginsWith($symbol, "KG")) {
			$langChar = $symbol[3];
		} else {
			$langChar = $symbol[0];
		}
		return Department::IdFromChar($langChar);
	}
}

// ************** 
class Student {
  public $id=null;
  public $firstName=null;
  public $lastName=null;
  public $gender=null;
  public $dateOfBirth=null;
  public $email=null;
  public $cellPhone=null;
  public $IsEnrolled = false;
  public $family = null;
  public $registration=null;
  public $studentStatus=null;
  public $languagePreference=null;
  public $firstGradeYear = null;

  public static $objArray = Array ();
  
  public static function GetStudent($row) {
  	$key = $row["Students2003.ID"];
    if (!array_key_exists($key, self::$objArray)) {
      self::$objArray[$key] = new Student($row);
    }
    return self::$objArray[$key];
  }

  private static function firstTimeCall() {
  	if (!empty(self::$objArray)) return;
  	GetAllData();
  }

  public static function GetItemById($key) {
  	self::firstTimeCall();
  	return self::$objArray[$key];
  }
  
  public static function AllStudents() {
  	self::firstTimeCall();
  	return self::$objArray;
  }
  

  public static function RegisteredStudents() {
  	self::firstTimeCall();
  	$students = Array();
  	foreach (Student::$objArray as $student) {
  		if ($student->IsEnrolled) $students[] = $student;
  	}
  	return $students;
  }

  public function Age(){
  	return Calendar::Age($this->dateOfBirth);
  }

  public function AgeAt($rule){
  	return Calendar::AgeAt($this->dateOfBirth, $rule);
  }

  public function Grade(){
  	return Calendar::GradeAt($this->firstGradeYear, Calendar::CurrentSession);
  }

  public function GradeAt($rule){
  	return Calendar::GradeAt($this->firstGradeYear, $rule);
  }

  public function GenderName() {
  	 return Gender::NameFromId($this->gender);
	 }

  public function LanguageInterest() {
  	if (!empty($this->languagePreference)) {
  		return Department::NameFromId($this->languagePreference);
  	}
  	return "Unknown";
  }


  public function fullName() {
  	return $this->firstName . " " . $this->lastName;
  }
  
  public function CellEmail() {
  	$fields = array();
  	if (!empty($this->cellPhone)) $fields[] = "Cell:" . $this->cellPhone;
  	if (!empty($this->email)) $fields[] = "email:" . $this->email;
  	return implode ($fields, ",");
  }

  public function parentsName() {
  	return $this->family->parentsName();
  }


  public function mailingListArray () {
    $mailArray = array();
    if (count($this->family->mother->mailingListArray()) >0) {
      $mailArray = array_merge($mailArray, $this->family->mother->mailingListArray());
    }
    if (count($this->family->father->mailingListArray()) > 0) {
      $mailArray = array_merge($mailArray, $this->family->father->mailingListArray());
    }
    
    $email = trim($this->email);
    if (!empty($email)) {$mailArray = array_merge($mailArray, explode(";", $email));}

    return $mailArray;
    return array_merge($this->family->mother->mailingListArray(), 
		       $this->family->father->mailingListArray(),
		       explode(";", $this->email));
  }



  public function __construct($row) {
    $this->id = $row["Students2003.ID"];
    $this->firstName = $row["Students2003.FIRST_NAME"];
    $this->lastName = $row["Students2003.LAST_NAME"];
    $this->gender = $row["Students2003.GENDER"];
    $this->email = $row["Students2003.EMAIL"];
    $this->cellPhone = $row["Students2003.cellphone"];


    $dob = $row["Students2003.DOB"];
    if (strtotime($dob) < strtotime("1970-01-01")) {
    	//    if ($dob < "1970-01-01") {
     $this->dateOfBirth = null;
    } else {
    	$this->dateOfBirth = $dob;
    }

    $this->family = Family::GetItemById($row["Students2003.PARENT_ID"]);
    $this->registration = new Registration($row);
    $this->studentStatus = StudentStatus::GetStudentStatusId($row["Students2003.STATUS"]);
    $this->IsEnrolled = $this->studentStatus->id == StudentStatus::Active;
    //  	if ($row["Students2003.STATUS"] == 1) {$this->IsEnrolled = true;}
    //	$grade = $row["Students2003.NEXT_YEAR_GRADE"];
    $this->firstGradeYear=$row["Students2003.YearFirstGrade"];
    $this->languagePreference =  $row["Students2003.LanguageInterest"];
  }
}

// ************** 
class Registration {
  public $id=null;
  public $language= null;
  public $culture=null;

  public function __construct($row) {
    $this->language = Course::GetCourseId($row["Students2003.SS_LANG_NEXT"], "l");
    $this->culture = Course::GetCourseId($row["Students2003.SS_CULT_NEXT"], "c");
  }

  public function CourseList() {
    return $this->language->symbol . " " . $this->culture->description;
  }
}


// ************** 
class Course {
  public $id=null;
  public $lc=null;
  public $symbol=null;
  public $description=null;
  public $room=null;
  public $teachers=null;
  
  public $department=null;
  public $level=null;

  public static $objArray = Array ();
  public static function GetCourse($row, $lc) {
    if ($lc == "l") {$key = $lc . $row["LanguageGrades.ID"];} 
    else if ($lc == "c") {$key = $lc . $row["CultureGrades.ID"]; }

    if (!array_key_exists($key, self::$objArray)) {
      $instance = new Course();
      self::$objArray[$key] = $instance;
      $instance->setVariables($row, $lc);
    }
    return self::$objArray[$key];
 }
 
   public static function GetCourseId($id, $lc) {
    if ($lc == "l") {$key = $lc . $id; }
    else if ($lc == "c") {$key = $lc . $id; }

    if (!array_key_exists($key, self::$objArray)) {
		return null;
    }
    return self::$objArray[$key];
 }
 

  private function setVariables($row, $lc) {
  	$this->lc = $lc;
    if ($lc == "l") {
      $this->id = $lc . $row["LanguageGrades.ID"];
      $this->SetVariablesFromRow($row, "LanguageGrades.Value", "LanguageGrades.Description",
				 "LanguageGrades.Room", "LanguageGrades.Teachers", $lc);
    } else if ($lc == "c") {
      $this->id = $lc . $row["CultureGrades.ID"];
      $this->SetVariablesFromRow($row, "CultureGrades.Value", "CultureGrades.Description",
				 "CultureGrades.Room", "CultureGrades.Teachers", $lc);
    }

  }
  
  private static function GetLevelFromPraveenSymbol($symbol, $description, $lc) {
  	if ($lc == "c") $symbol = $description;
  	  	if (empty($symbol)) return 0;
  	if (beginswith($symbol, "KG")) return 0;
  	if (strlen($symbol) < 2) {
  		print "Debug: having issues with symbol: $symbol\n"; 
  	}
  	return $symbol[1];
  }

  private function GetDepartmnetFromPraveenSymbol() {
  	$chartolook = $this->lc == "c"? $this->description[0] : $this->symbol[0];
  	return Department::IdFromChar($chartolook);
  }

  public function GetSectionFromPraveenSymbol() {
  	  $symboltocheck = $this->lc == "c"? $this->description : $this->symbol;
	  $courseSection = strlen($symboltocheck) > 2 ? $symboltocheck[2] : "";
	  if ($courseSection == "-") return null;
	  return $courseSection;
  }
  
  private function SetVariablesFromRow($row, $symbol, $description, $room, $teachers, $lc) {
      $this->symbol = $row[$symbol];
      $this->description = $row[$description];
      $this->room = $row[$room];
      $this->teachers = $row[$teachers];
      
//      print "what is going on $row[$symbol], $row[$description]  \n";
      $this->level =  self::GetLevelFromPraveenSymbol($this->symbol, $this->description, $this->lc);
      $this->department = $this->GetDepartmnetFromPraveenSymbol();
    //print "Adding course: dept = $this->department, level = $this->level: $this->symbol, $this->description: \n";
  }

}

// ************** 
class CourseCatalog {
	public $id=null;
	public $department = null;
	public $level= null;
	public $short=null;
	public $full = null;
	
	public static function CompareShort($a, $b) {
	  if ($a->short == $b->short) return 0;
	  return ($a->short < $b->short) ? -1 : 1;
	}

	public static function GetCourseCatalogFromCourse($course) {
		print "Debug: Going to look for $course->department, $course->level\n";
	 	self::firstTimeCall();
		foreach (self::$objArray as $coursecatalog ) {
			if (($coursecatalog->department == $course->department) && 
			    ($coursecatalog->level == $course->level))
			return $coursecatalog;
		}
		return null;
	}

	private static function ReadRow($row) {
		$key = $row["CourseCatalog.id"];
		if (!array_key_exists($key, self::$objArray)) 
			return  new self($row);
	}

	private static function firstTimeCall() {
		if (!empty(self::$objArray)) return;
		$query = "select * from CourseCatalog";
		$result = VidDb::query($query);
		$temp = array();
		while ($row = mysql_fetch_alias_array($result)) {
			$temp[] = new self($row);
		}
	        usort ($temp, "CourseCatalog::CompareShort");
		foreach ($temp as $value) {
		  if (array_key_exists($value->id, self::$objArray)) print "duplicate key found coursecatalog\n";
		  self::$objArray[$value->id] = $value;
		}
		
	}
		
	public static function GetItemById($key) {
	 	self::firstTimeCall();
		//		print "<p>debug: trying to find $key in coursecatalog\n";
		
		if (isset(self::$objArray[$key])) {
		  $item= self::$objArray[$key];
		return $item;
		}
		print "<p>debug: course catalog for key $key not found\n";
		return null;
	 }	

	 public static function GetAll() {
		self::firstTimeCall();
		return self::$objArray;
	}
	 
	private static $objArray = Array ();

	
	public function __construct($row) {
		$this->id = $row["CourseCatalog.id"];
		$this->department = $row["CourseCatalog.department"];
		$this->level = $row["CourseCatalog.level"];
		$this->short = $row["CourseCatalog.short"];
		$this->full = $row["CourseCatalog.full"];

	}
	
	public static function findAvailableCourse($department, $level) {
		foreach (self::GetAll() as $item) {
			if ($item->department == $department && $item->level == $level) return $item;
		}
		return null;
	}

}

// ************** 
class AvailableClass {
	public $id=null;
	public $year = null;
	public $session = null;
	public $course= null;
	public $section=null;
	public $room = null;
	public $startTime = null;
	public $endTime = null;
	
	private static function firstTimeCall() {
		if (!empty(self::$objArray)) return;
		$query = "select * from AvailableClass";
		$result = VidDb::query($query);
		while ($row = mysql_fetch_alias_array($result)) {
			self::ReadRow($row);
		}
	}

	public function url() {
	  $roster =  "<a href=\"/dakhila/php/dataViewer2.php?command=ClassRoster&classId=$this->id\">" . 
	    $this->short() . "</a>";

	  $room =  " (Room: " . $this->room->roomNumber . ")";
	  return $roster . $room;

	}

	public function IsChangeAllowed() {
	  if ($this->session == "2011-12") return true;
	  return false;
	}

	public function short() {
	       return empty($this->section) ? $this->course->short : $this->course->short . $this->section;
	}	
	public function shortWithoutSection() {
	       return empty($this->section) ? $this->course->short : $this->course->short;
	}		
	

	public static function GetItemById($key) {
	 	self::firstTimeCall();
	 	return self::$objArray[$key];
	 }	
	 
	public static function GetAll() {
		self::firstTimeCall();
		return self::$objArray;
	}
	
	public static function GetAllYear($year) {
		if ($year >= 2010) $year -= 2010;
		self::firstTimeCall();
		$availableYear = array();
		foreach (self::GetAll() as $item) {
			if($item->year == $year) $availableYear[$item->id] = $item;
		}
		return $availableYear;
	}
	
	 
	private static $objArray = Array ();

	private static function ReadRow($row) {
		$key = $row["AvailableClass.id"];
		if (!array_key_exists($key, self::$objArray)) 
			self::$objArray[$key] = new self($row);
		return self::$objArray[$key];
	}
	
	public function __construct($row) {
		$this->id = $row["AvailableClass.id"];
		$this->year= $row["AvailableClass.year"];
		$this->session = sprintf("%s-%s",  $this->year+2010 , $this->year+11);
		$this->course = CourseCatalog::GetItemById($row["AvailableClass.course"]);
		$this->section = $row["AvailableClass.section"];
		$this->room = Rooms::GetItemById($row["AvailableClass.room"]);
		$this->startTime = $row["AvailableClass.startTime"];
		$this->endTime = $row["AvailableClass.endTime"];

	}

	public static function findAvailableClassFromCourse($course) {
		//print "Looking for $course->description, ";
		$courseCatalog = CourseCatalog::GetCourseCatalogFromCourse($course);
		if (empty($courseCatalog)) die ("catalog not found found for $course->description");
		$courseSection = $course->GetSectionFromPraveenSymbol();

		self::firstTimeCall();
		foreach (self::$objArray as $availableClass) {
			if (($availableClass->course->id == $courseCatalog->id) && ($availableClass->section == $courseSection)){
			//	print "returned id should $availableClass->id\n";
				return $availableClass;
			}

		}

		$startTime = "10:00"; $endTime="11:00";
		if ($course->lc == "c") {$startTime = "11:00"; $endTime="11:45";}
		if ($course->symbol[0] == "K") {$startTime = "10:00"; $endTime="11:30";}

		$room = Rooms::findEastlakeRoomFromCourse($course);

		$sql = "insert into AvailableClass set ";
		$sql .= "year=0, course=$courseCatalog->id, section = \"$courseSection\", room = $room->id, startTime = \"$startTime\", endTime = \"$endTime\" ";
		//print "Debug: executing $sql;\n";
		$result = VidDb::query($sql);
		//print "inserted: course = $course->description\n";
		self::$objArray = Array();
		return self::findAvailableClassFromCourse($course);
	}
	
	public static function findAvailableClass($year, $department, $level, $section) {
		if ($year >= 2010) $year-= 2010;
//		print "Looking for course, dept: $department, level: $level      ";
		$course = CourseCatalog::findAvailableCourse($department, $level);
		
		if ($course == null) {
//			print "not found\n";
			return null;
		} else {
//			print "found $course->id\n";
		}

//		print "looking for course: $course->id, section: $section, year: $year\n";
		foreach (self::GetAllYear($year) as $item) {
			//print "comparing id $item->id, course=$item->course, course id = $course->id \n";
			// if section is null, find the first available
			if ($section == null) {
				if ($item->course->id == $course->id) return $item;
			} else {
				if ($item->course->id == $course->id && $item->section == $section) return $item;
			}
		}
		print "not found\n";
		//nothing found
		return null;
	}
	
}

// ************** 
class Facility{
	public $id=null;
	public $name=null;
	public $address=null;
	public $zip=null;
	
	const Eastlake = 1;
	const PHHS = 2;

	private static function firstTimeCall() {
		if (!empty(self::$objArray)) return;
		$query = "select * from Facilities";
		$result = VidDb::query($query);
		while ($row = mysql_fetch_alias_array($result)) {
			self::ReadRow($row);
		}
	}

	public static function GetItemById($key) {
	 	self::firstTimeCall();
	 	return self::$objArray[$key];
	 }	
	
	public static function GetAll() {
		self::firstTimeCall();
		return self::$objArray;
	}
	
	 private  static $objArray = Array ();

	private static function ReadRow($row) {
		$key = $row["Facilities.id"];
		if (!array_key_exists($key, self::$objArray)) 
			self::$objArray[$key] = new self($row);
		return self::$objArray[$key];
	}

	public function __construct($row) {
		$this->id = $row["Facilities.id"];
		$this->name = $row["Facilities.name"];
		$this->address = $row["Facilities.address"];
		$this->zip = $row["Facilities.zip"];
	}
}

// ************** 
class Rooms{
	public $id=null;
	public $facility = null;
	public $roomNumber = null;
	public $capacity =null;
		
	public static function CompareRoomnumber($a, $b) {
	  if ($a->roomNumber == $b->roomNumber) return 0;
	  return ($a->roomNumber < $b->roomNumber) ? -1 : 1;
	}

	private static function firstTimeCall() {
		if (!empty(self::$objArray)) return;
		$query = "select * from Rooms";
		$result = VidDb::query($query);


		$temp = array();
		while ($row = mysql_fetch_alias_array($result)) {
			$temp[] = new self($row);
		}
	        usort ($temp, "Rooms::CompareRoomnumber");
		foreach ($temp as $value) {
		  if (array_key_exists($value->id, self::$objArray)) print "duplicate key found room\n";
		  self::$objArray[$value->id] = $value;
		}

		return;

		while ($row = mysql_fetch_alias_array($result)) {
			self::ReadRow($row);
		}
	        usort (self::$objArray, "Rooms::CompareRoomnumber");
	}
	
	public static function GetItemById($key) {
	 	self::firstTimeCall();
		if (isset(self::$objArray[$key])) return self::$objArray[$key];
		print "debug: Room with id $key not found\n";
	 	return null;
	 }	
	
	public static function findEastlakeRoomFromCourse($course) {
		self::firstTimeCall();
		foreach (self::$objArray as $room) {
			//print "Trying, room = $room->roomNumber, facility = $room->facility\n";
			if ($room->roomNumber== $course->room && $room->facility == 1) return $room;
		}
		die( "Error: room not found for $course->symbol, $course->description, $course->room\n");
	}

	public static $objArray = Array ();

	private static function ReadRow($row) {
		$key = $row["Rooms.id"];
		if (!array_key_exists($key, self::$objArray)) 
			self::$objArray[$key] = new self($row);
		return self::$objArray[$key];
	}

	public function __construct($row) {
		$this->id = $row["Rooms.id"];
		$this->facility= Facility::GetItemById($row["Rooms.facility"]);
		$this->roomNumber = $row["Rooms.room"];
		$this->capacity = $row["Rooms.capacity"];
	}
}

// Enrolled students 
// ************** 
class Enrollment{
	public $student=null;
	public $class = null;
	public $academicGrade = null;
	public $disciplineGrade =null;

	public static function CompareStudentName($a, $b) {
	  if ($a->student->id == $b->student->id) return 0;
	  if ($a->student->lastName < $b->student->lastName) return -1;
	  if ($a->student->lastName > $b->student->lastName) return 1;
	  if ($a->student->firstName < $b->student->firstName) return -1;
	  if ($a->student->firstName > $b->student->firstName) return 1;
	  return 0;
	}

	// return all families in an availableclass
	public static function GetFamilies($class) {
	  $families = array();
	  $query = "select * from Enrollment where availableClass=$class";

	  $result = VidDb::query($query);
	  while ($row = mysql_fetch_alias_array($result)) {
	    $item = new self($row);
#	    $student = Student::GetItemById($item->student);
	    $families[] = $item->student->family;
	  }
	  return $families;
	}
	
	private static function ArrayFromQuery($query) {
		$enrollmentArray = Array();
		
		$result = VidDb::query($query);
		while ($row = mysql_fetch_alias_array($result)) {
			$enrollmentArray[] = new self($row);
		}
		
	        usort ($enrollmentArray, "Enrollment::CompareStudentName");
		return $enrollmentArray;
	}
	
	public static function GetEnrollmentForClass ($class) {
		$query = "select * 
		from Enrollment
		where availableClass = $class
		";
		return self::ArrayFromQuery($query);
	}
	
	public static function GetEnrollmentHistory($studentId) {
		$query = "
		select * from Enrollment
		left join AvailableClass on Enrollment.availableClass = AvailableClass.id
		left join CourseCatalog on AvailableClass.course = CourseCatalog.id
		where Enrollment.student = $studentId
		order by AvailableClass.year, CourseCatalog.department
		";
		return self::ArrayFromQuery($query);
	}

	public static function GetLanguageHistory($studentId) {
	  $enrollmentArray = Array();
	  foreach (self::GetEnrollmentHistory($studentId) as $item) {
	    if (Department::IsLanguage($item->class->course->department)) $enrollmentArray[] = $item;
	  }
	  return $enrollmentArray;

	  // this is the old code, we need to remove it after testing above in asmita evaluation report
		$query = "
		select * from Enrollment
		left join AvailableClass on Enrollment.availableClass = AvailableClass.id
		left join CourseCatalog on AvailableClass.course = CourseCatalog.id
		where Enrollment.student = $studentId
			and CourseCatalog.department <> " . Department::Culture . "
		order by AvailableClass.year
		";
		return self::ArrayFromQuery($query);
	}

	public static function GetAllEnrollmentForFacilitySession($facility, $year) {
		if ($year >= 2010) $year -= 2010;
		
		$query = "select * 
		from Enrollment
			inner join AvailableClass on Enrollment.availableClass = AvailableClass.id
			inner join Rooms on AvailableClass.room = Rooms.id
		where AvailableClass.year = $year and Rooms.facility = $facility
		";
		return self::ArrayFromQuery($query);	
	}
	
	public function __construct($row) {
		$this->student = Student::GetItemById($row["Enrollment.student"]);
		$this->class= AvailableClass::GetItemById($row["Enrollment.availableClass"]);
		$this->academicGrade = $row["Enrollment.gradeAcademic"];
		$this->disciplineGrade = $row["Enrollment.gradeDiscipline"];
	}
}

// ************** 
class StudentStatus {
  public $id=null;
  public $name = null;
  
  const Active = 1;
  const Waiting = 2;
  const Inactive = 3;

  public static $objArray = Array ();
  
  public static function GetStudentStatus($row) {
    $key = $row["Status.ID"];
    if (!array_key_exists($key, self::$objArray)) {
      $instance = new StudentStatus();
      self::$objArray[$key] = $instance;
      $instance->setVariables($row);
    }
    return self::$objArray[$key];
 }
 
   public static function GetStudentStatusId($key) {
    if (!array_key_exists($key, self::$objArray)) {
    	return null;
    }
    return self::$objArray[$key];
 }
 
 
  public function setVariables($row) {
    $this->id = $row["Status.ID"];
    $this->name = $row["Status.Value"];
  }
 
}

// ************** 
class FamilyCategory {
  public $id=null;
  public $name = null;

  const Waiting = 5;
  
  public static $objArray = Array ();
  public static function GetFamilyCategory($row) {
    $key = $row["Parent_Type.ID"];
    if (!array_key_exists($key, self::$objArray)) {
      $instance = new FamilyCategory();
      self::$objArray[$key] = $instance;
      $instance->setVariables($row);
    }
    return self::$objArray[$key];
 }
 
   public static function GetFamilyCategoryId($key) {
    if (!array_key_exists($key, self::$objArray)) {
    	return null;
    }
    return self::$objArray[$key];
 }
 
 
  public function setVariables($row) {
    $this->id = $row["Parent_Type.ID"];
    $this->name = $row["Parent_Type.Value"];
  }
 
}

// ************** 
class Family {
	public $id=null;
	public $mother = null;
	public $father = null;
	public $address=null;
	public $phone=null;
	public $category = null;
	public $priority_date = null;

	public static $objArray = Array ();

	public static function GetFamily($row) {
		$key = $row["Parents2003.ID"];
		if (!array_key_exists($key, self::$objArray)) {
			$instance = new Family();
			self::$objArray[$key] = $instance;
			$instance->setVariables($row);
		}
		return self::$objArray[$key];
	}


	private static function firstTimeCall() {
		if (!empty(self::$objArray)) return;
		GetAllData();
	}

	public static function GetItemById($key) {
		self::firstTimeCall();
		return self::$objArray[$key];
	}

	public static function GetAllFamilies() {
		self::firstTimeCall();
		return self::$objArray;
	}

	public function parentsName() {
		if ($this->mother->lastName == $this->father->lastName) {
			return $this->father->firstName . " / " .$this->mother->firstName . " "  . $this->father->lastName;
		}else {
			return $this->father->firstName . " "  . $this->father->lastName . " / " .$this->mother->firstName . " "  . $this->mother->lastName;
		}
	}
	
	public function GetRegisteredFamilies($facility, $year){
		$list = array();
		$done = array();
		foreach(Enrollment::GetAllEnrollmentForFacilitySession($facility, $year) as $enrollment) {
			$familyId = $enrollment->student->family->id;
			if (empty($done[$familyId])) {
				$list[] = $enrollment->student->family;	
			}
			$done[$familyId] = 1;
			
		}
		return $list;
	}
	
	public function GetWaitlistFamilies(){
		$list = array();
		foreach (self::GetAllFamilies() as $family) {
		if($family->category->id == FamilyCategory::Waiting) $list[] = $family;
		}
		return $list;
	}
	

	public function Children() {
		$children = array();
		foreach (Student::$objArray as $student) {
			if ($student->family->id == $this->id) $children[] = $student;
		}
		return $children;
	}



  public function setVariables($row) {
  	
    $this->id = $row["Parents2003.ID"];
//    print "*** I was here for $this->id\n";
    $this->mother = Parents::GetParent($row, "m");
    $this->father = Parents::GetParent($row, "f");
    $this->phone = $row["Parents2003.MH_PHONE"];
    $this->priority_date = $row["Parents2003.priority_date"];

    $this->address = new Address();
    $this->address->SetVariablesFromRow($row);
    
    $this->category = FamilyCategory::GetFamilyCategoryId($row["Parents2003.TYPE_CODE"]);
     
  }
}

// ************** 
class Address {
  public $addr1=null;
  public $addr2=null;
  public $city=null;
  public $state=null;
  public $zipcode=null;
  
  public function OneLineAddress() {
  	return $this->addr1 . ", " . $this->city . ", " . $this->state . " " . $this->zipcode;
  }

  public function SetVariablesFromRow($row) {
    $this->addr1 = $row["Parents2003.M_ADDRESS"];
    $this->city = $row["Parents2003.M_CITY"];
    $this->state = $row["Parents2003.M_STATE"];
    $this->zipcode = $row["Parents2003.M_ZIP_CODE"];
  }

}


// ************** 
class Parents {
  public $id=null;
  public $firstName = null;
  public $lastName = null;
  public $email = null;
  public $workPhone=null;
  public $cellPhone=null;
  public $IsContactable=false;

  public function mailingListArray () {
    if (!$this->IsContactable) { return null;}
    $email = trim($this->email);
    if (!empty($email)) {return explode(";", $email);}
  }


  public function fullName() {
    return $this->firstName . " " . $this->lastName;
  }
  
  public  function  WorkCellEmail() {
  	$fields = array();
  	if (!empty($this->workPhone)) $fields[] = "W:" . $this->workPhone;
  	if (!empty($this->cellPhone)) $fields[] = "C:" . $this->cellPhone;
  	if (!empty($this->email)) $fields[] = "email:" . $this->email;
  	return implode ($fields, ",");
  	  }

  private static $objArray = Array ();
  public static function GetParent($row, $mf) {
    $key= $mf . $row["Parents2003.ID"];
    if (!array_key_exists($key, self::$objArray)) {
      $instance = new Parents();
      self::$objArray[$key] = $instance;
      $instance->setVariables($row, $mf);
    }
    return self::$objArray[$key];
  }

  private function setVariables($row, $mf) {
    $this->id = $mf . $row["Parents2003.ID"];
    if ($mf == "m") {
      $this->SetVariablesFromRow($row, "Parents2003.MFIRST_NAME","Parents2003.MLAST_NAME",
				 "Parents2003.M_EMAIL", "Parents2003.MW_PHONE", "Parents2003.MC_PHONE");
      if ($row["Email_Preferences.ID"] & 1) {
	$this->IsContactable = true;
      }
    } else if ($mf == "f") {

      $this->SetVariablesFromRow($row, "Parents2003.FFIRST_NAME","Parents2003.FLAST_NAME",
				 "Parents2003.F_EMAIL", "Parents2003.FW_PHONE", "Parents2003.FC_PHONE");

      if ($row["Email_Preferences.ID"] & 2) {
	$this->IsContactable = true;
      }

    }
  }

  private function SetVariablesFromRow($row, $first, $last, $email, $work, $cell) {
      $this->firstName = $row[$first];
      $this->lastName = $row[$last];
      $this->email = $row[$email];
      $this->workPhone = $row[$work];
      $this->cellPhone = $row[$cell];
  }
}

// ******** Family Tracker
class FamilyTracker {
	const facility = 1;
	const prevYear = 0;
	const currYear = 1;

	public $year=null;
	public $family = null;
	public $previousYear = null;
	public $currentYear =null;
	public $tuition =null;

	public static $objArray = Array ();
	public static $students = Array ();

	private static function firstTimeCall() {
		if (!empty(self::$objArray)) return;
		$query = "select * from FamilyTracker";
		$result  = VidDb::query($query);
		while ($row = mysql_fetch_alias_array($result)) {
			$key = $row["FamilyTracker.family"];
			if (!array_key_exists($key, self::$objArray)) self::$objArray[$key] = new self($row);
		}
	}

	public static function GetItemById($key) {
		self::firstTimeCall();
		return self::$objArray[$key];
	}

	public static function GetAll() {
		self::firstTimeCall();
		return self::$objArray;
	}
	
	public static function CurrentYearStatus ($family){
		self::firstTimeCall();
		return self::$objArray[$family]->currentYear;
	}

	public function __construct($row) {
		$this->year = $row["FamilyTracker.year"];
		$this->family= $row["FamilyTracker.family"];
		$this->previousYear= $row["FamilyTracker.previousYear"];
		$this->currentYear = $row["FamilyTracker.currentYear"];
		$this->tuition = $row["FamilyTracker.tuition"];
	}


	private static function createInsertString($familyID, $prevStatus, $currStatus) {
		return " (" . self::currYear . ", $familyID, $prevStatus, $currStatus, 0)";
	}

	public static function UpdateFamilyTracker() {
		self::firstTimeCall();

		$insert = array();
		foreach ( Family::GetRegisteredFamilies(self::facility, self::prevYear) as $family) {
			if (empty(self::$objArray[$family->id])) {
				$insert[] = self::createInsertString($family->id
				, EnumFamilyTracker::enum('registered')
				,EnumFamilyTracker::enum('pendingRegistration'));
				self::$objArray[$family->id] = 1;
			}
		}

		foreach ( Family::GetWaitlistFamilies() as $family) {
			if (empty(self::$objArray[$family->id])) {
				$insert[] = self::createInsertString($family->id
				, EnumFamilyTracker::enum('waitlist')
				,EnumFamilyTracker::enum('pendingRegistration'));
				self::$objArray[$family->id] = 1;
			}
		}

		if (count($insert) > 0) {
			print "count of insert is " . count($insert) . "\n";
			$result = VidDb::query("insert into FamilyTracker values " . implode (", ", $insert));
		} else {
		}
		print "nothing to add\n";
	}

	private static function  verifyParent($family, $child) {
		//print "I was here\n";
		$student = Student::GetItemById($child);
		self::$students[$child] = $student;
		if ($student->family->id != $family) {
			print "parent error: family $family, child $child, family should be " . $student->family->id . "\n";
		} else {
			//print "parent child ok: family $family, child $child\n";
		}
	}

	public static function loadPayments () {
		//FamilyTracker::firstTimeCall();
		$filename = "/home/umesh/Dropbox/Vidyalaya-Management/Administration/2011.csv";
		if (($handle = fopen($filename, "r")) !== FALSE) {
			$header = fgetcsv($handle, 0, ",");
			$header = fgetcsv($handle, 0, ",");
			$i=1;
			$totalTuition=0;
			$done=array();
			$fileTuition = array();
			while ((list($family,$Check , $base, $new , $DVD , $CD , $PB , $Bag , $Ann , $Total ,$foo, $ch1 , $ch2 , $ch3 )
			= fgetcsv($handle, 0, ",")) !== FALSE) {
				if (!empty($family)) {

					$tuition = str_replace('$', "", $base) + str_replace("$", "", $new);
					$totalTuition += $tuition;
					
					
					if (empty($fileTuition[$family]))  $fileTuition[$family] = 0;
					$fileTuition[$family]  += $tuition;

					if (!empty($ch1)) self::verifyParent($family, $ch1);
					if (!empty($ch2)) self::verifyParent($family, $ch2);
					if (!empty($ch3)) self::verifyParent($family, $ch3);

				}
			}
			
			foreach ($fileTuition as $family => $tuition) {
				$tracker = FamilyTracker::GetItemById($family);
				if (empty($tracker)) throw new Exception("family $family not found in tracker, weird");
				if ($tracker->tuition != $tuition) {
					$sql = "update FamilyTracker set tuition = $tuition, currentYear = " .  EnumFamilyTracker::enum('registered');
					$sql .= " where family = $family and year= " . FamilyTracker::currYear . ";\n";
					$result = VidDb::query($sql);
					print $i++ . "$sql \n";
					
				} 
				$done[$family] = 1;
			}
				
			
			foreach(FamilyTracker::GetAll() as $tracker) {
				$family = $tracker->family;
				if ($tracker->tuition != 0) {
					if  ($done[$family] != 1) {
						print "check family " . $tracker->family . "\n";
					}
						
				}
			}
			
			$sql="select sum(tuition) from FamilyTracker where year= " . FamilyTracker::currYear;
			$result = VidDb::query($sql);
			$row = mysql_fetch_array($result);
			$databaseTuition = $row[0];
			$tuitionCheck = $databaseTuition == $totalTuition ? "OK" : "FAIL";
			print "Total Tuition in file = " . $totalTuition . ", Database = " . $databaseTuition . ", Check: $tuitionCheck" . "\n";
			
			$lang = array();
			$grade = array();
			foreach (self::$students as $student) {
				$level = $student->GradeAt(Calendar::RegistrationSession);
				if ($level > 9) $level = 9;
				if ($level != "KG")
				empty($lang[$student->languagePreference]) ? $lang[$student->languagePreference]=1 : $lang[$student->languagePreference]++;
				empty($grade[$level]) ? $grade[$level]= 1 : $grade[$level]++ ;
			}
			foreach ($lang as $key => $value) {
				print "Language: $key, Count: $value\n";
			}
			foreach ($grade as $key => $value) {
				print "Grade: $key, Count: $value\n";
			}
			
			print "Count of Students = " . count(self::$students) . "\n";
		}
	}


	public static function ReportPending() {
		$i=1;
		foreach (self::GetAll() as $tracker) {
			if ($tracker->currentYear != EnumFamilyTracker::enum('pendingRegistration')) continue;
				
			$family = Family::GetItemById ($tracker->family);
				
			$fields = Array();
			$fields[] = $i++;
			$fields[] = $family->id;
			$fields[] = $tracker->previousYear == EnumFamilyTracker::enum('waitlist') ? $family->priority_date : "Registered";
			$fields[] = $family->phone;
			$fields[] = $family->parentsName();
			$fields[] = $family->mother->email;
			$fields[] = $family->father->email;

			$fields[] = $family->mother->workPhone;
			$fields[] = $family->mother->cellPhone;

			$fields[] = $family->father->workPhone;
			$fields[] = $family->father->cellPhone;

			print implode (", ", $fields) . "\n";
		}
	}
}


// Query that is used to load all the students in classes
function CreateDatabaseQueryRegisteredStudents() {
  $conditionPraveen = <<< DATABASE_WHERE_REGISTERED
    WHERE (
	   ((Students2003.CONTINUING)<>2) 

	   And 
	   (
	    (Students2003.STATUS) Is Null Or (Students2003.STATUS)<>3)
	   ) 
    Or 
    (
     ((Students2003.STATUS)=1)
    ) 

DATABASE_WHERE_REGISTERED;

  $conditionUmesh = "where Students2003.STATUS=1";

    $orderBy = <<< DATABSE_ORDERBY_REGISTERED
ORDER BY Students2003.LAST_NAME, Students2003.FIRST_NAME, SchoolGrade.Description

DATABSE_ORDERBY_REGISTERED;

    return LoadStudents($conditionUmesh, $orderBy);
}

// Query that is used to find all registered students. I got it from Praveen but I am not using it in this code
function LoadStudents($condition, $orderBy) {
  $query = <<< DATABASE_QUERY_UMESH

    SELECT Students2003.* FROM Students2003 
  	 

$condition
$orderBy

DATABASE_QUERY_UMESH;
  return $query;
}

// Process data returned from the database
function mysql_fetch_alias_array($result) {
    if (!($row = mysql_fetch_array($result)))
    {
        return null;
    }

    $assoc = Array();
    $rowCount = mysql_num_fields($result);
    
    for ($idx = 0; $idx < $rowCount; $idx++)
    {
        $table = mysql_field_table($result, $idx);
        $field = mysql_field_name($result, $idx);
        $assoc["$table.$field"] = $row[$idx];
    }
    
    return $assoc;
}

// Function called once we have connection to run query and process data to populate students
function CreateDictionary($connection) {

	// load family category object
	$query = "select * from Parent_Type";
	if (!($result = mysql_query($query, $connection))) showerror();
	while ($row = mysql_fetch_alias_array($result)) {
		FamilyCategory::GetFamilyCategory($row);
	}
	
	// load family object
	$query = "select Parents2003.*, Email_Preferences.* from Parents2003
		INNER JOIN Email_Preferences ON Parents2003.PRIMARY_EMAIL_FLAG=Email_Preferences.ID
	";
	if (!($result = mysql_query($query, $connection))) showerror();
	while ($row = mysql_fetch_alias_array($result)) {
		Family::GetFamily($row);
	}
	
	// Load Courses
	$query = "select * from LanguageGrades";
	if (!($result = mysql_query($query, $connection))) showerror();
	while ($row = mysql_fetch_alias_array($result)) {
		Course::GetCourse($row, "l");
	}
	
	$query = "select * from CultureGrades";
	if (!($result = mysql_query($query, $connection))) showerror();
	while ($row = mysql_fetch_alias_array($result)) {
		Course::GetCourse($row, "c");
	}
	
	// load Student Status Enums
	$query = "select * from Status";
	if (!($result = mysql_query($query, $connection))) showerror();
	while ($row = mysql_fetch_alias_array($result)) {
		StudentStatus::GetStudentStatus($row);
	}
	
	
	// Load Students
	$query = LoadStudents("", "");
	//$query = CreateDatabaseQueryRegisteredStudents(); // puts some additional constraint - not used
	if (!($result = mysql_query($query, $connection))) showerror();
	$i=0;

	while ($row = mysql_fetch_alias_array($result)) {
		$id = $row["Students2003.ID"];
//		$dictStudents[$id] = new Student($row);
		$dictStudents[$id] = Student::GetStudent($row);
	}
	return $dictStudents;
}


// Main function called by outside world
function GetAllData() {
	$sapi = php_sapi_name();
	switch ( $sapi ) {
		case "cli":
			print "Big Data Looup";
			break;
	}

	$hostname = "vidyalaya.db.4718809.hostedresource.com";
	$databasename = "vidyalaya";
	$username = "vidyalaya";
	$password = "Praveen38";
	if (!$connection = @ mysql_connect($hostname, $username, $password))
	die("Cannot connect using hostname=$hostname, user=$username, password=$password\n");
	if (!mysql_selectdb($databasename, $connection))
	showerror();


	$dictStudents = CreateDictionary($connection);

	switch ( $sapi ) {
		case "cli":
			print "... Completed\n";
			break;
	}
			return $dictStudents;
}

?>
