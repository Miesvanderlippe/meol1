<?php

require_once('../Slim/Slim.php');
require_once('classes/database.php');
require_once('classes/crypt.php');

\Slim\Slim::registerAutoloader();

$slim 	 = new \Slim\Slim();
$db 	 = new DB();

$slim->get('/dieren/:pub', function($pub)
	use($slim, $db){
		$publicKey  = $pub;
		$hasPrivateKey = $db->HasPrivateKey($publicKey);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$dieren 	 = $db->GetAllAnimals();
		
		print(json_encode($dieren));
	}
);

$slim->get('/generate', function()
	use($slim, $db){
		if(true){

		$result 	 = $db->GenerateKeypair();
		
		print(json_encode($result));
	}
});

$slim->post('/dieren/:pub', function($pub)
	use($slim, $db){
		
		$publicKey  = $pub;
		$hasPrivateKey = $db->HasPrivateKey($publicKey);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$name		 = $slim->request()->post('name');
        $kind		 = $slim->request()->post('kind');
        $yearOfBirth = $slim->request()->post('yearOfBirth');
        $ownerID	 = $slim->request()->post('ownerID');

		$result		 = $db->AddAnimal($name, $kind, $yearOfBirth, $ownerID);

		print(json_encode(array('result'=>$result)));
});

$slim->get('/dieren/:id/:pub', function($id, $pub)
	use($db){

		$publicKey  = $pub;
		$hasPrivateKey = $db->HasPrivateKey($publicKey);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$dieren 	 = $db->GetAnimal($id);
		
		print(json_encode($dieren));
	}
);

$slim->get('/dieren/:id/eigenaar/:pub', function($id, $pub)
	use($db){
		$publicKey  = $pub;
		$hasPrivateKey = $db->HasPrivateKey($publicKey);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$dieren 	 = $db->GetOwnerByPet($id);
		
		print(json_encode($dieren));
	}
);

$slim->get('/eigenaars/:pub', function($pub)
	use($db){
		$publicKey  = $pub;
		$hasPrivateKey = $db->HasPrivateKey($publicKey);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$eigenaars 	 = $db->GetAllOwners();
		
		print(json_encode($eigenaars));
	}
);

$slim->post('/owners/:pub', function($pub)
	use($slim, $db){

		$publicKey  = $pub;
		$hasPrivateKey = $db->HasPrivateKey($publicKey);
		
		if(!$hasPrivateKey)
			$slim->notFound();
		
		$firstName	 = $slim->request()->post('firstName');
        $affix		 = $slim->request()->post('affix');
        $lastName	 = $slim->request()->post('lastName');
        $city		 = $slim->request()->post('city');

		$result		 = $db->AddOwner($firstName, $affix, $lastName, $city);

		print(json_encode(array('result'=>$result)));
});

$slim->get('/eigenaars/:id/:pub', function($id, $pub)
	use($db){
		$publicKey  = $pub;
		$hasPrivateKey = $db->HasPrivateKey($publicKey);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$eigenaar 	 = $db->GetOwner($id);
		
		print(json_encode($eigenaar));
	}
);

$slim->get('/eigenaars/:id/dieren/:pub', function($id, $pub)
	use($db){
		
		$publicKey  = $pub;
		$hasPrivateKey = $db->HasPrivateKey($publicKey);
		
		if(!$hasPrivateKey)
			$slim->notFound();
		$eigenaar 	 = $db->GetAnimalsByOwner($id);
		
		print(json_encode($eigenaar));
	}
);


$slim->run();