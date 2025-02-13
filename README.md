# Bb_api_scripts

The repo holds all of the various  REST-API scripts I use/have written
as the Blackboard admin.    Most (all?) of these scripts are not for
daily syncing, but for tasks that make my life as an admin easier.

How to get started with Blackboard API:
   https://developer.anthology.com/ 
    
The Bb Api reference:
   https://developer.anthology.com/portal/displayApi

You must set up Bb REST-API Access before using.

Each directory has it's own README file for specific directions

Linux Scripts
====================================
the "bash" directory contains a collection of BASH scripts.  These are 
the scripts I use most often, and most are 'non-damaging' - ie. they 
either get info, or make a change that can be very easily undone (such
as make a user 'unavailable'

Requirements for the Linux Scripts: 
  - Bb REST-API access
  - All scripts are writing in bash (Linux command line)
		  Must have `curl` and `jq` installed
  - Put your own credentials and URLs in the Bb_api-creds.example and
      rename to "Bb_api-creds.sh"

PHP Scripts
====================================
The "php" directory contains a collection of PHP based scripts. These
are still run from the command line.  These are the scripts that can 
be a bit more 'damaging" and/or are major batch action style jobs.

Requirements for the PHP Scripts:
   - TODO
   - (PHP, PHP_Curl libary, ??? )
   - modify the assets/config.example.php file 

PowerShell Scripts
====================================
Currently don't have any, but creating the folder as a placeholder for now.

