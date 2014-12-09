<?php

require_once('../Slim/Slim.php');

class DB extends PDO {

	protected $dbname	 = 'db71989';
	protected $dbuser	 = 'root';
	protected $dbpw		 = 'usbw';
	protected $dbport	 = '3307';
	protected $dbserver  = 'localhost';

	public function __construct($options = Null){
		
		if(!isset($options)) {

			$options = array(
			    PDO::ATTR_EMULATE_PREPARES => false,
			    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
			);
		}

		parent::__construct('mysql:host='. $this->dbserver . ';dbname=' . $this->dbname . ';port=' . $this->dbport, $this->dbuser, $this->dbpw, $options);
	}

	public function GetAllAnimals(){

		$query	 = 'SELECT `id`, `naam` FROM `meol1_dieren`';

        $reponse = parent::prepare($query);
        $reponse->execute();
        $result	 = $reponse->fetchAll(PDO::FETCH_ASSOC);

        return $result;
	}

	public function GetAllOwners(){

		$query	 = 'SELECT `id`, `voornaam`,`tussenvoegsel`,`achternaam`,`plaats` FROM `meol1_eigenaars`';

        $reponse = parent::prepare($query);
        $reponse->execute();

        $result	 = $reponse->fetchAll(PDO::FETCH_ASSOC);
        
        return $result;
	}

	public function GetAnimal($where){

		$query	 = 'SELECT * FROM `meol1_dieren` ';

		//Quickhand if decides wether to filter on name or id based on if the input is numeric. 
		//Have an animal with a numeric name? Too bad. You could solve this if it weren't for a school excercise.
		$whereCondition	 = 'WHERE ' . (is_numeric($where) ? 'id=UNHEX(:where)' : 'naam=UNHEX(:where)');

		$query	 = $query . $whereCondition . ' LIMIT 1';

		$reponse = parent::prepare($query);
		$where 	 = bin2hex($where);

		$reponse->bindParam(':where', $where);

        $reponse->execute();
        $result	 = $reponse->fetchAll(PDO::FETCH_ASSOC);

        return $result;
	}

	public function GetOwner($where){

		$query	 = 'SELECT `id`, `voornaam`,`tussenvoegsel`,`achternaam`,`plaats` FROM `meol1_eigenaars`';

		//Quickhand if decides wether to filter on name or id based on if the input is numeric. 
		//Have an owner with a numeric name? Too bad. You could solve this if it weren't for a school excercise.
		$whereCondition = 'WHERE ' . (is_numeric($where) ? 'id=UNHEX(:where)' : 'naam=UNHEX(:where)');

		$query	 = $query . $whereCondition . ' LIMIT 1';

		$reponse = parent::prepare($query);
		$where 	 = bin2hex($where);

		$reponse->bindParam(':where', $where);

        $reponse->execute();
        $result	 = $reponse->fetchAll(PDO::FETCH_ASSOC);

        return $result;
	}

	public function GetOwnerByPet($where){

		$query	 = 'SELECT `eigenaar_id` FROM `meol1_dieren` ';

		//Quickhand if decides wether to filter on name or id based on if the input is numeric. 
		//Have an animal with a numeric name? Too bad. You could solve this if it weren't for a school excercise.
		$whereCondition = 'WHERE ' . (is_numeric($where) ? 'id=UNHEX(:where)' : 'naam=UNHEX(:where)');

		$query	 = $query . $whereCondition . ' LIMIT 1';

		$reponse = parent::prepare($query);
		$where 	 = bin2hex($where);

		$reponse->bindParam(':where', $where);

        $reponse->execute();
        $result	 = $reponse->fetchAll(PDO::FETCH_ASSOC);

        return $result;
	}
}



\Slim\Slim::registerAutoloader();

$slim = new \Slim\Slim();

$slim->get('/dieren', function(){

	$db 		 = new DB();
	$dieren 	 = $db->GetAllAnimals();
	
	print(json_encode($dieren));
});

$slim->get('/dieren/:id', function($id){
	
	$db 		 = new DB();
	$dieren 	 = $db->GetAnimal($id);
	
	print(json_encode($dieren));
});

$slim->get('/dieren/:id/eigenaar', function($id){
	
	$db 		 = new DB();
	$dieren 	 = $db->GetOwnerByPet($id);
	
	print(json_encode($dieren));
});

$slim->get('/eigenaars', function(){
	
	$db 		 = new DB();
	$eigenaars 	 = $db->GetAllOwners();
	
	print(json_encode($eigenaars));
});

$slim->get('/eigenaars/:id', function($id){
	
	$db 		 = new DB();
	$eigenaar 	 = $db->GetOwner($id);
	
	print(json_encode($eigenaar));
});

$slim->run();