<?php

    class htmlView{
        //ApplicationContent $content
        //\view\NavigationView::getMenu();
        public function echoHtml($body){
            echo "
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset=UTF-8>
					<title>Laboration_Login</title>
				</head>
				<body>
					$body
				</body>
				</html>
		";
        }
        
    }