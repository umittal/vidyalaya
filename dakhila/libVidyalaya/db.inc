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
      $_SESSION["count"]=1;
      $_SESSION["start"]= time();
    } else {
      $_SESSION["count"]++;
    }
  }

  public static function IsLoggedIn() {
    VidSession::startSession();
    if (!isset($_SESSION["loginUsername"])) {
      return false;
    }
    return true;
  }

  public static function Menu() {
    if (!VidSession::IsLoggedIn()) return file_get_contents("../html/openmenu.inc");
    $username=$_SESSION["loginUsername"];
    if ($username != "umesh@vidyalaya.us") return file_get_contents("../html/usermenu.inc");
    return file_get_contents("../html/menu.inc");
  }

  public static function FooterWeb() {
    if (self::IsLoggedIn()) {
      $username=$_SESSION["loginUsername"];
      $dbserver=VidDB::ServerType();
      $count=$_SESSION['count'];
      return "Copyright (c) 2012 Vidyalaya Inc., ($username,$dbserver, $count )";
    } else {
      return "Copyright (c) 2012 Vidyalaya Inc., Public Access";
    }
  }

  public static function  sessionAuthenticate()  {
    self::startSession();
    // 1. Check if the user hasn't logged in
   
 if (!isset($_SESSION["loginUsername"])) {
	// The request does not identify a session
      $_SESSION["message"] = "You are not authorized to access the URL {" . $_SERVER["REQUEST_URI"] ."}" ;
	header("Location: /dakhila/php/dataViewer2.php?command=login");
	exit();
      }

    // 2. Check if the request is from a different IP address to previously
    if (!isset($_SESSION["loginIP"]) || 
	($_SESSION["loginIP"] != $_SERVER["REMOTE_ADDR"])) {
	// The request did not originate from the machine
	// that was used to create the session.
	// THIS IS POSSIBLY A SESSION HIJACK ATTEMPT

      	$_SESSION["message"] = "You are not authorized to access the URL {" . $_SERVER["REQUEST_URI"] . "} from the address {" . $_SERVER["REMOTE_ADDR"] . "}";
	header("Location: /dakhila/php/dataViewer2.php?command=login");
	exit();
      }
  }
}
   
class VidDb {
  private static $hostname = "vidyalaya.db.4718809.hostedresource.com";
  private static $databasename = "vidyalaya";
  private static $username = "vidyalaya";
  private static $password = "Praveen38";
  private static $connection = null;

  public static $dbserver=null;

  public static function ServerType() {
    if (!VidSession::IsLoggedIn()) return "Not logged in";
    if ($_SESSION["dbserver"] == self::$hostname) return "Production Server";
    return "Test Server";
  }

  private static function showerror() {
      die("Error " . mysql_errno() . " : " . mysql_error());
   }
  
  private static function showlocal($value) {
    $sapi = php_sapi_name();
    switch ( $sapi ) {
    case "cli":
      print "$value\n";
      break;
    }
  }
   	 
  private static function connect() {
    if (!empty(self::$connection)) return;
    $server = $command=isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME']  : "";
    if (getenv('localvidyalaya') || $server == "localhost" || strcasecmp($server, "ubuntudesk.home") == 0) {
      self::showlocal( "\n\n\t*****Using local  database, may be out of sync");
      self::showlocal( "\t**********************************************");
      if (!self::$connection =  mysql_connect("localhost", self::$username, self::$password))
	showerror("Cannot connect");
      self::$dbserver="localhost";
    } else {
      self::showlocal( "\n\n\t******Using production database");
      if (!self::$connection =  mysql_connect(self::$hostname, self::$username, self::$password))
	showerror("Cannot connect");
      self::$dbserver=self::$hostname;
    }
    if (!mysql_selectdb(self::$databasename, self::$connection))
      showerror();
  }

  public static function mysqlclean($input, $maxlength) {
    //     if (isset($array["{$index}"])) {
       $input = substr($input, 0, $maxlength);
        $input = mysql_real_escape_string($input, self::$connection);
        return ($input);
	//     }
	//     return NULL;
   }

  public static function updatePassword ($email, $password) {
    self::connect();
    $email = self::mysqlclean($email, 50);
    $password = self::mysqlclean($password, 10);
    if (!isset($email) || !isset($password)) return false;

    $query="select * from users where email='$email' ";
    $result = mysql_fetch_alias_array(VidDb::query($query));
    
    $password_digest = md5(trim($password));
    if (empty($result)) {
      $query="insert into users values ('$email', '$password_digest')";
    } else {
      $query="update users set password = '$password_digest' where email = '$email'";
    }
    self::query($query);

    $query = "SELECT password FROM users WHERE email = '{$email}' AND password = '{$password_digest}'";
    $result = self::query($query);
    if (mysql_num_rows($result) != 1) return false;
    return true;
  }

  public static function authenticateUser($username, $password) {
    $username = trim(strtolower($username));
    if ($username != "umesh@vidyalaya.us") return false;
    return self::authenticateOrdinaryUser($username, $password);
  }


  public static function authenticateOrdinaryUser($username, $password)
  {
    $username = trim(strtolower($username));
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
    $_SESSION["dbserver"] = self::$dbserver; // database server I am connected to
    $_SESSION["loginIP"] = $_SERVER["REMOTE_ADDR"]; // Register the IP address that started this session

    return true;
  }

   	
  public static function query($query) {
    self::connect();
    //    error_log($query);
    if (!($result = mysql_query($query, self::$connection))) self::showerror();

    self::showlocal($query);
    return $result;
  }
   	 

}
   ?>
