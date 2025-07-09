BASH README
====================================

These are the scripts I use most often, and most are 'non-damaging' 
- ie. they either get info, or make a change that can be very easily
undone (such as make a user 'unavailable')

------------------------------------

Requirements for the Linux Scripts: 
  - Bb REST-API access
  - All scripts are writing in bash (Linux command line)
		  Must have `curl` and `jq` installed
  - Put your own credentials and URLs in the Bb_api-creds.example and
      rename to "Bb_api-creds.sh"
  - Review the "Bb_api-resource.sh" file, in particular the regex 
      for "USER_CHECK_STRING"
	(If you need some help testing your regex:  https://regex101.com/  )
