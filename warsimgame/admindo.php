<?php
///////HEADER//////

//Session Handling & Zipping
session_start();
ob_start('ob_gzhandler');

//Includes
include 'inc/connect.php';
include 'inc/functions.php';
include 'inc/class.php';

//Timer
$clock = new timer;
$clock->start();

//Setup Ships
include 'inc/adminsetupships.php';
include 'inc/assignplanets.php';

/////////CODE BELOW////////////



/////////CODE ABOVE////////////
//Set mode:
$page['mapmode'] = '1,0,0';

//Render the map
include 'adminmap.php';

//Events
$page['minievent'] = 'This is Disabled';

//Stop the timer
$totaltime = $clock->stop();

//Nice layout
include 'templates/temp_game.php';

//Debug
/*
echo 'SYSTEM DEBUG:<br />';
echo $player->debug();
echo 'SQL QUERY COUNT: ' . $sql_count . '<br />';
echo $sql_log;*/
?>