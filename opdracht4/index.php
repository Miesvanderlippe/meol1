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
		    
		    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
			);
		}

		parent::__construct('mysql:host='. $this->dbserver . ';dbname=' . $this->dbname . ';port=' . $this->dbport, $this->dbuser, $this->dbpw, $options);
	}

	public function GetAllAnimals(){

		$query = 'SELECT `id`, `naam` FROM `meol1_dieren`';

        $reponse = parent::prepare($query);
        $reponse->execute();
        $result	 = $reponse->fetchAll(PDO::FETCH_ASSOC);

        return $result;
	}

	public function GetAnimal($query){


	}
}



\Slim\Slim::registerAutoloader();

$slim = new \Slim\Slim();

$slim->get('/dieren', function(){

	$db 	 = new DB();
	$dieren  = $db->GetAllAnimals();
	
	print(json_encode($dieren));
});

$slim->get('/dieren/:id', function($id){
	print(json_encode($id));
});

$slim->run();