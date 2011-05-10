<?php
require_once "vidyalaya.inc";

class EnrolledClass {
	private $startTime = null;
	private $endTime = null;
	private $count = null;
}

class RoomUtilization {
	private static $objArray = Array();

	private static function firstTimeCall($facility, $year) {
		if (!empty(self::$objArray)) return;
		$enrollment = Enrollment::GetAllEnrollmentForFacilitySession($facility, $year);
		foreach ($enrollment as $item) {
			$room = $item->class->room;
			self::$objArray[$room->id][$item->class->id][] = $item;
		}
		return self::$objArray;
	}


	public static function utilization($facility, $year) {
		self::firstTimeCall ($facility, $year);
		$i = 1;
		foreach (self::$objArray as  $roomid => $availableClass) {
			$room = Rooms::GetItemById($roomid);
			print "$i. Room Number: $room->roomNumber\n";
			foreach ($availableClass as $classid => $students) {
				$classa = AvailableClass::GetItemById($classid);
				$cc = CourseCatalog::GetItemById($classa->course->id);
				print "$classa->startTime - $classa->endTime: Course: " . $classa->short(). 
				", student count = " . count($students) . "\n";	
			}
			$i++;
			print "\n";
		}
		return Array();
	}


}


//doit();

?>
