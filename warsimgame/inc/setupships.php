<?php
//Assign Ships

//Check all data is valid...
if(!$_SESSION['shipid'] OR !$_SESSION['gameid']){
	//If not valid, clear it out, so it doesnt redirect back here again...
	$_SESSION['shipid'] = '';
	$_SESSION['gameid'] = '';
	
	$error = 'You are missing either a shipid or a gameid. Your session may have expired, please try again.';
	$error = urlencode($error);
	redirect('lobby.php?error=' . $error);
}

//Select `Our` Ship
$sql = 'SELECT * FROM ws_ships s, ws_users u, ws_shiprules sh, ws_teams t, ws_games g
WHERE s.userid=u.userid AND s.classid=sh.classid AND s.gameid = ' . $_SESSION['gameid'] . ' AND  s.gameid = g.gameid AND t.teamid = s.team AND s.shipid = ' . $_SESSION['shipid'];

$query = queryme($sql);

if(mysql_num_rows($query) != 1){
	//If not valid, clear it out, so it doesnt redirect back here again...
	$_SESSION['shipid'] = '';
	$_SESSION['gameid'] = '';
	
	$error = 'The shipid supplied is not valid. It may have been deleted, or the game may have been closed.';
	$error = urlencode($error);
	redirect('lobby.php?error=' . $error);
}

$row = mysql_fetch_assoc($query);

$ships[$row['shipid']] = new ship;
$ships[$row['shipid']]->assign($row);
$player =& $ships[$_SESSION['shipid']];

$shiplist[] = $row['shipid'];
$teamlist[] = $row['shipid'];

//Select all ships in a specific game
$sql = 'SELECT *
FROM ws_ships s, ws_users u, ws_shiprules sh, ws_teams t
WHERE s.userid=u.userid AND s.classid=sh.classid AND s.gameid = ' . $_SESSION['gameid'] . ' AND t.teamid = s.team AND s.shipid != ' . $_SESSION['shipid'] . ' AND ((hull > 0) OR (hull = 0 AND endgametime > ' . (time() - (60*15)) . ')) ORDER BY x,y';

$query = queryme($sql);

//Loop through all the ships, load each one as a new object
//Ships loaded as: $ships[shipid];
//ShipID list is loaded as: $shiplist[1->infinity];
//use the following code to loop through all ships:
/* foreach($shiplist as $id){
	$ships[$id].....
}*/

//Team and enemy loops can use their respective lists
//Player ship is on the team list.

while($row =@ mysql_fetch_assoc($query)){
	$ships[$row['shipid']] = new ship;
	$ships[$row['shipid']]->assign($row);
	
	$shiplist[] = $row['shipid'];

	if($row['team'] == $player->team AND $row['team'] != 1){
		$teamlist[] = $row['shipid'];
	} else {
		$enemylist[] = $row['shipid'];
	}
}

//Points 4 Ships Mode, grab current Credit status...
if($player->option_point_for_ships == 1){
	//P4S on... Grab Credit Amount...
	$player->credits = get_credit_amount();
}
?>