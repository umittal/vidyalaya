<?php

require_once "vidyalaya.inc";

// returns true if $str begins with $sub
function beginsWith( $str, $sub ) {
    return ( substr( $str, 0, strlen( $sub ) ) == $sub );
}

// return tru if $str ends with $sub
function endsWith( $str, $sub ) {
    return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
}

// trims off x chars from the front of a string
// or the matching string in $off is trimmed off
function trimOffFront( $off, $str ) {
    if( is_numeric( $off ) )
        return substr( $str, $off );
    else
        return substr( $str, strlen( $off ) );
}

// trims off x chars from the end of a string
// or the matching string in $off is trimmed off
function trimOffEnd( $off, $str ) {
    if( is_numeric( $off ) )
        return substr( $str, 0, strlen( $str ) - $off );
    else
        return substr( $str, 0, strlen( $str ) - strlen( $off ) );
}

function preg_grep_keys ($pattern, $input, $flags=0) {

  $keys = preg_grep("/{$pattern}/", array_keys($input), $flags);
  $vals = array();

  foreach ($keys as $key) {
    $vals[$key] = $input[$key];
  }
  return $vals;
}

function GetSingleIntArgument() {
	$sapi = php_sapi_name();
	switch ( $sapi ) {

		case "cli":

				
			if( $_SERVER["argc"] != 2) die ("sorry, i can only handle one argument right now\n");
			$line = trim($_SERVER["argv"][1]);
			if ($line == "q") break;
			$entry =intval($line);
			if ($entry == 0 ) {
				die( "found: $line, expecting an integer\n\n");
				break;
			}
			return $entry;
			break;

		default:
			die ("do not know how to handle $sapi yet\n");
	}

}

Class Calendar {
	const BaseYear = 2010
	, Today = 1
	, CurrentSession = 2
	, RegistrationSession = 3
	;
	
	public static function RegistrationYear() {
		return date("Y", time());
	}
	
	public static function CurrentYear() {
		$year = date("Y", time());
		$month = date("n", time());
		if ($month < 7) $year--;
		return $year;
	}
	
	public static function NextYear() {
		return CurrentYear() + 1;
	}
	
	public static function PreviousYear() {
		return CurrentYear() - 1;
	}
	
	public static function Years($from, $to) {
		return ($to - $from)/60/60/24/365;
	}
	
	public static function AgeAt($dob, $rule) {
		if (empty($dob)) return "Unknown";
		switch ($rule) {
			case Calendar::Today:
				return Calendar::Years(strtotime($dob), time());
			case Calendar::CurrentSession:
				return Calendar::Years(strtotime($dob), strtotime(Calendar::CurrentYear() . "-09-01"));
			case Calendar::RegistrationSession:
				return Calendar::Years(strtotime($dob), strtotime(Calendar::RegistrationYear() . "-09-01"));
				
			default:
				die ("calling AgeAt() is not implemented for $rule\n");
		}
	}
	
	public static function Age($dob) {
		return self::AgeAt($dob, Calendar::Today);
	}
	
	public static function GradeAt($first, $rule) {
		if (empty($first)) return "Unknown";
		switch ($rule) {
			case Calendar::CurrentSession:
				$yeardiff =  Calendar::CurrentYear() - $first + 1;
				break;
			case Calendar::RegistrationSession:
				$yeardiff =  Calendar::RegistrationYear() - $first +1;
				break;
			default:
				die ("calling GradeAt() is not implemented for $rule\n");
		}
		
		if ($yeardiff == 0) return "KG";
		if ($yeardiff > 12) return "College";
		
		return $yeardiff;
	}
	
}

class EnumFamilyTracker {
	const registered = 1;
	const waitlist = 2;
	const pendingRegistration = 3;
	const movingAway = 4;
	const notInterested = 5;
	const nextYear=6;
	const tooExpensive=7;

    public function enum($string){
    	
    		$tocheck = 'EnumFamilyTracker::'.$string;
    		$value =constant($tocheck);
    		if (empty($value)) throw new Exception("value $value for $string, $tocheck not found in EnumFamilyTracker"); 
        return $value;
    }
    
    public static function NameFromId($id) {
    	switch ($id) {
    		case self::registered: return "registered";
    		case self::waitlist: return "waitlist";
    		case self::pendingRegistration: return "pendingRegistration";
    		case self::movingAway: return "movingAway";
    		case self::notInterested: return "notInterested";
    		case self::nextYear: return "nextYear";
    		case self::tooExpensive: return "tooExpensive";
    	}
    }
}


?>