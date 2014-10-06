<?php


require_once 'modelLogin.php';
require_once 'viewHTML.php';
require_once 'databaseConnectionSettings.php';

	 class Repository{
	 	
		private $view;
		private $model;
		private $db;
		protected $dbConnection;
		protected $dbTable;
		
		public function connection(){
			if($this->dbConnection == NULL){
				$this->dbConnection = new  \PDO(\settings::$DB_CONNECTION, \settings::$DB_USERNAME, \settings::$DB_PASSWORD);
				$this->dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}
		}

		
		public function __construct()
		{
			$this->model = new modelLogin();
			$this->view = new viewHTML($this->model);

		}
		
		public function addUser(){
			$username = $this->view->getNewUsername();
			$password = $this->view->getNewPassword();
			
			$this->connection();
			$sql = "INSERT INTO members(username,password)
			VALUES ('$username','$password')";
				$q = $this->dbConnection->prepare($sql);
				
				
				$q->execute(array(':username'=>$username,':password'=>$password));	
					
		}
		
	}
