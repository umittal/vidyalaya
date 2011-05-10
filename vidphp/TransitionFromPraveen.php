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

$object = new TransitionFromPraveen();

$object->InsertAvalableEnrollment();

?>
