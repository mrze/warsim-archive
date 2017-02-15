<?php
//map.php
//Generates the javascript that goes in the '$page['map']' variable
//This includes placing planets, ships and any travel paths...

//Lets start with the planets:
//Refer to 'assignplanets.php' for more info

//Check if something is there to place:
if($planet){
	
	//Loop through each planet, and grab the key, so we can get data out of the parrallel arrays
	foreach($planet as $key=>$p_name){
		
		//The Javascript looks like this:
		//place_planet(8,0,'Planet: Ut','Planet.gif');
		//place_planet(x,y,Type ': ' Name,Type '.gif');
		$page['map'] .= 'place_planet(' . $planet_x[$key] . ',' . $planet_y[$key] . ',\'' . $planet_type[$key] . ': ' . $p_name .'\',\'' . $planet_type[$key] . '.gif\');';
	
	} //End Planet Foreach
	
}

//A bit of player data...
//Place the player current location marker...
$page['map'] .= 'place_movement(' . $player->x . ',' . $player->y . ',\'playertarget.gif\',\'You are Here\');';

//And, place a path from location to destination...
if($player->path){
	$page['map'] .= make_path($player->path);
}

//Ok, now we should grab the ships

//Ships Rules are:
//If Friendly [P[team] = S[team]] display, 
//If hull = 0, display wreck
//else display shiptype

//Start with the Friendly ships
//We should place a sensor range down with these ships, as they all contribute to the team sensor range.. thingy
//Sensor range can be found by calling $ships[id]->get_sensor_range() and type can be found with ->get_type()
//Refer to setupships.php and class.php for more info

//Check teamlist exists [although, player will always be in it, so its not really needed
if($teamlist){
	
	//Loop through every team ship, use the list, so as to save CPU usage [rather than loading up complete objects]
	foreach($teamlist as $teamid){
		
		//The javascript to place a ship should not be created yet, since there may be enemys on the square.
		//Thus, we load it into a multidimensional array: 
			//$square_colour[x][y] = 'red','green','blue','none'
			//$square_number[x][y] = 'number of ships, not wrecks'
			//$square_type[x][y]   = 'fleet','capital','wreck','fighter'
			
			//To 'blend' each of these, there are functions available:
				// blend_colour('new_colour','existing_colour');
				// blend_type('new_type','existing_type');
				
			//Each of these returns the new result which should be stored
			//$ships[$teamid]->//
			$x = $ships[$teamid]->x;
			$y = $ships[$teamid]->y;
			
			//Check it isnt docked..
			if($ships[$teamid]->dockedin == 0){
				
				if($ships[$teamid]->get_status() == 'destroyed'){
					$colour = 'none';
					$square_number[$x][$y] = $square_number[$x][$y] + 0;
				} else {
					$colour = 'green';
					$square_number[$x][$y] = $square_number[$x][$y] + $ships[$teamid]->squadsize;
				}
				
				$square_colour[$x][$y] = blend_colour($colour,$square_colour[$x][$y]);
				
				$square_type[$x][$y]   = blend_type($ships[$teamid]->get_type(),$square_type[$x][$y]);
				
				//HOWEVER, we can do the ships scanning stuff... so:
					//Javascript looks a bit like this:
					//sensor_radius(x,y,range,planet_x,planet_y,planet_x,planet_y,....);
				
				//Firstoff, we get the limits of the sensor range for this specific ship
				$sensor_x_max = $ships[$teamid]->x + $ships[$teamid]->get_sensor_range();
				$sensor_x_min = $ships[$teamid]->x - $ships[$teamid]->get_sensor_range();
				$sensor_y_max = $ships[$teamid]->y + $ships[$teamid]->get_sensor_range();
				$sensor_y_min = $ships[$teamid]->y - $ships[$teamid]->get_sensor_range();
			
				//Nextly: we find all planets that lie within this limit
				//Loop through all planets, and check each
				if($planet){
					foreach($planet as $key=>$p_name){
						//Check me :D
						if(is_between($planet_x[$key],$sensor_x_max,$sensor_x_min) AND is_between($planet_y[$key],$sensor_y_max,$sensor_y_min)){
							//If it is between it, we should put its X and Y values in a nice list... 
							$xy_val_list .= ',' . $planet_x[$key] . ',' . $planet_y[$key];
						}
					}
				}
				
				//Now, we have a list that may look a bit like this: ',2,3,2,4,5,3,8,3'
				//So, we can chuck in the js stuff, and add it to the pile to be sent off to the browser...
				$page['map'] .= 'sensor_radius(' . $ships[$teamid]->x . ',' . $ships[$teamid]->y . ',' . $ships[$teamid]->get_sensor_range() . $xy_val_list . ');';
				
				//Clear some variables...
				$xy_val_list = '';
				
				//And, we are done for the team ships for now :D
			}
	} //End Foreach	
}

//Enemy ships:
if($enemylist){
	
	//Loop through every enemy ship [IE: every *Other* ship]
	foreach($enemylist as $enemyid){
		
		//Add the ship decals onto the map
		$x = $ships[$enemyid]->x;
		$y = $ships[$enemyid]->y;
		
		if($ships[$enemyid]->dockedin == 0){
			
			//Check if its in range
			if(can_scan_square($x,$y)){
				if($ships[$enemyid]->get_status() == 'destroyed'){
					$colour = 'none';
					$square_number[$x][$y] = $square_number[$x][$y] + 0;
				} else {
					$colour = 'red';
					$square_number[$x][$y] = $square_number[$x][$y] + $ships[$enemyid]->squadsize;
				}
				
				$square_colour[$x][$y] = blend_colour($colour,$square_colour[$x][$y]);
				$square_type[$x][$y]   = blend_type($ships[$enemyid]->get_type(),$square_type[$x][$y]);
			}
		}
	} //End Foreach
}

//Now, loop through all the $square stuff, and make some JS!
//place_fleets(x,y,number,'typecolour.gif')
if($square_colour){
	foreach($square_colour as $x => $square_c){
		foreach($square_c as $y => $square){
			if($square_colour[$x][$y] == 'none'){
				$square_colour[$x][$y] = 'blue';
			}
			$page['map'] .= 'place_fleets(' . $x . ',' . $y . ',' . $square_number[$x][$y] . ',\'' . $square_type[$x][$y] . $square_colour[$x][$y] . '.gif\');';
			
		}
	}
}

//Package it all in a nice 6 pack...
$page['map'] = '<script language="javascript">
<!--
	var map_mode=\'scanner\';

	

	function load_functions(){
		
		change_map_mode(' . $page['mapmode'] . ');
		
		//Map:
		make_map();
	
		//Other stuff:
		' . $page['map'] . '
		
		//Finally, update teh map
		update_map();
	}
	
	function mousehasclicked(x,y){
		if(map_mode == \'scanner\'){
			top.location = "scanner.php?mode=scansquare&scan_x=" + x + "&scan_y=" + y;
		}
		if(map_mode == \'travel\'){
			top.location = "travel.php?mode=goto&trv_x=" + x + "&trv_y=" + y;
		}
		if(map_mode == \'weapons\'){
			top.location = "weapons.php?mode=targetsquare&scan_x=" + x + "&scan_y=" + y;
		}
	}
//-->
</script>';
?>