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
      break;
    }

$command=isset($_GET["command"]) ? $_GET["command"]: null ;
switch ($command) {
case "InsertFamily":
  DataInsert::InsertFamily();
  break;
case "waitlistme":
  DataInsert::waitlistme();
  break;
case "InsertEvent":
  DataInsert::InsertEvent();
  break;
case "InsertClass":
  DataInsert::InsertClass();
  break;
case "InsertChild":
  DataInsert::InsertChild();
  break;
case "AddTeacher":
  DataInsert::AddTeacher();
  break;
default:

  break;
}

class DataInsert {


  private static function error($message) {
    header("HTTP/1.0 400 Bad Request ");
    error_log( $message);
    exit();
  }

  public static function InsertChild() {

    $values = array();
    foreach ($_POST as $key => $value) {
      if (empty($value)) continue;
      switch($key) {
      case "Family":
	$values[] = "PARENT_ID = $value";
	break;
      case "First":
	$values[] = "FIRST_NAME = '$value'";
	break;
      case "Last":
	$values[] = "LAST_NAME = '$value'";
	break;
      case "Email":
	$values[] = "EMAIL = '$value'";
	break;
      case "Cell":
	$values[] = "cellphone = '$value'";
	break;
      case "gender":
	$values[] = "GENDER = $value";
	break;
      case "language":
	$values[] = "LanguageInterest = $value";
	break;
      case "dob":
	$values[] = "DOB = '$value'";
	break;
      case "yearFirst":
	$values[] = "YearFirstGrade = $value";
	break;
      default:
	self::error("Did not expect Key $key");
      }
    }
    if (empty($values)) {
      self::error("No Values Found");
    }

    $sql = "insert into Students2003 Set " . implode(",", $values);
    $sql = str_replace("a@b.c", "", $sql);
    //  self::error($sql);
    $result = VidDb::query($sql);
    if ($result == FALSE) {
      header("HTTP/1.0 200 ");
      print "Error insert failed, " . mysql_error();
      return;
    }
    $id = mysql_insert_id();
    header("HTTP/1.0 200 ");
    print "$id";


  }
  public static function waitlistme() {
    $values = array();
    foreach ($_POST as $key => $value) {
      if (empty($value)) continue;
      $value = trim($value);
      switch($key) {
      case "familyId":
	$values[] = "family = $value";
	break;
      default:
	self::error("Did not expect Key $key");
      }
    }
    if (empty($values)) {
      self::error("No Values Found");
    }
    $thisyear=Calendar::RegistrationYear() - 2010;
    $values[] = "year = $thisyear"  ;
    $values[] = "previousYear = " . EnumFamilyTracker::waitlist;
    $values[] = "currentYear = " . EnumFamilyTracker::pendingInvitation;
    $values[] = "tuition = 0 ";

    $sql = "insert into FamilyTracker Set " . implode(",", $values);
    //self::error($sql);
    $result = VidDb::query($sql);
    if ($result == FALSE) {
      header("HTTP/1.0 200 ");
      print "Error insert failed, " . mysql_error();
      return;
    }
    $id = mysql_insert_id();
    header("HTTP/1.0 200 ");
    print "$id";
  }

  public static function AddTeacher() {
      $classId = isset($_POST['classId']) ?  $_POST['classId'] : null;
      $isLead = isset($_POST['isLead']) ?  $_POST['isLead'] : 0;
      $email = isset($_POST['email']) ?  $_POST['email'] : null;

      $result = Teachers::AddTeacher($classId, $email, $isLead);
    if ($result == FALSE) {
      header("HTTP/1.0 200 ");
      print "Error insert failed, " . mysql_error();
      return;
    }
    header("HTTP/1.0 200 ");
    print "ok";
  }

  public static function InsertEvent() {
    $values = array();
    foreach ($_POST as $key => $value) {
      $value = trim($value);
      if (empty($value)) continue;
      $startHour = $startMinute = $startampm = $endHour = $endMinute = $endampm = "";
      switch($key) {
      case "eventType":
	$values[] = "eventtype = $value";
	break;
      case "eventDate":
	$values[] = "date = '$value'";
	break;
      case "portalId":
	$values[] = "portalid = $value";
	break;
      case "classId":
	$values[] = "class = $value";
	break;
      case "description":
	$values[] = "description = '$value'";
	break;

      case "startHour":
      case "startMinute":
      case "startampm":
      case "endHour":
      case "endMinute":
      case "endampm":
	$$key=$value;
	break;
      default:
	self::error("Did not expect Key $key");
      }
    }
    //    if (!(empty($startHour) && empty($startMinute) && empty($startampm))) $values[] = "starttime = " . formatSqlTime($startHour, $startMinute, $startampm);
    //    if (!(empty($endHour) && empty($endMinute) && empty($endampm))) $values[] = "endtime = " . formatSqlTime($endHour, $endMinute, $endampm);

    if (empty($values)) {
      self::error("No Values Found");
    }

    $sql = "insert into EventCalendar Set " . implode(",", $values);
    //    self::error($sql);
    $result = VidDb::query($sql);
    if ($result == FALSE) {
      header("HTTP/1.0 200 ");
      print "Error insert failed, " . mysql_error();
      return;
    }
    $id = mysql_insert_id();
    header("HTTP/1.0 200 ");
    print "$id";
  }


  public static function InsertClass() {
    $values = array();
    foreach ($_POST as $key => $value) {
      $value = trim($value);
      if (empty($value)) continue;
      switch($key) {
      case "course":
	$values[] = "course = $value";
	break;
      case "section":
	$values[] = "section = '$value'";
	break;
      case "startTime":
	$values[] = "startTime = '$value'";
	break;
      case "endTime":
	$values[] = "endTime = '$value'";
	break;
      default:
	self::error("Did not expect Key $key");
      }
    }
    if (empty($values)) {
      self::error("No Values Found");
    }

    // Obviously we can do better than that
    $values[] = "year = " . Calendar::RegistrationYear() -2010;
    $values[] = "room = 19";

    $sql = "insert into AvailableClass Set " . implode(",", $values);
    //self::error($sql);
    $result = VidDb::query($sql);
    if ($result == FALSE) {
      header("HTTP/1.0 200 ");
      print "Error insert failed, " . mysql_error();
      return;
    }
    $id = mysql_insert_id();
    header("HTTP/1.0 200 ");
    print "$id";

  }

  public static function InsertFamily() {
    $values = array();
    foreach ($_POST as $key => $value) {
      if (empty($value)) continue;
      $value = trim($value);
      switch($key) {
      case "homePhone":
	$values[] = "MH_PHONE = '$value'";
	break;
      case "addr1":
	$values[] = "M_ADDRESS = '$value'";
	break;
      case "city":
	$values[] = "M_CITY = '$value'";
	break;
      case "state":
	$values[] = "M_STATE = '$value'";
	break;
      case "zip":
	$values[] = "M_ZIP_CODE = '$value'";
	break;
      case "priority":
	$values[] = "priority_date = '$value'";
	break;


      case "mFirst":
	$values[] = "MFIRST_NAME = '$value'";
	break;
      case "fFirst":
	$values[] = "FFIRST_NAME = '$value'";
	break;
      case "mLast":
	$values[] = "MLAST_NAME = '$value'";
	break;
      case "fLast":
	$values[] = "FLAST_NAME = '$value'";
	break;
      case "mEmail":
	$values[] = "M_EMAIL = '$value'";
	break;
      case "fEmail":
	$values[] = "F_EMAIL = '$value'";
	break;
      case "mWork":
	$values[] = "MW_PHONE = '$value'";
	break;
      case "fWork":
	$values[] = "FW_PHONE = '$value'";
	break;
      case "mCell":
	$values[] = "MC_PHONE = '$value'";
	break;
      case "fCell":
	$values[] = "FC_PHONE = '$value'";
	break;
      default:
	self::error("Did not expect Key $key");
      }

    }

    if (empty($values)) {
      self::error("No Values Found");
    }

    $sql = "insert into Parents2003 Set " . implode(",", $values);

    //self::error($sql);
    $result = VidDb::query($sql);
    if ($result == FALSE) {
      header("HTTP/1.0 200 ");
      print "Error insert failed, " . mysql_error();
      return;
    }
    $id = mysql_insert_id();
    header("HTTP/1.0 200 ");
    print "$id";
  }


} // end of class

?>
