<?php

setlocale(LC_ALL, "sv_SE", "swedish");
require_once 'modelLogin.php';
class viewHTML {
	private $username = '';
	private $usrValue = '';
	private $newUsrValue = '';
	private $password = '';
	private $viewErrorMsg = '';
	private $contrlErrorMsg = '';
	private $model;
	private $repository;
	private $dat;
	private $regex = "/^[\ws*åäöÅÄÖ][^0-9]/";
	//TODO: FIXA
	public function __construct(modelLogin $model) {
		$this -> model = $model;
		$this -> repository = new Repository();
	}

	public function echoHTML($msg) {
		$ret = "";
		
		//Clock function
		/*
		 * nl2br allows \n within variable
		 * strftime let us print date from our locale time
		 */
		date_default_timezone_set('Europe/Stockholm');
		$dag = utf8_encode("%Aen");
		$this -> dat = $dat = nl2br(ucwords(strftime($dag . " den %d %B.\n " . "År" . " %Y.\n Klockan " . "Är" . " %X.")));
		$this -> contrlErrorMsg = $msg;
		//Om inloggningen lyckades
		if ($this -> model -> loginStatus()) {
			$ret = "
			<h1>
				Laboration_2
			</h1>
			<h2>
				Admin Inloggad!
			</h2>
			$this->contrlErrorMsg
			$this->viewErrorMsg
			<form  method='post'> 
		    	<input type='submit'  name='logOut' value='Logga ut'/>
			</form>
			" . $dat . "
        ";
		} else {
			$ret = $this -> loginEcho();
		}

		if ($this -> didUserPressNewMember()) {
			return $this -> newMemberEcho();
		}

		return $ret;
	}

	public function loginEcho() {
		$ret = "
        <h1>
			Laboration_2
		</h1>
		<a href='?register'>Register</a>
		<h2>
				Ej inloggad
		</h2>
	<h3>$this->contrlErrorMsg</h3>
    <h3>$this->viewErrorMsg</h3> 
       <form id='login'   method='post'>
    		<label for='username'>Username:</label>
    			<br>
    		<input type='text'  name='username' value='$this->usrValue' id='username'>
    			<br>
    		<label for='password'>Password:</label>
    			<br>
    		<input type='password'   name='password' id='password'>
    			<br>
    		<input type='checkbox' name='checkSave' value='remember'>Remember me
    			<br>
    		<input type='submit'  name='submit'  value='Submit'/>
	    </form>  
		 <div>
		 <p>$this->dat <br> </p>
		
		 </div>";
		return $ret;
	}

	public function newMemberEcho() {
		$ret = "
			<h1>
				Laboration_4
			</h1>
			<a href='?'>GoBack</a>
			<h2>
				Ej inloggad, Registrera ny Medlem
			</h2>
			<h3>
			$this->contrlErrorMsg
			$this->viewErrorMsg
			</h3>
			<form  method='post'> 
				<label for='newUsername'>Username:</label>
    			<br>
    		<input type='text'  name='newUsername'  value='$this->newUsrValue' id='newUsername'>
    			<br>
    		<label for='newPassword'>Password:</label>
    			<br>
    		<input type='password'   name='newPassword' id='newPassword'>
    			<br>
    		<label for='repeatPassword'>Repeat Password:</label>
    			<br>
    		<input type='password'   name='repeatPassword' id='repeatPassword'>
    			<br>
    			<br>
    		<input type='submit'  name='register' value='Register'/>
			</form>
			" . $this -> dat . "
	";
		return $ret;
	}

	//Sätter kakor och krypterar lösenord
	public function rememberUser() {
		setcookie('cookieUsername', $_POST['username'], time() + 60 * 60 * 24 * 30);
		setcookie('cookiePassword', md5($_POST['password']), time() + 60 * 60 * 24 * 30);

		$cookieTime = time() + 60 * 60 * 24 * 30;
		file_put_contents('cookieTime.txt', $cookieTime);
		$this -> message = "Login successfull and you will be remembered.";
	}

	//Kollar om kakorna är satta
	public function checkCookie() {
		if (isset($_COOKIE['cookieUsername']) && isset($_COOKIE['cookiePassword'])) {
			return true;
		} else {
			return false;
		}
	}

	public function removeCookies() {
		setcookie('cookieUsername', "", time() - 3600);
		setcookie('cookiePassword', "", time() - 3600);
	}


	public function setUsrValueOnSuccess(){
		setcookie('cookieNewUsername',$this->getNewUsername(), time() + 60 * 60 * 24 * 30);
				// $this->usrValue = $_COOKIE['cookieNewUsername'];
		// header('location: ?');
		// exit;
							
	}
			
	public function didUserPressRegisterMember() {
		
		$newUsername = $this -> getNewUsername();
		$newPassword = $this -> getNewPassword();
		$repeatPassword = $this -> getRepeatPassword();

		if (isset($_POST['register'])) {
			if ($newUsername == "") {
				$this -> newUsrValue = $newUsername;
				$this -> msg = "Username is missing.";
			} else if (strlen($newUsername) < 3 || strlen($newUsername) > 15) {
				$this -> newUsrValue = $newUsername;
				$this -> viewErrorMsg = "Username must contain more than 3 characters and less than 15";
			} else if (!preg_match($this->regex, $newUsername)) {
				$newUsername =  strip_tags($newUsername);
				$newUsername = trim(preg_replace('/[^a-zA-Z\d]/', '', $newUsername));
				$this -> newUsrValue = $newUsername;
				$this->viewErrorMsg = "Contains non char/numeric characters";
			} else if ($newPassword == "") {
				$this -> newUsrValue = $newUsername;
				$this -> viewErrorMsg = "Password is missing";
			} else if (strlen($newPassword) < 6) {
				$this -> newUsrValue = $newUsername;
				$this -> viewErrorMsg = "Password must contain more than 6 characters";
			} else if ($newUsername == "" && $newPassword == "") {
				$this -> newUsrValue = $newUsername;
				$this -> viewErrorMsg = "Username and password missing";
			} else if ($newPassword != $repeatPassword) {
				$this -> newUsrValue = $newUsername;
				$this -> viewErrorMsg = "Passwords doesn't match";
			} else if (!$this -> repository -> addUser()) {
				$this -> newUsrValue = $newUsername;
				$this -> viewErrorMsg = "User already exists.";
			}
			$_SESSION['newUsername'] = $this->getNewUsername();
			return TRUE;
		}

		return FALSE;

	}

	public function didUserPressLogin() {

		$username = $this -> getUsername();
		$password = $this -> getPassword();

		if (isset($_POST['submit'])) {
			if ($username == "") {
				$this -> usrValue = $username;
				$this -> msg = "Username is missing.";
			} else if ($password == "" && $username != "") {
				$this -> msg = "Password is empty.";
				$this -> usrValue = $username;
			} else if ($username != "" && $password != "Password") {
				$this -> usrValue = $username;

			}
			return TRUE;
		}
		return FALSE;

	}

	public function didUserPressLogout() {
		if (isset($_POST['logOut'])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function didUserPressNewMember() {
		if (isset($_GET['register'])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//Get funktioner --------------------------
	public function getUsername() {
		if (isset($_POST['username'])) {
			return $_POST['username'];
		}
	}

	public function getPassword() {
		if (isset($_POST['password'])) {
			return $_POST['password'];
		}
	}

	public function getNewUsername() {
		if (isset($_POST['newUsername'])) {
			return $_POST['newUsername'];
		}
	}

	public function getNewPassword() {
		if (isset($_POST['newPassword'])) {
			return $_POST['newPassword'];
		}
	}

	public function getRepeatPassword() {
		if (isset($_POST['repeatPassword'])) {
			return $_POST['repeatPassword'];
		}
	}

	public function getCookieUsername() {
		return $_COOKIE['cookieUsername'];
	}

	public function getCookiePassword() {
		return $_COOKIE['cookiePassword'];
	}

	public function checkedRememberBox() {
		if (isset($_POST['checkSave'])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	// Get funktioner slut ---------------------

}

//To handle undefined index notice when $_GET isn't set, I usually do this somewhere at the top of scripts $lastname = isset($_GET['lastname'])?$_GET['lastname']:'';
