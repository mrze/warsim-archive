<?php
//Assign Ships
$sql = 'SELECT * FROM ws_ships s, ws_users u, ws_shiprules sh, ws_teams t, ws_games g
WHERE s.userid=u.userid AND s.classid=sh.classid AND s.gameid = ' . $_SESSION['gameid'] . ' AND  s.gameid = g.gameid AND t.teamid = s.team AND hull > 0';
$query = queryme($sql);

//Scroll and setup...
while($row =@ mysql_fetch_assoc($query)){
	$ships[$row['shipid']] = new ship;
	$ships[$row['shipid']]->assign($row);
	
	$shiplist[] = $row['shipid'];
}

?>