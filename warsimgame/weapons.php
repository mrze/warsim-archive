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

//Title
$page['text'] .= '<div align="center"><strong>-Weapons-</strong></div><br />';

//Check for disabled/destroyed...
if($player->get_status() != 'normal'){
	$page['text'] .= 'Your ship is either disabled or destroyed. You cannot access this.';
} else {
	
	//Check if docked... display msg...
	if($player->dockedin != 0){
			$page['text'] .= 'Fo Shizzle Brother! Yo ride is inside a hustla shizzay. You CANNOT fizzy yo weapons or Soge will pop-a-cap in yo ass for doin\' that mah nigga! [Translation: You are currently docked inside another ship. You CANNOT fire your weapons.]';
	} else {
		//Ok, we are fine...
		if(!$_GET['mode']){
			//We are looking at a plain screen...
			$page['text'] .= 'Select a square on the map to target ships or click on a weapon image to see the range of that weapon overlayed on the map. <br /><br />';
			
			
			
			//Loop through all the weapons...
			$target = 1;
			while($target <= $weapon_count){
				//Stuff here..
		
				//Display a nice table for each weapon  that exists...
				if($weapon[$target]->get_max()){
				
					//It exists... so lets display stuff...
					
					$page['text'] .= '<table width="100%" border="0" onmouseover="return overlib(\'' . $weapon[$target]->get_ready() . ' Ready, ' . $weapon[$target]->get_recharging() . '  Recharging, ' . $weapon[$target]->get_damaged() . ' Damaged<br />' . $weapon[$target]->use . '<br />' . $weapon[$target]->reloadtime . ' min <em>overall</em> recharge time<br />' . $weapon[$target]->weaponrange . ' Square Weapon Range<br />' . $weapon[$target]->get_reload_times() . '\', CAPTION, \'' . $weapon[$target]->longname . '\',WIDTH,250);" onmouseout="return nd();" >
					<tr>
						<td width="1%"><img src="' . $weapon[$target]->smallimage . '" onclick="load_range(' . $weapon[$target]->weaponrange . ',' . $player->x . ',' . $player->y . ')" /></td>
						<td width="50%" class="norfont"><strong>' . $weapon[$target]->longname . '</strong></td>
						<td class="norfont"><strong>Ready: </strong>' . $weapon[$target]->get_ready() . '</td>
					</tr>
				</table><br />';
		
				}
		
				//\Stuff here...
				$target++;
			}
			
			//And allow them to clear the map...
			$page['text'] .= '<div align="center"><a href="#" onclick="load_range(-1,0,0)">[Click to Remove Range]</a></div>';
			
		} else {
			//We have inputted something...
			//Is it a square scan?
			//http://localhost/warsimgame/weapons.php?mode=targetsquare&sqr_x=10&sqr_y=11
			if($_GET['mode'] == 'targetsquare'){
				//Targetting a specific square, display a list of ships there... with a target/scan/hail link...
				if(is_numeric($_GET['scan_x']) AND is_numeric($_GET['scan_y']) AND $_GET['scan_x'] >= 0 AND $_GET['scan_x'] <= 19 AND $_GET['scan_y'] >= 0 AND $_GET['scan_y'] <= 19){
					$page['text'] .= get_square_ships_scan_nonallies_alive($_GET['scan_x'],$_GET['scan_y']);
				} else {
					$page['text'] .= 'OMG You supa 1337 hax0r! j00 Pwnz mee!! [There has been a user-stupidity inflicted error. Don\'t play with the URL]';
				}	
			}
			
			//Are we targetting a specific ship?
			//weapons.php?mode=targetship&shipid=2&target=Target
			if($_GET['mode'] == 'targetship'){
				//Targetting a specific ship...
				
				//Set scan flag...
				$scan_possible = false;
				
				//Ok, lets steal some scanner code... to check if the ship can be scanned or not...		
				//Also, you can't target yourself >_>
				if(is_numeric($_GET['shipid']) AND $ships[$_GET['shipid']]){
				
					//IS in range?
					if(can_scan_square($ships[$_GET['shipid']]->x,$ships[$_GET['shipid']]->y)){
						//Check it isnt docked...
						if($ships[$_GET['shipid']]->dockedin == 0 OR $ships[$_GET['shipid']]->dockedin == $player->shipid OR $_GET['shipid'] == $player->shipid){
							
							//Check 
							//Get scan <> self or <> team...
							if($_GET['shipid'] != $_SESSION['shipid']){
								if($ships[$_GET['shipid']]->team != $player->team OR $player->team == 1){
									$scan_possible = true;
								} else {
									$page['text'] .= 'Shooting your teammates is bad. If you wish to take out a teammate, set your allegence to \'freelance\'.';
								}
							} else {
								$page['text'] .= 'You cannot shoot yourself. Sensors detect your IQ has dropped by 15 points.';
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
				
				
				//Ok, if the scan is possible... then great... 
				//If it isnt... then OMGWTFBBQ!
				//Well, obviously, it is in range, so lets generate a nice 'Blow shit up' screen..
				if($scan_possible){
					//OK! We need to make up a similar thing to the No mode weapon thingy...
					//So, we will make table... with a dropdown, max [ready] -> 0, max defaulted, or a red 'NOT IN RANGE' if it isnt in range... got it? good...
					
					//Theres the form...
					$middle .= '<span class="underline">&raquo; Weapons:</span><br /><br />
					<form name="fire" method="post" action="weapons.php?mode=fireatship">';
		
					//Work out the distance...
					$d_stoe = distance($player->x,$player->y,$ships[$_GET['shipid']]->x,$ships[$_GET['shipid']]->y);
					
					//Now, to display all the weapons that are applicable
					//Loop through all the weapons...
					$target = 1;
					while($target <= $weapon_count){
						//Stuff here..
										
						//Display a nice table for each weapon  that exists...
						if($weapon[$target]->get_max()){
						
							//The weapon exists... lets check if its in range:
							//[Checks distance to ship < weapon range...]
							if($d_stoe <= $weapon[$target]->weaponrange){
								//In range
								
								
								//Since we can fire stuff, we need to create an option box...
								//<select name="dbname" size="1"><option value="0">0</option><option value="1">1</option>....<option value="100" selected="selected">100</option></select>
								//We will be doing it BACKWARDS!
								$option = '</select>';
								//Get max...
								$option_number = $weapon[$target]->get_ready();
								//Do selcted...
								$option = '<option value="' . $option_number . '" selected="selected">' . $option_number . '</option>' . $option;
								//Looop!!!
								if($option_number > 100){
									if($option_number > 500){
										$decrement = 50;
									} else {
										$decrement = 10;
									}
								} else {
									$decrement = 1;
								}
								
								while($option_number % $decrement != floor($option_number % $decrement)){
									$option_number--;
								}
								
								$option_number = $option_number - $decrement;
								
								if($option_number > 0){
									while($option_number >= 0){
										$option = '<option value="' . $option_number . '">' . $option_number . '</option>' . $option;
										
										$option_number = $option_number - $decrement;
									}
								} 
								$option = '<select name="' . $weapon[$target]->dbname . '" size="1">' . $option;
								//\End Get Option Bar thingy...
								
								
								//Now, put it into a nice table, and ship it off to japan...
								$middle .= '<table width="100%" border="0"  >
								<tr><td width="1%"><img src="' . $weapon[$target]->smallimage . '" onclick="load_range(' . $weapon[$target]->weaponrange . ',' . $player->x . ',' . $player->y . ')" onmouseover="return overlib(\'' . $weapon[$target]->get_ready() . ' Ready, ' . $weapon[$target]->get_recharging() . '  Recharging, ' . $weapon[$target]->get_damaged() . ' Damaged<br />' . $weapon[$target]->use . '<br />' . $weapon[$target]->reloadtime . ' min <em>overall</em> recharge time<br />' . $weapon[$target]->weaponrange . ' Square Weapon Range<br />' . $weapon[$target]->get_reload_times() . '\', CAPTION, \'' . $weapon[$target]->longname . '\',WIDTH,250);" onmouseout="return nd();" /></td>
								<td width="62%" class="norfont"><strong>' . $weapon[$target]->longname . '</strong></td>
								<td class="norfont">' . $option . '</td>
								</tr></table><br />';
		
							} else {
								//Out of range...
								$middle .= '<table width="100%" border="0" >
								<tr><td width="1%"><img src="' . $weapon[$target]->smallimage . '" onclick="load_range(' . $weapon[$target]->weaponrange . ',' . $player->x . ',' . $player->y . ')" onmouseover="return overlib(\'' . $weapon[$target]->get_ready() . ' Ready, ' . $weapon[$target]->get_recharging() . '  Recharging, ' . $weapon[$target]->get_damaged() . ' Damaged<br />' . $weapon[$target]->use . '<br />' . $weapon[$target]->reloadtime . ' min <em>overall</em> recharge time<br />' . $weapon[$target]->weaponrange . ' Square Weapon Range<br />' . $weapon[$target]->get_reload_times() . '\', CAPTION, \'' . $weapon[$target]->longname . '\',WIDTH,250);" onmouseout="return nd();" /></td>
								<td width="62%" class="norfont"><strong>' . $weapon[$target]->longname . '</strong></td>
								<td class="norfont"><span class="red"><strong>Out of Range!</strong></span></td>
								</tr></table><br />';
							}
						}
		
						//\Stuff here...
						$target++;
					}//End Loop through weapons
					
					//Close off form...
					$middle .= '<div align="center"><input type="submit" name="Submit" value="FIRE!" class="form" />
					  <input name="atship" type="hidden" id="atship" value="' . $_GET['shipid'] . '" /></div>';
					
					//Display it all with a nice scan :D
					$page['text'] .= $ships[$_GET['shipid']]->large_scan($middle);
				}		
			}
			
			//Have we confirmed a fire request?!?
			if($_GET['mode'] == 'fireatship'){
				//Fire at a ship...
				
				//Ok, this is what we need to do...
				//Check target is valid... [IE: Scannable...]
				//Loop through all weapons
				//Check if weapon has been submitted
				//if submitted, make sure <= ready amount
				//if submitted make sure in range
				//calculate damage
				//add damage to damage totals
				//un-load weapons
				//add to reload list...
				//lather, rinse and repeat...
				
				//Check target is valid
				if($ships[$_POST['atship']]){
					
					//Check is in sensor range...
					//Set scan flag...
					$scan_possible = false;
					
					//Ok, lets steal some scanner code... to check if the ship can be scanned or not...		
					//Also, you can't target yourself >_>
					//IS in range?
					if(can_scan_square($ships[$_POST['atship']]->x,$ships[$_POST['atship']]->y)){
						//Check it isnt docked...
						if($ships[$_POST['atship']]->dockedin == 0 OR $ships[$_POST['atship']]->dockedin == $player->shipid OR $_POST['atship'] == $player->shipid){
								
							//Check 
							//Get scan <> self or <> team...
							if($_GET['shipid'] != $_SESSION['shipid']){
								if($ships[$_GET['shipid']]->team != $player->team  OR $player->team == 1){
									$scan_possible = true;
								} else {
									$page['text'] .= 'Shooting your teammates is bad. If you wish to take out a teammate, set your allegence to \'freelance\'.';
								}
							} else {
								$page['text'] .= 'You cannot shoot yourself. Sensors detect your IQ has dropped by 15 points.';
							}
						} else {
							$page['text'] .= 'That ship is out of range.';
						}
					} else {
						$page['text'] .= 'That ship is out of range.';
					}
				
					
					//Ok, if the scan is possible... then great... 
					//If it isnt... then OMGWTFBBQ!
					//Well, obviously, it is in range, so lets generate a nice 'Blow shit up' screen..
					if($scan_possible){
					
						//Get ship range
						$d_stoe = distance($player->x,$player->y,$ships[$_POST['atship']]->x,$ships[$_POST['atship']]->y);
						
						//LOOP THROUGH WEAPONS
						$target = 1;
						while($target <= $weapon_count){
							
							//Current weapon text...
							$cwt = $weapon[$target]->dbname;
							
							if($_POST[$cwt]){
								//If we are in here... then this weapon has been fired...
								//Check range...
								if($d_stoe <= $weapon[$target]->weaponrange){
									//In Range...
									
									//Check we are not shooting too much...
									$_POST[$cwt] = min($_POST[$cwt],$weapon[$target]->get_ready());
									
									//Ok, so, i think we are good to calculate damage...
									
									///////////DAMAGE!!!!
									$number_fired = $_POST[$cwt];
									//echo 'FIRED: ' . $number_fired . ' ' . $weapon[$target]->longname . '<br />';
									
									$situation =  strtolower($player->classtype) . 'to' . strtolower($ships[$_POST['atship']]->classtype);
									$situationh = $situation . 'hits';
									$situationd = $situation . 'damage';
									//echo 'SITUATION: ' . $situationh . '<br />';;
									
									//echo 'ORG. HITRATE: ' . ($weapon[$target]->$situationh) . '%<br />';
									
									//echo 'VARIABILITY: 10%+/-<br />';
									
									$hitrand = (rand(90,110));
									$hit = floor(($number_fired * ($weapon[$target]->$situationh / 100)) * ($hitrand / 100)); 
									
									//echo 'BASE AMOUNT HIT: ' . $hit . '<br />';
									
									if($ships[$_POST['atship']]->shiplength > 900){
										//echo 'LARGE SHIP VS BONUS: +20% hr<br />';
										$mod = $mod + 10;
									}
									
									if($ships[$_POST['atship']]->shiplength > 1300){
										//echo 'MOFO HUGE SHIP VS BONUS: +20% hr<br />';
										$mod = $mod + 10;
									}
									
									if($ships[$_POST['atship']]->manu > 3){
										//echo 'DAMN HARD TO HIT SHIP: -6% hr<br />';
										$mod = $mod - 10;
									}
									
									if($ships[$_POST['atship']]->manu > 3){
										//echo 'DAMN FAST SHIP: -6% hr<br />';
										$mod = $mod - 10;
									}
									
									$hit = min(floor(($number_fired * ($weapon[$target]->$situationh / 100)) * (($hitrand / 100) + ($mod / 100))),$_POST[$cwt]); 
									$hit = max(0,$hit);
									$hit = min($_POST[$cwt],$hit);
									
									//echo 'NEW HIT: ' . $hit . ' <br /><br />';
									
									//echo 'DAMAGE PER HIT: ' . $weapon[$target]->$situationd . '<br />';
									
									//echo 'BASE DAMAGE DONE: ' . ($weapon[$target]->$situationd * $hit) . '<br />';
									
									if(distance($player->x,$player->y,$ships[$_POST['atship']]->x,$ships[$_POST['atship']]->y) > 2){
										//echo 'LARGE DISTANCE PENALTY: -10% dmg<br />';
										$dmgmod = $dmgmod - 10;
									}
									
									if(distance($player->x,$player->y,$ships[$_POST['atship']]->x,$ships[$_POST['atship']]->y) > 2){
										//echo 'HUGE DISTANCE PENALTY: -10% dmg<br />';
										$dmgmod = $dmgmod - 10;
									}
									
									if(distance($player->x,$player->y,$ships[$_POST['atship']]->x,$ships[$_POST['atship']]->y) == 0){
										//echo 'POINT BLANK BONUS: +20% dmg<br />';
										$dmgmod = $dmgmod + 10;
									}
									
									$dmgrand = rand(85,115);
									
									$dmgdone = floor($weapon[$target]->$situationd * ($dmgrand / 100) * $hit * ($dmgmod + 100) / 100);
									//echo 'NEW DAMAGE DONE: ' . floor($weapon[$target]->$situationd * $hit * ($dmgmod + 100) / 100) . '<br />';
									//echo '<br />';
									///////////DAMAGE!!!!
									
									//» Heavy Lasers:
									//48 fired
									//48 hit the Target
									//943 damage done
									//Weapon Reload in: 1 mins
									
									$report .= '<span class="underline">&raquo;&nbsp;' . $weapon[$target]->longname . ':</span><br />';
									$report .= $_POST[$cwt] . ' fired<br />';
									$report .= $hit . ' hit the Target<br />';
									$report .= $dmgdone . ' damage done<br />';
									$report .= 'Weapon Reload in: ' . $weapon[$target]->reloadtime . ' mins<br /><br />';
									
									//Add up total damage...
									$totaldamage[$weapon[$target]->attacks] += $dmgdone;
	
									//And finally, we can unload the weapons, and set reloads...						
									//Ok, we are going to put all the reloads into a nice array that looks like this:
									//$reload[secondstoreload][weaponname] = number to reload... 
									if($_POST[$cwt]){
										$reload[$weapon[$target]->reloadtime * 60][$cwt] = $_POST[$cwt];
										
										//On the fly reload schedule tampering...
										$player->reload_schedule[$weapon[$target]->reloadtime * 60][$cwt] += $_POST[$cwt];
										
										//Removes them from the player [note, need to update db later...]
										$player->$cwt = $player->$cwt - $_POST[$cwt];
									
										$reloadsql .= ', ' . $cwt . ' = ' . $player->$cwt . ' ';
									}
								}
							}
							
							$target++;
						}//\Loop through weapons
						
						//Apply damage...
						if($totaldamage){					
							//Do shields damage...
							if($totaldamage['shields']){
								if($ships[$_POST['atship']]->shields > 0){
									//Shields are up...
									if($ships[$_POST['atship']]->shields > $totaldamage['shields']){
										//We did not take down the shields...
										$ships[$_POST['atship']]->shields = $ships[$_POST['atship']]->shields - $totaldamage['shields'];
										$donedamage['shields'] += $totaldamage['shields'];
										$totaldamage['shields'] = 0;
										
										$shieldroll = rand(0,50) / 100;
										$shieldperc = $ships[$_POST['atship']]->shields / $ships[$_POST['atship']]->shieldsmax;
										
										//If shields are still up, see if they can be 'persuaded' to go down...
										if($shieldroll > $shieldperc AND $ships[$_POST['atship']]->shieldgen == 1){
											$ships[$_POST['atship']]->shieldgen = 0;
											$donedamage['shieldgen'] = 1;
										}
									} else {
										//We took down the shields...
										$totaldamage['shields'] = $totaldamage['shields'] - $ships[$_POST['atship']]->shields;
										$donedamage['shields'] = $ships[$_POST['atship']]->shields;
										$donedamage['shieldsdown'] = 1;
										$ships[$_POST['atship']]->shields = 0;
										if($ships[$_POST['atship']]->shieldgen == 1){
											$ships[$_POST['atship']]->shieldgen = 0;
											$donedamage['shieldgen'] = 1;
										}
									}
								}
							}
							
							//We have more to go?
							if($totaldamage['shields']){
								if($ships[$_POST['atship']]->hull > 0){
									if($ships[$_POST['atship']]->hull > $totaldamage['shields']){
										//Not destroyed hull...
										$donedamage['hull'] += $totaldamage['shields'];
										$ships[$_POST['atship']]->hull = $ships[$_POST['atship']]->hull - $totaldamage['shields'];
									} else {
										//Destroyed hull...
										$donedamage['hull'] += $ships[$_POST['atship']]->hull;
										$ships[$_POST['atship']]->hull = 0;
										$donedamage['destroyed'] = 1;
										
										$ships[$_POST['atship']]->endgametime = (time() + 2);
										
										//Lets stop any movement...
										$sql = 'DELETE FROM `ws_auto_movement` WHERE shipid = ' . $_POST['atship'];
										queryme($sql);
										//Remove the path... if any..
										$sql = 'UPDATE `ws_ships` SET `path` = "", endgametime = "' . $ships[$_POST['atship']]->endgametime . '" WHERE shipid = ' . $_POST['atship'];
										queryme($sql);
									}
								}
							}
							//\Do shields damage...
							
							//Lets Do Ionic Damage...
							if($player->option_ions_hit_shields == 1){
								if($totaldamage['ionic']){
									if($ships[$_POST['atship']]->shields > 0){
										//Apply damage to shields...
										if($ships[$_POST['atship']]->shields > $totaldamage['ionic']){
											//Shields still up...
											$ships[$_POST['atship']]->shields = $ships[$_POST['atship']]->shields - $totaldamage['ionic'];
											$donedamage['shields'] += $totaldamage['ionic'];
											$totaldamage['ionic'] = 0;
											
											$shieldroll = rand(0,50) / 100;
											$shieldperc = $ships[$_POST['atship']]->shields / $ships[$_POST['atship']]->shieldsmax;
										
											//If shields are still up, see if they can be 'persuaded' to go down...
											if($shieldroll > $shieldperc AND $ships[$_POST['atship']]->shieldgen == 1){
												$ships[$_POST['atship']]->shieldgen = 0;
												$donedamage['shieldgen'] = 1;
											}
										} else {
											//Shields down...
											$totaldamage['ionic'] = $totaldamage['ionic'] - $ships[$_POST['atship']]->shields;
											$donedamage['shields'] += $ships[$_POST['atship']]->shields;
											$donedamage['shieldsdown'] = 1;
											$ships[$_POST['atship']]->shields = 0;
											if($ships[$_POST['atship']]->shieldgen == 1){
												$ships[$_POST['atship']]->shieldgen = 0;
												$donedamage['shieldgen'] = 1;
											}
										}
									}
								}
							}
							
							if($totaldamage['ionic']){
								if($ships[$_POST['atship']]->ionic > 0){
									if($ships[$_POST['atship']]->ionic > $totaldamage['ionic']){
										//Still got ionic left...
										$ships[$_POST['atship']]->ionic = $ships[$_POST['atship']]->ionic - $totaldamage['ionic'];
										$donedamage['ionic'] += $totaldamage['ionic'];
										$totaldamage['ionic'] = 0;
									} else {
										//Blam! Disabled...
										$donedamage['ionic'] += $ships[$_POST['atship']]->ionic;
										$donedamage['disabled'] = 1;							
										dis_ship($_POST['atship']);
									}
								}
							}					
							
							//\Ionic Damage...
							
							//Dead?
							if($donedamage['destroyed']){
								kill_ship($_POST['atship'],'','');
							} else {
							
								//Update DB:
								$sql = 'UPDATE `ws_ships` SET hull = ' . $ships[$_POST['atship']]->hull . ', ionic = '.$ships[$_POST['atship']]->ionic .', shields= ' . $ships[$_POST['atship']]->shields . ',shieldgen = ' . $ships[$_POST['atship']]->shieldgen . ' WHERE shipid = ' . $_POST['atship'];
								queryme($sql);
							}
						}
						
						//OK! Some events/display...
						//$for_attacker
						//$for_defender
						//$donedamage
						
						//P4S Mode...
						$total_credits = $donedamage['shields'] + $donedamage['ionic'] + $donedamage['hull'];
						
						if($donedamage['disabled'] OR $donedamage['destroyed']){
							$total_credits = $total_credits * 1.15;
						}
						
						//If multiple Ships...
						if($ships[$_POST['atship']]->squadsize > 6){
							$total_credits = $total_credits / $ships[$_POST['atship']]->squadsize * 2;
						} else {
							$total_credits = $total_credits / 2;
						}
						
						//P4S Credits...
						$player->give_credits(floor($total_credits),'your attack on the ' . $ships[$_POST['atship']]->classname . ' <em>' . $ships[$_POST['atship']]->shipname . '</em>.');
						
						//General Startoff...
						$for_attacker = 'We attacked the ' . $ships[$_POST['atship']]->classname . ' <em>' . $ships[$_POST['atship']]->shipname . '</em> [Team: ' . $ships[$_POST['atship']]->teamname . '], piloted by ' . $ships[$_POST['atship']]->username . '. ';
						$for_defender = 'The ' . $player->classname . ' <em>' . $player->shipname . '</em> [Team: ' . $player->teamname . '], piloted by ' . $player->username . ', attacked us from ' . $player->get_position() . '. ';
												
						//Text For Supreme Missage...
						if(!$donedamage){
							$for_attacker .= 'However, they obviously had far superior equipment to us, since we did no damage. ';
							$for_defender .= 'However, luckly for us, they coulden`t hit the side of a barn with a turbolaser! We suffered no damage at all! ';
						}
						
						//Text For Shields Damage
						if($donedamage['shields']){
							
							$for_attacker .= 'We did ' . $donedamage['shields'] . ' damage to the target\'s shields. ';
							$for_defender .= 'They did ' . $donedamage['shields'] . ' damage to our shields';
							
							if($donedamage['shieldgen']){
								$for_attacker .= 'Sensors detect major malfunctions with the target\'s shield generator system, looks like they are offline. ';
							
							}
							
							if($donedamage['shieldsdown']){
								$for_defender .= ', which were taken offline! ';
							} else {
								if($donedamage['shieldgen']){
									$for_defender .= ', luckly they are still up, the same cannot be said for our shield generators, which were taken down by continual enemy fire. ';
								} else {
									$for_defender .= '. ';
								}
							}
						}
						
						//Text For Ionic Damage
						if($donedamage['ionic']){
							$for_attacker .= 'We did ' . $donedamage['ionic'] . ' ionic damage to the target\'s electronics and computer systems! ';
							$for_defender .= 'Our ship took ' . $donedamage['ionic'] . ' ionic damage! ';
							
							if($donedamage['disabled']){
								$for_attacker .= 'Sensors detect major power outtages and systems malfunctions aboard the target ship! Looks like we overloaded their electronics systems! They are dead in the sky! ';
								$for_defender .= 'Systems failin... *Static* ... *More Static* ... *Backup Power Operational* ... All systems suffering major malfunctions, Engines Down, Weapons Down, sensors... Not Responding, Backup Computer Online... ';
							}
						}
						
						//Text For Hull Damage
						if($donedamage['hull']){
							$for_attacker .= 'Our ships did ' . $donedamage['hull'] . ' damage to the enemys hull and engine systems. ';
							$for_defender .= 'We sustained ' . $donedamage['hull'] . ' damage to the hull and engines! ';
							
							if($donedamage['destroyed']){
								$for_attacker .= 'Sensor reports indicate there is a major hull and reactor breach aboard the target ship, another confirmed kill! ';
								if($ships[$_POST['atship']]->classtype == 'Fighter'){
									$for_defender .= 'Sensor reports indicate a major rupture occouring in the lower and upper hull segm... *Static*... *Oh my! We are going down*... *Eject! Eject!*... *Static*... [Your ship has been destroyed]';
								} else {
									$for_defender .= 'Sensor reports indicate a major rupture occouring in the lower and upper hull segm... *Static*... *Oh my! We are going down*... *Abandon Ship! Abandon Ship!*... *Static*... [Your ship has been destroyed]';
								}
							} else {
								$for_attacker .= 'Sensor reports indicate that the targets hull is at approximatly ' . round($ships[$_POST['atship']]->hull / $ships[$_POST['atship']]->hullmax * 100,2) . '%.';
								$for_defender .= 'Systems indicate that we are at ' . round($ships[$_POST['atship']]->hull / $ships[$_POST['atship']]->hullmax * 100,2) . '% hull integrity.';
							}
						}
								
						//Gen Report...
						$combatoverview = '<span class="underline">Attack Report:</span><br /><br /><small>' . $for_attacker . '</small><br /><br />';
						$player->give_event($for_attacker);
						$ships[$_POST['atship']]->give_event($for_defender);
						
						//Before output...... lets add in the weapons list again...
						//Theres the form...
						$middle .= '<span class="underline">&raquo; Weapons:</span><br /><br />
						<form name="fire" method="post" action="weapons.php?mode=fireatship">';
			
						//Work out the distance...
						$d_stoe = distance($player->x,$player->y,$ships[$_POST['atship']]->x,$ships[$_POST['atship']]->y);
						
						//Now, to display all the weapons that are applicable
						//Loop through all the weapons...
						$target = 1;
						while($target <= $weapon_count){
							//Stuff here..
											
							//Display a nice table for each weapon  that exists...
							if($weapon[$target]->get_max()){
							
								//The weapon exists... lets check if its in range:
								//[Checks distance to ship < weapon range...]
								if($d_stoe <= $weapon[$target]->weaponrange){
									//In range
									
									
									//Since we can fire stuff, we need to create an option box...
									//<select name="dbname" size="1"><option value="0">0</option><option value="1">1</option>....<option value="100" selected="selected">100</option></select>
									//We will be doing it BACKWARDS!
									$option = '</select>';
									//Get max...
									$option_number = $weapon[$target]->get_ready();
									//Do seelcted...
									$option = '<option value="' . $option_number . '" selected="selected">' . $option_number . '</option>' . $option;
									//Looop!!!
									if($option_number > 0){
										while($option_number >= 0){
											$option = '<option value="' . $option_number . '">' . $option_number . '</option>' . $option;
											$option_number--;
										}
									}
									
									$option = '<select name="' . $weapon[$target]->dbname . '" size="1">' . $option;
									//\End Get Option Bar thingy...
									
									
									//Now, put it into a nice table, and ship it off to japan...
									$middle .= '<table width="100%" border="0"  >
									<tr><td width="1%"><img src="' . $weapon[$target]->smallimage . '" onclick="load_range(' . $weapon[$target]->weaponrange . ',' . $player->x . ',' . $player->y . ')" onmouseover="return overlib(\'' . $weapon[$target]->get_ready() . ' Ready, ' . $weapon[$target]->get_recharging() . '  Recharging, ' . $weapon[$target]->get_damaged() . ' Damaged<br />' . $weapon[$target]->use . '<br />' . $weapon[$target]->reloadtime . ' min <em>overall</em> recharge time<br />' . $weapon[$target]->weaponrange . ' Square Weapon Range<br />' . $weapon[$target]->get_reload_times() . '\', CAPTION, \'' . $weapon[$target]->longname . '\',WIDTH,250);" onmouseout="return nd();" /></td>
									<td width="62%" class="norfont"><strong>' . $weapon[$target]->longname . '</strong></td>
									<td class="norfont">' . $option . '</td>
									</tr></table><br />';
			
								} else {
									//Out of range...
									$middle .= '<table width="100%" border="0" >
									<tr><td width="1%"><img src="' . $weapon[$target]->smallimage . '" onclick="load_range(' . $weapon[$target]->weaponrange . ',' . $player->x . ',' . $player->y . ')" onmouseover="return overlib(\'' . $weapon[$target]->get_ready() . ' Ready, ' . $weapon[$target]->get_recharging() . '  Recharging, ' . $weapon[$target]->get_damaged() . ' Damaged<br />' . $weapon[$target]->use . '<br />' . $weapon[$target]->reloadtime . ' min <em>overall</em> recharge time<br />' . $weapon[$target]->weaponrange . ' Square Weapon Range<br />' . $weapon[$target]->get_reload_times() . '\', CAPTION, \'' . $weapon[$target]->longname . '\',WIDTH,250);" onmouseout="return nd();" /></td>
									<td width="62%" class="norfont"><strong>' . $weapon[$target]->longname . '</strong></td>
									<td class="norfont"><span class="red"><strong>Out of Range!</strong></span></td>
									</tr></table><br />';
								}
							}
			
							//\Stuff here...
							$target++;
						}//End Loop through weapons
						
						//Close off form...
						$middle .= '<div align="center"><input type="submit" name="Submit" value="FIRE!" class="form" />
						 <input name="atship" type="hidden" id="atship" value="' . $_POST['atship'] . '" /></div>';
	
						//AND! FINALLY! Make a nice scan!
						$page['text'] .= $ships[$_POST['atship']]->large_scan('<span class="underline">Combat Report:</span><br /><br /><small>' . $report . '</small>' . $combatoverview . $middle);
						
					} //\is in scan range...
				} else {
					$page['text'] .= 'OMG You supa 1337 hax0r! j00 Pwnz mee!! [There has been a user-stupidity inflicted error. Don\'t play with the Hidden Variables]';
				}
				
				//If reload exists... lets reload!
				//echo 'RELOAD: ' . print_array($reload);
				//SQL n shit...
				if($reload){
					foreach($reload as $timetorl=>$reload2)
					{
						$fields = '';
						$values = '';
						foreach($reload2 as $wn=>$wa){
							$fields .= ',' . $wn;
							$values .= ',' . $wa;
						}
						//echo 'INSERT INTO `ws_auto_reload` (time_expires,shipid,gameid' . ($fields) . ') VALUES (' . (time() + $timetorl) . ',' . $player->shipid . ',' . $_SESSION['gameid'] . $values . ');<br />';
						$sql = 'INSERT INTO `ws_auto_reload` (time_expires,shipid,gameid' . ($fields) . ') VALUES (' . (time() + $timetorl) . ',' . $player->shipid . ',' . $_SESSION['gameid'] . $values . ');';
						$query = queryme($sql);
					}
				}
				
				//update players ship...
				//$reloadsql
				$sql = 'UPDATE `ws_ships` SET `shipid`=`shipid` ' . $reloadsql . ' WHERE `shipid` = ' . $player->shipid;
				$query = queryme($sql);
				
				//Events... and combat report...
				//Combat report...
				
			} //\Mode fire...
		}
	}
}

/////////CODE ABOVE////////////
//Set mode:
$page['mapmode'] = '1,0,0';

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