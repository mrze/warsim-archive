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
$page['text'] .= '<div align="center"><strong>-Scanners-</strong></div><br />';

//Check for disabled/destroyed...
if($player->get_status() == 'destroyed'){
	$page['text'] .= 'Your ship is destroyed. You cannot access this.';
} else {

	//Now, check if the user has inputted anything... or not...
	if(!$_GET){
		//IE: Nothing, so we can just be a super cool frood and show all the ships in the surrounding vacinity...
		$page['text'] .= get_all_ships_scan();
		
	} else {
		//If the user has clicked a square and demanded a scan...
		if($_GET['mode'] == 'scansquare'){
			//mode=scansquare&scan_x=10&scan_y=7
			//Some processing on the inputs
			if(is_numeric($_GET['scan_x']) AND is_numeric($_GET['scan_y']) AND $_GET['scan_x'] >= 0 AND $_GET['scan_x'] <= 19 AND $_GET['scan_y'] >= 0 AND $_GET['scan_y'] <= 19){
				$page['text'] .= get_square_ships_scan($_GET['scan_x'],$_GET['scan_y']);
			} else {
				$page['text'] .= 'OMG You supa 1337 hax0r! j00 Pwnz mee!! [There has been a user-stupidity inflicted error. Don\'t play with the URL]';
			}	
		}
		
		//If the user has clicked a ship, from either of the two scanners, he gets this :D OMG
		if($_GET['mode'] == 'scanship'){
			//First round of eliminations....
			if(is_numeric($_GET['shipid']) AND $ships[$_GET['shipid']]){
			
				//IS in range?
				if(can_scan_square($ships[$_GET['shipid']]->x,$ships[$_GET['shipid']]->y)){
					//Check it isnt docked...
					if($ships[$_GET['shipid']]->dockedin == 0 OR $ships[$_GET['shipid']]->dockedin == $player->shipid OR $_GET['shipid'] == $player->shipid){
						//Get scan
						$page['text'] .= $ships[$_GET['shipid']]->large_scan();
						
						//Give event to the 'scannee'
						if(($ships[$_GET['shipid']]->team != $player->team OR $player->team == 1) AND $_GET['shipid'] != $player->shipid){
							if($player->dockedin != 0){
								$event_text = 'We have been scanned by the ' . $player->classname . ' <em>' . $player->shipname . '</em> (Team: ' . $player->teamname . ') from position ' . $player->get_position() . ' [Docked inside the ' . $ships[$player->dockedin]->classname . ' <em>' . $ships[$player->dockedin]->shipname .'</em>]';
							} else {
								$event_text = 'We have been scanned by the ' . $player->classname . ' <em>' . $player->shipname . '</em> (Team: ' . $player->teamname . ') from position ' . $player->get_position() . '.';
							}
							
							$ships[$_GET['shipid']]->give_event($event_text);
						}
					} else {
						$page['text'] .= 'That ship is out of range.';
					}
				} else {
					$page['text'] .= 'That ship is out of range.';
				}
			} else {
				$page['text'] .= 'OMG You supa 1337 hax0r! j00 Pwnz mee!! [There has been a user-stupidity inflicted error. Don\'t play with the URL]';
			}
		}
	}
}

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