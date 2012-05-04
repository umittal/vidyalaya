<?php

    $sapi = php_sapi_name();
    switch ( $sapi ) {
    case "cli":
      $rootDir =  "/var/www/dakhila";
      require_once "$rootDir/libVidyalaya/db.inc";
      require_once "$rootDir/libVidyalaya/vidyalaya.inc";
      break;
    default:
      $rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
      require_once "$rootDir/libVidyalaya/db.inc";
      require_once "$rootDir/libVidyalaya/vidyalaya.inc";
      VidSession::sessionAuthenticate();
      break;
    }

$command=isset($_GET["command"]) ? $_GET["command"]: null ;
switch ($command) {
case "CourseCatalog":
  Dataserver::CourseCatlog();
  break;
case "ChangeClass":
  Dataserver::ChangeClass();
  break;
case "UpdateContacts":
  Dataserver::UpdateContacts();
  break;
case "TrackerChange":
  Dataserver::TrackerChange();
  break;
case "RegisterEvent":
  Dataserver::RegisterEvent();
  $ref = $_SERVER['HTTP_REFERER'];
  header( 'refresh: 0; url='.$ref);
  break;
default:

  break;
}

class Dataserver {

  public static function CourseCatlog() {
    $output= array();
    $csv= array();
      header('Cache-Control: no-cache, must-revalidate');
      header('Content-type: application/json');
    foreach (CourseCatalog::GetAll() as $course) {
      $output[] = array("id" => $course->id, 'department' => Department::NameFromId($course->department),
			'level' =>  $course->level, 'short' =>  $course->short, 'full' => $course->full);
    }
    print json_encode(array('identifier'=> 'id', 'label' => 'id', 'items' => $output));
    return;

  }

  private static function error($message) {
    header("HTTP/1.0 400 Bad Request ");
    error_log( $message);
    exit();
  }

  public static function RegisterEvent() {
    $MFS=$_POST["MFS"];
    $mfsId=$_POST["mfsId"];
    $itemId=$_POST["itemId"];
    $action=$_POST["action"];
    if ($action == "" || $itemId == "" || $mfsId == "" || $MFS == "") return;
    switch ($action) {
    case "register":
      ItemRegistration::RegisterInterst($MFS, $mfsId, $itemId);
      break;
    case "decline":
      ItemRegistration::Decline($MFS, $mfsId, $itemId);
      break;
    default:
      print"dont know what to do";
      break;
    }
  }

  public static function ChangeClass() {
    $from = $_POST["currentClass"];
    $to = $_POST["newClass"];
    $studentId = $_POST["studentId"];

    
    $fromClass = AvailableClass::GetItemById($from);
    if ($fromClass == null) self::error("class not found");
    $toClass = AvailableClass::GetItemByShort($to, $fromClass->session);
    if ($toClass == null) self::error("class --$to-- not found");
    if ($fromClass->course->department != $toClass->course->department) 
      self::error("cannot use this facility to change department");
    if ($fromClass->course->department == Department::Culture && $fromClass->course->level != $toClass->course->level)
      self::error("For Culture you can only change section, not level");



    $query = "update Enrollment set availableClass = " . $toClass->id . " where student = $studentId and availableClass = " . $from;
    error_log($query);
    $result = VidDb::query($query);


    header("HTTP/1.0 200 ");
    print "class changed";
  }

  public static function TrackerChange() {
    $familyId = $_POST["familyId"];
    $currentYear = $_POST["status"];
    $tuition = $_POST["tuition"];
    $thisyear=Calendar::RegistrationYear() - 2010;

    $sql = "update FamilyTracker Set currentYear=$currentYear, tuition = $tuition where family=$familyId and year=$thisyear";
    //self::error($sql);
    $result = VidDb::query($sql);
    if ($result == FALSE) {
      header("HTTP/1.0 200 ");
      print "Error insert failed, " . mysql_error();
      return;
    }
    $rowcount = mysql_affected_rows ( );
    header("HTTP/1.0 200 ");
    print "$rowcount row  updated";
  }

  public static function UpdateContacts() {
    $studentId = $_POST["studentId"];
    $emergency = preg_replace('/[^0-9]/', '', $_POST["emergency"]);
    $primary = preg_replace('/[^0-9]/', '', $_POST["primary"]);
    $dentist = preg_replace('/[^0-9]/', '', $_POST["dentist"]);
    $hospital = preg_replace('/[^0-9]/', '', $_POST["hospital"]);
    
    $status = "";
    $student = Student::GetItemById($studentId);
    if (is_null($student)) {
      self::error("Student for  id $studentId not found");
    }

    if (strlen($emergency) == 10 && $student->contacts["Emergency"] != $emergency) {
      $status .= "emergency "; $phone=$emergency;
      OtherContacts::CreatePhone($phone);
      $student->UpdateStringField("EmergencyContact", $phone);
    }

    if (strlen($primary) == 10 && $student->contacts["Primary"] != $primary) {
      $status .= "primary "; $phone=$primary;
      OtherContacts::CreatePhone($phone);
      $student->UpdateStringField("PrimaryDoctor", $phone);
    }

    if (strlen($dentist) == 10 && $student->contacts["Dentist"] != $dentist) {
      $status .= "dentist "; $phone=$dentist;
      OtherContacts::CreatePhone($phone);
      $student->UpdateStringField("Dentist", $phone);
    }

    if (strlen($hospital) == 10 && $student->contacts["Hospital"] != $hospital) {
      $status .= "hospital "; $phone=$hospital;
      OtherContacts::CreatePhone($phone);
      $student->UpdateStringField("Hospital", $phone);
    }

    header("HTTP/1.0 200 ");
    print "$status";
  }

  public static function InsertFamily() {
      self::error("I am loser");
    $values = array();
    foreach ($_POST as $key => $value) {
      if (empty($value)) continue;
      switch($key) {
      case "homePhone":
	$values[] = "MH_PHONE = $value";
	break;
      case "addr1":
	$values[] = "M_ADDRESS = $value";
	break;
      case "city":
	$values[] = "M_CITY = $value";
	break;
      case "state":
	$values[] = "M_STATE = $value";
	break;
      case "zip":
	$values[] = "M_ZIP_CODE = $value";
	break;
      case "mfirst":
	$values[] = "MFIRST_NAME = $value";
	break;
      case "ffirst":
	$values[] = "FFIRST_NAME = $value";
	break;
      case "mLast":
	$values[] = "MLAST_NAME = $value";
	break;
      case "fLast":
	$values[] = "FLAST_NAME = $value";
	break;
      case "mEmail":
	$values[] = "M_EMAIL = $value";
	break;
      case "fEmail":
	$values[] = "F_EMAIL = $value";
	break;
      case "mWork":
	$values[] = "MW_PHONE = $value";
	break;
      case "fWork":
	$values[] = "FW_PHONE = $value";
	break;
      case "mCell":
	$values[] = "MC_PHONE = $value";
	break;
      case "fCell":
	$values[] = "FC_PHONE = $value";
	break;
      default:
	self::error("Did not expect Key $key");
      }

    }

    if (empty($values)) {
      self::error("No Values Found");
    }

    $sql = "insert into Parents2003 Set " . implode(",", $values);
    $result = VidDb::query($sql);
    if ($result == FALSE) {
      header("HTTP/1.0 200 ");
      print "Error insert failed, " . mysql_error();
      return;
    }
    $id = mysql_insert_id();
    header("HTTP/1.0 200 ");
    print "Family id $id created sucessfully";
  }


} // end of class

?>
