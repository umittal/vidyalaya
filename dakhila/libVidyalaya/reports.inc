<?php
require_once "vidyalaya.inc";

class EnrolledClass {
	private $startTime = null;
	private $endTime = null;
	private $count = null;
}

class RoomUtilization {
	private $room = null;
	private $capacity = null;
	private $entrolledClass=null;
	
	private static function utilization($facility, $year) {
		$utilization = Array();
		$enrollment = Enrollment::GetAllEnrollmentForFacilitySession($facility, $year);
		foreach ($enrollment as $item) {
			$utilization[$item->class->room][] = $item;
		}
	}
	
}


//doit();

?>
