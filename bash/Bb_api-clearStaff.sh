#!/bin/bash
## Clear out contact and title information for staff members
## ------------------------------------------------------------------
## Get the credentials/my Application Keys
source Bb_api-creds.sh
## Get shared resources
source Bb_api-resource.sh

# =====================================================================
#  Define some output variables
#     Setting all three to "FALSE" should result in nothing on screen
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
BLANKJSON='
{
  "job": {
    "title": "",
    "department": "",
    "company": "PA Virtual Charter School"
  },
  "contact": {
    "homePhone": "",
    "mobilePhone": "",
    "businessPhone": "",
    "businessFax": ""
  },
  "address": {
    "street1": "",
    "street2": "",
    "city": "",
    "state": "",
    "zipCode": "",
    "country": ""
  }
}
'
# =====================================================================
function usage() {
	echo ""
	echo "Use the Blackboard API to clear out a users title and unneccessary contact information."
	echo "      '-u USER' The Blackboard User ID of the user"
	echo "      '-t' to operate on test server instead of production" 
	usage_short
}
# =====================================================================
function usage_short() {
	echo ""
	echo "	$0 -t -u [UserID] "
	echo "	use -h for full help"
	echo ""
}
# =====================================================================
while getopts ":u:th" OPTION ; do
	case $OPTION in
		h)
			usage
			exit 1
			;;
		u)
			BBID=$OPTARG
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
## we need a user to update
if [[ -z $BBID ]]; then
	echo "	ERROR: BB User ID is required"
	usage_short
	exit 1
else
	# Now that we have a user, we can set the path
	PATH=learn/api/public/v1/users/userName:$BBID
	REQUEST=PATCH
fi
# -------------------------------------------
#Set the location of the json file
JSON_FILE=$(/bin/mktemp /tmp/bb_api_Unavail_XXXXXXXX.json)
echo $BLANKJSON > $JSON_FILE

# -------------------------------------------
## Get authentication Token
auth
## run the specific API call
runcurl



if [[ $DEBUG = TRUE ]]; then
	echo "##------##------##------##------##-------##-------##-------##"
	echo "WARNING: debug files left in /tmp/ - review and/or delete"
	echo "##------##------##------##------##-------##-------##-------##"
else
	## Remove temp file
	/bin/rm -f $JSON_FILE
fi
exit


