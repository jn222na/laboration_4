<?php

require_once 'modelLogin.php';
require_once 'viewHTML.php';
require_once 'databaseConnectionSettings.php';

class Repository {

	private $view;
	private $model;
	private $db;
	protected $dbConnection;
	private $dbTable = "members";
	 private $rowUsername = "username";
 	 private $rowPassword = "password";
	public function connection() {
		if ($this -> dbConnection == NULL) {
			$this -> dbConnection = new \PDO(\settings::$DB_CONNECTION, \settings::$DB_USERNAME, \settings::$DB_PASSWORD);
			$this -> dbConnection -> setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
	}

// $sql = "INSERT INTO $this->dbTable (" . self::$sUsername . ", " . self::$sPassword . ") VALUES (?, ?)";

	public function __construct() {
		
	}

	public function addUser($username,$password) {
		try{
    		$this -> connection();
    		$sql = "INSERT INTO ".$this->dbTable."(" . $this->rowUsername . ", " . $this->rowPassword . ") VALUES (?, ?)";
    		
	      //Kollar om namnet redan finns 
        	$sqlDuplicate = "SELECT * FROM ".$this->dbTable."  WHERE " . $this->rowUsername . " = ?";

			$paramsDuplicate = array($username);

			$queryDuplicate = $this->dbConnection->prepare($sqlDuplicate);

			$queryDuplicate->execute($paramsDuplicate);

			$result = $queryDuplicate->fetch();

			if (strtolower($result[$this->rowUsername]) === strtolower($username)) {
				return false;
			}
		//Annars lägg till användare
			else{
			    $params = array($username, $password);
			$query = $this -> dbConnection -> prepare($sql);
			$query -> execute($params);
			return true;
			}

		} catch (\Exception $e) {
			die("An error occured in the database! addUser");
	    }
	}
	
	
	public function fetchCredentials($username,$password){
		if(isset($username,$password)){
	try{	    
		$this->connection();
        $query = $this -> dbConnection -> prepare
        ("SELECT " . $this->rowUsername . ", password FROM ".$this->dbTable." WHERE " . $this->rowUsername . "=:" . $this->rowUsername . " AND " . $this->rowPassword . "=:" . $this->rowPassword . "");
		$query->bindParam(':' . $this->rowUsername . '', $username, PDO::PARAM_STR);
        $query->bindParam(':' . $this->rowPassword . '', $password, PDO::PARAM_STR);
		
		$query->execute();
		 $user_id = $query->fetchColumn();
		  if($user_id == false)
        {
				return false;
        }
		return true;
    }
    catch(\Exception $e){
		   die("An error occured in the database! fetchCredentials");
		}
		}
	}

}

