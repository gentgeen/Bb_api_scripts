#!/bin/bash
## Make a course unavailable or "by term" .  
##  Can be done with single user from command line, or via a CSV for bulk actions
## ------------------------------------------------------------------
## Get the credentials/my Application Keys
source Bb_api-creds.sh
## Get shared resources
source Bb_api-resource.sh

# =====================================================================
#  Define some output variables
#     Setting QUIET to TRUE and the other two to FALSE should result in nothing on screen
#
#     Each is independent of other - so setting DEBUG to true does not necessary set VERBOSE to true
#  QUIET = send curls normal output to /dev/null
#  VERBOSE = assorted notes and visual feedback that the script is running
#  DEBUG = creates /tmp/bb_api_xxxx files for review 
## ------------------------------------------------------------------
VERBOSE="FALSE"
DEBUG="FALSE"
QUIET="FALSE"

# =====================================================================
#  Define some locally used variables
## ------------------------------------------------------------------
# Get a TS variable
TIMESTAMP=$(date +%s)
## Default filter for jq command line JSON processor
#      /usr/bin/jq -r "$FILTER"
FILTER='.'

## ------------------------------------------------------------
UNAVAIL_JSON='{ "availability": { "available": "No" } } '
AVAIL_JSON='{ "availability": { "available": "Term" } } '
###  at our school, "term" is the default/preferred availability. If 
###  your school just uses the Y/N option, you can change the above to "Yes"
###        AVAIL_JSON='{ "availability": { "available": "Yes" } } '

# =====================================================================
function usage() {
	echo ""
	echo "Use the Blackboard API to make a course unavailable or 'available by term'."
	echo "      '-s [avail|unavail]' Make the status 'available by term' or unavailable " 
	echo "      '-c COURSE|FILE' The BB CourseID or the file contains a list of Blackboard CourseIDs"
	echo "      '-t' to operate on test server instead of production" 
	usage_short
}
# =====================================================================
function usage_short() {
	echo ""
	echo "	$0 -t -s [avail|unavail] -c [COURSEID|FILE] "
	echo "	use -h for full help"
	echo ""
}
# =====================================================================
while getopts ":s:c:th" OPTION ; do
	case $OPTION in
		h)
			usage
			exit 1
			;;
		s)
			STATUS=$OPTARG
			;;
		c)
			COURSE_LIST=$OPTARG
			;;
		t)
			URL=$TESTURL
			;;
		?)
			echo "	ERROR: Unknown option \" -$OPTARG\" "
			usage_short
			exit 1
			;;
	esac
done

# -------------------------------------------
## Make sure both options have been given, or show usage
if [[ -z $STATUS ]] && [[ -z $COURSE_LIST ]]; then
		echo "ERROR: Both Status and Course are required "
		usage_short
		exit 1
fi

#Set the location of the json file
JSON_FILE=$(/bin/mktemp /tmp/bb_api_avail_XXXXXXXX.json)

## Make sure status is either "avail" or "unavail", and set JSON file accordingly
if [[ $STATUS == "avail" ]]; then
	echo $AVAIL_JSON > $JSON_FILE
elif [[ $STATUS == "unavail" ]]; then
	echo $UNAVAIL_JSON > $JSON_FILE
else
	echo "ERROR: status must be either 'avail' or 'unavail'"
	usage_short
	exit 1
fi

## Get authentication Token
auth
## Set the curl request type for the API
REQUEST=PATCH

if [[ -r $COURSE_LIST ]]; then
	# Does the COURSE_LIST exist, and readable by 'me'
	while read -r ID; do
		# Set the URL path for the API
		PATH=learn/api/public/v2/courses/courseId:$ID
		#Send to the curl function
		runcurl
	done <$COURSE_LIST
else
	# Set the URL path for the API
	PATH=learn/api/public/v2/courses/courseId:$COURSE_LIST
	#Send to the curl function
	runcurl
fi

## --------------------------------------------------------------------

if [[ $DEBUG = TRUE ]]; then
	echo "##------##------##------##------##-------##-------##-------##"
	echo "WARNING: debug files left in /tmp/ - review and/or delete"
	echo "##------##------##------##------##-------##-------##-------##"
else
	## Remove temp file
	/bin/rm -f $JSON_FILE
fi
exit
