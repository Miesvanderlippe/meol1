<?php

require_once('../Slim/Slim.php');
require_once('classes/database.php');
require_once('classes/crypt.php');

\Slim\Slim::registerAutoloader();

$slim 	 = new \Slim\Slim();
$db 	 = new DB();

$slim->get('/dieren/:pub/:time/:hash', function($pub, $time, $hash)
	use($slim, $db){

		if(!$db->CheckTimeStamp($time))
			$slim->notFound();

		$hasPrivateKey = $db->HasPrivateKey($pub);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$hashVars = array(
			'page'=>'dieren',
			'timestamp'=>$time
		);

		if(!$db->CheckHash($pub, $hash, $hashVars))
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

$slim->post('/dieren/:pub/:time/:hash', function($pub, $time, $hash)
	use($slim, $db){
		
		if(!$db->CheckTimeStamp($time))
			$slim->notFound();

		$hasPrivateKey = $db->HasPrivateKey($pub);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$hashVars = array(
			'page'=>'dieren',
			'timestamp'=>$time
		);

		if(!$db->CheckHash($pub, $hash, $hashVars))
			$slim->notFound();

		$name		 = $slim->request()->post('name');
        $kind		 = $slim->request()->post('kind');
        $yearOfBirth = $slim->request()->post('yearOfBirth');
        $ownerID	 = $slim->request()->post('ownerID');

		$result		 = $db->AddAnimal($name, $kind, $yearOfBirth, $ownerID);

		print(json_encode(array('result'=>$result)));
});

$slim->get('/dieren/:id/:pub/:time/:hash', function($id, $pub, $time, $hash)
	use($slim, $db){

		if(!$db->CheckTimeStamp($time))
			$slim->notFound();

		$hasPrivateKey = $db->HasPrivateKey($pub);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$hashVars = array(
			'page'=>'dieren',
			'id'=>$id,
			'timestamp'=>$time
		);

		if(!$db->CheckHash($pub, $hash, $hashVars))
			$slim->notFound();

		$dieren 	 = $db->GetAnimal($id);
		
		print(json_encode($dieren));
	}
);

$slim->get('/dieren/:id/eigenaar/:pub/:time/:hash', function($id, $pub, $time, $hash)
	use($slim, $db){
		
		if(!$db->CheckTimeStamp($time))
			$slim->notFound();

		$hasPrivateKey = $db->HasPrivateKey($pub);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$hashVars = array(
			'page'=>'dieren',
			'id'=>$id,
			'timestamp'=>$time
		);

		if(!$db->CheckHash($pub, $hash, $hashVars))
			$slim->notFound();

		$dieren 	 = $db->GetOwnerByPet($id);
		
		print(json_encode($dieren));
	}
);

$slim->get('/eigenaars/:pub/:time/:hash', function($pub, $time, $hash)
	use($slim, $db){

		if(!$db->CheckTimeStamp($time))
			$slim->notFound();

		$hasPrivateKey = $db->HasPrivateKey($pub);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$hashVars = array(
			'page'=>'eigenaars',
			'timestamp'=>$time
		);

		if(!$db->CheckHash($pub, $hash, $hashVars))
			$slim->notFound();

		$eigenaars 	 = $db->GetAllOwners();
		
		print(json_encode($eigenaars));
	}
);

$slim->post('/eigenaars/:pub/:time/:hash', function($pub, $time, $hash)
	use($slim, $db){

		if(!$db->CheckTimeStamp($time))
			$slim->notFound();

		$hasPrivateKey = $db->HasPrivateKey($pub);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$hashVars = array(
			'page'=>'eigenaars',
			'timestamp'=>$time
		);

		if(!$db->CheckHash($pub, $hash, $hashVars))
			$slim->notFound();
		
		$firstName	 = $slim->request()->post('firstName');
        $affix		 = $slim->request()->post('affix');
        $lastName	 = $slim->request()->post('lastName');
        $city		 = $slim->request()->post('city');

		$result		 = $db->AddOwner($firstName, $affix, $lastName, $city);

		print(json_encode(array('result'=>$result)));
});

$slim->get('/eigenaars/:id/:pub/:time/:hash', function($id, $pub, $time, $hash)
	use($slim, $db){
		
		if(!$db->CheckTimeStamp($time))
					$slim->notFound();

		$hasPrivateKey = $db->HasPrivateKey($pub);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$hashVars = array(
			'page'=>'eigenaars',
			'timestamp'=>$time
		);

		if(!$db->CheckHash($pub, $hash, $hashVars))
			$slim->notFound();

		$eigenaar 	 = $db->GetOwner($id);
		
		print(json_encode($eigenaar));
	}
);

$slim->get('/eigenaars/:id/dieren/:pub/:time/:hash', function($id, $pub, $time, $hash)
	use($slim, $db){
		
		if(!$db->CheckTimeStamp($time))
			$slim->notFound();

		$hasPrivateKey = $db->HasPrivateKey($pub);
		
		if(!$hasPrivateKey)
			$slim->notFound();

		$hashVars = array(
			'page'=>'eigenaars',
			'id'=>$id,
			'timestamp'=>$time
		);

		if(!$db->CheckHash($pub, $hash, $hashVars))
			$slim->notFound();

		$eigenaar 	 = $db->GetAnimalsByOwner($id);
		
		print(json_encode($eigenaar));
	}
);


$slim->run();