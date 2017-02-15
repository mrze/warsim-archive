<?php
//auto_movement.php
//Querys the database, and moves any ships that need moving...

//Grab all movements that have their time_expires time elapsed already.....
$sql = 'SELECT * FROM `ws_auto_movement` WHERE time_expires < ' . time() . ' AND gameid = ' . $_SESSION['gameid'];
$query = queryme($sql);

if(mysql_num_rows($query)){
	
	//Loop through all the result rows...
	while($row =@ mysql_fetch_assoc($query)){
	
		//Figure out how many times it has repeated since the timer expired
		$repeats = 1 + floor((time() - $row['time_expires']) / $row['repeattime']);
		//Second copy of Repeats
		$repeats2 = $repeats;
		
		//Split path up into an array
		$path = explode(',',$row['path']);
		
		//Remove the squares we have 'been to'...
		while($repeats != 0 AND $path){
			$temp = array_shift($path);
			$check = explode('-',$temp);
			if(check_for_burnination($row['shipid'],time(),$check[0],$check[1])){
				$repeats = 1;
			}
			$repeats--;
		}
	
		//Split path back into a string
		$path = implode(',',$path);
	
		//Get the 'last visited' position as reported by array_shift... and split it into an array
		$temp = explode('-',$temp);
		
		//Update ship
		$sql = 'UPDATE `ws_ships` SET `path`="' . $path . '",`x`=' . $temp[0] . ',`y`=' . $temp[1] . ' WHERE `shipid` = ' .  $row['shipid']  . ' OR `dockedin` = ' . $row['shipid'];
		$query = queryme($sql);
		
		//And in the object...
		$ships[$row['shipid']]->path = $path;
		$ships[$row['shipid']]->x	 = $temp[0];
		$ships[$row['shipid']]->y	 = $temp[1];
		
		//Get the next time for update...
		$row['time_expires'] = $row['time_expires'] + ($row['repeattime'] * $repeats2);
		
		//Update movement db
		$sql = 'UPDATE `ws_auto_movement` SET `path`="' . $path . '", `time_expires`="' . $row['time_expires'] . '" WHERE `shipid`= ' .  $row['shipid'];
		$query = queryme($sql);
		
		//If we have no-where else to go... lets give 'em a destination reached message...
		if(!$path){
			$event_text = 'We have reached our destination successfully, sublight engines are being deactivated. Current position: ' . $ships[$row['shipid']]->get_position();
			$ships[$row['shipid']]->give_event($event_text,$row['endtime']);
		}
		
		check_for_burnination($row['shipid'],$row['endtime']);
	}
	
	$sql = 'DELETE FROM `ws_auto_movement` WHERE `path`=""';
	$query = queryme($sql);
}

?>