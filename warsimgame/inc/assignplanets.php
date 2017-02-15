<?php
//Assign Planets
//Incase we are on a special page...
if(!$_SESSION['gameid'] AND $_GET['game']){
	$_SESSION['gameid'] = $_GET['game'];
}

//Load planets...
$sql = 'SELECT s.planettype, s.planetname, s.planetx, s.planety
FROM ws_planets s, ws_games g
WHERE g.gameid = ' . $_SESSION['gameid'] . ' AND s.mapid = g.mapid';

$query = queryme($sql);

//Loads each planet into $planet array, which details can be found in its parrallel forms...
while($row =@ mysql_fetch_assoc($query)){
	$planet[] = $row['planetname'];
	$planet_type[] = $row['planettype'];
	$planet_x[] = $row['planetx'];
	$planet_y[] = $row['planety'];
}

?>