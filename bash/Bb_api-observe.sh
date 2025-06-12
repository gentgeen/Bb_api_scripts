#!/bin/bash
## Check the Observer/Observee status of a given user
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
## For my needs, the default output of observer/observee API was too noisy
## Use the option below to filter output to  only be the fields listed
## Leaving this blank will will show all fields. 
FIELDS="userName,availability"

# =====================================================================
function usage() {
	echo ""
	echo "Use the Blackboard API to check the users assigned observers or observees."
	echo "      '-r [parent|child]' the users role"
	echo "           'parent' refers to those users that have observees."
	echo "           'child' refers to those users that have observers."
	echo "      '-u BBID' Blackboard ID of the user."
	echo "      '-t' to operate on test server instead of production" 
	usage_short
}
# =====================================================================
function usage_short() {
	echo ""
	echo "	$0 -t -r [parent|child] -u BBID "
	echo "	use -h for full help"
	echo ""
}
# =====================================================================
while getopts ":r:u:th" OPTION ; do
	case $OPTION in
		h)
			usage
			exit 1
			;;
		r)
			ROLE=$OPTARG
			;;
		u)
			BBUSER=$OPTARG
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
if [[ -z $ROLE ]] && [[ -z $BBUSER ]]; then
	usage
	exit 0
fi

## Verify that the username fits our username format
if [[ $BBUSER =~ ${USER_CHECK_STRING} ]]; then
	## Get authentication Token
	auth
	# Set the curl request type for the API
	REQUEST=GET
	# Set the URL path for Curl
	if [[ $ROLE == "parent" ]]; then
		PATH=/learn/api/public/v1/users/userName:$BBUSER/observees?fields=$FIELDS
	elif [[ $ROLE == "child" ]]; then
		PATH=/learn/api/public/v1/users/userName:$BBUSER/observers?fields=$FIELDS
	else
		echo "ERROR: Invalid Role option"
		usage_short
		exit 1
	fi
	#Send to the curl function
	runcurl
else
	echo "	ERROR: Username format is incorrect"
	usage_short
	exit 1
fi

exit
