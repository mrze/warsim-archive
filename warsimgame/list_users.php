<?php
//list_users.php
//Lists all users in a game...
session_start();

include 'inc/connect.php';

if($_SESSION['userid'] AND $_SESSION['shipid'] AND $_SESSION['gameid']){
	$text .= 'Users Online In Game: <br /><br />';
	
	$sql = 'SELECT * FROM `ws_usersonline` WHERE lastactive >= ' . (time() - (60*10));
	$query = queryme($sql);
	
	while($row =@ mysql_fetch_assoc($query)){
		$text .= '&nbsp;&raquo;&nbsp;&nbsp;' . $row['username'] . ' aboard the <em>' . $row['shipname'] . '</em><br />';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Users Online</title>
<link href="warsim.css" rel="stylesheet" type="text/css" />
</head>
<body class="background">
<?=$text?>
</body>
</html>
