<?php

require_once('../Slim/Slim.php');

\Slim\Slim::registerAutoloader();

$slim = new \Slim\Slim();

$slim->get('/', function(){
	print('Slim werkt');
});

$slim->get('/test', function(){
	print('Nog een test ofzo');
});

$slim->run();