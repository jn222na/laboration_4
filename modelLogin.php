<?php


    class modelLogin{
    
    private $username = "Admin";
    private $password = "Password";
    private $regex = '/[^A-Za-zåäöÅÄÖ0-9\-_\.]/i';
	private $rep;
    public function __construct(){
        $this->rep = new Repository();
    }
	


			
    //Lyckad inloggning sätt sessionen till webbläsaren användaren loggade in i
    	public function checkLogin($username, $password) {
    		if($this->rep->fetchCredentials()){
			  $_SESSION['login'] = $username;
	           $_SESSION["checkBrowser"] = $_SERVER['HTTP_USER_AGENT']; 
			      return true;
			}
			 
	}
       
        public function destroySession(){
            session_unset();
            session_destroy();
        }
        //kollar om sessionen är satt och att den är samma webbläsare som vid inloggning
        public function loginStatus(){
                 if(isset($_SESSION['checkBrowser']) && $_SESSION["checkBrowser"] === $_SERVER['HTTP_USER_AGENT']){
                     if(isset($_SESSION['login'])){
                         return TRUE;
                     }
                 }
                else{
                    return FALSE;
                }
            
        }
        
        public function checkLoginCookie($username,$password){
            $getCookieTime = file_get_contents('cookieTime.txt');
            if ($username == $this->username && $password == md5($this->password) && $getCookieTime > time()){
				$_SESSION["login"] = $username;
				$_SESSION["checkBrowser"] = $_SERVER['HTTP_USER_AGENT'];
    			return TRUE;
			}
			else{
				return FALSE;
			}
        }
        
    }