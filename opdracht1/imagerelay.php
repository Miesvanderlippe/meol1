<?php

$apikey = "b20a23e4857a0c68";
$apiurl = "http://api.wunderground.com/api/";

$urlPrefix = $apiurl . $apikey;
$urlSuffix = urldecode($_GET['url']);

$remoteImage = $urlPrefix . $urlSuffix;
$imginfo = getimagesize($remoteImage);
header("Content-type: " . $imginfo['mime'] );
readfile($remoteImage);