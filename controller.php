<?php

require_once 'viewHTML.php';
require_once 'modelLogin.php';
require_once 'Repository.php';
        class  controller{
          private $view;
          private $model;
		  private $dbActions;
          public function __construct() {
              $this->model = new modelLogin();
              $this->view = new viewHTML($this->model);
              $this->dbActions = new Repository();
          }
          
          public function login(){
              $username = $this->view->getUsername();
              $password = $this->view->getPassword();
			  $newUsername = $this->view->getNewUsername();
			  $newPassword = $this->view->getNewPassword();
			  $repeatPassword = $this->view->getRepeatPassword();
              $msg = "";
              
			 
			  
    //Om sessionen inte är satt 
            if($this->model->loginStatus() == FALSE){
    // kolla om cookies är satta
		   	if($this->view->checkCookie()){
	  //Är dom det skicka kaknamn och kaklösen vidare 
				if($this->model->checkLoginCookie($this->view->getCookieUsername(), $this->view->getCookiePassword())){
					$msg = "Login with cookies successfull";
				}else{
    //annars ta bort
					$this->view->removeCookies();
					$msg = "Cookie contains wrong information";
				}
			}
		}
    //Om användaren vill logga in          
              if($this->view->didUserPressLogin()){
                if($username != "" && $password != ""){
    //Om han kryssat i "remember me"                
                  if($this->model->checkLogin($username, $password)){
                      $msg = "Successful login";
                      if($this->view->checkedRememberBox()){
                          $this->view->rememberUser();
                          $msg = "Login successful you will be remembered";
                      }
                  }
                  else{
                      $msg ="Trouble logging in (Username/Password)";
                  }
               }
              }
    //Om användaren klickar logout
              if($this->view->didUserPressLogout()){
                  $this->view->removeCookies();
                  $this->model->destroySession();
                  $msg =  "User logged out";
              }
              
    //registrera        
                
 				 if($this->view->didUserPressNewMember()){
 				     $this->view->setNewUsernameCookie();
               		if($this->view->didUserPressRegisterMember()){
                     if($this->dbActions->addUser($newUsername,$newPassword)){
							    header("Location: ?");
							}
							else{
							     $this->view->checkExistingMember();
							}
							
               }
			}
                
              
    //Skickar med meddelandet till echoHTML
              return $this->view->echoHTML($msg);
          }
        } 