<?php
//lobby.php
//Shows users the games available to join
//Allows users to create ships in those games
//Also shows users the ships they already have in the games
//Autoredirect to game.php if $_SESSION['gameid'] is already set...

//Session handling & Page GZipping
session_start();
ob_start('ob_gzhandler');

//Includes:
require_once 'inc/connect.php';
require_once 'inc/class.php';
require_once 'inc/functions.php';

//If not logged in, kick out...
if(!$_SESSION['loggedin']){
	$_SESSION = array();
	
	$error = 'That page has restricted access, you were not logged in, or your session expired.';
	
	$error = urlencode($error);
	
	redirect('index.php?error=' . $error);
}

//If already logged into a game... and a ship... redirect... else delete game session id
if($_SESSION['gameid']){
	//If shipid, then everything is normal, procede as usual
	if($_SESSION['shipid']){
		redirect('game.php');
	} else {
		//Gameid without shipid, on lobby page? Kinda strange, so, we remove the gameid
		$_SESSION['gameid'] = '';
	}
} else {
	if($_SESSION['shipid']){
		//Shipid without gameid? Even stranger, remove it...
		$_SESSION['shipid'] == '';
	} 
}

//Display it
include 'templates/temp_addmaps.php';

?>