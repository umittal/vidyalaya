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
    case 1: return 400;
    default: return 500; 
    }
  }

  private function NewStudentFee() {
    if (empty($this->students)) return;
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

  private static function FeeCheckBody($family, $amount, $fh, $year1, $year2) {
    $amt1 = isset($year1[$family->id]) ? $year1[$family->id] : 0 ;
    $amt2 = isset($year2[$family->id]) ? $year2[$family->id] : 0 ;

    if ($amount != $amt1 + $amt2) die ("error in total=$amount, amt1=$amt1, amt2=$amt2, this should not happen\n");
    $body = "";
    $body .= "<p>We will be mailing the check as per following details.</p><table style='margin-left:30px'>";
    $body .= "<tr><td width='100px'>Family ID</td><td>$family->id</td?></tr>";
    $body .= "<tr><td>Amount</td><td>$ ". number_format($amount, 2) . "</td></tr>";
    $body .= "<tr><td rowspan=3 valign=top>Address</td><td>" . $family->mother->fullName() . "</td></tr>";
    
    $body .= "<tr><td>" . $family->address->addr1 . "</td></tr>";
    $body .= "<tr><td>" . $family->address->city . ", " . $family->address->state . " " . $family->address->zipcode . "</td></tr>";
    $body .= "</table>";
     
    $body .= "<p>Please let us know if any correction is required.</p><p>Regards</p><p>Vidyalaya Admissions<br />(sent by: Umesh Mittal)</p>";

    $csv=array();
    $csv[] = 	    $family->id;
    $csv[] = 	    number_format($amount, 2);
    $csv[] = 	    $family->mother->fullName();
    $csv[] = 	    $family->address->addr1;
    $csv[] = 	    $family->address->city;
    $csv[] = 	    $family->address->state;
    $csv[] = 	    sprintf ("'%s", $family->address->zipcode);
    $csv[] = 	    $amt1;
    $csv[] = 	    $amt2;
    fputcsv($fh, $csv);
    return $body;
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

  public static function email1112() {
    $year1 = self::CreateForYear(2011);
    $year2 = self::CreateForYear(2012);
    $total = array();
    $amt1 = array(); $amt2=array();

    foreach ($year1 as $item) {
      if ($item->status != EnumFamilyTracker::registered) continue;
      $total[$item->family->id] =$item->diff;
      $amt1[$item->family->id] = $item->diff;
      $amt2[$item->family->id] = 0;
    }

    foreach ($year2 as $item) {
      if ($item->status != EnumFamilyTracker::registered) continue;
      $amt2[$item->family->id] = $item->diff;
      if (array_key_exists($item->family->id, $total)) {
	$total[$item->family->id] +=$item->diff;
      } else {
	$total[$item->family->id] =$item->diff;
      }
    }

    $production=0;
    $subject = "Tuition Refund, Family ";
    $mail =   Mail::SetupMailAdmissions();
    if ($production != 1) {
      $subject = "[Test] $subject";
      $draft = "<p>This is a draft <br />";
    }

    $fh = tmpfile();
    if (!$fh) die ("could not open temporary file for writing");
    fwrite ($fh, "ID, Amount, Name, Address, City, State, Zip, 2011, 2012\n");

    foreach($total as $familyId => $diff) {
      $s = "$subject $familyId";
      $mail->Subject = $s;
      //      print "$familyId, $diff\n";
      //      if ($familyId != 469 ) continue;

      $family=Family::GetItemById($familyId);
      Mail::SetFamilyAddress($mail, $family, $production);

      $salutation = "<p>Dear " . $family->parentsName() . ",";
      //      $body = str_replace("==AMOUNT==", $diff, file_get_contents("FeeCheck.html"));
      $body = self::FeeCheckBody($family, $diff, $fh, $amt1, $amt2);
      $mail->Body = $draft . $salutation . $body;
      $mail->AltBody = "Family: $family->id"; //Text Body

      continue;
      die ("i die here");

      //      return;
      if(!$mail->Send()) {
	echo "Mailer Error: $family->id:  " . $mail->ErrorInfo . "\n";
	return;
      }  else {
	echo "Message has been sent, Family $family->id\n";
      }

      //      die("hello\n");
      
      $mail->ClearAllRecipients(); 
      $mail->ClearAttachments(); 
      $mail->ClearCustomHeaders(); 
    }

    $filename = "/home/umesh/Dropbox/Vidyalaya-Management/Administration/FeeRefund.csv";
    fseek($fh, 0);
    file_put_contents("$filename", stream_get_contents($fh));
    print "saved $filename\n";
    fclose($fh);
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
