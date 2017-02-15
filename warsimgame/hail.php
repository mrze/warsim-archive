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
$page['text'] .= '<div align="center"><strong>-Communications-</strong></div><br />';

if($player->get_status() == 'normal'){
	//Ok, so if we want to send a message to a ship... thar she blows...
	if($_GET['mode'] == 'hailship'){
		if(is_numeric($_GET['shipid']) AND $ships[$_GET['shipid']]){
			if(can_scan_square($ships[$_GET['shipid']]->x,$ships[$_GET['shipid']]->y)){
				$page['text'] .= 'Use this to send a message to your target. The target ship will be informed of our location and shiptype.<br /><br />';
				
				$page['text'] .= '<strong>Recipient: </strong>' . $ships[$_GET['shipid']]->classname . '<em> ' . $ships[$_GET['shipid']]->shipname . '</em><br />';
				$page['text'] .= '<strong>Piloted by: </strong>' . $ships[$_GET['shipid']]->username . '<br /><br />';
				
				$page['text'] .= '<form name="form1" id="form1" method="post" action="hail.php"><input name="shipid" type="hidden" id="shipid" value="' . $_GET['shipid'] . '" /><div align="center"><textarea name="hailtext" cols="30" rows="5" id="hailtext" class="form">Message Here...</textarea><br /><br /><input name="mode" type="submit" id="mode" value="Send Hail" class="form" /></div></form><br /> <em>[You Cannot use HTML with this and Smileys should never have been invented. Don\'t abuse this.]</em>';
			} else {
				$page['text'] .= 'Use this to send a message to your target. The target ship will be informed of our location and shiptype.<br /><br />';
				
				$page['text'] .= '<strong>Recipient: </strong> XfC948Fsid$3s%^2dsfg@#4 <br />';
				$page['text'] .= '<strong>Piloted by: </strong> bFreFdsf34@$%224dfsd5$^423 <div align="center" class="red">[Data Corruption - Ending Stream]</div><br /><br />';
				$page['text'] .= '<span class="red">Our sensors have failed to detect a ship matching that description within our sensor range. Unfortunatly, a message cannot be sent to that ship.';
			}
		} else {
			$page['text'] .= 'OMG j00 l337 h4x0r! j00 Pwnz mee!! [There has been a user-stupidity inflicted error. Don\'t play with the URL]';
		}
	} else {
	
		//If we want to send a message :D come to the right place...
		if($_POST){
			if(is_numeric($_POST['shipid']) AND $ships[$_POST['shipid']]){
				//Are they in range?
				if(can_scan_square($ships[$_POST['shipid']]->x,$ships[$_POST['shipid']]->y)){
					//Strip HTML tags, br it...
					$_POST['hailtext'] = stripslashes($_POST['hailtext']);
					$_POST['hailtext'] = htmlentities($_POST['hailtext'],ENT_QUOTES);
					$_POST['hailtext'] = nl2br($_POST['hailtext']);
					
					//Add layout
					if($player->dockedin != 0){
						$_POST['hailtext'] = 'Sir! We Recieved a Hail from <em>' . $player->username . '</em> aboard the ' . $player->classname . ' <em>' . $player->shipname . '</em> [Team: ' . $player->teamname . '] from position ' . $player->get_position() . ' [Docked inside the ' . $ships[$player->dockedin]->classname . ' <em>' . $ships[$player->dockedin]->shipname .'</em>]:<br /><br /><div align="center"><div class="yellow" align="left">' . $_POST['hailtext'] . ' [<a href="hail.php?mode=hailship&shipid=' . $player->shipid . '&hail=Hail">Reply?</a>]</div></div>'; 
					} else {
						$_POST['hailtext'] = 'Sir! We Recieved a Hail from <em>' . $player->username . '</em> aboard the ' . $player->classname . ' <em>' . $player->shipname . '</em> [Team: ' . $player->teamname . '] from position ' . $player->get_position() . ':<br /><br /><div align="center"><div class="yellow" align="left">' . $_POST['hailtext'] . ' [<a href="hail.php?mode=hailship&shipid=' . $player->shipid . '&hail=Hail">Reply?</a>]</div></div>';
					}
					//Add SQL UNBUSTING stuff
					$_POST['hailtext'] = addslashes($_POST['hailtext']);
					
					//SQL TIME
					$ships[$_POST['shipid']]->give_event($_POST['hailtext']);
					
					$page['text'] .= '<span class="green">Hail Sent Successfully to the ' . $ships[$_POST['shipid']]->classname . ' <em>' . $ships[$_POST['shipid']]->shipname . '</em>.</span>';
				} else {
					$page['text'] .= '<span class="red">Sir! Sensor reports suggest that the transmission may have failed, since the target ship moved out of sensor range...</span>';
				}
			}
		}
	}
} else {
	$page['text'] .= 'Your ship is either Disabled or Destroyed and you cannot send messages.';
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
