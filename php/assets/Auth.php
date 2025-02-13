<?php
/*
 * Auth.php
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
function GetToken(){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, BaseURL.'/learn/api/public/v1/oauth2/token' );
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
	curl_setopt($curl, CURLOPT_USERPWD, KEY . ':' . SECRET );


//	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
//		'Accept: application/json',
//		'Content-Type: application/json',
//	));
//	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	// EXECUTE:
	$result = curl_exec($curl);
	if(!$result){die("Connection Failure");}
	curl_close($curl);

	$my_auth = (json_decode($result,true));

	if (array_key_exists('access_token',$my_auth)) {
		return $my_auth['access_token'];
	} elseif (array_key_exists('error',$my_auth)) {
		die ('Authentication Failed: ' . $my_auth['error_description'] );
	} else {
		die ('Authentication Failed: Unknown Issue ');
	}
}

?>
