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
$page['text'] .= '<div align="center"><strong>-Travel-</strong></div><br />';

//Some variable modifications... incase we get some noobie hackz0rs trying to go to '20,20' >_> :D
if($_GET['trv_x']){
	$_GET['trv_x'] = min(max($_GET['trv_x'],0),19);
}
if($_GET['trv_y']){
	$_GET['trv_y'] = min(max($_GET['trv_y'],0),19);
}

//Ok, Travel only works when we are not disabled/destroyed, right?
if($player->get_status() != 'normal'){
	$page['text'] .= 'Your ship is either disabled or destroyed. You cannot access this.';
} else {
	//Are we docked?!
	if($player->dockedin != 0){
		$page['text'] .= 'ZOMG SOGE! Stop deleting my HDD! and stop trying to travel while you are docked. You must undock before you can move freely again.';
	} else {
		//Ok, so we arnt disabled/destroyed...
		
		//HAS ANYTHING BEEN INPUTTED?
		if($_GET['mode']){
			//Stuff inputted...
			
			//If we want to MOVE somewhere...
			//IE: Move request submitted
			if($_GET['mode'] == 'goto'){
				
				//Check if player is moving or not...
				if($player->path){
					//Moving... get last path position
					$now_xy = last_square($player->path);
					$now_xy = explode('-',$now_xy);
					$now_x = $now_xy[0];
					$now_y = $now_xy[1];				
				} else {
					//Not moving... just get normal position
					$now_x = $player->x;
					$now_y = $player->y;
				}
				
				//We have the path generated... So, for the sake of reducing code, we can TEMPORARLY 
				//add this to the players path, and it will display it...
				//HOWEVER, when doing this, we need to make sure we calculate the Travel time properly... [and ajust it when
				//it comes time to update the travel listing]
				
				if($now_x == $_GET['trv_x'] && $now_y == $_GET['trv_y']){
					$page['text'] .= 'That Square is already your current destination. ';
				} else {
				
					//Grab the new path:
					$path = generate_path($now_x,$now_y,$_GET['trv_x'],$_GET['trv_y']);
					
					//Get the time for travelling the path
					$timefornewpath = $player->get_travel_time(count_squares($path));
					
					if($player->path){
						$player->path .= ',' . $path;
						
						//To make the total time, we need initial time remaining 
						$sql = 'SELECT * FROM `ws_auto_movement` WHERE `shipid` = ' . $_SESSION['shipid'];
						$query = queryme($sql);
						$row = mysql_fetch_assoc($query);
						
						//Grab time remaining...
						$rem_time = $row['endtime'] - time();
						$rem_time = $rem_time + $timefornewpath;
						
					} else {
						$player->path = $path;
						$rem_time = $timefornewpath;
					}
					
					$page['text'] .= '<strong>Calculating Path...</strong><br /><br />
								<strong>Current Position:</strong> ' . $player->get_position() . '<br />
								<strong>Intended Destination:</strong> [' . $_GET['trv_x'] . ',' . $_GET['trv_y'] . ']<br /><br />
								<strong>Distance:</strong> ' . count_squares($player->path) . ' clicks<br />
								<strong>Route:</strong> ' . str_replace(">","->",str_replace("-",",",str_replace(","," > ",$player->path))) . '<br /><br />
								<strong>ETA:</strong> ' . parse_time($rem_time) . '<br /><br />		
								<div align="center"><form name="Travel" method="get" action="">
								  <input type="submit" class="form" name="go" value="Start Travelling" />
								  <input type="hidden" name="trv_x" value="' . $_GET['trv_x'] . '" />
								  <input type="hidden" name="trv_y" value="' . $_GET['trv_y'] . '" />
								  <input type="hidden" name="mode" value="go" />
								</form></div>';
				}
			}
			
			//WE HAVE CONFIRMATION!!!!123
			//Lets GO ---------->
			if($_GET['mode'] == 'go'){
				//Same process as before:
				
				//Check if player is moving or not...
				if($player->path){
					//Moving... get last path position
					$now_xy = last_square($player->path);
					$now_xy = explode('-',$now_xy);
					$now_x = $now_xy[0];
					$now_y = $now_xy[1];				
				} else {
					//Not moving... just get normal position
					$now_x = $player->x;
					$now_y = $player->y;
				}
				
				if($now_x == $_GET['trv_x'] && $now_y == $_GET['trv_y']){
					$page['text'] .= 'That Square is already your current destination. ';
				} else {
					
					//Grab the new path:
					$path = generate_path($now_x,$now_y,$_GET['trv_x'],$_GET['trv_y']);
									
					//Get the time for travelling the path
					$timefornewpath = $player->get_travel_time(count_squares($path));
						
					if($player->path){
						$player->path .= ',' . $path;
							
						//To make the total time, we need initial time remaining 
						$sql = 'SELECT * FROM `ws_auto_movement` WHERE `shipid` = ' . $_SESSION['shipid'];
						$query = queryme($sql);
						$row = mysql_fetch_assoc($query);
		
						//Grab time remaining...
						$rem_time = $row['endtime'] - time();
						$rem_time = $rem_time + $timefornewpath;
							
						//Update auto_movement...
						$sql = 'UPDATE `ws_auto_movement` SET `path`="' . $player->path . '",`endtime`="' . ($rem_time + time()) . '" WHERE `shipid`=' . $_SESSION['shipid'];
						$query = queryme($sql);
						
						//Update Ships
						$sql = 'UPDATE `ws_ships` SET `path`="' . $player->path . '"  WHERE `shipid`=' . $_SESSION['shipid'];
						$query = queryme($sql);
						
						//Give the player an event...
						$event_text = 'Course Change successful! Flying to: [' . $_GET['trv_x'] . ',' . $_GET['trv_y'] . '] ETA: ' . parse_time($rem_time);
						$player->give_event($event_text);
						
						//Redirect to travel page...
						redirect('travel.php');
					} else {
						$player->path = $path;
						$rem_time = $timefornewpath;
						
						//Add in auto_movement query
						$sql = 'INSERT INTO `ws_auto_movement` (`time_expires`,`shipid`,`gameid`,`path`,`repeattime`,`endtime`) VALUES (' . ($player->get_travel_time(1) + time()) . ',' . ($_SESSION['shipid']) . ',' . $_SESSION['gameid'] . ',"' . ($player->path) . '",' . ($player->get_travel_time(1)) . ',' . ($rem_time + time()) . ');';
						$query = queryme($sql);
						
						//Update Ships
						$sql = 'UPDATE `ws_ships` SET `path`="' . $player->path . '"  WHERE `shipid`=' . $_SESSION['shipid'];
						$query = queryme($sql);
	
						//Give event...
						$event_text = 'Sublight Engines Engaged! Flying to: [' . $_GET['trv_x'] . ',' . $_GET['trv_y'] . '] ETA: ' . parse_time($rem_time);
						$player->give_event($event_text);
						
						//Redirect to travel page...
						redirect('travel.php');
					}
				}
			}
			
			
			//We are stopping...
			if($_GET['mode'] == 'Abort Sublight'){
				//Ok, firstly, we need to do a couple of things:
				//Set player as NOT moving
				
				if($player->path){
					//DB:
					$sql = 'UPDATE `ws_ships` SET `path`="" WHERE  `shipid`=' . $_SESSION['shipid'];
					$query = queryme($sql);
					
					//Class:
					$player->path = '';
					
					//Remove any movement stuff from the database
					$sql = 'DELETE FROM `ws_auto_movement` WHERE  `shipid`=' . $_SESSION['shipid'];
					$query = queryme($sql);
					
					//Give the player an event...
					$event_text = 'Sublight Travel Aborted Successfully. Current Position: ' . $player->get_position();
					$player->give_event($event_text);
					
					//Finally, This message will self destruct in 10 seconds?
					//Redirect to travel page
					redirect('travel.php');
				} else {
					$page['text'] .= 'Cut engines! Engines are already cut sir! [Don\'t you feel like an idiot? :P]';
				}
			}
	
		} else {
		
			//Nothing inputted...
			//Are we moving already?
			if($player->path){
				//If we are moving, display the button to STOP as well as some text saying how we can always add more movement on...
				
				//Grab finish time...
				$sql = 'SELECT * FROM `ws_auto_movement` WHERE `shipid` = ' . $_SESSION['shipid'];
				$query = queryme($sql);
				$row = mysql_fetch_assoc($query);
				
				//Grab time remaining...
				$rem_time = $row['endtime'] - time();
				
				//_NOW_ display the message:
				$page['text'] .= 'Your ship is currently travelling, you can extend your trip by adding another waypoint by clicking on the map, or you can abort your current journey by clicking the abort button.<br /><br />';
				
				$page['text'] .= 'In sublight for another: ' . parse_time($rem_time) . '<br /><br />';
				
				$page['text'] .= '<form name="abort" id="abort" method="get" action=""><div align="center"><input name="mode" type="submit" id="Abort" value="Abort Sublight" class="form" /></div></form>';
			
			
			} else {
			
				//Else, display normal message
				
				$page['text'] .= 'Select your destination on the map...';	
			
			}
		}
	}
}

/////////CODE ABOVE////////////
//Set mode:
$page['mapmode'] = '0,1,0';

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
