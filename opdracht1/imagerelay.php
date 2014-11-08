<?php

/*
	Little script to add private bits to the url and relay the image found there.
	This results in never displaying your API key to the public.

	This script is incomplete and just a proof of concept that you can indeed hide private keys.
	It needs checks for everything and access restrictions
*/

$apikey		 = "b20a23e4857a0c68";
$apiurl		 = "http://api.wunderground.com/api/";

$urlPrefix	 = $apiurl . $apikey;
$urlSuffix	 = urldecode($_GET['url']);

$remoteImage = $urlPrefix . $urlSuffix;
$imginfo	 = getimagesize($remoteImage);
header("Content-type: " . $imginfo['mime'] );
readfile($remoteImage);