<?php 

session_start();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Ships in Game</title>
<link href="warsim.css" rel="stylesheet" type="text/css" />
</head>

<body class="dispkey">
<strong>Ship List [Recently Alive]: </strong><br />
<table width="100%">

<?php 

include 'inc/connect.php'; 
$count = 1;
$query = queryme('SELECT `shipname`,`classname`,`username`,`hull`,`ionic` FROM ws_ships s, ws_shiprules sr, ws_users u WHERE s.classid = sr.classid AND s.userid = u.userid ORDER BY s.shipid');
while($row =@ mysql_fetch_assoc($query)){
	if($row['hull'] < 1){
		echo '<tr><td>' . $count . '</td><td>' . $row['shipname'] . ' [Destroyed]</td><td>' . $row['classname'] . '</td><td>' . $row['username'] . '</td></tr>';
	} else {
		if($row['ionic'] < 1){
			echo '<tr><td>' . $count . '</td><td>' . $row['shipname'] . ' [Disabled]</td><td>' . $row['classname'] . '</td><td>' . $row['username'] . '</td></tr>';
		} else {
			echo '<tr><td>' . $count . '</td><td>' . $row['shipname'] . '</td><td>' . $row['classname'] . '</td><td>' . $row['username'] . '</td></tr>';
		}
		
	}
	$count++;
}

echo '</table><br /><strong>Number of ships in DB: </strong>' . mysql_num_rows($query) . '<br />';

$query = queryme('SELECT `shipid` FROM `ws_ships` ORDER BY `shipid` DESC LIMIT 1');
$row = mysql_fetch_assoc($query);

echo '<strong>Number of ships in Archives: </strong>' . $row['shipid'] . '<br />';

$query = queryme('SELECT `shipid` FROM `ws_ships` WHERE hull > 0');

echo '<strong>Num Ships Alive:</strong> ' . mysql_num_rows($query) . '<br />';

$query = queryme('SELECT * FROM `ws_events`');
echo '<strong>Total Events in DB: </strong>' . mysql_num_rows($query) . '<br />';
$query = queryme('SELECT `eventid` FROM `ws_events` ORDER BY `eventid` DESC LIMIT 1');
$row = mysql_fetch_assoc($query);
echo '<strong>Total Events so far: </strong>' . $row['eventid'] . '<br />';
$query = queryme('SELECT `username` FROM ws_users u');
echo '<strong>Total Users: </strong>' . mysql_num_rows($query) . '<br /><br />';

echo '<br /><br /><strong>Users With #Ships: </strong><table>';

$query = queryme('SELECT `username`,`usershipcount` FROM ws_users WHERE `usershipcount` != 0 ORDER BY `usershipcount` DESC LIMIT 60');

if(mysql_num_rows($query)){
	while($places =@ mysql_fetch_assoc($query)){
		$bigplaces[$places['username']] = $places['usershipcount'];
	}
	
	arsort($bigplaces);
	reset($bigplaces);
	
	echo '<table width="100%">';
	
	foreach($bigplaces as $k=>$v){
		echo '<tr><td>' . $k . '</td><td>' . $v . '</td></tr>';
	}
} else {
	echo '<td>No ships yet, sry, kthxbai</td>';
}
echo '</table>';
?>


</body>
</html>
