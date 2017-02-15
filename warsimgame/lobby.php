<?php
//lobby.php
//Shows users the games available to join
//Allows users to create ships in those games
//Also shows users the ships they already have in the games
//Autoredirect to game.php if $_SESSION['gameid'] is already set...

//Session handling & Page GZipping
session_start();
ob_start('ob_gzhandler');

//Includes:
require_once 'inc/connect.php';
require_once 'inc/class.php';
require_once 'inc/functions.php';

//If not logged in, kick out...
if(!$_SESSION['loggedin']){
	$_SESSION = array();
	
	$error = 'That page has restricted access, you were not logged in, or your session expired.';
	
	$error = urlencode($error);
	
	redirect('index.php?error=' . $error);
}

//If already logged into a game... and a ship... redirect... else delete game session id
if($_SESSION['gameid']){
	//If shipid, then everything is normal, procede as usual
	if($_SESSION['shipid']){
		redirect('game.php');
	} else {
		//Gameid without shipid, on lobby page? Kinda strange, so, we remove the gameid
		$_SESSION['gameid'] = '';
	}
} else {
	if($_SESSION['shipid']){
		//Shipid without gameid? Even stranger, remove it...
		$_SESSION['shipid'] == '';
	} 
}

//Ok, user managment is done... for the moment; lets generate some dynamic game data:

//Make a list of all games available:

//Select all of the players ships:
$sql = 'SELECT shipid,gameid,classname,shipname FROM ws_ships s,ws_shiprules sh WHERE s.classid = sh.classid AND s.hull > 0 AND s.userid = ' . $_SESSION['userid'];
$query = queryme($sql);

//Create some text to allow the player to login to his own ship
while($row =@ mysql_fetch_assoc($query)){
	$gameships[$row['gameid']] .= '&nbsp;&raquo;&nbsp;<a href = "joinship.php?shipid=' . $row['shipid'] . '">' . $row['classname'] . ' <em>' . $row['shipname'] . '</em></a><br />';
}

//Select all games...
$sql = 'SELECT * FROM ws_games g, ws_systems s WHERE g.mapid = s.mapid ORDER BY g.gameid ASC';
$query = queryme($sql);

//Display all games
if(mysql_num_rows($query) > 0){
	//Display all games in a nice table
	while($row =@ mysql_fetch_assoc($query)){
		$page['gamesavailable'] .= '<table width="100%" class="scanresults2"><tr><td width="30%" valign="top" class="lobby1"><div align="center">&nbsp;<img src="' . random_big_ship_img() . '" width="100" height="100" alt="" /></div></td><td width="70%" valign="top" class="lobby1"><strong>Game Name: ' . $row['gamename'] . '</strong><br /><strong>Game Type:</strong> Deathmatch<br /><strong>System:</strong> ' . $row['mapname'] . '<br /><strong>Started by:</strong> ' . $row['start_by'] . '<br />';
		//Game Attributes:
		$page['gamesavailable'] .= '<strong>Game Options:</strong> <small>';
		if($row['option_team']){
			$page['gamesavailable'] .= 'Teams On ';
			
			//Can Create Teams?
			if($row['option_createteam']){
				$page['gamesavailable'] .= ', Players Can Create Teams';
			} else {
				$page['gamesavailable'] .= ', Teams Created By GM';
			}
			
			//Can Join Freelance Team?
			if($row['option_must_join_team']){
				$page['gamesavailable'] .= ', Freelance Option Not Available';
			}
			
		} else {
			$page['gamesavailable'] .= ', Teams Off';
		}
		if($row['option_ions_hit_shields']){
			$page['gamesavailable'] .= ', Ion Weapons Hit Shields';
		} else {
			$page['gamesavailable'] .= ', Ion Weapons Don\'t Hit Shields';
		}
		
		if($row['option_oneshiplimit']){
			$page['gamesavailable'] .= ', One Active Ship Per Player';
		}
		
		//Restricted ships?
		if($row['option_restricted_ships']){
			$page['gamesavailable'] .= ', Some Ships are Not Available';
		}
		
		//Limit Team respawn
		if($row['option_team_limit_respawn']){
			$page['gamesavailable'] .= ', Can Only Respawn In/Near Friendly Capital Ships';
		}
		
		//Distance from enemy respawn...
		if($row['option_limit_respawn_radially']){
			$page['gamesavailable'] .= ', Must Respawn atleast 5 squares away from Death location or aboard a friendly Cap ship';
		}
		
		//Points for Ships?
		if($row['option_point_for_ships']){
			$page['gamesavailable'] .= ', Gain Points by Fighting &amp; Spend Points on Bigger Ships!';
		
			//This is displayed if the player is already in this game...
			$query2 = queryme('SELECT `credits` FROM `ws_p4s_accounts` WHERE userid = ' . $_SESSION['userid'] . ' AND gameid = ' . $row['gameid']);
			if(mysql_num_rows($query2) == 1){
				$creditsarray = mysql_fetch_assoc($query2);				
				$extra = '<br />You have ' . number_format($creditsarray['credits']) . ' Credits in this game';
			} else { 
				$extra = '';
			}
		} else {
			$extra = '';
		}
		
		//Passworded?
		if($row['option_gamepass']){
			$page['gamesavailable'] .= ', This game is Passworded<br /></small><br />';
		} else {
			$page['gamesavailable'] .= '</small><br /><br />';
		}
		
		$page['gamesavailable'] .=  '<a href="joingame.php?game=' . $row['gameid'] . '">Join Game</a>';
		
		if($extra){
			$page['gamesavailable'] .= '<br />' . $extra;
		}
		
		//If we created a 'ship login' link before for the player, and this is the game, display it
		if($gameships[$row['gameid']]){
			$page['gamesavailable'] .= '<br /><br />You are already in this game as:<br /><small>';
			$page['gamesavailable'] .= $gameships[$row['gameid']];
			$page['gamesavailable'] .= '</small>';
		}
		
		//Close off the table row
		$page['gamesavailable'] .= '</td></tr></table>';
	}
	
} else {
	//If no games available, display error
	$page['gamesavailable'] = '<br /><span class="red">There are currently <strong>NO</strong> Open Games for you to join. Sorry.</span>';
}

//Display it
include 'templates/temp_lobby.php';

?>