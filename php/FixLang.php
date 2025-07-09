<?php
/*
 * FixLang.php
 *   usage:  FixLang.php USERNAME 
 * 
 *    Take BB UserID as input
 *    Set the users LANG setting back to our schools Default setting
 *     Set the VARIABLE $langPack for default
 * 
 *  ===================================================================
 * Copyright 2025 Kevin Squire <gentgeen@wikiak.org>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 */
// ====================================================================
include_once ("assets/config.php");
include_once ("assets/Call.php");
include_once ("assets/Auth.php");

define ('DEBUG',FALSE);
// ====================================================================
// VARIABLES
$langPack="en_us_k12";

// ====================================================================
// FUNCTIONS
function CreateJSONFile ($lp) {
	//Create the JSON file for changing language pack
	$contents=' 
{
  "locale": {
    "id": "'.$lp.'"
  }
}
';
	return $contents;
}
// ====================================================================
/* Check for username.  
 * 		argv[0] is the file name.
 * 		argv[1] needs to be the Bb Username
 */

if ( isset($argv[1]) ) {
	$userBBid = $argv[1];
} else {
	print "     You must include the Bb Username.".PHP_EOL;
	print "     Example:  ".$argv[0]." brownc001".PHP_EOL;
	die ("USAGE ERROR".PHP_EOL);
}

// --------------------------------------------------------------------

$my_token=GetToken();
$langFile=CreateJSONFile($langPack);
$url = BaseURL.'/learn/api/public/v1/users/userName:'.$userBBid;

$c = callAPI('PATCH',$url,$my_token,$langFile );

// When successful, $c will contain the JSON info the user. 
if (isset($c['userName'])) {
	print "SUCCESS: ".$c['userName']." now has language pack set to ".$c['locale']['id'] . PHP_EOL;
} else {
	print "ERROR: really should never see this, so something odd happened" . PHP_EOL;
} 
?>
