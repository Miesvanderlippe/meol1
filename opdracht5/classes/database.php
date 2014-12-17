<?php

require_once('classes/crypt.php');

class DB extends PDO {

	protected $dbname	 = 'db71989';
	protected $dbuser	 = 'root';
	protected $dbpw		 = 'usbw';
	protected $dbport	 = '3307';
	protected $dbserver  = 'localhost';

	protected $privateKey;
	protected $publicKey;

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

	public function CheckTimeStamp($timestamp){
		
		$maxTimeDiffMinutes = 5;
		$maxTimeDiffSec = $maxTimeDiffMinutes * 60;

		$timeDiff = (time() - $timestamp);
		
	    return $timeDiff < $maxTimeDiffSec;
	}

	public function GenerateKeypair(){

		$query = 	'INSERT INTO `meol1_keys`(public, private) VALUES (?, ?)';
		
		$response = parent::prepare($query);

		$publicKey = Crypt::RandomString(16);
		$privateKey = Crypt::RandomString(64);

		$response->bindParam(1, $publicKey);
        $response->bindParam(2, $privateKey);
        
        $result	 = $response->execute();

        return $result;
	}

	private function GetPrivateKey($publicKey){
		
		if($publicKey == $this->publicKey)
			return $this->privateKey;

		$query	 = 'SELECT `id`, `private` FROM `meol1_keys` WHERE `public`=?';

        $response = parent::prepare($query);
        $response->bindParam(1, $publicKey);
        $response->execute();
        $result	 = $response->fetchAll(PDO::FETCH_ASSOC);

        if(!isset($result[0]) || empty($result[0]))
        	return false;

        $privateKey = $result[0]['private'];
        
        $this->privateKey = $privateKey;
        $this->publicKey  = $publicKey;

        return $privateKey;
	}

	public function HasPrivateKey($publicKey){

		$key = self::GetPrivateKey($publicKey);

        return $key !== false;
	}

	public function GetAllAnimals(){

		$query	 = 'SELECT `id`, `naam` FROM `meol1_dieren`';

        $response = parent::prepare($query);
        $response->execute();
        $result	 = $response->fetchAll(PDO::FETCH_ASSOC);

        return $result;
	}

	public function GetAllOwners(){

		$query	 = 'SELECT `id`, `voornaam`,`tussenvoegsel`,`achternaam`,`plaats` FROM `meol1_eigenaars`';

        $response = parent::prepare($query);
        $response->execute();

        $result	 = $response->fetchAll(PDO::FETCH_ASSOC);
        
        return $result;
	}

	public function GetAnimal($where){

		$query = 'SELECT `naam`,`soort`,`geboortejaar`,`eigenaar_id` FROM `meol1_dieren` ';

		//Quickhand if decides wether to filter on name or id based on if the input is numeric. 
		//Have an animal with a numeric name? Too bad. You could solve this if it weren't for a school excercise.
		$whereCondition	 = 'WHERE ' . (is_numeric($where) ? 'id=UNHEX(:where)' : 'naam=UNHEX(:where)');

		$query	 = $query . $whereCondition . ' LIMIT 1';

		$response = parent::prepare($query);
		$where 	 = bin2hex($where);

		$response->bindParam(':where', $where);

        $response->execute();
        $result	 = $response->fetchAll(PDO::FETCH_ASSOC);

        return $result;
	}

	public function GetOwner($where){

		$query	 = 'SELECT `id`, `voornaam`,`tussenvoegsel`,`achternaam`,`plaats` FROM `meol1_eigenaars`';

		//Quickhand if decides wether to filter on name or id based on if the input is numeric. 
		//Have an owner with a numeric name? Too bad. You could solve this if it weren't for a school excercise.
		$whereCondition = 'WHERE ' . (is_numeric($where) ? 'id=UNHEX(:where)' : 'naam=UNHEX(:where)');

		$query	 = $query . $whereCondition . ' LIMIT 1';

		$response = parent::prepare($query);
		$where 	 = bin2hex($where);

		$response->bindParam(':where', $where);

        $response->execute();
        $result	 = $response->fetchAll(PDO::FETCH_ASSOC);

        return $result;
	}

	public function GetOwnerByPet($where){

		$query	 = 'SELECT `eigenaar_id` FROM `meol1_dieren` ';

		//Quickhand if decides wether to filter on name or id based on if the input is numeric. 
		//Have an animal with a numeric name? Too bad. You could solve this if it weren't for a school excercise.
		$whereCondition = 'WHERE ' . (is_numeric($where) ? 'id=UNHEX(:where)' : 'naam=UNHEX(:where)');

		$query	 = $query . $whereCondition . ' LIMIT 1';

		$response = parent::prepare($query);
		$where 	 = bin2hex($where);

		$response->bindParam(':where', $where);

        $response->execute();
        $result	 = $response->fetchAll(PDO::FETCH_ASSOC);

        return $result;
	}

	public function GetAnimalsByOwner($where){

		$query = 'SELECT `meol1_dieren`.`id`, `meol1_dieren`.`naam`,`meol1_dieren`.`soort`,`meol1_dieren`.`geboortejaar`, `meol1_dieren`.`eigenaar_id`, CONCAT(`meol1_eigenaars`.`voornaam`, \' \' ,if(`meol1_eigenaars`.`tussenvoegsel` IS NULL ,"",`meol1_eigenaars`.`tussenvoegsel` ), \' \' ,`meol1_eigenaars`.`achternaam`) AS `naam` FROM meol1_dieren LEFT JOIN `db71989`.`meol1_eigenaars` ON `meol1_dieren`.`eigenaar_id` = `meol1_eigenaars`.`id`';

		//Quickhand if decides wether to filter on name or id based on if the input is numeric. 
		$whereCondition = 'WHERE `meol1_dieren`.`eigenaar_id`=UNHEX(:where)';
		$query = $query . $whereCondition;

		$response = parent::prepare($query);
		$where 	 = bin2hex($where);

		$response->bindParam(':where', $where);

        $response->execute();
        $result	 = $response->fetchAll(PDO::FETCH_ASSOC);

        return $result;
	}

	public function AddAnimal($name, $kind, $yearOfBirth, $ownerID){

		$query = 	'INSERT INTO `meol1_dieren`(naam, soort, geboortejaar, eigenaar_id)
					 VALUES (?, ?, ?, ?)';

		$response = parent::prepare($query);

		$response->bindParam(1, $name);
        $response->bindParam(2, $kind);
        $response->bindParam(3, $yearOfBirth);
        $response->bindParam(4, $ownerID);

        $result	 = $response->execute();

        return $result;
	}

	public function AddOwner($firstName, $affix, $lastName, $city){

		$query = 	'INSERT INTO `meol1_eigenaars`(naam, soort, geboortejaar, eigenaar_id)
					 VALUES (?, ?, ?, ?)';

		$response = parent::prepare($query);

		$response->bindParam(1, $firstName);
        $response->bindParam(2, $affix);
        $response->bindParam(3, $lastName);
        $response->bindParam(4, $city);

        $result	 = $response->execute();

        return $result;
	}
}