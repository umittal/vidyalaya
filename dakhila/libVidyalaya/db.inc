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
?>
