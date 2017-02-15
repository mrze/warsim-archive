<?php
//logout.php
//Clears session data, and logs the user out

//Session Handling & Zipping
session_start();
ob_start('ob_gzhandler');

//Includes
include 'inc/connect.php';
include 'inc/functions.php';
include 'inc/class.php';

//Clear Session
$_SESSION = array();

//Redirect
redirect ('index.php');

?>
