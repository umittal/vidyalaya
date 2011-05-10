<?php
   // This file is the same as example 6-7, but includes mysqlclean() and shellclean() 

   $hostname = "vidyalaya.db.4718809.hostedresource.com";
   $databasename = "vidyalaya";
   $username = "vidyalaya";
   $password = "Praveen38";

   function showerror()
   {
      die("Error " . mysql_errno() . " : " . mysql_error());
   }

   function mysqlclean($array, $index, $maxlength, $connection)
   {
     if (isset($array["{$index}"]))
     {
        $input = substr($array["{$index}"], 0, $maxlength);
        $input = mysql_real_escape_string($input, $connection);
        return ($input);
     }
     return NULL;
   }

   function shellclean($array, $index, $maxlength)
   {
     if (isset($array["{$index}"]))
     {
       $input = substr($array["{$index}"], 0, $maxlength);
       $input = EscapeShellCmd($input);
       return ($input);
     }
     return NULL;
   }

   function vidyalaya() {
      if (!$connection =  mysql_connect($hostname, $username, $password))
           showerror("Cannot connect");
      if (!mysql_selectdb($databasename, $connection))
           showerror();
      return $connection;

   }
   
   class VidDb {
   	private static $hostname = "vidyalaya.db.4718809.hostedresource.com";
   	private static $databasename = "vidyalaya";
   	private static $username = "vidyalaya";
   	private static $password = "Praveen38";
   	private static $connection = null;
   	 
   	private static function connect() {
   		if (!empty(self::$connection)) return;
   		if (!self::$connection =  mysql_connect(self::$hostname, self::$username, self::$password))
   			showerror("Cannot connect");
   		if (!mysql_selectdb(self::$databasename, self::$connection))
   			showerror();
   	}
   	
   	public static function query($query) {
   		self::connect();
		// do the cli check here
		print "executing query: $query\n";
   		if (!($result = mysql_query($query, self::$connection))) showerror();
		print "count is " . count($result) . "\n";
   		return $result;
   	}
   	 

   }
   ?>
