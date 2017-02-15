<?php
///////HEADER//////

//Session Handling & Zipping
session_start();
ob_start('ob_gzhandler');

//Includes
include 'inc/connect.php';


///////\Header//////

//If not logged in, kick out...
if(!$_SESSION['loggedin']){
	$_SESSION = array();
	
	$error = 'That page has restricted access, you were not logged in, or your session expired.';
	
	$error = urlencode($error);
	
	redirect('index.php?error=' . $error);
}

//We are in... Now, lets just display Forums with threads...
$query = queryme('SELECT * FROM `ws_fr_forums` ORDER BY `forder` ASC');

//Display the rows...
if(mysql_num_rows($query) > 0){
	while($row =@ mysql_fetch_assoc($query)){
		//Display nicely
		$forum_forums .= '<tr><td>' . $row['fname'] . '<br /><small>' . $row['fdesc'] . '</small></td><td>' . $row['fpostcount'] . '</td><td>By: ' . $row['flastpostname'] . '<br /><small>' . $row['flastposttime'] . '</small></td>';
	}
} else {
	
}