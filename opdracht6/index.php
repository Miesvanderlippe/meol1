<?php

require_once('../Slim/Slim.php');
require_once('/classes/apiinteraction.php');

\Slim\Slim::registerAutoloader();

$slim 	 = new \Slim\Slim();

$slim->run();