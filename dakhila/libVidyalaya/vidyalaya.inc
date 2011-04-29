<?php
require_once "util.inc";

class Language {
	public $id = null;

	const Hindi = 1
	, Gujarati = 2
	, Telugu = 3
	;

	public function Name() {
		return Language::NameFromId($this->id);
	}

	public static function NameFromId($number) {
		switch ($number) {
			case 1: return "Hindi";
			case 2: return "Gujarati";
			case 3: return "Telugu";
			default: return "Unknown";
		}
	}

	public static function NameFromChar($char) {
		switch ($char) {
			case "H": return Language::NameFromId(1);
			case "G": return Language::NameFromId(2);
			case "T": return Language::NameFromId(3);
			default: return "unknown";
		}
	}

	public static function IdFromChar($char) {
		switch ($char) {
			case "H": return 1;
			case "G": return 2;
			case "T": return 3;
			default: return null;
		}
	}


	public static function NameFromPraveenSymbol($symbol) {
		if (beginsWith($symbol, "KG")) {
			$langChar = $symbol[count($symbol)-1];
		} else {
			$langChar = $symbol[0];
		}
		return Language::NameFromChar($langChar);
	}
	
	public static function IdFromPraveenSymbol($symbol) {
		if (beginsWith($symbol, "KG")) {
			$langChar = $symbol[3];
		} else {
			$langChar = $symbol[0];
		}
		return Language::IdFromChar($langChar);
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
 
 
 
 public function LanguageInterest() {
 	if (!empty($this->languagePreference)) {
 		return Language::NameFromId($this->languagePreference);
 	}
 	return "Unknown";
 }


  public function fullName() {
    return $this->firstName . " " . $this->lastName;
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


    $dob = $row["Students2003.DOB"];
    if (strtotime($dob) < strtotime("1970-01-01")) {
//    if ($dob < "1970-01-01") {
     $this->dateOfBirth = null;
} else {
    $this->dateOfBirth = $dob;
}
    
    $this->family = Family::GetFamilyId($row["Students2003.PARENT_ID"]);
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
				 "LanguageGrades.Room", "LanguageGrades.Teachers");
    } else if ($lc == "c") {
      $this->id = $lc . $row["CultureGrades.ID"];
      $this->SetVariablesFromRow($row, "CultureGrades.Value", "CultureGrades.Description",
				 "CultureGrades.Room", "CultureGrades.Teachers");
    }

  }

  private function SetVariablesFromRow($row, $symbol, $description, $room, $teachers) {
      $this->symbol = $row[$symbol];
      $this->description = $row[$description];
      $this->room = $row[$room];
      $this->teachers = $row[$teachers];
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
 
   public static function GetFamilyId($id) {
   	$key = (integer) $id;
    if (!array_key_exists($key, self::$objArray)) {
//    	print "*** No family found for id $key\n";
    	return null;
    }
    return self::$objArray[$key];
 }
 
 
 public function parentsName() {
   	if ($this->mother->lastName == $this->father->lastName) {
  		return $this->father->firstName . " / " .$this->mother->firstName . " "  . $this->father->lastName;
  	}else { 
  	  return $this->father->firstName . " "  . $this->father->lastName . " / " .$this->mother->firstName . " "  . $this->mother->lastName;
  	}
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
  $hostname = "vidyalaya.db.4718809.hostedresource.com";
  $databasename = "vidyalaya";
  $username = "vidyalaya";
  $password = "Praveen38";
  if (!$connection = @ mysql_connect($hostname, $username, $password))
    die("Cannot connect using hostname=$hostname, user=$username, password=$password\n");
  if (!mysql_selectdb($databasename, $connection))
    showerror();


  //  echo "I made it to main\n";

  $dictStudents = CreateDictionary($connection);
  return $dictStudents;
}

?>

