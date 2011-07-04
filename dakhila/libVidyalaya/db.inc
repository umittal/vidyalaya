<?php

class PlaceHolder {
  public static function shellclean($array, $index, $maxlength) {
    if (isset($array["{$index}"])) {
      $input = substr($array["{$index}"], 0, $maxlength);
      $input = EscapeShellCmd($input);
      return ($input);
    }
    return NULL;
  }

}


class VidSession {
  public static function startSession() {
    if (!isset($_SESSION["count"])) {
      session_start();
      $_SESSION["count"]=0;
      $_SESSION["start"]= time();
    }
  }


  public static function  sessionAuthenticate()
  {
    self::startSession();
    // 1. Check if the user hasn't logged in
    if (!isset($_SESSION["loginUsername"])) {
	// The request does not identify a session
      $_SESSION["message"] = "You are not authorized to access the URL {" . $_SERVER["REQUEST_URI"] ."}" ;
	header("Location: /dakhila/php/dataViewer2.php?command=login");
	exit;
      }

    // 2. Check if the request is from a different IP address to previously
    if (!isset($_SESSION["loginIP"]) || 
	($_SESSION["loginIP"] != $_SERVER["REMOTE_ADDR"])) {
	// The request did not originate from the machine
	// that was used to create the session.
	// THIS IS POSSIBLY A SESSION HIJACK ATTEMPT

      	$_SESSION["message"] = "You are not authorized to access the URL {" . $_SERVER["REQUEST_URI"] . "} from the address {" . $_SERVER["REMOTE_ADDR"] . "}";
	header("Location: /dakhila/php/dataViewer2.php?command=login");
	exit;
      }

  }

}
   
class VidDb {
  private static $hostname = "vidyalaya.db.4718809.hostedresource.com";
  private static $databasename = "vidyalaya";
  private static $username = "vidyalaya";
  private static $password = "Praveen38";
  private static $connection = null;

  private static function showerror() {
      die("Error " . mysql_errno() . " : " . mysql_error());
   }
   	 
  private static function connect() {
    if (!empty(self::$connection)) return;
    if (!self::$connection =  mysql_connect(self::$hostname, self::$username, self::$password))
      showerror("Cannot connect");
    if (!mysql_selectdb(self::$databasename, self::$connection))
      showerror();
  }

  private function mysqlclean($input, $maxlength) {
    //     if (isset($array["{$index}"])) {
       $input = substr($input, 0, $maxlength);
        $input = mysql_real_escape_string($input, self::$connection);
        return ($input);
	//     }
	//     return NULL;
   }

  public static function authenticateUser($username, $password)
  {
    self::connect();
    VidSession::startSession();
    // Clean the data collected in the <form>
    $username = self::mysqlclean($username, 50);
    $password = self::mysqlclean($password, 10);
    // Test the username and password parameters
    if (!isset($username) || !isset($password)) return false;

    // Create a digest of the password collected from the challenge
    $password_digest = md5(trim($password));

    // Formulate the SQL find the user
    $query = "SELECT password FROM users WHERE email = '{$username}' AND password = '{$password_digest}'";
    $result = self::query($query);
    if (mysql_num_rows($result) != 1) return false;
    $_SESSION["loginUsername"] = $username; // Register the loginUsername
    $_SESSION["loginIP"] = $_SERVER["REMOTE_ADDR"]; // Register the IP address that started this session

    return true;
  }

   	
  public static function query($query) {
    self::connect();
    //    error_log($query);
    if (!($result = mysql_query($query, self::$connection))) self::showerror();

    $sapi = php_sapi_name();
    switch ( $sapi ) {
    case "cli":
      print "executing query: $query, count is " . count($result) . "\n";

      break;
    }
    return $result;
  }
   	 

}
   ?>
