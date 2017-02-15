<?php
//auto_movement.php
//Querys the database, and moves any ships that need moving...

//Grab all movements that have their time_expires time elapsed already.....
$sql = 'SELECT * FROM `ws_auto_reload` WHERE time_expires < ' . time() . ' AND gameid = ' . $_SESSION['gameid'];
$query = queryme($sql);

if(mysql_num_rows($query)){
	
	//Loop through all the result rows...
	while($row =@ mysql_fetch_assoc($query)){
		$message = '';
		$sql_ex = '';
		//Get the ship, add weapons...
		
		$target = 1;
		while($target <= $weapon_count){
			$dbname = $weapon[$target]->dbname;
			if($row[$dbname]){
				$ships[$row['shipid']]->$dbname += $row[$dbname];
				$sql_ex .= ', ' . $dbname . '  = ' . $ships[$row['shipid']]->$dbname;
				
				$message .= $row[$dbname] . ' ' . $weapon[$target]->longname . ' have been reloaded and are ready to fire. ';
			}
		
			$target++;
		}
		
		//Update Database
		$sql = 'UPDATE `ws_ships` SET `shipid`=`shipid`' . $sql_ex . ' WHERE `shipid`=' . $row['shipid'];
		queryme($sql);
		
		//Give msg...
		if($ships[$row['shipid']]){
			$ships[$row['shipid']]->give_event($message, $row['time_expires']);
		}
	}
	
	//Delete all those that have been completed
	$sql = 'DELETE FROM `ws_auto_reload` WHERE time_expires < ' . time() . ' AND gameid = ' . $_SESSION['gameid'];
	$query = queryme($sql);
}
?>