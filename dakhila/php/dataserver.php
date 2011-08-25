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
      header('Cache-Control: no-cache, must-revalidate');
      header('Content-type: application/json');
      break;
    }


$command=$_GET["command"];
switch ($command) {
case "CourseCatalog":
  Dataserver::CourseCatlog();
  break;
default:

  break;
}

class Dataserver {

  public static function CourseCatlog() {
    $output= array();
    $csv= array();
    foreach (CourseCatalog::GetAll() as $course) {
      $output[] = array("id" => $course->id, 'department' => Department::NameFromId($course->department),
			'level' =>  $course->level, 'short' =>  $course->short, 'full' => $course->full);
    }
    print json_encode(array('identifier'=> 'id', 'label' => 'id', 'items' => $output));
    return;

  }
}
?>


