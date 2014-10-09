<?php
require_once 'controller.php';
require_once 'htmlView.php';
session_start();
		  $loginCntrl = new controller();
		  $htmlBody = $loginCntrl->login();
		  
		  // $navigation = new \controller\Navigation();
		  // $asd = $navigation->doControll();
		  
		  $view = new htmlView();
		  $view->echoHtml($htmlBody);


