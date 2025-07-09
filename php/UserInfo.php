<?php
/*
 * UserInfo.php
 *   usage:  UserInfo.php USERNAME 
 * 
 *    Take BB UserID as input
 *    return the full JSON data as an array
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

// ====================================================================
// FUNCTIONS

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
$url = BaseURL.'/learn/api/public/v1/users/userName:'.$userBBid;

$c = callAPI('GET',$url,$my_token,'');

// When successful, $c will contain the JSON info the user. 
if (isset($c['userName'])) {
	print "SUCCESS: ".PHP_EOL;
	print_r ($c);
} else {
	print "ERROR: really should never see this, so something odd happened" . PHP_EOL;
} 
?>
