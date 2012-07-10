<?php

$libDir = realpath(dirname(__FILE__));
require_once "$libDir/vidyalaya.inc";

class FeeCheck {
  public $year=null;
  public $family=null;
  public $registrationDate=null;
  public $status=null;

  public $tuition=0;
  public $newEnrollment=0;
  public $adjustment=0;
  public $required=0;
  public $paid=0;
  public $diff=0;

  public $students=null;

  private function UpdateFromTracker($tracker) {
    $this->year = $tracker->year + 2010;
    $this->family = Family::GetItembyId($tracker->family);
    $this->registrationDate = $tracker->year > 1 ? $tracker->registrationDate : "2011-09-01";
    $this->status = $tracker->currentYear;
    $this->paid = $tracker->tuition;
  }

  private function calcTuition() {
    switch (count($this->students)) {
    case 0: return 0;
    case 1: return 450;
    default: return 550; 
    }
  }

  private function NewStudentFee() {
    foreach($this->students as $studentId => $es) {
      $first = 2010 +  Enrollment::firstYear($studentId) ;
      //      print "feecheck debug: $studentid, $first\n";
      if ($this->year <= $first) $this->newEnrollment += 50;
    }
  }

  private function adjustment() {
    if ($this->year < 2012) return;
    if (empty($this->registrationDate ) || $this->registrationDate == "0000-00-00") {
      $this->adjustment += 25;
      return;
    }
    if ($this->registrationDate < "2012-04-23") $this->adjustment -= 25;
    if ($this->registrationDate > "2012-06-04") $this->adjustment += 25;
  }

  private function updateFee() {
    $this->tuition = $this->calcTuition();
    $this->newStudentFee();
    $this->adjustment();
    $this->required = $this->tuition + $this->newEnrollment + $this->adjustment;
    $this->diff = $this->paid - $this->required;
  }

  private static function CreateObjectArray($trackerArray, $enrollmentArray) {
    $objArray = array();
    foreach ($trackerArray as $tracker) {
      //      print "Debug: I was here\n";
      if ($tracker->tuition != 0 || $tracker->currentYear == EnumFamilyTracker::registered) {
	$obj = new self();
	$obj->UpdateFromTracker($tracker);
	$key = $tracker->family . ":" . $tracker->year;
	$objArray[$key] = $obj;
      }
    }

    foreach ($enrollmentArray as $e) {
      $key = $e->student->family->id . ":" . $e->class->year;

      if(array_key_exists($key, $objArray)) {
	$obj = $objArray[$key];
	$obj->students[$e->student->id][]=$e;
      }
    }

    foreach( $objArray as $item) {
      $item->updateFee();
    }
    return $objArray;
  }

  public static function CreateForFamily($familyId) {
    $trackerArray = FamilyTracker::FamilyHistory($familyId);
    $enrollmentArray = Enrollment::FamilyHistory($familyId);
    return self::CreateObjectArray($trackerArray, $enrollmentArray);

  }

  public static function CreateForYear($year) {
    $trackerArray = FamilyTracker::GetAllForYear($year);
    $enrollmentArray = Enrollment::GetAllForyear($year);
    return self::CreateObjectArray($trackerArray, $enrollmentArray);
  }

  public function __toString() {
    $csv = array();
    $csv[] = sprintf ("Year: %d", $this->year);
    $csv[] = sprintf ("Family:%3d,  %s", $this->family->id, $this->family->parentsName());
    $csv[] = sprintf ("Registration Date: %s", $this->registrationDate);
    foreach ($this->students as $studentId => $es) {
      $s = sprintf ("Student: %s", Student::GetItemById($studentId));
      foreach ($es as $e) {
	$s .= sprintf (", %s", $e->class);
      }
      $csv[] =$s;
    }

    $csv[] = sprintf ("Tuition: %5d", $this->tuition);
    $csv[] = sprintf ("New Student: %5d", $this->newEnrollment);
    $csv[] = sprintf ("Adjustment: %5d", $this->adjustment);
    $csv[] = sprintf ("Required: %5d", $this->required);
    $csv[] = sprintf ("Paid: %5d", $this->paid);
    $feecheck = $this->diff == 0 ? "OK" : "CHECKFEE";
    $csv[] = sprintf ("Diff: %5d, %s", $this->diff, $feecheck);
    return implode("\n", $csv);
  }
}

?>
