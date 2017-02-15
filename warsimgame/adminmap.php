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


//Check teamlist exists [although, player will always be in it, so its not really needed
if($shiplist){
	
	//Loop through every team ship, use the list, so as to save CPU usage [rather than loading up complete objects]
	foreach($shiplist as $teamid){
				
			$x = $ships[$teamid]->x;
			$y = $ships[$teamid]->y;
			
			if($ships[$teamid]->get_status() == 'destroyed'){
				$colour = 'none';
				$square_number[$x][$y] = $square_number[$x][$y] + 0;
			} else {
				$colour = 'green';
				$square_number[$x][$y] = $square_number[$x][$y] + $ships[$teamid]->squadsize;
			}
				
			$square_colour[$x][$y] = blend_colour($colour,$square_colour[$x][$y]);
			$square_type[$x][$y]   = blend_type($ships[$teamid]->get_type(),$square_type[$x][$y]);
				
			$colour = 'green';
			
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
			top.location = "admindo.php?mode=targetsquare&scan_x=" + x + "&scan_y=" + y;
		}
		if(map_mode == \'travel\'){
			top.location = "admindo.php?mode=targetsquare&scan_x=" + x + "&scan_y=" + y;
		}
		if(map_mode == \'weapons\'){
			top.location = "admindo.php?mode=targetsquare&scan_x=" + x + "&scan_y=" + y;
		}
	}
//-->
</script>';
?>