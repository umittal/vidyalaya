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
}
?>


