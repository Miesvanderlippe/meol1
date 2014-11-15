<?php

//Get controller
require_once("rest.php");
//Strip URL
$url		 =	$_SERVER["REQUEST_URI"];
$baseUrl	 =	$_SERVER["SCRIPT_NAME"];
$url		 =	str_replace($baseUrl, '', $url);
//URL -> Array
$urlArray	 =	array();
$urlArray	 =	explode('/', $url);
//Clean up array
if($urlArray[0] == '' || empty($urlArray[0]))
	array_shift($urlArray);

$rest = new REST();

$method = $rest->method;

$JSONVals = array(
	"method"=>$method,
	"commando"=>(isset($urlArray[0])? $urlArray[0] : Null),
	"value"=>(isset($urlArray[1])? $urlArray[1] : Null)
);

$JSONData = json_encode($JSONVals);

print($JSONData);