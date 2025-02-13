#!/bin/bash
## TEMPLATE FILE for Bb_API Scripts
##   Add some details about the file here
##   can do search for <TODO> to find locations of customizing
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
VERBOSE="TRUE"
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
  "availability": {
    "available": "No"
  }
}
'
# =====================================================================
function usage() {
	echo ""
	echo "Use the Blackboard API to DO SOME STUFF <TODO>  ."
	echo "      '-s' the string that would be passed                  "
	echo "      '-t' to operate on test server instead of production" 
	usage_short
}
# =====================================================================
function usage_short() {
	echo ""
	echo "	$0 -t -s [STRING] <TODO>"
	echo "	use -h for full help"
	echo ""
}
# =====================================================================
while getopts ":s:th" OPTION ; do
	case $OPTION in
		h)
			usage
			exit 1
			;;
		S)
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

# -------------------------------------------
## verify the user input <TODO>
if [[ ! -z $STRING ]]; then
	echo "	ERROR: STRING required"
	usage_short
	exit 1
fi

### <TODO> IF YOU need a temp file, make sure it starts with /tmp/bb_api_
#Set the location of the json file
## JSON_FILE=$(/bin/mktemp /tmp/bb_api_Unavail_XXXXXXXX.json)
## echo $BLANKJSON > $JSON_FILE

## Get authentication Token
auth
# <TODO> Set the curl request type for the API
REQUEST=PATCH
# <TODO> Set the URL path for Curl
PATH=learn/api/public/v1/users/userName:$BBID
#Send to the curl function
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
