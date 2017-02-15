<?php
//automate.php
//calls other automate scripts...

//Movement script:
include 'auto_movement.php';

//Reload Script:
include 'auto_reload.php';

//User Online thingy:
user_reg();

//1/200 chance of randomly fixing the DB [1 extra SQL CALL]
if(rand(1,200) == 200){
	//Cull Long time dead ships
	$sql = 'DELETE FROM `ws_ships` WHERE hull <= 0 AND endgametime <= ' . (time() - 60*5);
	$query = queryme($sql);
	
	//Cull long time dead events [leave only 30 mins worth]
	$sql = 'DELETE FROM `ws_events` WHERE time <= ' . (time() - 60*30);
	$query = queryme($sql);
	
	//Optomize!
	$sql = 'OPTIMIZE TABLE `ws_events` ,`ws_ships`';
	$query = queryme($sql);
}

?>