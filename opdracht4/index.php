<?php

require_once('../Slim/Slim.php');

\Slim\Slim::registerAutoloader();

$slim = new \Slim\Slim();

$slim->get('/dieren', function(){
	print(json_encode('dieren'));
});

$slim->get('/dieren/:id', function($id){
	print(json_encode($id));
});

$slim->run();