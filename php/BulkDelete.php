<?php
/*
 * BulkDelete.php
 *   Assorted API to replicate the "Bulk Delete" option found in Original view
 *          "bulk delete users" to delete all student users
 *          "bulk delete announcements" to delete all announcements
 *     Might add in sometime in the future: 
 *           groups
 *           messages
 *           discussion boards
 * 
 *   usage:  BulkDelete.php [users|announcements] COURSEID
 * 
 *    Take the delete item as the first option
 *    Take BB CourseID as the second option
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
// Array to hold the different options available. 
$deleteOptions=array('users','announcements');

// ====================================================================
// FUNCTIONS
function usage() {
	// need to add for correct way arguments 
}
// --------------------------------------------------------------------
function DeleteUsers($courseID) {
	$my_token=GetToken();
	
	// Get list of users enrolled
	$url = BaseURL.'/learn/api/public/v1/courses/courseId:'.$courseID.'/users';
	$c = callAPI('GET',$url,$my_token,'');

	foreach ($c['results'] as $myUser) {
		if ($myUser['courseRoleId'] == 'Student') {
			print "Removing user ".$myUser['userId']." with role of ".$myUser['courseRoleId']." ...  ".PHP_EOL;
		 	$delURL = $url."/".$myUser['userId'] ;
			$d = callAPI ('DELETE',$delURL,$my_token,'');
		}
	}
	return;
}

// --------------------------------------------------------------------
function DeleteAnnouncements($courseID) {
	$my_token=GetToken();
	
	// Get list of announcements
	$url = BaseURL.'/learn/api/public/v1/courses/courseId:'.$courseID.'/announcements';
	$c = callAPI('GET',$url,$my_token,'');

	foreach ($c['results'] as $myPost) {
		print "Removing post ".$myPost['id']." from ".$courseID." ...  ".PHP_EOL;
	 	$delURL = $url."/".$myPost['id'] ;
		$d = callAPI ('DELETE',$delURL,$my_token,'');
	}
	return;
}

// ====================================================================
/* Check for argv[1] needs to be member of deleteOptions array       */

if ( in_array($argv[1],$deleteOptions)) {
	$myDelete = $argv[1];
} else {
	$out = implode(", ",$deleteOptions);
	print "     First argument must be one of the approved options: ".PHP_EOL;
	print "        ".$out.PHP_EOL;
	die ("USAGE ERROR".PHP_EOL); 
}

/* Check for argv[2] needs to be the Course/Org ID  (string) */
if ( isset($argv[2]) ) {
	$myCourseID = $argv[2];
} else {
	print "     Second argument of CourseID is required".PHP_EOL;
	print "     Example:  ".$argv[0]." ".$argv[1]." HS-MAT-ALG101.25.1".PHP_EOL;
	die ("USAGE ERROR".PHP_EOL);
}
// --------------------------------------------------------------------
/* Choose which function to run     */

if ($myDelete == "users" ) {
	$x = DeleteUsers($myCourseID);
	print "All 'Student' users deleted from ". $myCourseID .PHP_EOL ;

} elseif ($myDelete == "announcements" ) {
	$x = DeleteAnnouncements($myCourseID);
	print "All announcements deleted from ". $myCourseID .PHP_EOL ;

} else {
	print "How did you even get here???";
	die;
}

exit;

?>
