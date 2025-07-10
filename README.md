# Bb_api_scripts

The repo holds all of the various  REST-API scripts I use/have written
as the Blackboard admin for PA Virtual Charter School.    Most (all?) 
of these scripts are not for daily syncing, but for tasks that make 
my life as an admin easier. i.e.: These scripts are not meant to be
run on some form of automation, there is little to no error handling.
For the most part, errors kill the process, and require you to review
the error.

While I have done my best to make the scripts generic, the are 100% 
designed around the idea of my workflow, and my schools policies,
naming conventions, etc.  I highly suggest you review before using!!!!

========================================================================

How to get started with Blackboard API:
   https://developer.anthology.com/ 

The Bb API reference:
   https://developer.anthology.com/portal/displayApi

You must set up Bb REST-API integration before using these scripts

Each directory has it's own README file for specific directions

========================================================================

------------------------------------
BASH 
------------------------------------
the "bash" directory contains a collection of BASH scripts.  These are 
the scripts I use most often, and most are 'non-damaging' - ie. they 
either get info, or make a change that can be very easily undone (such
as make a user 'unavailable')

Requirements for the Linux Scripts: 
  - Bb REST-API access
  - All scripts are writing in bash (Linux command line)
      Must have `curl` and `jq` installed
  - Put your own credentials and URLs in the Bb_api-creds.example and
      rename to "Bb_api-creds.sh"
  - Review the "Bb_api-resource.sh" file, in particular the regex 
      for "USER_CHECK_STRING"
      (If you need some help testing your regex: https://regex101.com/ )

------------------------------------
PHP
------------------------------------
The "php" directory contains a collection of PHP based scripts. These 
are run from the command line. These are the scripts that can be a bit 
more 'damaging" and/or are major batch action style jobs (such as 
running through loops, logic checks for processing, etc)

Before using, you will need to edit the assets/config.example.php file.
   - put in your own URL, Key and Secret in the CONSTANTS section
   - Review the COMMON VARIABLES section to adjust for your own needs
   - save file as "config.php"  

Some of the scripts are specifically designed and included to 
illustrate the process.  I did my best to keep the code simple, a
provide a lot of notes/comments.  They should be easy enough to read 
for novices to get an idea and (hopefully) allow you to come up with 
your own scripts based on your own schools needs.
  - FixLang.php -- Takes a username as input, creates the necessary JSON 
      file to send along with the API call. 
      - Sends the "PATCH" command to update a user record.
  - UserInfo.php -- Takes a username as input, and returns the user's
      JSON info file as a PHP array.
      - Sends the "GET" command option
  - ListCourses.php -- example of how to work with paging data return, 
      and can create a CSV file of course IDs
      - Sends the "GET" command option, with multiple result return
  - DeleteCourses.php -- Takes a CSV as input
      - Sends the "DELETE" option 

------------------------------------
POWERSHELL
------------------------------------
When I was working on my AT25 conference proposal, I thought I would 
convert a number of my bash scripts over to Powershell.  While trying 
to do so, I found that my Powershell scripting skills had gotten VERY 
VERY rusty, and it was just too much effort for the time I had, and 
considering I don't use nor will I be using Powershell scripts, the 
amount of work was not going to be worth it.  

[I also  realized that Windows users could use the PHP scripts as 
presented, so this became even less of a "must do"]

If anyone wants to convert, I am happy to hear from you.
