<?php
/*
 * MergeCourses.php
 * -------------------------------------------------------------------- 
 * Take a list of courseIDs, along with a defined "Master" class ID
 * and merge them using the Blackbaord API.
 * 
 *  -- Courses are defined in the $requests multi-dimentional array.
 *  -- Multiple merge requests can be handled by multiple entries 
 *       into the $requests array
 *  -- Setting the contstants "CREATEMASTER" and/or "RENAMECOURSE" will
 *       skip that step.
 *  -- Due to additional manual steps that are necessary within my
 *      schools SIS system, I rarely run this with more than 10 "main"
 *      courses at a time.  This has the added benefit of not timing out
 *      the token.  
 * --------------------------------------------------------------------
 *  PROCESS OVERVIEW:
 *		* if CREATEMASTER is true, a copy of the first child class 
 *              listed is created to be the master
 * 		* Master course is then put in the correct Node 
 * 		* Next it loops through the list of child classes and:
 * 			-- if RENAMECOURSE is true, the course has "(sec #)" 
 *                appended to the name
 * 			-- all students are removed from the course 
 *                (having students enrolled in multiple child courses
 *                 during merge will lead to it not merging)
 * 			-- child course is merged into the master
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
$workdir = dirname(__FILE__);
if (!$workdir){ die ("Could not get Current Working Directory"); }
// ====================================================================
// getting the list of classes to be merged.
$requests = array (
	//FORMAT:  main_class_id => array (list,of,child,class,ids)

'HS-CAR-TCH050_E.24.m04' => array('HS-CAR-TCH050_E.24.4','HS-CAR-TCH050_E.24.8','HS-CAR-TCH050_E.24.98'),
'HS-CAR-OTH040.24.m06' => array('HS-CAR-OTH040.24.8','HS-CAR-OTH040.24.10'),

// DO not remove next ); .. closes out the $requests array
);
// ====================================================================
include_once ("assets/config.php");
include_once ("assets/Call.php");
include_once ("assets/Auth.php");

define ('DEBUG',false);
define ('CREATEMASTER',true);
define ('RENAMECOURSE',true);

// ====================================================================
//      DO NOT EDIT BELOW THIS POINT
// ====================================================================
function CreateJSONFile ($master) {
	// Create the JSON file needed for course copy

$contents='{
  "targetCourse": {
    "courseId": "'.$master.'"
  },
  "copy": {
    "adaptiveReleaseRules": true,
    "announcements": true,
    "assessments": true,
    "blogs": true,
    "calendar": true,
    "contacts": true,
    "contentAlignments": true,
    "contentAreas": true,
    "discussions": "None",
    "glossary": true,
    "gradebook": true,
    "groupSettings": true,
    "journals": true,
    "retentionRules": true,
    "rubrics": true,
    "settings": {
      "availability": true,
      "bannerImage": true,
      "duration": true,
      "enrollmentOptions": true,
      "guestAccess": true,
      "languagePack": true,
      "navigationSettings": true,
      "observerAccess": true
    },
    "tasks": true,
    "wikis": true
  }
}
';

	return $contents;
}
// ------------------------------------------------------------------

function GetNode($course) {
	global $hierarchyNodes;
	// return the node ID for the given course
	// AS of today, there is no API call to find node of just one course
	// So we just going by the ID string of the given course. (HS/MS/ES)
	$school = strtoupper(substr($course,0,2));
	if (array_key_exists($school,$hierarchyNodes)) {
		return $hierarchyNodes[$school];
	} else {
		return;
	}
}
// ====================================================================
$my_token=GetToken();

$keys = array_keys($requests);
for($z = 0; $z < count($requests); $z++) {
	$master = $keys[$z];
	$children = $requests[$keys[$z]];    

	if (CREATEMASTER) {
		$masterFile = CreateJSONFile($master);
		$url = BaseURL.'/learn/api/public/v2/courses/courseId:'.$children[0].'/copy';
		$c = callAPI('POST',$url,$my_token,$masterFile);
		if (!empty($c)) {
			print "Create Master Course Failed: ".PHP_EOL;
			print_r($c);
			die();
		} else {
			print "SUCCESS: Master Course created".PHP_EOL;
		}
		print "Pausing to allow copy to complete...".PHP_EOL;
		sleep(15);
		// Put Master into the correct node
		$node = GetNode($children[0]);
		if ($node) { // if the node exists, put the master course into that node
			print "Putting Master course ".$master." into Node ".$node .PHP_EOL ;
			$url = BaseURL.'/learn/api/public/v1/institutionalHierarchy/nodes/externalId:'.$node.'/courses/courseId:'.$master;
			$c = callAPI('PUT',$url,$my_token,'{ "isPrimary": true }' );
		}

	}
	
	$i=1;
	foreach ($children as $classId) {
		if (RENAMECOURSE) {
			print "Getting Course $i Name ..." .PHP_EOL;
			//Get course information
			$url = BaseURL.'/learn/api/public/v2/courses/courseId:'.$classId ;
			$c = callAPI('GET',$url,$my_token,'');
			$courseName=$c['name'];
			// Getting Section number from $classId
			$t=explode('.', $classId);
			$courseSection = array_pop($t);
	
			print "Adding ( $courseSection ) to Course $i ( $courseName )..." .PHP_EOL;
			// Rename the course to include the section number
			$file = '{"name":"'.$courseName.' (sec '.$courseSection.') "}';
			$d = callAPI('PATCH',$url,$my_token,$file);
		}
	
		//-------------------------------------
		//     Merge course into Master
		//-------------------------------------
		print "Get course enrollment for ".$classId . PHP_EOL;
		// Before we merge, we need to clean out the enrollment of the child class.  We will  delete all 'students' in the class
		$url = BaseURL.'/learn/api/public/v1/courses/courseId:'.$classId.'/users?role=Student' ;
		$e = callAPI('GET',$url,$my_token,'');
		foreach ($e['results'] as $result ) {
			$stuList[] = $result['userId'];
		}
		if(!empty($stuList)) {
			foreach ($stuList as $userId) {
				print "   Removing User ". $userId . " from Class ".$classId. PHP_EOL;
				$url = BaseURL.'/learn/api/public/v1/courses/courseId:'.$classId.'/users/'.$userId ;
				$f = callAPI('DELETE',$url,$my_token,'');
				// sleep(2);
			}
			unset ($stuList);   // Empty stuList to get it ready for next run
		} else {print "Student list is empty".PHP_EOL ;}
	
		// Now all the students have been removed, we can do the Merge.
		print "Merging $classId into $master" .PHP_EOL;
		$url = BaseURL.'/learn/api/public/v1/courses/courseId:'.$master.'/children/courseId:' . $classId ; 
		$g = callAPI('PUT',$url,$my_token,'');
		print "Pause before processing next course....".PHP_EOL.PHP_EOL;
		sleep(5);
		$i++;
	}
}

print "Process Completed " . PHP_EOL ;

?>
