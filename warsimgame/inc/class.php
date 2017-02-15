<?php

////////////////////////////////////////////////////////////////////////////////

class timer
{
	//Holds the time information
	var $mtime;
	var $start;
	var $stop;
	var $total;
	
	function start()
	{
		//Start the timer
		$this->mtime = microtime ();
		$this->mtime = explode (' ', $this->mtime);
		$this->start = $this->mtime[1] + $this->mtime[0];
	}
	
	function stop()
	{
		//Stops the timer
		$this->mtime = microtime ();
		$this->mtime = explode (' ', $this->mtime);
		$this->stop = $this->mtime[1] + $this->mtime[0];
		return round (($this->stop - $this->start), 5) . ' seconds';
	}
}

////////////////////////////////////////////////////////////////////////////////

class ship
{
	var $reload_schedule;
	var $reload_schedule_set;
	var $credits;

	//'Mon Calamari MC-80b 11 (Team: 11) from position [11,11]'
	//$player->classname . ' <em>' . $player->shipname . '</em> (Team: ' . $player->team . ') from position ' . $player->get_position();
	
	function assign($row)
	{
		//Import Ship		
		foreach($row as $key => $value){
			$this->$key = $value;
		}
		
		
		
		//Do some 'POST IMPORT' processing... mostly relating to fighters...
		//If >=3 but <6 then its a group...
		if($this->maxsquadsize >= 3 AND $this->maxsquadsize <= 5){
			$this->classname .= ' Assault Group';
		}
		
		//If > 6 its a squadron
		if($this->maxsquadsize >= 6){
			$this->classname .= ' Squadron';
		}
		
		/*********************************************************************************************************
		**********************************************************************************************************
												OPT!
		**********************************************************************************************************
		*********************************************************************************************************/
		
		//Set max...
		$this->hullmax = $this->squadsize * $this->hullmax;
		$this->ionicmax = $this->squadsize * $this->ionicmax;
		$this->shieldsmax = $this->squadsize * $this->shieldsmax;
		$this->hvlasermax = $this->squadsize * $this->hvlasermax;
		$this->ioncannonmax = $this->squadsize * $this->ioncannonmax;
		$this->turbolasermax = $this->squadsize * $this->turbolasermax;
		$this->ionbatterymax = $this->squadsize * $this->ionbatterymax;
		$this->protonmax = $this->squadsize * $this->protonmax;
		$this->concussionmax = $this->squadsize * $this->concussionmax;
		$this->passengers = $this->passengers * $this->squadsize;
		
		//Check within limits...
		$this->hull = min($this->hullmax,$this->hull);
		$this->ionic = min($this->ionicmax,$this->ionic);
		$this->shields = min($this->shieldsmax,$this->shields);
		$this->ioncannon = min($this->ioncannonmax,$this->ioncannon);
		$this->turbolaser = min($this->turbolasermax,$this->turbolaser);
		$this->ionbattery = min($this->ionbatterymax,$this->ionbattery);
		$this->proton = min($this->protonmax,$this->proton);
		$this->concussion = min($this->concussionmax,$this->concussion);
		
		//Set real max...
		$this->hullmax = $this->hullmax / $this->squadsize * $this->maxsquadsize;
		$this->ionicmax = $this->ionicmax / $this->squadsize * $this->maxsquadsize;
		$this->shieldsmax = $this->shieldsmax / $this->squadsize * $this->maxsquadsize;
		$this->hvlasermax = $this->hvlasermax / $this->squadsize * $this->maxsquadsize;
		$this->ioncannonmax = $this->ioncannonmax / $this->squadsize * $this->maxsquadsize;
		$this->turbolasermax = $this->turbolasermax / $this->squadsize * $this->maxsquadsize;
		$this->ionbatterymax = $this->ionbatterymax / $this->squadsize * $this->maxsquadsize;
		$this->protonmax = $this->protonmax / $this->squadsize * $this->maxsquadsize;
		$this->concussionmax = $this->concussionmax / $this->squadsize * $this->maxsquadsize;
		$this->passengers = $this->passengers / $this->squadsize * $this->maxsquadsize;
	}
	
	function set_rs()
	{
		if(!$this->reload_schedule_set){
			//Get reloads... $reload_schedule
			$sql = 'SELECT `time_expires`, `hvlaser` , `ioncannon` , `turbolaser` , `ionbattery` , `proton` , `concussion` FROM `ws_auto_reload` WHERE `shipid`= ' . $this->shipid . ' ORDER BY `time_expires` ASC';
			$query = queryme($sql);
			
			while($row =@ mysql_fetch_assoc($query)){
				$this->reload_schedule[($row['time_expires'] - time())] = array('hvlaser' => $row['hvlaser'], 'ioncannon' => $row['ioncannon'], 'turbolaser' => $row['turbolaser'], 'ionbattery' => $row['ionbattery'], 'proton' => $row['proton'], 'concussion' => $row['concussion']);
			}
			$this->reload_schedule_set = true;
		}
	}
	
	function get_picture()
	{
		if($this->get_status() == 'destroyed'){
			return 'images/ships/' . $this->damagedimage;
		} else {
			return 'images/ships/' . $this->normalimage;
		}
	}
	
	function get_sensor_range()
	{
		//Gets the sensor range, takes into account wether the ship is alive, or dead.
		if($this->get_status() == 'destroyed'){
			return 0;
		} else {
			$range = (2 * floor(log($this->sensors))) + 1;
			if($this->shiptype == 'Fighter'){
				$range = ceil($range * 1.5);
			}
			return $range;
		}
	}
	
	function get_type()
	{
		//Gets the type of the ship, either wreck, or capital/fighter/freightor [lowercase]
	
		if($this->get_status() == 'destroyed'){
			return 'wreck';
		} else {
			return strtolower($this->classtype);
		}
	}
	
	function get_status()
	{
		if($this->hull > 0){
			if($this->ionic > 0){
				return 'normal';
			} else {
				return 'disabled';
			}
		} else {
			return 'destroyed';
		}
	}
	
	function get_moving_status()
	{
		//Are we docked?
		if($this->dockedin != 0){
			return 'Ship is Docked';
		}
	
		//If path has stuff in it, we are obviously moving
		if($this->path){
			$text = 'Ship is Moving ';
		} else {
			return 'Ship is Stationary';
		}
		
		//If we are still here, then we are moving... grab first coordinate from the path...
		$next = explode(',',$this->path,2);
		
		//$xy will be an array containing the next square as [0,1]
		$xy = explode('-',$next[0]);
		
		//Find out wether we are going north or south, or sitting where we are...
		$rise = $xy[1] - $this->y;
		if($rise == '1'){
			$text .= 'S';
		}
		if($rise == '-1'){
			$text .= 'N';
		}
		
		//Find out which horizontal directon we are going in
		$run = $xy[0] - $this->x;
		if($run == '1'){
			$text .= 'E';
		}
		if($run == '-1'){
			$text .= 'W';
		}
		
		return $text;
	}
	
	function give_event($text,$time = 'na')
	{
		if($time == 'na'){
			$time = time();
		}
				
		//Insertion >=O
		$sql = 'INSERT INTO `ws_events` (`shipid` , `time` , `text` )VALUES (' . $this->shipid . ', ' . $time . ', "' . $text . '");';
		$query = queryme($sql);
	}
	
	function give_team_event($text,$time = 'na')
	{
		if($time == 'na'){
			$time = time();
		}
				
		//Insertion >=O
		$sql = 'INSERT INTO `ws_events` (`shipid` , `time` , `text` )VALUES ("team' . $this->team . '", ' . $time . ', "' . $text . '");';
		$query = queryme($sql);
	}
	
	function give_game_event($text,$time = 'na')
	{
		if($time == 'na'){
			$time = time();
		}
				
		//Insertion >=O
		$sql = 'INSERT INTO `ws_events` (`shipid` , `time` , `text` )VALUES ("game' . $_SESSION['gameid'] . '", ' . $time . ', "' . $text . '");';
		$query = queryme($sql);
	}
	
	function get_shield_gen()
	{
		if($this->shieldgen == 1){
			return 'Operational';
		} else {
			return 'Disabled';
		} 
	}
	
	function get_position()
	{
		return '[' . $this->x . ',' . $this->y . ']';
	}
	
	function get_travel_time($squares)
	{
		//Returns in seconds
		$temp = round((30 / $this->sublight),2);
		$temp = floor(min(max($temp,0.4),2) * $squares * 60);
		return $temp;
	}
	
	
	function ship_overview($first = '',$second = '',$third = '')
	{
		global $weapon;
		global $ships;
		
		//Get nice movement info
		$movingstatus = $this->get_moving_status();
		if($this->path){
			//If we are moving, find an eta for us love...
			$sql = 'SELECT * FROM `ws_auto_movement` WHERE `shipid` = ' . $_SESSION['shipid'];
			$query = queryme($sql);
			$row = mysql_fetch_assoc($query);
			$rem_time = $row['endtime'] - time();
			$rem_time = $rem_time + $timefornewpath;
			
			//Add it to the end
			$movingstatus .= '</strong><br /><strong>ETA:</strong> ' . parse_time($rem_time) . '<strong>';
		}
		
		//Credits... if available.. [P4S]
		if($this->option_point_for_ships){
			$credits = '<strong>Credits:</strong> ' . number_format($this->credits) . ' &curren;<br />';
		}
		
		if($this->squadsize > 1){
			$squadsize = '<strong>Squad Size: </strong>' . $this->squadsize . ' of ' . $this->maxsquadsize . '<br />';
		}
		
		$text .= 	'<!-- Begin -->
			  <div align="center"><table width="100%" class="scanresults">
			     <tr>
					<td width="1%" valign="top"><img src="' . $this->get_picture() . '" width="100" height="100" onmouseover="return overlib(\'' . $this->classname . '\', CAPTION, \'Ship Type\');" onmouseout="return nd();" /></td>
					<td valign="top">
					<span class="underline">&raquo; ' . $this->shipname . '</span><br />
					<strong>Pilot: </strong> ' . $this->username . '<br />
					<strong>Position: </strong> ' . $this->get_position() . '<br />
					<strong>Team: </strong> ' . $this->teamname . '<br />	
					<strong>' . $movingstatus . '</strong><br />
					' . $squadsize . '
					' . $credits . '
					</td>
			  	 </tr>
				 <tr>
				 	<td colspan="2">
						<span class="underline">&raquo; Ship Systems:</span><br /><br />
						<strong>Shields: </strong>' . number_format($this->shields) . ' / ' . number_format($this->shieldsmax) . '<br />
						<strong>Shield Generators:</strong> ' . $this->get_shield_gen() . '<br />
						' . make_bar($this->shields, $this->shieldsmax,'Shields') . '<br />
						<strong>Hull: </strong> ' . number_format($this->hull) . ' / ' . number_format($this->hullmax) . '<br />
						' .  make_bar($this->hull, $this->hullmax,'Hull') . '<br />
						<strong>Ionic Capitance: </strong>' . number_format($this->ionic) . ' / ' . number_format($this->ionicmax) . '<br />
						' .  make_bar($this->ionic,$this->ionicmax,'Ionic Cap') . '<br />';
					
		$text .=  '<table width="60%" align="center"><th>';
						
						
						
		//Little 'Attributes table' with Docking bays, tractors, assault teams, and sensor range... button thingys
		if($this->dockingbaymax > 0){
			
			if(($this->dockingbaymax - $this->dockedwithin) > 0){
				$text .= '<img src="images/weapons/dockingbayYES.gif" onmouseover="return overlib(\'' . ($this->dockedwithin) . ' Ships Docked<br />Space For: ' . ($this->dockingbaymax) . ' squadrons\', CAPTION,\'Docking Bays\');" onmouseout="return nd();" /></th>';
			} else {
				$text .= '<img src="images/weapons/dockingbayNO.gif" onmouseover="return overlib(\'' . ($this->dockedwithin) . ' Ships Docked<br />Space For: ' . ($this->dockingbaymax) . ' squadrons<br />Docking Bays Full\', CAPTION,\'Docking Bays\');" onmouseout="return nd();"/></th>';
			} 
		} else {
			$text .= '<img src="images/weapons/dockingbayNONO.gif" onmouseover="return overlib(\'No Docking Bays\', CAPTION,\'Docking Bays\');" onmouseout="return nd();"/></th>';
		}
							
		//Does this ship have marines...
		if($this->marines > 0){
			$text .= '<th><img src="images/weapons/marines.gif" onmouseover="return overlib(\'' . $this->marines . ' Marines Ready<br />Passenger Capacity: ' . $this->passengers . '\', CAPTION,\'Marines\');" onmouseout="return nd();" /></th>';
		} else {
			$text .= '<th><img src="images/weapons/marinesno.gif" onmouseover="return overlib(\'Not carrying any Marines<br />Passenger Capacity: ' . $this->passengers . '\', CAPTION,\'Marines\');" onmouseout="return nd();" /></th>';
		}

		//Ditto for tractor beams
		if($this->tractormax){
			if($this->tractor){
				$text .= '<th><img src="images/weapons/gravityGeneratorYES_ON.gif" onmouseover="return overlib(\'' . $this->tractor . ' / ' . $this->tractormax . ' Tractor Beams Ready<br />If Only they were implemented...\', CAPTION,\'Tractor Beams\');" onmouseout="return nd();"/></th>';
			} else {
				$text .= '<th><img src="images/weapons/gravityGeneratorYES_OFF.gif" onmouseover="return overlib(\'' . $this->tractor . ' / ' . $this->tractormax . ' Tractor Beams Ready<br />If Only they were implemented...\', CAPTION,\'Tractor Beams\');" onmouseout="return nd();"/></th>';
			}
		} else {
			$text .= '<th><img src="images/weapons/gravityGeneratorNO.gif" onmouseover="return overlib(\'No Tractor Beams\', CAPTION,\'Tractor Beams\');" onmouseout="return nd();"/></th>';
		}
		$text .= '<th><img src="images/weapons/scanners.gif" onmouseover="return overlib(\'Sensors: ' . $this->sensors . '<br />Range: ' . $this->get_sensor_range() . '\', CAPTION,\'Sensors\');" onmouseout="return nd();"/></th>';

		
		$text .=  '</table>';
				
		//Weapons...
		$wl = $this->weapon_list();
		
		//If there is something to display...
		if($wl AND $this->get_status() == 'normal'){
			$text .= '<span class="underline">&raquo; Weapon Report:</span><br /><br />';
			$text .= $wl;
		}
		
		if($this->get_status() == 'destroyed'){
			$text .= '<br /><br /><div align="center"><a href="goto-lobby.php">Your ship is destroyed. Click here to Go Back to the Lobby and Create a New one.</a></div>';
		}
		if($this->classtype == 'Capital' AND $this->get_status() == 'normal')
		{
			$text .= '<br /><div align="center"><a href="hailall.php?mode=hailall">Hail Entire System</a></div>';
		}
		
		//Close off the tag...
		$text .= '</td></tr></table></div>';
			  
		return $text;
	}
	
	function weapon_list()
	{
	
	global $weapon;
	global $weapon_count;
	
	//Loop through all the weapons...
	$target = 1;
	while($target <= $weapon_count){
		//Stuff here..

		//Display a nice table for each weapon  that exists...
		if($weapon[$target]->get_max()){
		
			//It exists... so lets display stuff...
			
			$text .= '<table width="100%" border="0">
			<tr  onmouseover="return overlib(\'' . $weapon[$target]->get_ready() . ' Ready, ' . $weapon[$target]->get_recharging() . '  Recharging, ' . $weapon[$target]->get_damaged() . ' Damaged<br />' . $weapon[$target]->use . '<br />' . $weapon[$target]->reloadtime . ' min <em>overall</em> recharge time<br />' . $weapon[$target]->weaponrange . ' Square Weapon Range<br />' . $weapon[$target]->get_reload_times() . '\', CAPTION, \'' . $weapon[$target]->longname . '\',WIDTH,250,LEFT,ABOVE);" onmouseout="return nd();" >
				<td width="1%"><img src="' . $weapon[$target]->smallimage . '" onclick="load_range(' . $weapon[$target]->weaponrange . ',' . $this->x . ',' . $this->y . ')" /></td>
				<td width="50%" class="norfont"><strong>' . $weapon[$target]->longname . '</strong></td>
				<td class="norfont"><strong>Ready: </strong>' . $weapon[$target]->get_ready() . '</td>
			</tr>
			<tr>
				<td colspan="3"><div align="center">' . make_tri_bar($weapon[$target]->get_ready(),$weapon[$target]->get_recharging(),$weapon[$target]->get_damaged(),$weapon[$target]->longname) . '</div></td>
			</tr>
		</table><br />';

		}

		//\Stuff here...
		$target++;
	}
	
	return $text;
	
	}
	
	function small_scan($team, $button = '',$others = 1)
	{		
		global $player;
		if($this->team == $team AND $team != 1){
			$ally = ' <small><span class="green">[Ally]</span></small>';
		}
		if($this->shipid == $player->shipid){
			$ally = ' <small><span class="green">[You]</span></small>';
		}
		
		$text .= '<table width="100%" class="scanresults">
		<tr>
		<td width="1%" valign="top"><img src="' . $this->get_picture() . '" width="100" height="100" onmouseover="return overlib(\'' . $this->classname . '\', CAPTION, \'Ship Type\');" onmouseout="return nd();" /></td>
		<td valign="top">
		<strong><a href="scanner.php?mode=scanship&shipid=' . $this->shipid . '">
		&raquo;	' . $this->shipname . '</a>' . $ally . '</strong>
		<br /><strong>Location:</strong> ' . $this->get_position() . '<br />';
		
		if($this->squadsize > 1){
			$text .= '<strong>Squad Size</strong>: ' . $this->squadsize . ' of ' . $this->maxsquadsize . '<br />';
		}
		
		$text .= '<table width="100%" align="left">
		<tr><td width="1%" align="center" class="scanresultsnb">S</td>
		<td>' . make_bar($this->shields,$this->shieldsmax,'Shields') . '</td>
		</tr>
		<tr>
		<td width="1%" align="center" class="scanresultsnb">H</td>
		<td>' . make_bar($this->hull,$this->hullmax,'Hull') . '</td>
		</tr>
		<tr>
		<td width="1%" align="center" class="scanresultsnb">I</td>
		<td>' . make_bar($this->ionic,$this->ionicmax,'Ionic') . '</td>
		</tr>
		</table>
		</td>
		</tr>
		<tr>
		<td colspan="2" align="center"><form style="display: inline"  method="get" action="scanner.php"><input type="hidden" name="mode" value="scanship" /><input type="hidden" name="shipid" value="' . $this->shipid . '" /><input type="submit" name="scan" value="Scan" class="form" /></form>';
		
		//Display Hail + Target links
		if($this->hull > 0 AND $others){
			$text .= '&nbsp;<form style="display: inline"  method="get" action="hail.php"><input type="hidden" name="mode" value="hailship" /><input type="hidden" name="shipid" value="' . $this->shipid . '" /><input type="submit" name="hail" value="Hail" class="form" /></form>';
			
			if($this->team != $team OR $team == 1){
				if($this->shipid != $player->shipid){
					$text .= '&nbsp;<form style="display: inline"  method="get" action="weapons.php"><input type="hidden" name="mode" value="targetship" /><input type="hidden" name="shipid" value="' . $this->shipid . '" /><input type="submit" name="target" value="Target" class="form" /></form>';
				}
			}
		}
		
		$text .= $button;
		
		$text .= '</td></tr></table><br />';	
		
		return $text;
	}
	
	function large_scan($middle = ''){
		global $player;
		
		$movingstatus = $this->get_moving_status();
	
		$text = '<!-- Begin -->
			  <div align="center"><table width="100%" class="scanresults">
			     <tr>
					<td width="1%" valign="top"><img src="' . $this->get_picture() . '" width="100" height="100" onmouseover="return overlib(\'' . $this->classname . '\', CAPTION, \'Ship Type\');" onmouseout="return nd();" /></td>
					<td valign="top">
					<span class="underline">&raquo; ' . $this->shipname . '</a></span><br />
					<strong>Pilot: </strong> ' . $this->username . '<br />
					<strong>Position: </strong> ' . $this->get_position() . '<br />
					<strong>Team: </strong> ' . $this->teamname . '<br />
					<strong>' . $movingstatus . '</strong>';
					
					if($this->squadsize > 1){
						$text .= '<br /><strong>Squad Size</strong>: ' . $this->squadsize . ' of ' . $this->maxsquadsize . '<br />';
					}
										
					$text .= '</td>
			  	 </tr>
				 <tr>
				 	<td colspan="2"><br />';
										
		$text .= '<span class="underline">&raquo; Ship Systems:</span><br /><br />
					<strong>Shields: </strong>' . number_format($this->shields) . ' / ' . number_format($this->shieldsmax) . '<br />
					<strong>Shield Generators:</strong> ' . $this->get_shield_gen() . '<br />
					' . make_bar($this->shields, $this->shieldsmax,'Shields') . '<br />
					<strong>Hull: </strong>' . number_format($this->hull) . ' / ' . number_format($this->hullmax) . '<br />
					' .  make_bar($this->hull, $this->hullmax,'Hull') . '<br />
					<strong>Ionic Capitance: </strong>' . number_format($this->ionic) . ' / ' . number_format($this->ionicmax) . '<br />
					' .  make_bar($this->ionic, $this->ionicmax,'Ionic') . '<br />';
				
				
		//Either middle or buttons... Which one?
		if($middle){
		
			$text .= $middle;
		
		} else {
		
			//Hail/Shoot buttons
			if($this->hull > 0){
				$text .= '<br /><strong>Options:</strong><br /><div align="center">';
				
				$text .= '&nbsp;<form style="display: inline"  method="get" action="hail.php"><input type="hidden" name="mode" value="hailship" /><input type="hidden" name="shipid" value="' . $this->shipid . '" /><input type="submit" name="hail" value="Hail" class="form" /></form>';
				
				if($this->team != $player->team OR $this->team == 1){
					if($this->shipid != $player->shipid){
						$text .= '&nbsp;<form style="display: inline"  method="get" action="weapons.php"><input type="hidden" name="mode" value="targetship" /><input type="hidden" name="shipid" value="' . $this->shipid . '" /><input type="submit" name="target" value="Target" class="form" /></form>';
					}
				}
				
				$text .= '</div>';
			}
		}
		
		$text .= '</td></tr></table></div><!-- End -->';
			  
		return $text;
	}

	function give_credits($credits,$reason)
	{
		if($this->option_point_for_ships){
			//Check P4S is on...
			
			//Now We add the stuff...
			$query = queryme('UPDATE `ws_p4s_accounts` SET credits=credits+' . floor($credits) . ' WHERE userid = ' . $_SESSION['userid'] . ' AND gameid = ' . $_SESSION['gameid']);
			
			$this->give_event(floor($credits) . ' credits</span> were added to your account for ' . $reason);
		}
	}
	

	function debug()
	{
		//Echo Object Vars
		$text .= "<pre>";
		$text .= print_r(get_object_vars($this),1);
		$text .= "</pre>";
		
		return $text;		
	}
}

////////////////////////////////////////////////////////////////////////////////
//Declare Weapons..

class weapon
{
	var $longname;
	var $dbname;
	var $smallimage;
	var $largeimage;
	var $weaponrange;
	var $reloadtime;
	var $use;
	var $capitaltocapitaldamage;
	var $capitaltocapitalhits;
	var $capitaltofreightordamage;
	var $capitaltofreightorhits;
	var $capitaltofighterdamage;
	var $capitaltofighterhits;
	var $freightortocapitaldamage;
	var $freightortocapitalhits;
	var $freightortofreightordamage;
	var $freightortofreightorhits;
	var $freightortofighterdamage;
	var $freightortofighterhits;
	var $fightertocapitaldamage;
	var $fightertocapitalhits;
	var $fightertofreightordamage;
	var $fightertofreightorhits;
	var $fightertofighterdamage;
	var $fightertofighterhits;
	var $attacks;
	
	function assign($ln,$db,$sm,$lr,$wr,$rt,$use,$ctocd,$ctoch,$ctofd,$ctofh,$ftocd,$ftoch,$ftofd,$ftofh,$attacks)
	{
		$this->longname = $ln;
		$this->dbname = $db;
		$this->smallimage = $sm;
		$this->largeimage = $lr;
		$this->weaponrange = $wr;
		$this->reloadtime = $rt;
		$this->use = $use;
		$this->attacks = $attacks;
		
		$this->capitaltocapitaldamage = $ctocd;
		$this->capitaltocapitalhits = $ctoch;
		$this->capitaltofreightordamage = $ctofd;
		$this->capitaltofreightorhits  = $ctofh + 10;
		$this->capitaltofighterdamage = $ctofd;
		$this->capitaltofighterhits = $ctofh;
		
		$this->freightortocapitaldamage = $ftocd;
		$this->freightortocapitalhits = $ftoch;
		$this->freightortofreightordamage = $ftofd;
		$this->freightortofreightorhits = $ftofh + 10;
		$this->freightortofighterdamage = $ftofd;
		$this->freightortofighterhits = $ftofh;

		$this->fightertocapitaldamage = $ftocd;
		$this->fightertocapitalhits = $ftoch;
		$this->fightertofreightordamage = $ftofd;
		$this->fightertofreightorhits = $ftofh + 10;
		$this->fightertofighterdamage = $ftofd;
		$this->fightertofighterhits = $ftofh;

	}
	
	function get_ready()
	{
		global $player;
		$rdvar = $this->dbname;
		return $player->$rdvar;
	}
	
	function get_recharging()
	{
		global $player;
		
		return $this->get_max() - $this->get_ready() - $this->get_damaged();
	}
	
	function get_damaged()
	{
		global $player;
		$damvar = $this->dbname . 'dam';
		return $player->$damvar;
	}
	
	function get_max()
	{
		global $player;
		$maxvar = $this->dbname . 'max';
		return $player->$maxvar;
	}
	
	function get_reload_times()
	{
		global $player;
	
		//Get reload schedule...
		$player->set_rs();

		//IF the array exists... lets loop through it...
		if($player->reload_schedule){
			//Loop through each record, and grab the array of reloads plus time...
			foreach($player->reload_schedule as $time=>$wmds){
				//If our weapon exists (> 0) we have a time to reload :D
				if($wmds[$this->dbname] > 0){
					$text .= '&nbsp;&raquo;&nbsp;' . $wmds[$this->dbname] . ' ready in ' . parse_time($time) . '<br />';
				}
			}
		}
		if($text){
			return 'Reload Schedule:<br />' . $text;
		}
	}
	
	function debug()
	{
		//Echo Object Vars
		$text .= "<pre>";
		$text .= print_r(get_object_vars($this),1);
		$text .= "</pre>";
		
		return $text;		
	}
}

///INITIALISE WEAPONS
if($_SESSION['gameid']){
	$sql = 'SELECT * FROM ws_weapons w, ws_games g WHERE g.gameid = ' . $_SESSION['gameid'] . ' AND w.weaponid = g.weaponid';
	$query = queryme($sql);
	$target = 1;
	
	while($row = mysql_fetch_assoc($query)){
		$weapon[$target] = new weapon;
		$weapon[$target]->assign($row['longname'],$row['dbname'],$row['button_image'],$row['image'],$row['range'],$row['reloadtime'],$row['use'],$row['ctocd'],$row['ctoch'],$row['ctofd'],$row['ctofh'],$row['ftocd'],$row['ftoch'],$row['ftofd'],$row['ftofh'],$row['attacks']);
		$target++;
		
	}
}

$weapon_count = count($weapon);
?>