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

//Load planets if available...
//Since we dont have a GAMEID to begin with, we have to redefine the assign planets stuff to work with a mapid...
if($_GET['mapid']){
	if(is_numeric($_GET['mapid'])){
		//Assign Planets
		//Incase we are on a special page...
	
		//Load planets...
		$sql = 'SELECT s.planettype, s.planetname, s.planetx, s.planety
		FROM ws_planets s
		WHERE s.mapid = ' . $_GET['mapid'];
		
		$query = queryme($sql);
		
		//Loads each planet into $planet array, which details can be found in its parrallel forms...
		while($row =@ mysql_fetch_assoc($query)){
			$planet[] = $row['planetname'];
			$planet_type[] = $row['planettype'];
			$planet_x[] = $row['planetx'];
			$planet_y[] = $row['planety'];
		}
	}
}

//Now load up the planets into a funky JS script...
//Make map...
if($planet){
	
	//Loop through each planet, and grab the key, so we can get data out of the parrallel arrays
	foreach($planet as $key=>$p_name){
		
		//The Javascript looks like this:
		//place_planet(8,0,'Planet: Ut','Planet.gif');
		//place_planet(x,y,Type ': ' Name,Type '.gif');
		$page['map'] .= 'place_planet(' . $planet_x[$key] . ',' . $planet_y[$key] . ',\'' . $planet_type[$key] . ': ' . $p_name .'\',\'' . $planet_type[$key] . '.gif\');';
	
	} //End Planet Foreach
	
}

//Load up a List of maps available, and display them at the left...
$sql = 'SELECT * FROM `ws_systems` ORDER BY mapname';
$query = queryme($sql);

while($row =@ mysql_fetch_assoc($query)){
	$page['text'] .= '&nbsp;&raquo;&nbsp;<a href="browsemaps.php?mapid=' . $row['mapid'] . '">' . $row['mapname'] . '</a><br />';
}

//Display it
include 'templates/temp_browsemaps.php';

?>