<?php

require_once('../Slim/Slim.php');
require_once('classes/database.php');
require_once('classes/crypt.php');

\Slim\Slim::registerAutoloader();

$slim 	 = new \Slim\Slim();
$db 	 = new DB();

$slim->get('/dieren', function()
	use($db){

	$dieren 	 = $db->GetAllAnimals();
	
	print(json_encode($dieren));
});

$slim->get('/generate', function()
	use($slim, $db){
		if(true){

		$result 	 = $db->GenerateKeypair();
		
		print(json_encode($result));
	}
});

$slim->get('/test/:key', function($key)
	use($slim, $db){
		if(true){
			$result = $db->GetPrivateKey($key);
			print($result);
		}
	}
);



$slim->post('/dieren', function()
	use($slim, $db){
		
		$name		 = $app->request()->post('name');
        $kind		 = $app->request()->post('kind');
        $yearOfBirth = $app->request()->post('yearOfBirth');
        $ownerID	 = $app->request()->post('ownerID');

		$result		 = $db->AddAnimal($name, $kind, $yearOfBirth, $ownerID);

		print(json_encode(array('result'=>$result)));
});

$slim->get('/dieren/:id', function($id)
	use($db){

	$dieren 	 = $db->GetAnimal($id);
	
	print(json_encode($dieren));
});

$slim->get('/dieren/:id/eigenaar', function($id)
	use($db){

	$dieren 	 = $db->GetOwnerByPet($id);
	
	print(json_encode($dieren));
});

$slim->get('/eigenaars', function()
	use($db){

	$eigenaars 	 = $db->GetAllOwners();
	
	print(json_encode($eigenaars));
});

$slim->post('/owners', function()
	use($slim, $db){
		
		$firstName	 = $app->request()->post('firstName');
        $affix		 = $app->request()->post('affix');
        $lastName	 = $app->request()->post('lastName');
        $city		 = $app->request()->post('city');

		$result		 = $db->AddOwner($firstName, $affix, $lastName, $city);

		print(json_encode(array('result'=>$result)));
});

$slim->get('/eigenaars/:id', function($id)
	use($db){

	$eigenaar 	 = $db->GetOwner($id);
	
	print(json_encode($eigenaar));
});

$slim->get('/eigenaars/:id/dieren', function($id)
	use($db){

	$eigenaar 	 = $db->GetAnimalsByOwner($id);
	
	print(json_encode($eigenaar));
});


$slim->run();