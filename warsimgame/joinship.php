<?php 
//joinship.php
//Accepts ?shipid=# as an input, and validates that
//If user logged in = user ship is registered to then, logs them into the specific game
//else, ERRORIZE!

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

if(!is_numeric($_GET['shipid']) OR !$_GET['shipid']){
	$error = 'Ship IDs are always numeric. Please don`t tamper with the urls.';
	
	$error = urlencode($error);
	
	redirect('lobby.php?error=' . $error);
}


$sql = 'SELECT `shipid`,`gameid`,`userid` FROM `ws_ships` WHERE `shipid`=' . $_GET['shipid'];
$query = queryme($sql);

if(mysql_num_rows($query) != 1){
	$error = 'There has been an error in the ship database. The ship specified by `joinship.php` does not exist. If you think this is the fault of the game, please report it to the creator.';
	
	$error = urlencode($error);
	
	redirect('lobby.php?error=' . $error);
}

$row = mysql_fetch_assoc($query);

if($_SESSION['userid'] != $row['userid']){
	$error = 'You are not permitted to use that ship since you did not create it.';
	
	$error = urlencode($error);
	
	redirect('lobby.php?error=' . $error);
}

$_SESSION['shipid'] = $row['shipid'];
$_SESSION['gameid'] = $row['gameid'];

redirect('game.php');

?>