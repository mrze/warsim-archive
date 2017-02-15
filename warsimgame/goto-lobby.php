<?php
//goto-lobby.php
//Clears game/ship data, and moves the user to the lobby

//Session Handling & Zipping
session_start();
ob_start('ob_gzhandler');

//Includes
include 'inc/connect.php';
include 'inc/functions.php';
include 'inc/class.php';

//Clear Session
$_SESSION['shipid'] = '';
$_SESSION['gameid'] = '';

//Redirect
redirect ('lobby.php');

?>