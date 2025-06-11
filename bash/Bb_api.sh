#!/bin/bash
##  Use the Bb API to request information about a user, course or organization
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
# setting this script to quiet really doesn't make sense, so left at FALSE
QUIET="FALSE"

# =====================================================================
#  Define some locally used variables
## ------------------------------------------------------------------
# Get a TS variable
TIMESTAMP=$(date +%s)
## Default filter for jq command line JSON processor
#      /usr/bin/jq -r "$FILTER"
FILTER='.'

# =====================================================================
function usage_short() {
	echo ""
	echo "	$0 -t -o [user|primary|course|external] -s [userName|User PrimaryID|CourseID|Course ExternalID]"
	echo "	use -h for full help"
	echo ""
}
# =====================================================================
function usage() {
	echo ""
	echo "Use the Blackboard API to get information about a user, course or organization."
	echo "      '-o user' when searching for user information with BB Username "
	echo "      '-o primary' when searching for user information using  "
	echo "                   the primary ID (ex: _##_# ) "
	echo "      '-o course' when using Blackboard Course [or org] ID"
	echo "      '-o external' when using Blackboard Course [or org] external ID"
	echo "      '-o any' allows for any URL string from the API via the -s option"
	echo "               for GET options only"
	echo "      '-s STRING' is to string to search for based on the option"
	echo "      '-t' to search on test server instead of production" 
	echo "      EASTER EGG: use '-o version -s kitty' to get Blackboard Learn version"
	usage_short
}
# =====================================================================

while getopts ":o:s:th" OPTION ; do
	case $OPTION in
		h)
			usage
			exit 1
			;;
		o)
			OPER=$OPTARG
			;;
		s)
			STRING=$OPTARG
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

## Make sure both options have been given, or show usage
if [[ -z $STRING ]] && [[ -z $OPER ]]; then
	usage
	exit 0
fi

## No matter what option, we need a string to search
if [[ -z $STRING ]]; then
	echo "	ERROR: Require a search string"
	usage_short
	exit 1
fi


## Check which "haystack" we are going to search through
if [[ $OPER == "user" ]]; then
	BBID=$STRING
	PATH=learn/api/public/v1/users/userName:$BBID

elif [[ $OPER == "primary" ]]; then
	PRIMARY=$STRING
	PATH=learn/api/public/v1/users/$PRIMARY


## V1 endpoint deprecated - use v2 for version 3400.8.0 and greater
elif [[ $OPER == "course" ]]; then
	COURSEID=$STRING
	PATH=learn/api/public/v2/courses/courseId:$COURSEID

## V1 endpoint deprecated - use v2 for version 3400.8.0 and greater
elif [[ $OPER == "external" ]]; then
	COURSEID=$STRING
	PATH=learn/api/public/v2/courses/externalId:$COURSEID

elif [[ $OPER == "any" ]]; then
	PATH=$STRING


elif [[ $OPER == "version" ]]; then
	# We are going to get blackboard version (no string is acutally needed here)
	NONEED=$STRING
	PATH=learn/api/public/v1/system/version

else 
	echo "	ERROR: Require search option "
	usage_short
	exit 1
fi

## First get authenticated
auth
# Now get the info requested for $STRING
echo "Information for $STRING "
REQUEST="GET"
runcurl

if [[ $DEBUG = TRUE ]]; then
	echo "##------##------##------##------##-------##-------##-------##"
	echo "WARNING: debug files left in /tmp/ - review and/or delete"
	echo "##------##------##------##------##-------##-------##-------##"
fi


exit
