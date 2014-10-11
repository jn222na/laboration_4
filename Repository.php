<?php

require_once 'modelLogin.php';
require_once 'viewHTML.php';
require_once 'databaseConnectionSettings.php';

class Repository {

	private $view;
	private $model;
	private $db;
	protected $dbConnection;
	protected $dbTable;
	 private static $rowUsername = "username";
 	 private static $rowPassword = "password";
	public function connection() {
		if ($this -> dbConnection == NULL) {
			$this -> dbConnection = new \PDO(\settings::$DB_CONNECTION, \settings::$DB_USERNAME, \settings::$DB_PASSWORD);
			$this -> dbConnection -> setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
	}

// $sql = "INSERT INTO $this->dbTable (" . self::$sUsername . ", " . self::$sPassword . ") VALUES (?, ?)";

	public function __construct() {
		
	}

	public function addUser() {
		$username = $_POST['newUsername'];
		$password = $_POST['newPassword'];
		try{
    		$this -> connection();
    		$sql = "INSERT INTO members(" . self::$rowUsername . ", " . self::$rowPassword . ") VALUES (?, ?)";
	    //Kollar om namnet redan finns 
        	$sqlDuplicate = "SELECT * FROM members WHERE " . self::$rowUsername . " = ?";

			$paramsDuplicate = array($username);

			$queryDuplicate = $this->dbConnection->prepare($sqlDuplicate);

			$queryDuplicate->execute($paramsDuplicate);

			$result = $queryDuplicate->fetch();

			if (strtolower($result[self::$rowUsername]) === strtolower($username)) {
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
	
	
	public function fetchCredentials(){
		if(isset($_POST['username'], $_POST['password'])){
	try{	    
		$this->connection();
        $query = $this -> dbConnection -> prepare("SELECT username, password FROM members WHERE username=:username AND password=:password");
		$query->bindParam(':username', $_POST['username'], PDO::PARAM_STR);
        $query->bindParam(':password', $_POST['password'], PDO::PARAM_STR);
		
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
