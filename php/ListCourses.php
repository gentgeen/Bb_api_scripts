<?php
/*
 * ListCourses.php
 * ====================================================================
 * MY ATTEMPT A DOCUMENTING/EXPLAINING WHAT HAPPENS AND HOW THIS WORKS
 * 
 * When you are returned data from the API call that gives you multiple
 * results (such as 'list all users' vs 'give me just 1 user'), you get
 * a multi-dimensional array where the first key is "results", and the
 * value is the multi-dimensional array for the items.
 * 
 * If your list of items includes MORE than 100 results, then the API 
 * breaks up the results, and adds a second key of "paging", and the 
 * value is the URL to send to get the next set of results.
 * 
 * 
 * array(
 * 		[results] = array (
 * 			[0] = array ( FIRST RESULT )
 * 			[1] = array ( SECOND RESULT )
 * 			[2] = array ( THIRD RESULT )
 * 			...
 * 			...
 * 			[99] = array (ONE HUNDREDTH RESULT)
 * 		)
 * 		[paging] = array (
 * 			[nextPage] = path for next page of results
 * 		)
 * 
 * The "paging" array will only show up if there are more results, so
 * testing 'isset' will allow you determine if you need to make an
 * additional call for next page of results.
 * 
 * NOTE: when you make your next call, that results array will reset to 
 * zero, so you can not simply 'merge' the different result arrays. I 
 * overcame this with a foreach loop, where I looped through 
 * the results array each time, with each record of the results array
 * being added to a master 'list array'
 * 
 */
// ====================================================================
include_once ("assets/config.php");
include_once ("assets/Call.php");
include_once ("assets/Auth.php");

define ('DEBUG',false);
// ====================================================================
// VARIABLES
$list = array(); // initiate the output array 
$runme = TRUE; // while this is true, we will keep making API calls using the 'nextPage' url.
               // once the paging is done, we set this to FALSE

// $url = BaseURL.'/learn/api/public/v3/courses;						// show BOTH courses and organizations
// $url = BaseURL.'/learn/api/public/v3/courses?organization=true';		// show  ONLY organizations
// $url = BaseURL.'/learn/api/public/v3/courses?organization=false';	// show ONLY courses
// $url = BaseURL.'/learn/api/public/v3/courses?courseId=DEMO';			// show courses or organizations that have "DEMO" in the courseID string

$url = BaseURL.'/learn/api/public/v3/courses?organization=false&courseId=DEMO';		// show ONLY courses that have "DEMO" in the course ID string

// ====================================================================
// FUNCTIONS

// --------------------------------------------------------------------
// ====================================================================

$my_token=GetToken();

// start running our while loop.  as long as we have another page of
// of results, we want to (a) put the results into our master list and
// (b) run the CURL command to get the next page of results

while ($runme) {
	// Send the Curl command 
	$c = callAPI('GET',$url,$my_token,'' );
	// Take our list of results, and put them into the master list
	foreach ($c['results'] as $record ) {
		$list[] = $record;
	}
	// Check  if there is another page of results or not
	if (isset($c['paging'])) {
		// we have more pages
		$runme = TRUE;
		$url = BaseURL . $c['paging']['nextPage'];
	} else {
		// we are out of pages, so we don't need to run this while loop again
		$runme = FALSE;
	}
}

// ---- couple of different output options -------
// Print the raw array
//print_r($list);

// Get the number of results in the master list, and print that total out
//$num = count($list);
//print "Total Number of Results: ".$num . PHP_EOL;

// print a CSV file with 3 columns: "CourseID","created date","Availability" 
foreach($list as $num => $course ) {
	print '"'. $course['courseId'] . '","' . $course['created'] . '","' . $course['availability'] ['available'] . '"' . PHP_EOL ;
}

?>
