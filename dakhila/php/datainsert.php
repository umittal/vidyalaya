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
case "InsertChild":
  DataInsert::InsertChild();
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

  public static function InsertFamily() {
    $values = array();
    foreach ($_POST as $key => $value) {
      if (empty($value)) continue;
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
