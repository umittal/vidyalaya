<?php
/**
 * Sunday - Vidyalaya Sunday School Manager
 *
 * Copyright (c) 2011 - Umesh Mittal <umesh@vidyalaya.us>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this library in the file LICENSE.LGPL; if not, write to the
 * Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307 USA
 *
 * Alternatively, you may distribute this software under the terms of the
 * PHP License, version 3.0 or later.  A copy of this license should have
 * been distributed with this file in the file LICENSE.PHP .  If this is not
 * the case, you can obtain a copy at http://www.php.net/license/3_0.txt.
 *
 * @link http://www.vidyalaya.us/
 * @copyright 2011 Umesh Mittal
 * @author Umesh Mittal <umesh@vidylaya.us>
 * @package sunday

 */

function sunday_usage() {
	echo
    "\nUsage: {$_SERVER["argv"][0]} [options] command\n\n".
    "Options:\n".
    " -h\t\tShow this message\n".
	" -e\t\temail address\n".
	"Following Commands are accepted\n\n" .
	"EmailCheck\n" .
"utilTesting\n".
"listAllStudents\n".
"listAllFamilies\n" .
"loadWaitingList\n" .
"checkWaitingList\n" .
"printMemberList\n".
"cityAllocation\n".
"cultureAge\n".
	""
	;
}

function getoptions() {

	$opts = array();

	if ( $_SERVER["argc"] == 1 )
	return $opts;

	$i = 1;
	while ($i < $_SERVER["argc"]) {

		switch ($_SERVER["argv"][$i]) {

			case "--help":
			case "-h":
				$opts["h"] = true;
				$i++;
				break;

			case "-l":
				$opts["l"] = true;
				$i++;
				break;

			case "-p":
				if ( !isset($_SERVER["argv"][$i+1]) )
				die("-p switch requires a size parameter\n");
				$opts["p"] = $_SERVER["argv"][$i+1];
				$i += 2;
				break;

			case "-o":
				if ( !isset($_SERVER["argv"][$i+1]) )
				die("-o switch requires an orientation parameter\n");
				$opts["o"] = $_SERVER["argv"][$i+1];
				$i += 2;
				break;

			case "-b":
				if ( !isset($_SERVER["argv"][$i+1]) )
				die("-b switch requires a path parameter\n");
				$opts["b"] = $_SERVER["argv"][$i+1];
				$i += 2;
				break;

			case "-f":
				if ( !isset($_SERVER["argv"][$i+1]) )
				die("-f switch requires a filename parameter\n");
				$opts["f"] = $_SERVER["argv"][$i+1];
				$i += 2;
				break;

			case "-v":
				$opts["v"] = true;
				$i++;
				break;

			case "-d":
				$opts["d"] = true;
				$i++;
				break;

			case "-t":
				if ( !isset($_SERVER['argv'][$i + 1]) )
				die("-t switch requires a comma separated list of types\n");
				$opts["t"] = $_SERVER['argv'][$i+1];
				$i += 2;
				break;

			default:
				$opts["command"] = $_SERVER["argv"][$i];
				$i++;
				break;
		}

	}
	return $opts;
}

//var_dump($_SERVER);

$libDir="/var/www/dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";

global $_dompdf_show_warnings, $_dompdf_debug, $_DOMPDF_DEBUG_TYPES;

$sapi = php_sapi_name();
$options = array();

switch ( $sapi ) {

	case "cli":

		$opts = getoptions();

		if ( isset($opts["h"]) || (!isset($opts["command"]) && !isset($opts["l"])) ) {
			sunday_usage();
			exit;
		}
		break;

	default:
		print "I do not really know what to do here where sapi = $sapi";
}
