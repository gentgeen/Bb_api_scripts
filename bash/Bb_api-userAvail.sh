#!/bin/bash
## Make a user available/unavailable.  
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
AVAIL_JSON='{ "availability": { "available": "Yes" } } '

# =====================================================================
function usage() {
	echo ""
	echo "Use the Blackboard API to make a user account available or unavailable."
	echo "      '-s [avail|unavail]' Make the user status available or unavailable " 
	echo "      '-u USERNAME|FILE' The BB Username or the file contains a list of Blackboard User IDs"
	echo "      '-t' to operate on test server instead of production" 
	usage_short
}
# =====================================================================
function usage_short() {
	echo ""
	echo "	$0 -t -s [avail|unavail] -u [USERNAME|FILE] "
	echo "	use -h for full help"
	echo ""
}
# =====================================================================
while getopts ":s:u:th" OPTION ; do
	case $OPTION in
		h)
			usage
			exit 1
			;;
		s)
			STATUS=$OPTARG
			;;
		u)
			USER_LIST=$OPTARG
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
if [[ -z $STATUS ]] && [[ -z $USER_LIST ]]; then
		echo "ERROR: Both Status and User are required "
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

if [[ -r $USER_LIST ]]; then
	# Does the user_list exist, and readable by 'me'
	while read -r BBID; do
		# Set the URL path for the API
		PATH=learn/api/public/v1/users/userName:$BBID
		#Send to the curl function
		runcurl
	done <$USER_LIST
elif [[ $USER_LIST =~ ${USER_CHECK_STRING} ]] ; then
	# matches single user string
	# Set the URL path for the API
	PATH=learn/api/public/v1/users/userName:$USER_LIST
	#Send to the curl function
	runcurl
else 
	echo "ERROR: User option is invalid"
	usage_short
	exit 1
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
