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
	if($player->classtype == 'Capital'){
		if($_GET['mode'] == 'hailall'){
			$page['text'] .= 'Use this to send a message to all the ships in the System. All ships will be informed of our location and shiptype.<br /><br />';
					
			$page['text'] .= '<strong>Recipient: </strong>All Ships</em><br />';
			$page['text'] .= '<strong>Piloted by: </strong>Everybody<br /><br />';
					
			$page['text'] .= '<form name="form1" id="form1" method="post" action="hailall.php"><input name="shipid" type="hidden" id="shipid" value="' . $_GET['shipid'] . '" /><div align="center"><textarea name="hailtext" cols="30" rows="5" id="hailtext" class="form">Message Here...</textarea><br /><br /><input name="mode" type="submit" id="mode" value="Send Hail" class="form" /></div></form><br /> <em>[You Cannot use HTML with this and Smileys should never have been invented. Don\'t abuse this.]</em>';
		} else {
		
			//If we want to send a message :D come to the right place...
			if($_POST){
				//Strip HTML tags, br it...
				$_POST['hailtext'] = stripslashes($_POST['hailtext']);
				$_POST['hailtext'] = htmlentities($_POST['hailtext'],ENT_QUOTES);
				$_POST['hailtext'] = nl2br($_POST['hailtext']);
						
				//Add layout
				$_POST['hailtext'] = 'Sir! We Recieved a System Wide Unencoded Hail from <em>' . $player->username . '</em> aboard the ' . $player->classname . ' <em>' . $player->shipname . '</em> [Team: ' . $player->teamname . '] from position ' . $player->get_position() . ':<br /><br /><div align="center"><div class="yellow" align="left">' . $_POST['hailtext'] . ' [<a href="hail.php?mode=hailship&shipid=' . $player->shipid . '&hail=Hail">Reply?</a>]</div></div>'; 
						
				//Add SQL UNBUSTING stuff
				$_POST['hailtext'] = addslashes($_POST['hailtext']);
						
		
				//SQL TIME
				$player->give_game_event($_POST['hailtext']);
						
				$page['text'] .= '<span class="green">Hail Sent Successfully to all ships in the system.</span>';
			}
		}
	} else {
		$page['text'] .= 'Sending a message to all the ships in the system will overload our sensors and communications facilities. The operations manual says only Capital Class ships have powerful enough equipment to send messages of this type.';
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
