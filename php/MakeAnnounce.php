<?php
/*
 * MakeAnnounce.php
 *   usage:  MakeAnnounce.php BB_COURSE_ID
 * 
 *    Take BB Course ID as input
 *    Post a pre-defined announcement defined in the CreateJSONFile function
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
function CreateJSONFile () {
	//Create the JSON file for changing language pack
	$contents=' 
{
  "title": "Example Announcement from REST API",
  "body": "<p>This is just an example, permanent <em>(i.e. no end date)</em> announcement posted via the <strong>Blackboard REST API</strong>.</p>",
  "draft": false,
  "availability": {
    "duration": {
      "type": "Permanent",
      "start": "2025-03-31T15:33:24.393Z",
      "end": null
    }
  }
}
';
	return $contents;
}
// ====================================================================
/* Check for username.  
 * 		argv[0] is the file name.
 * 		argv[1] needs to be the Bb Course ID
 */

if ( isset($argv[1]) ) {
	$courseID = $argv[1];
} else {
	print "     You must include the Bb Course ID.".PHP_EOL;
	print "     Example:  ".$argv[0]." HS-MAT-101.24.01".PHP_EOL;
	die ("USAGE ERROR".PHP_EOL);
}

// --------------------------------------------------------------------

$my_token=GetToken();
$jFile=CreateJSONFile();
$url = BaseURL.'/learn/api/public/v1/courses/courseId:'.$courseID.'/announcements';

$c = callAPI('POST',$url,$my_token,$jFile);

// When successful, $c will contain the full announcement JSON

print_r($c);
print PHP_EOL ;


?>
