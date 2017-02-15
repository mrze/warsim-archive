<?php
///////HEADER//////

//Session Handling & Zipping
session_start();
ob_start('ob_gzhandler');

//Includes
include 'inc/connect.php';
include 'inc/functions.php';
include 'inc/class.php';

//Timer
$clock = new timer;
$clock->start();

//Setup Ships
include 'inc/setupships.php';
include 'inc/assignplanets.php';

//Automated stuff
include 'inc/automate.php';

/////////CODE BELOW////////////

//Add a nice little header thingy:
$page['text'] .= '<div align="center"><strong>-Events-</strong></div><br /><span style="font-size: 8pt">';

//extract events
if($player->endgametime < $player->joingametime){
	$sql = 'SELECT * FROM `ws_events` WHERE (`shipid` = "' . $player->shipid . '" OR `shipid` = "team' . $player->team . '" OR `shipid` = "game' . $_SESSION['gameid'] . '") AND  `time` >= ' . $player->joingametime . ' ORDER BY `eventid` DESC';
} else {
	$sql = 'SELECT * FROM `ws_events` WHERE (`shipid` = "' . $player->shipid . '" OR `shipid` = "team' . $player->team . '" OR `shipid` = "game' . $_SESSION['gameid'] . '") AND  `time` >= ' . $player->joingametime . ' AND `time` <= ' . $player->endgametime . ' ORDER BY `eventid` DESC';
}

$query = queryme($sql);

if(mysql_num_rows($query)){
	
	//Display events...
	while($row =@ mysql_fetch_assoc($query)){
		$page['text'] .= '&raquo; <strong>' . date("F j, g:i a",$row['time']) . '</strong> - ' . $row['text'] . '<br /><br />';
	}
	
} else {

	//Display error
	$page['text'] .= 'You have not recieved any events yet.';

}

//Close small tag:
$page['text'] .= '</span>';

/////////CODE ABOVE////////////
//Set mode:
$page['mapmode'] = '0,0,1';

//Render the map
include 'map.php';

//Events
$page['minievent'] = get_events();

//Stop the timer
$totaltime = $clock->stop();

//Nice layout
include 'templates/temp_game.php';

//Debug
/*
echo 'SYSTEM DEBUG:<br />';
echo $player->debug();
echo 'SQL QUERY COUNT: ' . $sql_count . '<br />';
echo $sql_log;*/
?>
