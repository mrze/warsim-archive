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

//Make the overview...
$page['text'] .= '<div align="center"><strong>-Docking-</strong></div><br />';

//Ok, Travel only works when we are not disabled/destroyed, right?
if($player->get_status() != 'normal'){
	$page['text'] .= 'Your ship has been disabled or destroyed. You cannot access this.';
} else {
	
	//We are ok, lets get on with this 'docking' thing...
	//Ok, Heres the five seven:
		//Caps cannot dock. However, some caps with a docking bay can view a list of ships docked inside them...
		//Freightors/Fighters can dock...
	
	//If we are trying to do an action, that goes after this 'important stuff'...
	if(!$_GET['mode']){
		
		$page['text'] .= '<strong>Ships Docked:</strong><br /><br />';
		
		//If we havent got a docking bay, dont even bother...
		if($player->dockingbaymax == 0){
			$page['text'] .= 'Your ship does not have a docking bay.';
		} else {
			//We have a docking bay...		
			//Show total squads docked... and total amount that can fit...
			$page['text'] .= 'Squadrons Docked: ' . $player->dockedwithin . '<br /> Docking Bay Capacity: ' . $player->dockingbaymax;
			
			if($player->dockedwithin){
				//Get ships docked...
				$sql = 'SELECT `shipid` FROM `ws_ships` WHERE `dockedin` = ' . $player->shipid;
				$query = queryme($sql);
				
				$page['text'] .= '<br /><br />';
				
				//List scans...
				while($row =@ mysql_fetch_assoc($query)){
					$button_text = '&nbsp;<form style="display: inline"  method="get" action="dock.php"><input type="hidden" name="mode" value="kickship" /><input type="hidden" name="shipid" value="' . $row['shipid'] . '" /><input type="submit" name="dock" value="Kick Ship" class="form" /></form>';
					$page['text'] .= $ships[$row['shipid']]->small_scan($player->team,$button_text);
				}
			}
		}
	
		$page['text'] .= '<br /><br /><strong>Docked In:</strong><br /><br />';
		
		//If we are a cap... we cant dock in anything...
		if($player->classtype == 'Capital'){
			$page['text'] .= 'Your ship cannot dock inside other ships.';
		} else {
			
			//Ok, so we CAN dock inside stuff, if we are, display a small scan... with some stuff
			if($player->dockedin > 0){
				//Docked!!!
				$page['text'] .= 'Your ship is docked inside:<br /><br />';
				
				//Make docked in ship thingy... and with a 'Leave' button...
				$button_text = '&nbsp;<form style="display: inline"  method="get" action="dock.php"><input type="hidden" name="mode" value="leaveship" /><input type="submit" name="dock" value="Undock" class="form" /></form>';
				$page['text'] .= $ships[$player->dockedin]->small_scan($player->team,$button_text);
			} else {
				//Not docked
				$page['text'] .= 'Your ship is not docked inside any other ships<br /><br />';
				
				//Freelancers cant dock... Period.
				if($player->team == 1){
					$page['text'] .= 'Only People in teams can enjoy the benefits of docking. Get some friends, join a team, then you can dock.';
				} else {
					//Find out if there are any other ships around to dock inside...
					$sql = 'SELECT `shipid` FROM ws_ships s, ws_users u, ws_shiprules sh WHERE s.userid=u.userid AND s.classid=sh.classid AND s.gameid = ' . $_SESSION['gameid'] . ' AND x=' . $player->x . ' AND y=' . $player->y . ' AND team="' . $player->team . '" AND hull>0 AND ionic>0 AND dockedwithin < dockingbaymax';
					$query = queryme($sql);
					
					
					
					//If there are results.. display them... else... display error
					if(mysql_num_rows($query)){
						
						$page['text'] .= 'Click \'Dock\' to dock inside the selected ship:<br /><br /> ';
						
						//Loop thru all the ships
						while($row =@ mysql_fetch_assoc($query)){
							$button_text = '&nbsp;<form style="display: inline"  method="get" action="dock.php"><input type="hidden" name="mode" value="dockinsideship" /><input type="hidden" name="shipid" value="' . $row['shipid'] . '" /><input type="submit" name="dock" value="Dock" class="form" /></form>';
							$page['text'] .= $ships[$row['shipid']]->small_scan($player->team,$button_text);
						}
					} else {
						$page['text'] .= 'There are no ships in this square that you can dock within. ';
					}
				}
			}
		}
	} else {
		
		//OMG! we are trying to dock?
		if($_GET['mode'] == 'dockinsideship'){
			//dock.php?mode=dockinsideship&shipid=5&dock=Dock
			//We need to check that the inputted shipid is valid, and that we can dock inside it... 
			if($ships[$_GET['shipid']]){
				//Valid ship...
				
				//Check we ourselves are a valid ship to use for docking...
				if($player->classtype != 'Capital'){
					//Check if we are at the same location...
					if($player->x == $ships[$_GET['shipid']]->x AND $player->y == $ships[$_GET['shipid']]->y AND $player->dockedin == 0){
					
						//Check teams are the same...
						if($player->team == $ships[$_GET['shipid']]->team){
							//Ship id valid, same spot, check we have room...
							if($ships[$_GET['shipid']]->dockedwithin < dockingbaymax){
								
								//We CAN dockinside, so lets start it off by making our ship the centre of attention
								//by changing its dockedinside thingy...
								$player->dockedin = $_GET['shipid'];
								
								//And update it in the DB...
								$sql = 'UPDATE `ws_ships` SET `dockedin` = ' . $player->dockedin . ', path="" WHERE shipid = ' . $player->shipid;
								$query = queryme($sql);
								
								$sql = 'DELETE FROM `ws_auto_movement` WHERE shipid = ' . $player->shipid;
								$query = queryme($sql);
								
								//And, add one to the target dock recipient...
								$ships[$_GET['shipid']]->dockedwithin++;
								$sql = 'UPDATE `ws_ships` SET dockedwithin = dockedwithin + 1 WHERE shipid = ' . $_GET['shipid'];
								$query = queryme($sql);
								
								$page['text'] .= 'Docking Successful! You are now inside the ' . $ships[$_GET['shipid']]->classname . ' <em>' . $ships[$_GET['shipid']]->shipname . '</em>';
								
								//Give messages...
								$squad_message = 'You have docked inside the ' . $ships[$_GET['shipid']]->classname . ' <em>' . $ships[$_GET['shipid']]->shipname . '</em>.';
								$player->give_event($squad_message);
								$dockee_message = 'The ' . $player->classname . ' <em>' . $player->shipname . '</em> has docked in your docking bays.';
								$ships[$_GET['shipid']]->give_event($dockee_message);
							
							} else {
								$page['text'] .= 'Trans-Dimensional Hull Error. The intended dock target is full. Thus, you and your swashbuckling band cannot dock inside it.';
							}
						} else {
							$page['text'] .= 'Whats up bro? Mackin\' wit tha enemy?! We cant have tizzy! [Translation: You cannot dock inside a ship from a different team.]';
						}
					} else {
						$page['text'] .= 'Spatial Space Flux Error. [The intended dock target is not at the same square as your ship].';
					}
				} else {
					$page['text'] .= 'Soge wins for docking an ISD inside itself! :D NO. This Can NOT happen. Bad Soge. And bad you for doing it too. Capital ships CANNOT dock.';
				}
			} else {
				$page['text'] .= 'Supplied ShipID is invalid.';
			}			
		}
		
		//OMG! we are trying to kick a docked ship?
		if($_GET['mode'] == 'kickship'){
			//Things to check:
			//	We are the host ship
			//	The smaller ship is aboard us...
			//	Um... yeah... valid ids...
			
			//dock.php?mode=kickship&shipid=6&dock=Kick+Ship
			if($ships[$_GET['shipid']]){
				if($ships[$_GET['shipid']]->dockedin == $player->shipid){
					$ships[$_GET['shipid']]->x = $player->x;
					$ships[$_GET['shipid']]->y = $player->y;
					$ships[$_GET['shipid']]->dockedin = 0;
					
					//Kick ship :D
					$sql = 'UPDATE `ws_ships` SET x = ' . $ships[$_GET['shipid']]->x . ', y = ' . $ships[$_GET['shipid']]->y . ', dockedin = ' . $ships[$_GET['shipid']]->dockedin . ' WHERE shipid = ' . $_GET['shipid'];
					$query = queryme($sql);
					
					$player->dockedwithin--;
					
					//Update host ship...
					$sql = 'UPDATE `ws_ships` SET dockedwithin = dockedwithin - 1 WHERE shipid = ' . $player->shipid;
					$query = queryme($sql);
					
					$page['text'] .= 'The selected ship was kicked successfully from your docking bays.';
					
					//MEssages
					$dockee_message = 'You have kicked the ' . $ships[$_GET['shipid']]->classname . ' <em>' . $ships[$_GET['shipid']]->shipname . '</em> from your docking bays.';
					$player->give_event($dockee_message);
					$squad_message = 'You have been personally kicked from the docking bays of the ' . $player->classname . ' <em>' . $player->shipname . '</em> by the captain.';
					$ships[$_GET['shipid']]->give_event($squad_message);
				} else {
					$page['text'] .= 'Spatial Space Flux Error. [The intended kick target is not within the confines of your ship].';
				}
			} else {
				$page['text'] .= 'Supplied ShipID is invalid.';
			}
		}
		
		if($_GET['mode'] == 'leaveship'){
			//We can leave the ship we are docked in...
			if($player->dockedin != 0){
				//Ok, so lets get it on... wait...
				//Lets Get OUT!
				
				$olddock = $player->dockedin;
				
				$ships[$olddock]->dockedwithin--;
				
				//First, remove the ship number from the carrier...
				$sql = 'UPDATE `ws_ships` SET dockedwithin = dockedwithin - 1 WHERE shipid = ' . $olddock;
				$query = queryme($sql);
				
				$player->x = $ships[$olddock]->x;
				$player->y = $ships[$olddock]->y;
				$player->dockedin = 0;
				
				//now, update das player...
				$sql = 'UPDATE `ws_ships` SET dockedin = 0, x= ' . $player->x . ', y = ' . $player->y . ' WHERE shipid = ' . $player->shipid;
				$query = queryme($sql);
				
				//Done...
				$page['text'] .= 'You have successfully undocked.';
				
				//Events...
				$dockee_message = 'The ' . $player->classname . ' <em>' . $player->shipname . '</em> has undocked from your ship.';
				$ships[$olddock]->give_event($dockee_message);
				$squad_message = 'You have successfully undocked from the ' . $ships[$olddock]->classname . ' <em>' . $ships[$olddock]->shipname . '</em>.';
				$player->give_event($squad_message);
			} else {
				$page['text'] .= 'The PHP parser gives you a look something like this:<br /><br />O_o<br /><br />As your ship is not docked... Perhaps you should see a doctor...';
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