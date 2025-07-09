<?php
/* Add your own URL, Key and Secret below, then save file as:
 *      config.php
 */
// ====================================================================
## CONSTANTS -- My Application Keys
define ('BaseURL',"https://YOUR.BB_URL.com");
define ('KEY',"YOUR-APP-KEY-GOES-HERE");
define ('SECRET',"YOUR_OWN_SPECIAL_SECRET_GOES_HERE");
// ====================================================================
## COMMON VARIABLES --  Things that I want to only set once 

// List the school code and Node listed in the BB Hierarchy
// The following are used for my "MergeCourses.php" script.
$hierarchyNodes = array (
	'HS' => 'node-high-parent01',
	'MS' => 'node-middle-parent01',
	'ES' => 'node-elem-parent01',
	'DEMO' => 'node-sandbox' 
);


?>
