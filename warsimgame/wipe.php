<?php
session_start()
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Wipe the Database...</title>

<link href="warsim.css" rel="stylesheet" type="text/css" />
</head>

<body class="body">

<?php

if($_GET['password']){
	if($_GET['password'] == 'omgwtfbbq' AND $_SESSION['userid']){
		echo '<div align="center" class="green">Wipe Successful</div>';
		
		include 'inc/connect.php';
		
		queryme('TRUNCATE TABLE `ws_events`;');
		queryme('TRUNCATE TABLE `ws_ships`;');
		queryme('TRUNCATE TABLE `ws_auto_reload`;');
		queryme('TRUNCATE TABLE `ws_auto_movement`;');
		queryme('TRUNCATE TABLE `ws_teams`;');
		$query = queryme('INSERT INTO `ws_teams` ( `gameid` , `teamname` , `teamleadershipid` , `teamleadertext` , `teampassword` , `teamallowlist` ) VALUES ("0", "Freelance", "0", "No Leader", "", "0");');
		
		$sql = 'SELECT `username` FROM ws_users WHERE userid = ' . $_SESSION['userid'];
		$query = queryme($sql);
		$row = mysql_fetch_assoc($query);
		
		queryme('INSERT INTO `ws_breach_logs` (time,note) VALUES ("' . time() . '","USER ID: ' . $_SESSION['userid'] . ' USERNAME: ' . $row['username'] . ' FROM IP: ' . $_SERVER['REMOTE_ADDR'] . ' NOTE: Initiated wipe of database.");');
		
		echo 'USER ID INITIATING WIPE: ' . $_SESSION['userid'] . '<br />USERNAME: ' . $row['username'] . '<br />IP ADDRESS: ' . $_SERVER['REMOTE_ADDR'] . '<br />LOGGING ON<br /><br />';
		
		echo 'SQL QUERIES SUCCESSFUL: ' . $sql_count . '<br />';
		echo $sql_log . '<br />';
	} else {
		include 'inc/connect.php';
	
		echo '<div align="center" class="red">Wipe Failed</div>';
		if($_SESSION['userid']){
			$sql = 'SELECT `username` FROM `ws_users` WHERE userid = ' . $_SESSION['userid'];
			$query = queryme($sql);
			$row = mysql_fetch_assoc($query);
			
			queryme('INSERT INTO `ws_breach_logs` (time,note) VALUES ("' . time() . '","USER ID: ' . $_SESSION['userid'] . ' USERNAME: ' . $row['username'] . ' FROM IP: ' . $_SERVER['REMOTE_ADDR'] . ' NOTE: Initiated wipe of database, failed due to incorrect password.");');
		}
	}
}

?>

<div align="center">
  <form name="wipeconfirm" id="wipeconfirm" method="get" action="wipe.php">
      Wipe Confirm Password: <input name="password" type="text" id="password" class="form" /><br />
      <input type="submit" name="Submit" value="Submit" class="form" />  
  </form>
</div>
</body>
</html>
