<?php
/*
 * Call.php
 * 
 * Copyright 2021 Kevin Squire <gentgeen@wikiak.org>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */

// --------------------------------------------------------------------
function callAPI($method, $url, $token, $data){
   $curl = curl_init();
   switch ($method){
      case "GET":
         curl_setopt($curl,CURLOPT_CUSTOMREQUEST, "GET");
         break;
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         break;
      case "PATCH":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
         break;
      case "DELETE":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
         break;

      default:
         if ($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
   }

   if ($data) { curl_setopt($curl, CURLOPT_POSTFIELDS, $data); }

   // OPTIONS:
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLINFO_HEADER_OUT, true);
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Authorization: Bearer '.$token ,
      'Accept: application/json',
      'Content-Type: application/json')
    );
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	if (DEBUG) {
		$debug = curl_getinfo($curl);
		print_r($debug);
		print PHP_EOL . PHP_EOL . PHP_EOL . "=========================" .PHP_EOL;
	}

   // EXECUTE:
   $result = curl_exec($curl);
//   if(!$result){die("Connection Failure to ". $url . PHP_EOL );}
   curl_close($curl);

	$out = (json_decode($result,true));

	checkResponse($out);

	return $out;
}

// -------------------------------------------------------------------

function checkResponse($income) {
	if (is_null($income)) {
		print "Returning JSON data is null";
		return;
	}
	// now check for the normal 'error' messages. 
	if (array_key_exists('error',$income)) {
		die ('ERROR 001: ' . $income['error_description'] . PHP_EOL );
	} elseif ( array_key_exists('status',$income) && $income['status'] >= '400' )  {
		die ('ERROR 002: '.$income['message'] . PHP_EOL);
		// print 'ERROR 002: '.$income['message'] . PHP_EOL;
	}
	return;
}


?>
