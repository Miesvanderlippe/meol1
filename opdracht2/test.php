<?php

$url = 'http://localhost:8080/opdracht2/index.php/kat/123';

$fields = 'data1=YOLO&data2=RIGHT';

/*
	GET
*/
$curl	 = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$GET	 = curl_exec($curl);

curl_close($curl);

/*
	POST
*/
$curl	 = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$POST	 = curl_exec($curl);

curl_close($curl);

/*
	PUT
*/

$curl	 = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$PUT = curl_exec($curl);

curl_close($curl);

/*
	DELETE
*/

$curl	 = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$DELETE	 = curl_exec($curl);

curl_close($curl);

print($GET);
print('<br/>');
print($POST);
print('<br/>');
print($PUT);
print('<br/>');
print($DELETE);