<?php
//joingame.php
//Creates a ship in the database for the specific game chosen '$_GET['game']'
//Checks $_GET['game'] is a legal game
//Allows the user to fill in a form to build the ship
//Inputs the ship into a db
//Moves onto -> joinship.php?shipid=? which logs into the game automatically

//Session handling & Page GZipping
session_start();
ob_start('ob_gzhandler');

//Includes:
require_once 'inc/connect.php';
require_once 'inc/class.php';
require_once 'inc/functions.php';
require_once 'inc/assignplanets.php';

//Timer
$clock = new timer;
$clock->start();

//If not logged in, kick out...
if(!$_SESSION['loggedin']){
	$_SESSION = array();
	
	$error = 'That page has restricted access, you were not logged in, or your session expired.';
	
	$error = urlencode($error);
	
	redirect('index.php?error=' . $error);
}

//Check the validity of $_GET['game']
if(!is_numeric($_GET['game'])){
	redirect('lobby.php');
}

$sql = 'SELECT * FROM ws_games WHERE gameid = ' . $_GET['game'];
$query = queryme($sql);
$game = mysql_fetch_assoc($query);

//If there ISNT one result, redirect to lobby... [IE: must be one result to see the create ship screen...]
if(mysql_num_rows($query) != 1){
	redirect('lobby.php');
}

if($game['option_oneshiplimit'] == 1)
{
	$sql = 'SELECT `shipid` FROM `ws_ships` WHERE userid=' . $_SESSION['userid'] . ' AND gameid=' . $_GET['game'] . ' AND hull>0 AND ionic>0';
	$query = queryme($sql);
	if(mysql_num_rows($query) > 0){
		$error = 'That game has One ship per player Enforced. You already have a suitable ship in that game. You can only recreate if your ship becomes disabled or destroyed. If your the Suicidal type, Fly into the Sun to Destroy your ship.';
	
		$error = urlencode($error);
		
		redirect('lobby.php?error=' . $error);
	}
}

if($game['option_restricted_ships']){
	$restrict = explode(',',$game['option_restricted_ships']);
} else {
	$restrict = array();
}

if($game['option_point_for_ships']){
	redirect('joingamep4s.php?game=' . $_GET['game']);
}

//Ok, we are safe... FOR NOW...
//Check to see if somebody has sent a ship request....
if($_GET['mode'] == 'createship'){
	//Build the ship
	if($game['option_team'] == 0){
		$_GET['team'] = 1;
	}
	
	//Check everything is there...
	if($_GET['shipid'] && $_GET['shipname'] && is_numeric($_GET['team']) && is_numeric($_GET['shipid'])){
		//Check isnt restricted...
		if(!in_array($row['classid'],$restrict)){
			//Check password...
			if($game['option_gamepass']){
				if(trim(strtoupper($_GET['gamepass'])) != trim(strtoupper($game['option_gamepass']))){
					$error = 'Incorrect Game password for that game.';
	
					$error = urlencode($error);
		
					redirect('lobby.php?error=' . $error);
				}
			}
			//Validate team...
			if($_GET['team'] != 1){
				$sql = 'SELECT * FROM `ws_teams` WHERE `teamid` = ' . $_GET['team'] . ' AND `gameid` = ' . $_GET['game'] . ' AND `teampassword`="' . $_GET['teampass'] . '"';
				$query = queryme($sql);
				if(mysql_num_rows($query) != 1){
					$error = 'Team Password Incorrect.';
	
					$error = urlencode($error);
		
					redirect('joingame.php?error=' . $error);
				} else {
					$team = mysql_fetch_assoc($query);
				}
			}
			
			//Freelance restrict on..?
			if($game['option_must_join_team']){
				if($_GET['team'] == 1){
					$error = 'Invalid Team Selection.';
	
					$error = urlencode($error);
		
					redirect('joingame.php?game=' . $_GET['game'] . '&error=' . $error);
				}
			}
			
			//Check if it needs to be docked...
			if($game['option_team_limit_respawn']){
				//Grab team leader shipid...
				$dockedinside = $team['teamleadershipid'];
			}
			
			//Radially?
			if($game['option_limit_respawn_radially']){
				//new location must be > 4 squares away from previous...

				//Grab last location...
				$sql = 'SELECT * FROM `ws_games_lastspawn` WHERE `gameid` = ' . $_GET['game'] . ' AND `userid` = ' . $_SESSION['userid'];
				
				$query = queryme($sql);
				
				if(mysql_num_rows($query) > 0){
					//We have a last spawn pos...
					$lsp = mysql_fetch_assoc($query);
					
					if($lsp['timestamp'] > (time() - 1200)){
						//Worry about it
						if(distance($_GET['x'],$_GET['y'],$lsp['x'],$lsp['y']) <= 4){

							//Are we in a team [so we can check if we have a cap near us?
							if($_GET['team'] != 1){
								$sql = 'SELECT `shipid` FROM ws_ships s, ws_shiprules sr WHERE s.classid=sr.classid AND s.gameid = ' . $_GET['game'] . ' AND sr.classtype = "Capital" AND sr.dockingbaymax` > s.dockedwithin` AND s.x = ' . $_GET['x'] . ' AND s.y = ' . $_GET['y'] . ' AND s.teamid = ' . $_GET['team'];
								
								//Check if any there...
								$query = queryme($sql);
								
								if(mysql_num_rows($query) > 0){
									//We have a winner! Dock in that ship!
									$std = mysql_fetch_assoc($query);
									
									//Dock within the first ship!
									$dockedinside = $std['shipid'];
								}
							}
							//If $dockedinside is not set, then -> map screen again, since no caps present...
							if(!$dockedinside){
								$error = 'You are respawning within your restricted area. You must be atleast 5 squares away from your last death point, or you must wait 10 mins before next respawn.';
	
								$error = urlencode($error);
		
								redirect('joingame.php?game=' . $_GET['game'] . '&error=' . $error);
							}
						}
					}
				}
			} //\Radially
			
			//Add ship to the database
			$new_shipid = add_ship($_GET['game'],$_GET['shipid'],$_GET['shipname'],$_GET['team'],$_GET['x'],$_GET['y'],$dockedinside);
			
			//Finish 'er off with a redirect to the login page...
			redirect('joinship.php?shipid=' . $new_shipid);
			
		} else {
			$error = 'The ship type you selected has been restricted in this game. You cannot use it.';
		
			$error = urlencode($error);
	
			redirect('joingame.php?game=' . $_GET['game'] . '&error=' . $error);
		}
	
	//Some fields are missing... so display error and reload		
	} else {
		$error = 'You are missing one or more fields. Make sure every field is filled in.';
		
		$error = urlencode($error);
	
		redirect('joingame.php?game=' . $_GET['game'] . '&error=' . $error);
	}
}
	
//Display Ship List + Form to input
//in the form: <option>shipname</option>
//and every so often: <option>Freightors: </option> [titles...]

$sql = 'SELECT * FROM `ws_shiprules` WHERE display=1 ORDER BY `classtype` ASC, `shiplength` DESC';
$query = queryme($sql);

$lasttype = '';

while($row = mysql_fetch_assoc($query)){
	if(!in_array($row['classid'],$restrict)){
		//Add in a spacer row if the type changed
		if($row['classtype'] != $lasttype){
			$page['forminput'] .= '<option onclick="loadshipstats(\'None Selected\',\'Acclamator.gif\',\'None Selected\',\'N/A\',\'N/A\',\'N/A\',\'N/A\',\'N/A\',\'N/A\',\'N/A\',\'N/A\',\'N/A\',\'N/A\')">Type ' . $row['classtype'] . ': </option>';
			$lasttype = $row['classtype'];
		}
		
		//MArine Number...
		$marines = num_marines($row['passengers'],$row['dedicated_transport'],$row['classtype'],$row['maxsquadsize']);
		
		//Change Ded tp to 'Yes/No'...
		if($row['dedicated_transport']){
			$row['dedicated_transport'] = 'Yes';
		} else {
			$row['dedicated_transport'] = 'No';
		}
		
		//Weapons...
		$weapons = '';
		
		if($row['hvlasermax']){
			$weapons .= ' HV: ' . ($row['hvlasermax'] * $row['maxsquadsize']);
		}
		if($row['ioncannonmax']){
			$weapons .= ' IC: ' . ($row['ioncannonmax'] * $row['maxsquadsize']);
		}
		if($row['turbolasermax']){
			$weapons .= ' TL: ' . ($row['turbolasermax'] * $row['maxsquadsize']);
		}
		if($row['ionbatterymax']){
			$weapons .= ' IB: ' . ($row['ionbatterymax'] * $row['maxsquadsize']);
		}
		if($row['protonmax']){
			$weapons .= ' PT: ' . ($row['protonmax'] * $row['maxsquadsize']);
		}
		if($row['concussionmax']){
			$weapons .= ' CM: ' . ($row['concussionmax'] * $row['maxsquadsize']);
		}
		if(!$weapons){
			$weapons = 'None';
		} else {
			$weapons = '<br />' . $weapons;
		}
		///Weapons
		 
		 //Add Group...
		if($row['maxsquadsize'] > 1 AND $row['maxsquadsize'] <= 3){
			$row['classtype'] = $row['classtype'] . ' Assault Group';
		}
		if($row['maxsquadsize'] == 12){
			$row['classtype'] = $row['classtype'] . ' Squadron';
		} 
		 
		$page['forminput'] .= '<option value="' . $row['classid'] . '" onclick="loadshipstats(\'' . $row['classname'] . '\',\'' . $row['normalimage'] . '\',\'' . $row['classtype'] . '\',\'' .  $row['maxsquadsize'] . '\',\'' . number_format($row['hullmax']  * $row['maxsquadsize']) . '\',\'' . number_format($row['shieldsmax']  * $row['maxsquadsize']) . '\',\'' . number_format($row['ionicmax']  * $row['maxsquadsize']) . '\',\'' . number_format($row['passengers']  * $row['maxsquadsize']) . '\',\'' . number_format($marines  * $row['maxsquadsize']) . '\',\'' . $row['dedicated_transport']  . '\',\'' . $weapons . '\',\'' . ($row['tractormax']  * $row['maxsquadsize']) . '\',\'N/A\',\'' . $row['sublight'] . '\')">&raquo; ' .  dot_string($row['classname']) . '</option>';
	}
}

//Team dropdown...
$sql = 'SELECT * FROM `ws_teams` WHERE `gameid` = ' . $_GET['game'] . ' OR `gameid` = 0 ORDER BY `teamid`';
$query = queryme($sql);

while($row =@ mysql_fetch_assoc($query)){
	if(!$game['option_must_join_team'] OR $row['gameid'] != 0){
		$teamstuff .= '<option value="' . $row['teamid'] . '">' . dot_string($row['teamname']) . '</option>';
	}
}

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

//Respawn map...
$sql = 'SELECT * FROM `ws_games_lastspawn` WHERE `gameid` = ' . $_GET['game'] . ' AND `userid` = ' . $_SESSION['userid'];
$query = queryme($sql);

if($game['option_limit_respawn_radially']){
	if(mysql_num_rows($query) > 0){
		$lsp = mysql_fetch_assoc($query);
		
		if($lsp['timestamp'] > (time() - 1200)){
			//Display partial map...
			$page['map'] .= ' spawn_radius(' . $lsp['x'] . ',' . $lsp['y'] . ',4);';
		}
	} 
}


//Display page...
include 'templates/temp_joingame.php';

//Stop the timer
$totaltime = $clock->stop();

echo $totaltime;

?>