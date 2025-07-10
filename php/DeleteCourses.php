<?php
/*
 *
 *  DeleteCourses.php
 * 
 *   usage:  DeleteCourses.php CSVFILE 
 * 
 *    Take a CSV file with BB course ID in column one, and delete them
 * 
 *       ListCourses.php is designed to create the necessary output
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
/* Check for input CSV file
 * 		argv[0] is this files name.
 * 		argv[1] needs to be the CSV file
 */

if ( isset($argv[1]) ) {
	$inFile = $argv[1];
} else {
	print "     You must include the CSV file.".PHP_EOL;
	print "     Example:  ".$argv[0]." example.csv".PHP_EOL;
	die ("USAGE ERROR".PHP_EOL);
}

// --------------------------------------------------------------------

$my_token=GetToken();

// open the CSV file
if (( $file = fopen($inFile,'r')) !== FALSE) {
	// We were able to open the incoming file
	while (($line = fgetcsv($file)) !== FALSE) {
		// $line is an array of the csv elements along the row
		// $line[0] is the first item in that row
		$courseID = $line[0];	// This reassigning of the variable is really unnecessary, just makes next few lines easier to read for humans

		print "Deleting ".$courseID." ... ". PHP_EOL;
		$url = BaseURL."/learn/api/public/v3/courses/courseId:".$courseID;
		$c = callAPI('DELETE',$url,$my_token,'' );
	}
	fclose($file);

} else {
	// opening the incoming file failed somehow
	print "ERROR: Could not open file ". $inFile . PHP_EOL;
}

?>
