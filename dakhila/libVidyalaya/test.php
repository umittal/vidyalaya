<?php
require_once "db.inc";
require_once "vidyalaya.inc";

$classId= 74;
  foreach (Enrollment::GetEnrollmentForClass($classId)  as $item) {
    $student = $item->student;
    print "$student->id, $student->lastName, $student->firstName\n";
  }


?>

