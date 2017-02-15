<?php
//index.php
//Allows users to create accounts, or login to accounts
//If logged in, redirect automatically to the lobby...

//Session handling & Page GZipping
session_start();
ob_start('ob_gzhandler');

//Includes:
require_once 'inc/connect.php';
require_once 'inc/class.php';
require_once 'inc/functions.php';

//Check wether user is logged in or not
if($_SESSION['loggedin']){
	redirect('lobby.php');
}

//If we have an error note, display it under tabletwo
if($_GET['error']){
	$page['tabletwo'] = '<div class="red">Error: ' . $_GET['error'] . '</div>';
}

//Check wether user has loggedin/signed up
if($_POST){
	if($_POST['do'] == 'createaccount'){
		//Create Account
		 $page['tableone'] .= create_account ($_POST['handle'],$_POST['password'],$_POST['email']);
	}
	if($_POST['do'] == 'login'){
		//Login to Account
		$page['tabletwo'] .= login ($_POST['handle'],$_POST['password']);		
	}
}

/*
if (eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$',  
       $_REQUEST['email'])) {
echo 'Valid';
} else {
echo 'Invalid';
} 
*/

include 'templates/temp_index.php';
?>