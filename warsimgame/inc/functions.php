<?php
//General Functions

//List:
/*
print_array();
redirect();
create_account();
login();
random_big_ship_img();
add_ship();
generate_status_overview($shipid)
make_bar(value,width);
make_weapons_bar(ready,reloading,destroyed)
blend_colour()
blend_type()
distance()
is_between($target,$number1,$number2)
get_bearing($x,$y,tox,toy)
path_square($path)
make_path($path)
dot_string($string);
*/

function print_array($array)
{
	//Alias of print_r with <pre></pre> around it
	
	$text = '<pre>' . print_r($array,1) . '</pre>';
	
	return $text;
}

function redirect($url)
{
	//Redirect the browser to the url specified
	
	header('Location: http://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/' . $url);
	exit;
}

function create_account ($handle,$password,$email)
{
	//check if any fields have been missed
	if(!$handle or !$password or !$email){
		$message = '<div class="red">Error, you are missing one or more form fields. Please fill all of them out completely.</div>';
	
	//everything alright so far
	} else {
		//Password cant be same as handle...
		if($password == $handle){
			$message = '<div class="red">Error, you cannot use your handle as your password.</div>';
					
		} else {
			$sql = 'SELECT * FROM `ws_users` WHERE username="' . $handle . '"';
			$query = queryme ($sql);
			$num_rows = mysql_num_rows($query);
			
			if($num_rows == 0){			
				$sql = 'INSERT INTO `ws_users` (`username`,`userpass`,`useremail`,`userip`,`signupdate`,`auth`) VALUES ("' . $handle . '","' . md5(md5(md5(md5($password)))) . '","' . $email . '","' . $_SERVER['REMOTE_ADDR'] . '","' . time() . '","1")';
				$query = queryme ($sql);
				
				$message = '<div class="green">Successfully Created your account.<br />Please Login using the form to the right</div>';
			} else {
				$message = '<div class="red">Error, that handle is already in use.</div>';
			}
		}
	}
	return $message;
}

function login ($handle,$password)
{
	//Check for both handle and password input
	if(!$handle or !$password){
		$message = '<div class="red">Error, both Handle and Password fields must be filled out.</div>';
	} else {
		//Check for existing database entry
		$sql = 'SELECT * FROM `ws_users` WHERE username="' . $handle . '" and userpass="' . md5(md5(md5(md5($password)))) . '"';
		$query = queryme ($sql);

		//Everything is good...
		if(mysql_num_rows($query) == '1'){
			$results = mysql_fetch_assoc($query);
			
			if($results['auth'] == '1'){
				
				$_SESSION['loggedin'] = 1;
				$_SESSION['userid'] = $results['userid'];
				$_SESSION['username'] = $results['username'];
				
				//$sql = 'INSERT INTO 
				
				redirect ('lobby.php');
			
			} else {
				$message = '<div class="red">Login Failed. Your Account has not been Authorised yet.</div>';
			}
		} else {
			//Not found
			$message = '<div class="red">Login Failed. Incorrect Username/Password.</div>';
		}
	}

	return $message;
}

function random_big_ship_img()
{
	//Gets a random 'big ship' image url
	
	return 'images/ships/bigship/' . rand(1,10) . '.jpg';
}

function add_ship($game,$classid,$name,$team,$x,$y,$dockedin = 0)
{
	//Adds a ship to the database... Returns added shipid
	
	$name = str_replace("'",'`',$name);
	$name = htmlentities($name);
	$team = str_replace("'",'`',$team);
	
	$sql = 'SELECT * FROM `ws_shiprules` WHERE `classid` = ' . $classid;
	$query = queryme($sql);
	$sr = mysql_fetch_assoc($query);
	
	/*********************************************************************************************************
	**********************************************************************************************************
											OPT!
	**********************************************************************************************************
	*********************************************************************************************************/
	
	//Update max's to relate to ships in sqn...
	$sr['hullmax'] = $sr['maxsquadsize'] * $sr['hullmax'];
	$sr['shieldsmax'] = $sr['maxsquadsize'] * $sr['shieldsmax'];
	$sr['ionicmax'] = $sr['maxsquadsize'] * $sr['ionicmax'];
	$sr['hvlasermax'] = $sr['maxsquadsize'] * $sr['hvlasermax'];
	$sr['ioncannonmax'] = $sr['maxsquadsize'] * $sr['ioncannonmax'];
	$sr['protonmax'] = $sr['maxsquadsize'] * $sr['protonmax'];
	$sr['concussionmax'] = $sr['maxsquadsize'] * $sr['concussionmax'];
	$sr['turbolasermax'] = $sr['maxsquadsize'] * $sr['turbolasermax'];
	$sr['passengers'] = $sr['maxsquadsize'] * $sr['passengers'];
	
	//Set marine number
	//Dedicated assault ship? Add extra...
	$sr['passengers'] = num_marines($sr['passengers'],$sr['dedicated_transport'],$sr['classtype'],$sr['maxsquadsize']);
	
	//Are we docked inside the team leaders ship? We need to grab X,Y and set the dock id properly...
	if($dockedin != 0){
		$sql = 'SELECT `x`,`y` FROM `ws_ships` WHERE `shipid` = ' . $dockedin;
		$query = queryme($sql);
		$dockedinship = mysql_fetch_assoc($query);
		
		$x = $dockedinship['x'];
		$y = $dockedinship['y'];
		
		if($sr['classtype'] == 'Capital'){
			$dockedin = 0;
			
			$modx = rand(1,3) - 2;
			$mody = rand(1,3) - 2;
			
			$x += $modx;
			$y += $mody;
			
			$x = max(0,min(19,$x));
			$y = max(0,min(19,$y));
			$set = 1;
		} else {
			//Update ship holding the other ships...
			$sql = 'UPDATE `ws_ships` SET dockedwithin=dockedwithin+1 WHERE shipid = ' . $dockedin;
			$query = queryme($sql);
			$set = 2;
		}
		
		
	}
	
	
	//Build the ship...
	$sql = "INSERT INTO `ws_ships` 
(`joingametime`,`marines`,`tractor`,`classid` , `gameid` , `userid` , `shipname` , `team` , `x` , `y` , `dockedin` , `squadsize`,`hull` , `shieldgen`,`shields` , `ionic` , `hvlaser` , `hvlaserdam` , `ioncannon` , `ioncannondam` , `turbolaser` , `turbolaserdam` , `ionbattery` , `ionbatterydam` , `proton` , `protondam` , `concussion` , `concussiondam` )
VALUES 
('" . time() . "','" . $sr['passengers'] . "','" . $sr['tractormax'] . "','$classid', '$game', '$_SESSION[userid]', '$name', '" . $team . "', '$x', '$y', '$dockedin', '$sr[maxsquadsize]','$sr[hullmax]',1,'$sr[shieldsmax]', '$sr[ionicmax]', '$sr[hvlasermax]', '0', '$sr[ioncannonmax]', '0', '$sr[turbolasermax]', '0', '$sr[ionbatterymax]', '0', '$sr[protonmax]', '0', '$sr[concussionmax]', '0'
);";
	$query = queryme($sql);
	
	//Grab the Added Ship ID for giving events and returning
	$added_ship_id = mysql_insert_id();

	//Add a ship to the shipcount...
	queryme('UPDATE `ws_users` SET `usershipcount`=`usershipcount`+1 WHERE userid = ' . $_SESSION['userid']);
	
	//Get join message...
	$sql = 'SELECT `option_joinmsg` FROM `ws_games` WHERE gameid = ' . $game;
	$query = queryme($sql);
	$g = mysql_fetch_assoc($query);
	$g['option_joinmsg'] = str_replace('[position]','[' . $x . ',' . $y . ']', $g['option_joinmsg']);
	$g['option_joinmsg'] = addslashes($g['option_joinmsg']);
	
	//Give join message...
	if($g['option_joinmsg']){
		$sql = 'INSERT INTO `ws_events` (shipid,time,text) VALUES (' . $added_ship_id . ',' . (time() + 2) . ',"' . $g['option_joinmsg'] . '");';
		$query = queryme($sql);	
	}
	
	//if($set == 2){
		//Join Fighter
	//}
	
	return $added_ship_id;
}

function num_marines($pass,$dt,$type,$ss)
{
	if($type != 'Fighter'){
		if($dt == 1){
			//Max 250 marines for each transport ship [others are drivers, pilots, etc...]
			$pass = min(250*$ss,$pass - (1*$ss)); // -1 for pilot...
		} else {
			//If over 1000, need more support crew...
			$pass = floor($pass * 0.3);
		}
	} else {
		$pass = 0;
	}
	
	return $pass;
}

function generate_status_overview($shipid)
{
	$text .= '<img src="images/ships/' . $g_ships[$shipid]->normalimage . '" /><br />
	Pilot: ' . $g_ships[$shipid]->username . '<br />
	Shiptype: ' . $g_ships[$shipid]->classname . '<br />
	Hull: ' . $g_ships[$shipid]->hull . ' / ' . $g_ships[$shipid]->hullmax;
	
	return $text;	
}

function make_bar($value1,$value2,$text,$width = '90%')
{
	$value =@ ($value1 / $value2) * 100;

	$value = round($value,2);
	
	$value2 = 100 - $value;

	return '<div align="center">
<table width="' . $width . '" height="17" cellpadding="0" cellspacing="0" onmouseover="return overlib(\'' . $text . ' at ' . $value . '%\', CAPTION, \'' . $text . '\');" onmouseout="return nd();">
<td width="' . $value . '%" class="greenbg"></td>
<td width="' . $value2 . '%" class="redbg"></td>
</table>
</div>';
}

function blend_colour($newcolour,$existingcolour){
	//Used in the mapmaking process, to blend scanner blip colours, input 'new, then 'current', it outputs the result

	if(!$existingcolour){
		return $newcolour;
	}

	if($existingcolour == 'none'){
		return $newcolour;
	}
	
	if($existingcolour == 'green'){
		if($newcolour == 'green'){
			return 'green';
		}
		
		if($newcolour == 'red'){
			return 'blue';
		}
		
		if($newcolour == 'none'){
			return 'green';
		}	
	}
	
	if($existingcolour == 'blue'){
		if($newcolour == 'green'){
			return 'blue';
		}
		
		if($newcolour == 'red'){
			return 'blue';
		}
		
		if($newcolour == 'none'){
			return 'blue';
		}	
	}
	
	if($existingcolour == 'red'){
		if($newcolour == 'green'){
			return 'blue';
		}
		
		if($newcolour == 'red'){
			return 'red';
		}
		
		if($newcolour == 'none'){
			return 'red';
		}	
	}
}

function blend_type($newtype,$existingtype){
	//Used in the mapmaking process, input the newtype and the current type, and it gives you the result...
	if($existingtype == 'wreck' OR !$existingtype){
		return $newtype;
	} else {
		if($newtype != 'wreck'){
			return 'fleet';
		} else {
			return $existingtype;
		}
	}
}

function distance($x1,$y1,$x2,$y2)
{
//		»	Pythagorean Distance between [x1,y1] and [x2,y2]
	return round(sqrt(pow(($x2 - $x1),(2)) + pow(($y2 - $y1),(2))),2);
}

function is_between($target,$number1,$number2)
{
	if($target <= $number1 AND $target >= $number2){
		return true;
	} 
	if($target >= $number1 AND $target <= $number2){
		return true;
	}
	
	return false;
}

function get_bearing($x,$y,$to_x,$to_y)
{	
	//Returns the bearing [in degrees] from $x,$y to $to_x,$to_y
	
	$run = $to_x - $x;
	$rise  = $to_y - $y;
	
	if($run == 0){
		if($rise > 0){
			$bearing = 360;
		} else {
			$bearing = 180;
		}
	}
	if($rise == 0){
		if($run > 0){
			$bearing = 90;
		} else {
			$bearing = 270;
		}
	}
	
	if(!$bearing){
		$gradient = $rise / $run;
		$angle = atan($gradient) * 180 / pi();
		
		if($rise > 0){
			if($run > 0){
				$bearing = 90 - $angle;
			} else {
				$bearing = 360 - (90+$angle);
			}
		} else {
			if($run > 0){
				$bearing = 180-(90+$angle);
			} else {
				$bearing = 270-$angle;
			}
		}
	}
	
	return $bearing;
}

function path_square($path)
{
//		»	Returns all but the last square of a path

	$path = explode(',',$path);
	array_pop($path);
	$path = implode(',',$path);
	return $path;
}

function last_square($path)
{
//		»	Returns the last square of a path. 
	$path = explode(',',$path);
	return array_pop($path);
}

function make_path($path)
{
//		»	Generates the javascript for the players path, and returns it.
	$path2 = explode(",",$path);
	$last_square = array_pop($path2);
	$move .= 'place_movement2(\'' . $last_square . '\',\'playerdestination.gif\',\'Destination\');';
	$path = path_square($path);
	$path = explode(',',$path);
	foreach($path as $square){
		if($square){
			$move .= 'place_movement2("' . $square . '","playerpath.gif","Path");';
		}
	}
	return $move;
}

function generate_path($from_x,$from_y,$to_x,$to_y)
{
//		»	Generates a path from $from_x,$from_y to $to_x,$to_y
//		»	Returns a text string with the path: "10-10,11-11,11-12,11-13...."
	
	global $planet_type;
	global $planet_x;
	global $planet_y;
	
	if($planet_type){
		foreach($planet_type as $k=>$pt){
			if($pt == 'Sun'){
				$sun_at[$planet_x[$k]][$planet_y[$k]] = true;
			}
		}
	}
	
	$real_from_x = $from_x;
	$current_x = $from_x;
	
	$real_from_y = $from_y;
	$current_y = $from_y;
	
	$real_to_x = $to_x;
	$real_to_y = $to_y;
	
	while($current_x != $real_to_x OR $current_y != $real_to_y){
		//Simple homing stuff
		if($real_to_x > $current_x){
			$mod_x = 1;
		}
		if($real_to_x < $current_x){
			$mod_x = -1;
		}
		if($real_to_x == $current_x){
			$mod_x = 0;
		}
		if($real_to_y > $current_y){
			$mod_y = 1;
		}
		if($real_to_y < $current_y){
			$mod_y = -1;
		}
		if($real_to_y == $current_y){
			$mod_y = 0;
		}
		
		//Check if your about to ram into a sun...
		if($sun_at[$current_x + $mod_x][$current_y + $mod_y]){
			//If we actually want to ram into a star...
			if($real_to_x != ($current_x + $mod_x) OR $real_to_y != ($current_y + $mod_y)){
				//Work out WHERE we need to go after the next square... 
				if($real_to_x > $current_x){
					$new_mod_x = 1;
				}
				if($real_to_x < $current_x){
					$new_mod_x = -1;
				}
				if($real_to_x == $current_x){
					$new_mod_x = 0;
				}
				if($real_to_y > $current_y){
					$new_mod_y = 1;
				}
				if($real_to_y < $current_y){
					$new_mod_y = -1;
				}
				if($real_to_y == $current_y){
					$new_mod_y = 0;
				}
								
				$intended_x = $current_x + $mod_x + $new_mod_x;
				$intended_y = $current_y + $mod_y + $new_mod_y;
								
				if($mod_x == 0){
					if($mod_x + 1 < 20){
						$mod_x = $mod_x + 1;
					} else {
						$mod_x = $mod_x - 1;
					}
				} else {
					if($mod_y == 0){
						if($mod_y + 1 < 20){
							$mod_y = $mod_y + 1;
						} else {
							$mod_y = $mod_y - 1;
						}
					} else {
						$current_x = $current_x + $mod_x;
						$current_y = $current_y + 0;
						
						$logging .= $current_x . "-" . $current_y . ",";
								
									
						$current_x = $current_x + $mod_x;
						$current_y = $current_y + $mod_y;
						
						$logging .= $current_x . "-" . $current_y . ",";
							
									
						$current_x = $current_x + 0;
						$current_y = $current_y + $mod_y;					
						
						$logging .= $current_x . "-" . $current_y . ",";
													
						$done = 1;
					}
				}
			}
		}
		if($done == '1'){
			$done = 0;
		} else {
			$current_x = $current_x + $mod_x;
			$current_y = $current_y + $mod_y;
			$logging .= $current_x . "-" . $current_y . ",";
		}
	}
	
	$logging = substr($logging,0,strlen($logging)-1);
	
	return $logging;
}

function count_squares($path)
{
//		»	Returns the number of squares in a path.
	$array = explode(',',$path);
	return count($array);
}

function parse_time($seconds)
{
//		»	Generates an output for the time remaining...
	
	//Stop Negative times/numbers:
	if($seconds < 0){
		$seconds = 0;
	}
	
	//Days:
	$days = floor($seconds / (60*60*24));
	$seconds = $seconds % (60*60*24);
	$hours = floor($seconds / (60*60));
	$seconds = $seconds % (60*60);
	$minutes = floor($seconds / (60));
	$seconds = $seconds % (60);
	
	if($minutes < 10 AND ($hours OR $days)){
		$minutes = '0' . $minutes;
	}
	if($seconds < 10 AND ($minutes OR $hours OR $days)){
		$seconds = '0' . $seconds;
	}
	
	if($days){
		return $days . 'd ' . $hours . 'h ' . $minutes . 'm ' . $seconds . 's';
	} else {
		if($hours){
			return $hours . 'h ' . $minutes . 'm ' . $seconds . 's';
		} else {
			if($minutes){
				return $minutes . 'm ' . $seconds . 's';
			} else {
				return $seconds . 's';
			}
		}
	}
}

function can_scan_square($x,$y)
{
	global $player;
	global $ships;
	global $teamlist;
	global $planet_x;
	global $planet_y;
	
	if($teamlist){
		foreach($teamlist as $teamid){
			//Reset some arrays...
			$blocking_planets_y = array();
			$blocking_planets_x = array();
			
			//Check If square is in player Scan Range
			if($ships[$teamid]->get_sensor_range() >= distance($ships[$teamid]->x,$ships[$teamid]->y,$x,$y) AND $ships[$teamid]->dockedin == 0){	
				//If it is, we are in luck...
				//Check if there are planets nearby...
				if($planet_x){
					foreach($planet_x as $key=>$p_name){
						//Check me :D
						if($planet_x[$key] != $ships[$teamid]->x OR $planet_y[$key] != $ships[$teamid]->y){
							if(is_between($planet_x[$key],$ships[$teamid]->x,$x) AND is_between($planet_y[$key],$ships[$teamid]->y,$y)){
								//If it is between it, we should put its X and Y values in a nice list... 
								$blocking_planets_y[] = $planet_y[$key];
								$blocking_planets_x[] = $planet_x[$key];
							}
						}
					} //Next planet...
				}
				
				$ok = true;
				
				//We have gotten this far: planet is in range... and possible planets that can block have been loaded into $blocking_planets...
				if($blocking_planets_x){
					foreach($blocking_planets_x as $kpx => $planx){
						
						if($x == $planx AND $y == $blocking_planets_y[$kpx]){
							return true;
						}
						
						//Here we get bearings
						$s_to_p = get_bearing($ships[$teamid]->x,$ships[$teamid]->y,$planx,$blocking_planets_y[$kpx]);
						$p_to_x = get_bearing($planx,$blocking_planets_y[$kpx],$x,$y);

						$difference = abs($s_to_p - $p_to_x);
						if($difference >= 325 OR $difference <= 35){
							$ok = false;
						}
					}
					
					return $ok;
				} else {
					return true;
				}
			}			
		} //Next team ship...
	}
}

function get_all_ships_scan()
{
	global $shiplist;
	global $ships;
	global $player;
	
	$text = '';
	
	//Scroll through all the ships, pick out those that are in range...
	foreach($shiplist as $sl){
		if(can_scan_square($ships[$sl]->x,$ships[$sl]->y)){
			if($ships[$sl]->dockedin == 0 OR $ships[$sl]->dockedin == $player->shipid OR $ships[$sl]->shipid == $player->shipid){
				if($ships[$sl]->hull > 0){
					if($ships[$sl]->ionic > 0){
						$text .= $ships[$sl]->small_scan($player->team,'');
					} else {	
						$sometext .= $ships[$sl]->small_scan($player->team,'');
					}
				} else {
					$extratext .= $ships[$sl]->small_scan($player->team,'');
				}
			}
		}
	}
	
	return $text . $sometext . $extratext;
}

function get_square_ships_scan($x,$y)
{
	global $shiplist;
	global $ships;
	global $player;
	
	$text = '';
	
	//Check if we can scan that square
	if(can_scan_square($x,$y)){
		foreach($shiplist as $sl){
			if($ships[$sl]->x == $x AND $ships[$sl]->y == $y){
				if($ships[$sl]->dockedin == 0 OR $ships[$sl]->dockedin == $player->shipid OR $ships[$sl]->shipid == $player->shipid){
					if($ships[$sl]->hull > 0){
						if($ships[$sl]->ionic > 0){
							$text .= $ships[$sl]->small_scan($player->team,'');
						} else {	
							$sometext .= $ships[$sl]->small_scan($player->team,'');
						}
					} else {
						$extratext .= $ships[$sl]->small_scan($player->team,'');
					}
				}
			}
		}
	} else {
	//display error
		
		$text .= 'That square is out of range. ';
	}
	
	//No ships found?
	if(!$text){
		$text .= 'There were no ships at that location.';
	
	}
	
	return $text . $sometext . $extratext ;
}

function get_square_ships_scan_nonallies($x,$y)
{
	global $enemylist;
	global $ships;
	global $player;
	
	$text = '';
	
	//Check if we can scan that square
	if(can_scan_square($x,$y)){
		foreach($enemylist as $sl){
			if($ships[$sl]->x == $x AND $ships[$sl]->y == $y){
				if($ships[$sl]->dockedin == 0 OR $ships[$sl]->dockedin == $player->shipid OR $ships[$sl]->shipid == $player->shipid){
					if($ships[$sl]->hull > 0){
						if($ships[$sl]->ionic > 0){
							$text .= $ships[$sl]->small_scan($player->team,'');
						} else {	
							$sometext .= $ships[$sl]->small_scan($player->team,'');
						}
					} else {
						$extratext .= $ships[$sl]->small_scan($player->team,'');
					}
				}
			}
		}
	} else {
	//display error
		
		$text .= 'That square is out of range. ';
	}
	
	//No ships found?
	if(!$text){
		$text .= 'There were no targettable ships at that location.';
	
	}
	
	return $text . $sometext . $extratext;
}

function get_square_ships_scan_nonallies_alive($x,$y)
{
	global $enemylist;
	global $ships;
	global $player;
	
	$text = '';
	
	//Check if we can scan that square
	if(can_scan_square($x,$y)){
		if($enemylist){
			foreach($enemylist as $sl){
				if($ships[$sl]->x == $x AND $ships[$sl]->y == $y){
					if(($ships[$sl]->dockedin == 0 OR $ships[$sl]->dockedin == $player->shipid OR $ships[$sl]->shipid == $player->shipid) AND $ships[$sl]->hull > 0){
						if($ships[$sl]->hull > 0){
							if($ships[$sl]->ionic > 0){
								$text .= $ships[$sl]->small_scan($player->team,'');
							} else {	
								$sometext .= $ships[$sl]->small_scan($player->team,'');
							}
						} else {
							$extratext .= $ships[$sl]->small_scan($player->team,'');
						}
					}
				}
			}
		}
	} else {
	//display error
		
		$text .= 'That square is out of range. ';
	}
	
	//No ships found?
	if(!$text){
		$text .= 'There were no targettable ships at that location.';
	
	}
	
	return $text . $sometext . $extratext;
}

function get_events(){
	global $player;

	//Gets all the events for 'players' ship...
	//extract events
	if($player->endgametime < $player->joingametime){
		$sql = 'SELECT * FROM `ws_events` WHERE (`shipid` = "' . $player->shipid . '" OR `shipid` = "team' . $player->team . '" OR `shipid` = "game' . $_SESSION['gameid'] . '") AND  `time` >= ' . $player->joingametime . ' ORDER BY `eventid` DESC LIMIT 0,12';
	} else {
		$sql = 'SELECT * FROM `ws_events` WHERE (`shipid` = "' . $player->shipid . '" OR `shipid` = "team' . $player->team . '" OR `shipid` = "game' . $_SESSION['gameid'] . '") AND  `time` >= ' . $player->joingametime . ' AND `time` <= ' . $player->endgametime . ' ORDER BY `eventid` DESC LIMIT 0,12';
	}
	$query = queryme($sql);
	
	if(mysql_num_rows($query)){
	
		//Display events...
		while($row =@ mysql_fetch_assoc($query)){
			$text .= '&raquo; <strong>' . date("F j, g:i a",$row['time']) . '</strong> - ' . $row['text'] . '<br /><br />';
		}
	} else {
		
		//Display error
		$text = 'You have not recieved any events yet.';
	}
	
	//Format it....
	
	$text = '<div class="eventmini"><small>
	<div align="center" class="underline">Recent Events:</div>
	' . $text . '
	</small></div>';
	
	return $text;
}

function make_tri_bar($first,$second,$third,$text,$width = '90%')
{
	$total = $first+$second+$third;
	$firstr = round($first / $total * 100,2);
	$secondr = round($second / $total * 100,2);
	$thirdr = round($third / $total * 100,2);
	
	return '<div align="center">
<table width="' . $width . '" height="17" cellpadding="0" cellspacing="0" onmouseover="return overlib(\'' . $first . ' Ready, ' . $second . ' Recharging, ' . $third . ' Destroyed\', CAPTION, \'' . $text . '\', WIDTH, 250,LEFT,ABOVE);" onmouseout="return nd();">
<td width="' . $firstr . '%" class="greenbg"></td>
<td width="' . $secondr . '%" class="amberbg"></td>
<td width="' . $thirdr . '%" class="redbg"></td>
</table>
</div>';
}

function dot_string($string,$chars = 22)
{
	if(strlen($string) > $chars){
		$string = substr($string,0,$chars);
		
		$string .= '...';
	}
	
	return $string;
}

function check_for_burnination($shipid,$time,$x = 'na',$y = 'na')
{
	global $ships;
	global $planet;
	global $planet_type;
	global $planet_x;
	global $planet_y;
	
	if($x == 'na' AND $y == 'na'){
		$x = $ships[$shipid]->x;
		$y = $ships[$shipid]->y;
		$message = true;		
	} else {
		$message = false;
	}
	
	//See if player x == planet x and player y == planet y...
	if($planet AND $ships[$shipid]){
		if($ships[$shipid]->hull > 0){
			foreach($planet as $key=>$p_name){
			
				if($planet_x[$key] == $x AND $planet_y[$key] == $y AND $planet_type[$key] == 'Sun'){
					//Burninate!
					
					$ships[$shipid]->x = $x;
					$ships[$shipid]->y = $y;
					
					if($message == true){
						kill_ship($shipid,'You crashed successfully into the side of the sun. You were burninated completely, and your ship was completely fried. Everybody died. The funeral was nice. There was milk. And pie. You didn\'t get any though. Because you\'re dead. ',$time);
					}
					
					return true;
				}
			}
		}
	}
}

function user_reg()
{
	global $player;
	global $total_online;
	global $total_ingame;
	//Ok, we have the users name:
	//$player->username
	//We have the game:
	//$_SESSION['gameid']
	//We have the session id...
	//session_id()
	//We have a ship id... 
	//$_SESSION['shipid']
	
	//Check if we have your session in the list...
	$sql = 'SELECT `shipid` FROM `ws_usersonline` WHERE session_id="' . session_id() . '"';
	$query = queryme($sql);
	
	if(mysql_num_rows($query) != 0){
		//Not in the online list.
		//OK! So, We need to do an update query to update the last time active...
		$sql = 'UPDATE `ws_usersonline` SET gameid=' . $_SESSION['gameid'] . ', shipid=' . $_SESSION['shipid'] . ',lastactive=' . time() . ',userid= ' . $_SESSION['userid'] . ',username="' . $player->username . '",shipname="' . $player->shipname . '" WHERE session_id="' . session_id() . '"';
		$query = queryme($sql);
		
	} else {
		//Is in online list
		$sql = 'INSERT INTO `ws_usersonline` (gameid,shipid,lastactive,userid,username,shipname,session_id) VALUES (' . $_SESSION['gameid'] . ',' . $_SESSION['shipid'] . ',' . time() . ',' . $_SESSION['userid'] . ',"' . $player->username . '","' . $player->shipname . '","' . session_id() . '");';
		queryme($sql);
		
	}
	
	//Get total
	$sql = 'SELECT count(*) FROM `ws_usersonline` WHERE lastactive >= ' . (time() - (60*10));
	$query = queryme($sql);

	$row = mysql_fetch_assoc($query);
	$total_online = $row['count(*)'];
	
	//And Delete old ones...
	$sql = 'DELETE FROM `ws_usersonline` WHERE lastactive < ' . (time() - (60*10));
	queryme($sql);
}

function kill_ship($shipid,$event,$time)
{
	global $ships;

	if($event){
		$ships[$shipid]->give_event($event,$time); 
	}
	
	$ships[$shipid]->hull = 0;
	$ships[$shipid]->ionic = 0;
	$ships[$shipid]->shields = 0;
	$ships[$shipid]->shieldgen = 0;
	$ships[$shipid]->tractor = 0;
	$ships[$shipid]->marines = 0;
	$ships[$shipid]->path = '';
	$ships[$shipid]->endgametime = (time() + 2);
						
	//Gut the ship...
	$sql = 'UPDATE `ws_ships` SET hull = 0, ionic=0, shields=0, tractor=0, marines=0, shieldgen=0,path="",endgametime= ' . $ships[$shipid]->endgametime . ',x = ' . $ships[$shipid]->x . ',y = ' . $ships[$shipid]->y . ' WHERE shipid = ' . $shipid;
	queryme($sql);

	//Lets stop any movement...
	$sql = 'DELETE FROM `ws_auto_movement` WHERE shipid = ' . $shipid;
	queryme($sql);

	//Any reloads...
	$sql = 'DELETE FROM `ws_auto_reload` WHERE shipid = ' . $shipid;
	queryme($sql);
	
	//move any ships aboard...
	$sql = 'UPDATE `ws_ships` SET dockedin=0 WHERE dockedin=' . $shipid;
	queryme($sql);
	
	//Set last death loc...
	$sql = 'SELECT * FROM `ws_games_lastspawn` WHERE `gameid` = ' . $ships[$shipid]->gameid . ' AND `userid` = ' . $ships[$shipid]->userid;
	$query = queryme($sql);
	if(mysql_num_rows($query) > 0){
		$sql = 'UPDATE `ws_games_lastspawn` SET `x`= ' . $ships[$shipid]->x . ', `y` = ' . $ships[$shipid]->y . ',`timestamp` = ' . time() . ' WHERE `userid` = ' . $ships[$shipid]->userid . ' AND `gameid` = ' . $ships[$shipid]->gameid;
	} else {
		$sql = 'INSERT INTO `ws_games_lastspawn` (`x`,`y`,`userid`,`gameid`,`timestamp`) VALUES (' . $ships[$shipid]->x . ',' . $ships[$shipid]->y . ',' . $ships[$shipid]->userid . ',' . $ships[$shipid]->gameid . ',' . time() . ')';
	}
	$query = queryme($sql);
	
	//Do the successionist thing...
	if($ships[$shipid]->teamleadershipid == $shipid){
		//Pass on the leadership...
		$sql = 'SELECT `ws_shipid` FROM `ships` WHERE `team`="' . $ships[$shipid]->team . '" AND `hull`>0 ORDER BY `ionic` DESC';
		$query = queryme($sql);
		
		if(mysql_num_rows($query) == 0){
			//Delete team..
			$sql = 'DELETE FROM `ws_teams` WHERE teamid=' . $ships[$shipid]->team;
			$query = queryme($sql);
			
			//Set them all to freelance, so they are still loaded [without a team...]
			$sql = 'UPDATE `ws_ships` SET team = 1 WHERE `team` = ' . $ships[$shipid]->team;
			$query = queryme($sql);
			
			//Give the 'they are dead' message...
			$ships[$shipid]->give_game_event('The Team ' . $ships[$shipid]->teamname . ' lead by ' .$ships[$shipid]->teamleadertext . ' has been disbanded after they were Annihilated in battle... They were a pushover anyway!');			
		
		} else {
			//New leader...
			$row = mysql_fetch_assoc($query);
			
			//New leader = $ships[$row['shipid']]->
			$ships[$shipid]->give_game_event('Viva La Revolution! ' . $ships[$row['shipid']]->username . ' is the new leader of ' . $ships[$row['shipid']]->teamname . ' after the incompetant ' . $ships[$shipid]->username . ' died in battle...');
			
			$sql = 'UPDATE `ws_teams` SET  `teamleadershipid` = "' . $row['shipid'] . '",  teamleadertext = "' . $ships[$row['shipid']]->username . '" WHERE  `teamid` = "' . $ships[$row['shipid']]->team . '"';
			$query = queryme($sql);
		}
	}
}

function dis_ship($shipid)
{
	global $ships;

	$ships[$shipid]->ionic = 0;
	
	//Set last death loc...
	$sql = 'SELECT * FROM `ws_games_lastspawn` WHERE `gameid` = ' . $ships[$shipid]->gameid . ' AND `userid` = ' . $ships[$shipid]->userid;
	$query = queryme($sql);
	if(mysql_num_rows($query) > 0){
		$sql = 'UPDATE `ws_games_lastspawn` SET `x`= ' . $ships[$shipid]->x . ', `y` = ' . $ships[$shipid]->y . ',`timestamp` = ' . time() . ' WHERE `userid` = ' . $ships[$shipid]->userid . ' AND `gameid` = ' . $ships[$shipid]->gameid;
	} else {
		$sql = 'INSERT INTO `ws_games_lastspawn` (`x`,`y`,`userid`,`gameid`,`timestamp`) VALUES (' . $ships[$shipid]->x . ',' . $ships[$shipid]->y . ',' . $ships[$shipid]->userid . ',' . $ships[$shipid]->gameid . ',' . time() . ')';
	}
	$query = queryme($sql);

	//Lets stop any movement...
	$sql = 'DELETE FROM `ws_auto_movement` WHERE shipid = ' . $shipid;
	queryme($sql);
	//Any reloads...
	$sql = 'DELETE FROM `ws_auto_reload` WHERE shipid = ' . $shipid;
	queryme($sql);
										
	//Remove the path... if any..
	$sql = 'UPDATE `ws_ships` SET `path` = ""  WHERE shipid = ' . $shipid;
	queryme($sql);
}

function get_credit_amount()
{
	global $game;
	global $player;
	
	//Ok, if we cant find the players userid and gameid in the p4s_accounts db, then default, else use that amount...
	if($_SESSION['gameid']){
		$query = queryme('SELECT `credits` FROM `ws_p4s_accounts` WHERE userid = ' . $_SESSION['userid'] . ' AND gameid = ' . $_SESSION['gameid']);
	} else {
		$query = queryme('SELECT `credits` FROM `ws_p4s_accounts` WHERE userid = ' . $_SESSION['userid'] . ' AND gameid = ' . $_GET['game']);
	}
	if(mysql_num_rows($query) == 0){
		if($game['option_default_point_amount']){
			return $game['option_default_point_amount'];
		} else {
			return $player->option_default_point_amount;
		}
	} else {
		$row = mysql_fetch_assoc($query);
		return $row['credits'];
	}
}
?>