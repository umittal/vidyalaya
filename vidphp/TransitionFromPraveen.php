<?php
$libDir="../dakhila/libVidyalaya";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";

class TransitionFromPraveen {
	private $outAvailable = null;
	private $outEnrollment = null;
	private $tableEnrollment = null;
	private $tableAvailable= null;
	private $registeredStudent = Array();
	
	
	private function enrollmentFromStudentCourse($student, $course) {
		$availableClass = AvailableClass::findAvailableClassFromCourse($course);
		$sqlEnrollment = "insert into $this->tableEnrollment set ";
		$sqlClause = " student = $student->id, availableClass = $availableClass->id";
		//print "course = $course->description, clause = $sqlClause\n";
		fwrite ($this->outEnrollment, $sqlEnrollment. $sqlClause . ";\n");
	}

	public function InsertAvalableEnrollment() {
		//die ("Do not run this again\n");
		$i = 1;
		foreach ($this->registeredStudent as $student) {
			print "$i: $student->id, ". $student->fullName() . "\n";
			self::enrollmentFromStudentCourse($student, $student->registration->language);
			if ($student->registration->culture->description != "CK")
				self::enrollmentFromStudentCourse($student, $student->registration->culture);
			$i++;
		}
	}
	
	 public function __construct() {
	 	$tmpDir = "/tmp";

		$this->tableAvailable = "AvailableClass";
		$this->tableEnrollment = "Enrollment";
		$filenameAvailable = "$tmpDir/$this->tableAvailable.sql";
		$filenameEnrollment = "$tmpDir/$this->tableEnrollment.sql";
		$this->outAvailable = fopen($filenameAvailable, "w");
		$this->outEnrollment = fopen($filenameEnrollment, "w");

		$result = VidDb::query("delete  from $this->tableEnrollment;");
		$result = VidDb::query("delete  from $this->tableAvailable;");
		fwrite($this->outAvailable, "delete  from $this->tableAvailable;\n");
		fwrite($this->outEnrollment, "delete  from $this->tableEnrollment;\n");
		
		if (empty($this->registeredStudent)) $this->registeredStudent = Student::RegisteredStudents();
	 }

}

class CourseTeacher {

  private static $doneArray = Array();

  private static function printTeacher($course, $parent, $family, $symbol, $c) {
    if ($symbol=="CK") $availableClass = AvailableClass::GetItemById(236); else
    $availableClass = AvailableClass::findAvailableClassFromCourse($course);
    $email = empty($parent->email) ? "missing email" : $parent->email;
    print "$symbol, $parent->firstName, $parent->lastName, $c $family->id, $parent->email \n";

  }
  
  private static function processCourseTeacher($course) {
    if (!empty(self::$doneArray[$course->id])) return;
    $symbol = $course->lc == "c" ? $course->description : $course->symbol;
#    print "$symbol: $course->teachers\n";
    foreach (explode(",", $course->teachers) as $a) {
      $teacher = explode (" ", trim($a));
      $first = $teacher[0];
      $last = $teacher[count($teacher) - 1];

      $found = 0;
      foreach(Family::GetAllFamilies() as $family) {
	if ($first == $family->mother->firstName && $last == $family->mother->lastName) {
	  self::printTeacher($course, $family->mother, $family, $symbol, "m");
	  //;	  print "$symbol, $first, $last, m $family->id, " . $family->mother->email . "\n";
	  $found++;
	} 
	if ($first == $family->father->firstName && $last ==$family->father->lastName) {
	  self::printTeacher($course, $family->father, $family, $symbol, "f");
	  $found++;
	}
      }
      if ($found != 1) print "$symbol, $first, $last,, found= $found\n";
    }

    self::$doneArray[$course->id] = 1;
  }
  public static function ConvertTeacherToObject() {
    foreach (Student::RegisteredStudents() as $student) {
      self::processCourseTeacher($student->registration->language);
      self::processCourseTeacher($student->registration->culture);
    }
  }

}

//CourseTeacher::ConvertTeacherToObject();

$object = new TransitionFromPraveen();

$object->InsertAvalableEnrollment();

?>
