<?php

//Remote Database Variables
$myhost='localhost';
$myuser='balthouse'; #root
$mypassword=''; #
$mydatabase='balthouse_com'; #warsimnew

//Set page title
$page['title'] = 'Warsim V1.0';

//Connect
$link = @mysql_connect($myhost, $myuser, $mypassword);

if(!$link){
	//Local Database Variables
	$myhost='localhost';
	$myuser='root'; #balthouse
	$mypassword=''; #
	$mydatabase='warsimnew'; #balthouse_com
	
	//Connect
	$link = @mysql_connect($myhost, $myuser, $mypassword);
}

mysql_select_db($mydatabase);

function queryme ($sql)
{
//		»	Queries the Database, returns the result identifier
	global $sql_log;
	global $sql_count;
	
	$sql_count++;	
	$sql_log .= '&nbsp;&raquo;&nbsp;&nbsp;SQL: ' . htmlentities($sql,ENT_QUOTES) . '<br />';
	$query = mysql_query($sql) or die('Error: ' . $sql . ' - ' . mysql_error());
	return $query;
}
?>