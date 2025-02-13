

# regular expression to check individual user string
##  This example matches ID ( _##_# ):  USER_CHECK_STRING="^_[0-9]{1,6}_[0-9]$"
##  This example matches PAVCS style usernames:  USER_CHECK_STRING="^[a-zA-Z]+[0-9]{0,3}p?$"
USER_CHECK_STRING="^[a-zA-Z]+[0-9]{0,3}p?$"


## ============================================================
function auth() {
	## Authentication Token from Bb.
	if [[ $VERBOSE = TRUE ]]; then 
		local OPT='--progress-bar'
	else
		local OPT='--silent'
	fi

	if [[ $VERBOSE = TRUE ]]; then echo "Get Token..."; fi
	TOKEN=`/usr/bin/curl $OPT --user $KEY:$SECRET --data "grant_type=client_credentials"  $URL/learn/api/public/v1/oauth2/token | /usr/bin/jq -r ".access_token"`

	if [[ $DEBUG = TRUE ]]; then
		# Create a token file for debugging later
		TOKEN_FILE=$(/bin/mktemp /tmp/bb_api_token.XXXXXXXX)
		# Run the curl command again, but this time save everything to the TOKEN_FILE
		TOKEN_FULL=`/usr/bin/curl $OPT --user $KEY:$SECRET --data "grant_type=client_credentials" $URL/learn/api/public/v1/oauth2/token --output $TOKEN_FILE`
		echo "Token File is: $TOKEN_FILE "
	fi
}

# =====================================================================
function runcurl() {
	if [[ $VERBOSE = TRUE ]]; then 
		local OPT='--progress-bar'
		echo "URL is: $URL/$PATH "
	else
		local OPT='--silent'
	fi

	if [[ $QUIET = TRUE ]]; then 
		local QUIET_OPT='--output /dev/null'
	else
		local QUIET_OPT=''
	fi



	if [[ $DEBUG = TRUE ]]; then
		echo "Curl options: $OPT"
		echo "Request mode: $REQUEST"
		echo "Token is: $TOKEN"
		echo "The JSON file is: $JSON_FILE"
		echo "URL is: $URL/$PATH "
	fi

	if [[ -f $JSON_FILE ]]; then
		/usr/bin/curl $OPT $QUIET_OPT --show-error -X $REQUEST -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" -d @"$JSON_FILE" $URL/$PATH | /usr/bin/jq -r "$FILTER"
	else 
		/usr/bin/curl $OPT $QUIET_OPT --show-error -X $REQUEST -H "Authorization: Bearer $TOKEN" $URL/$PATH | /usr/bin/jq -r "$FILTER"
	fi

}

# =====================================================================
# I don't think I need this anywhere, but just in case I do, moving it 
# to it's very own function.
function getTTL() {    
	# Grab the tokens "time to live" (in seconds)
	TTL=`/usr/bin/curl $OPT --user $KEY:$SECRET --data "grant_type=client_credentials"  $URL/learn/api/public/v1/oauth2/token | /usr/bin/jq -r ".expires_in"`
	echo "TimeToLive is: $TTL seconds"
}
