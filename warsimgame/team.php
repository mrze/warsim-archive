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
include 'inc/setupships.php';
include 'inc/assignplanets.php';

//Automated stuff
include 'inc/automate.php';

/////////CODE BELOW////////////

//Make the overview...
$page['text'] .= '<div align="center"><strong>-Team Options-</strong></div><br />';

//Dead thingy
if($player->get_status() == 'destroyed'){
	$page['text'] .= 'Your ship is destroyed. You cannot access this.';
} else {
	//Disabled thingy...
	if($player->option_team == 0){
		$page['text'] .= 'Teams are disabled in this game. Everybody is a freelancer.';
	} else {
		if(!$_GET['mode']){
		
			
			//Current Team
			$page['text'] .= '<strong>Current Team:</strong> ' . $player->teamname . '<br /><br />';
			$page['text'] .= '<strong>Team Leader:</strong> ' . $player->teamleadertext . '<br /><br />';
			
			//List team members...
			if($player->teamallowlist){
				//Select all but leader...
				$sql = 'SELECT `shipid` FROM `ws_ships` WHERE `team` = ' . $player->team . ' AND `shipid` != ' . $player->teamleadershipid;
				$query = queryme($sql);
				
				//Display it
				$page['text'] .= '<strong>Team Members:</strong><br /><br /><small>';
				$page['text'] .= '&raquo;&nbsp;' . $ships[$player->teamleadershipid]->username . ' aboard the ' . $ships[$player->teamleadershipid]->classname . ' <em>' . $ships[$player->teamleadershipid]->shipname . '</em> [Leader]<br />';
				
				while($row =@ mysql_fetch_assoc($query)){
					$page['text'] .= '&raquo;&nbsp;' . $ships[$row['shipid']]->username . ' aboard the ' . $ships[$row['shipid']]->classname . ' <em>' . $ships[$row['shipid']]->shipname . '</em><br />';
				}
				
				$page['text'] .= '</small><br /><br />';
			}
			
			//Options...
			$page['text'] .= '<strong>Options:</strong><br /><br /><table width="100%"><tr>';
			
			
			//Can Change team...?
			if(!$player->option_no_team_change){
				$page['text'] .= '<td><div align="center">&nbsp;<form style="display: inline"  method="get" action="team.php"><input type="hidden" name="mode" value="changeteam" /><input type="submit" name="text" value="Change Team" class="form" /></form></div></td>';
			}
			
			//If you are the team leader...
			if($player->shipid == $player->teamleadershipid){
				//Kick member
				$page['text'] .= '</tr><tr>';
				$page['text'] .= '<td><div align="center">&nbsp;<form style="display: inline"  method="get" action="team.php"><input type="hidden" name="mode" value="kick" /><input type="submit" name="text" value="Kick Member" class="form" /></form></div></td>';
				$page['text'] .= '</tr><tr>';
				
				//Team Details
				$page['text'] .= '<td><div align="center">&nbsp;<form style="display: inline"  method="get" action="team.php"><input type="hidden" name="mode" value="editdetails" /><input type="submit" name="text" value="Edit Details" class="form" /></form></div></td>';
			}
			
			//If Freelance, allow startage of new teamages...
			if($player->team == 1 AND $player->option_createteam == 1){
				$page['text'] .= '<td><div align="center">&nbsp;<form style="display: inline"  method="get" action="team.php"><input type="hidden" name="mode" value="startteam" /><input type="submit" name="text" value="Start New Team" class="form" /></form></div></td>';
			}
			
			//Close table...
			$page['text'] .= '</tr></table>';
		
		} else { //End No mode...
			//Now there ARE MODES! MODES REIGN SUPREME!
			if($_GET['mode'] == 'changeteam'){
				//If Option is disabled...
				if(!$player->option_no_team_change){
					//If leader... cant leave... must appoint new leader...
					if($player->shipid == $player->teamleadershipid){
						$page['text'] .= 'You are currently the leader of a team. You cannot leave this position until you appoint another leader [Under Team Options], or you disband the team [Under Team Options].';
					} else {
						//We want to let the user change his team... 
						//It will display all the teams...
						//Give the user a nice textbox to write a reason... 
						//Submit it, and add it to the leaders events... with a link to click to accept/decline...
						$page['text'] .= '<strong>Current Team:</strong> ' . $player->teamname . '<br /><br />';
						$page['text'] .= '<form style="display: inline"  method="get" action="team.php"><input type="hidden" name="mode" value="changeteampart2" /><strong>New Team Choices:</strong> ';
				
						//Display a list of teams...
						$sql = 'SELECT * FROM `ws_teams` WHERE teamid != ' . $player->team . ' AND (gameid = ' . $_SESSION['gameid'] . ' OR gameid = 0)';
						$query = queryme($sql);
						
						if(mysql_num_rows($query) == 0){
							$page['text'] .= 'There are no teams in this game yet.';
						
						} else {
							//Now, put them into a nice select box thingy...
							$page['text'] .= '<div align="center"><select name="newteam">';
							while($row =@ mysql_fetch_assoc($query)){
								$page['text'] .= '<option value="' . $row['teamid'] . '">' . $row['teamname'];
								
								if($row['teamleadershipid'] > 0){
									$page['text'] .= ' lead by ' . $row['teamleadertext'];
								}
								
								$page['text'] .= '</option>';
							}
							$page['text'] .= '</select></div><br />';
							
							$page['text'] .= '<strong>Team password:</strong><div align="center"><input type="text" name="teampass" onmouseover="return overlib(\'`Freelance` Does _NOT_ need a password. <br /><br />Team passwords are how people join. If you wish to join the team, ask the leader for the password...\', CAPTION, \'Team Password\',WIDTH,225);" onmouseout="return nd();" />
							<br /><br /><input name="submit" type="submit" value="Join Team" class="form" /></div>';
							$page['text'] .= '<br /><em><small>Please Note: Upon initiating team change, if you are accepted [IE: get the password right] you will leave your previous team, everybody within the previous team will get an event saying you `defected` and you will become a Member of the new team.</small></em>';
						}
					}
				} else {
					$page['text'] .= 'You cannot create teams in this game.';
				}
			}
		
			//And that leads onto...
			///team.php?mode=changeteampart2&newteam=3&msgtext=Message+Here...&submit=Send+Request
			if($_GET['mode'] == 'changeteampart2'){
				if($player->shipid == $player->teamleadershipid){
					$page['text'] .= 'You are currently the leader of a team. You cannot leave this position until you appoint another leader [Under Team Options], or you disband the team [Under Team Options].';
				} else {
					//Validate team exists...
					if(is_numeric($_GET['newteam'])){
						if($_GET['newteam'] == $player->team){
							$page['text'] .= 'You are already a member of that team.';
						} else {
					
							//Check it exists...
							$sql = 'SELECT * FROM ws_teams WHERE (gameid=' . $_SESSION['gameid'] . ' OR gameid=0) AND teamid = ' . $_GET['newteam'];
							$query = queryme($sql);
							
							if(mysql_num_rows($query) == 1){
								$row = mysql_fetch_assoc($query);
								if($_GET['teampass'] == $row['teampassword'] OR $_GET['newteam'] == 1){
									$page['text'] .= 'You are now a member of the team: ' . $row['teamname'] . '<br /><br />The map will update on the <a href="game.php">next page</a>.';
									if($player->team != 1){
										$player->give_team_event('That scumbag ' . $player->username . ' aboard the ' . $player->classname . ' <em>' . $player->shipname . '</em> has defected from our team! Kiiiill!');
									}
									
									$player->team = $_GET['newteam'];
									if($player->team != 1){
										$player->give_team_event($player->username . ' aboard the ' . $player->classname . ' <em>' . $player->shipname . '</em> has joined our team...');
									} else {
										$player->give_event('You have changed teams, your team is now ' . $row['teamname']);
									}
									
									$sql = 'UPDATE `ws_ships` SET team = ' . $_GET['newteam'] . ' WHERE shipid = ' . $player->shipid;
									$query = queryme($sql);
								} else {
									$page['text'] .= 'Incorrect password, your team has not changed.';
								}
							} else {
								$page['text'] .= 'That Team does not exist.';
							}
						}
					} else {
						$page['text'] .= 'That Team does not exist.';
					}
		
				}
			}
			
			//Edit details... like... 
			//http://localhost/warsimgame/team.php?mode=editdetails&text=Edit+Details
			//Name... leader... description... accept type...
			if($_GET['mode'] == 'editdetails'){
				if($player->shipid == $player->teamleadershipid){
					$page['text'] .= 'This page allows you to edit certain aspects of your team, such as its name, details and member accept method.<br /><br /><form method="get" action="team.php">';
					$page['text'] .= 'Team Name: <br /><div align="center"><input type="text" name="teamname" value="' . $player->teamname . '" /></div><br />';
								
					//Leader select 
					$page['text'] .= '<br />Leader: <br /><div align="center"><select name="leader">';
						//loop thru ppl...
						$sql = 'SELECT `shipid` FROM `ws_ships` WHERE `team` = ' . $player->team . ' AND `shipid` != ' . $player->teamleadershipid;
						$query = queryme($sql);
						
						//Display it
						$page['text'] .= '<option value="' . $ships[$player->teamleadershipid]->shipid . '" selected>' . $ships[$player->teamleadershipid]->username . ' aboard ' . $ships[$player->teamleadershipid]->shipname . '</option>';
						
						while($row =@ mysql_fetch_assoc($query)){
							$page['text'] .= '<option value="' . $ships[$row['shipid']]->shipid . '">' . $ships[$row['shipid']]->username . ' aboard ' . $ships[$row['shipid']]->shipname . '</option>';
						}
						
					$page['text'] .= '</select></div><br />Team Password:<div align="center"><input type="text" name="teampassword" value="' . $player->teampassword . '" onmouseover="return overlib(\'Team passwords are how people join. To join a team, you must know the name and the password... Tell any prospective team members the password and they can join.\', CAPTION, \'Team Password\',WIDTH,225);" onmouseout="return nd();" /></div><br /><div align="center"><input type="hidden" name="mode" value="editdetailspart2"><input name="submit" type="submit" value="Update Team Details" class="form" /></div><br /><small><em>Please Note: If you change the name of your team or disband the team, everybody in this game will get an event notifying them of the change. Also, if you kick a member, members of your team will be notified of the change.</em></small><br /><br /><div align="center"><input type="submit" name="disband" value="Disband Team!" class="form"></div></form>';
				} else {
					$page['text'] .= 'You\'re not meant to be here...';
				}
			}
			
			//Goes to...
			if($_GET['mode'] == 'editdetailspart2'){
				if($player->shipid == $player->teamleadershipid){
					$sql = '';
					if(!$_GET['disband']){
						if($player->teamname != stripslashes($_GET['teamname'])){
							$player->give_game_event('The team formerly known as ' . $player->teamname . ' has changed its name to ' . $_GET['teamname'] . '.');
							
							$page['text'] .= 'Changing Team Name...<br /><span class="green">New Name: ' . stripslashes($_GET['teamname']) . '</span><br />';
							$sql = ', teamname = "' . $_GET['teamname'] . '"';				
						}
						
						if($_GET['leader'] != $player->teamleadershipid){
							if($ships[$_GET['leader']]){
								if($ships[$_GET['leader']]->team == $player->team){
									$page['text'] .= 'Changing leader... New leader is <span class="green">' . $ships[$_GET['leader']]->username . '</span>';
									$sql .= ', teamleadertext = "' . $ships[$_GET['leader']]->username . '", teamleadershipid= "' . $_GET['leader'] . '"';
								
									$player->give_game_event('Viva La Revolution! ' . $ships[$_GET['leader']]->username . ' is the new leader of ' . $player->teamname);
								}
							}
						}
						
						$sql = 'UPDATE `ws_teams` SET teamid=teamid ' . $sql . ' WHERE teamid = ' . $player->teamid;
						queryme($sql);
						
						$page['text'] .= '<br /><br />If you are seeing this, then everything is <span class="green">A-OK!</span>';
					} else {
						//Disbanding the great team...
						$page['text'] .= 'Team: ' . $player->teamname . ' disbanded successfully :\'(<br /><br />Former Members of ' . $player->teamname . ' are now Freelancers.<br /><br />The map will update on the <a href="game.php">next page</a>';
						
						$oldteam = $player->team;
						
						$sql = 'SELECT `shipid` FROM `ws_ships` WHERE `team` = ' . $player->team;
						$query = queryme($sql);
						
						//Updated...
						while($row =@ mysql_fetch_assoc($query)){
							$ships[$row['shipid']]->team = 1;
						}
						
						$sql = 'UPDATE `ws_ships` SET `team`=1 WHERE `team` = ' . $oldteam;
						queryme($sql);
						
						$sql = 'DELETE FROM `ws_teams` WHERE teamid = ' . $oldteam;
						queryme($sql);
						
						$player->give_game_event('The Team ' . $player->teamname . ' lead by ' . $player->username . ' has been disbanded... They were a pushover anyway!');
					}
				} else {
					$page['text'] .= 'You\'re not meant to be here...';
				}
			}
			
			//Start up a new team!!! OMGOMGOMGOMG!
			if($_GET['mode'] == 'startteam'){
				if($player->team == 1){
					//Slap them up a bit...
					$page['text'] .= 'DISCLAIMER: Teams use up alot of CPU power to create and manage. Only create a team if you actually have team members willing to fight with you. Do not create a team for the sake of creating a team named after yourself. Admins have the power to delete teams and temporarly ban people who decide it is fun to make a team of one.<br /><br />';
				
					//Ok, show us the form...
					$page['text'] .= '<form method="get" action="team.php"><input type="hidden" name="mode" value="startteampart2" />Team Name: <br /><div align="center"><input type="text" name="teamname"  onmouseover="return overlib(\'The team name is Case Sensitive, it is no longer all caps...\', CAPTION, \'Team Name\',WIDTH,225);" onmouseout="return nd();" /></div><br />Team Join Password:<div align="center"><input type="text" name="teampass"  onmouseover="return overlib(\'This password is stored as plain text, so do not make it the same as your user account password. For reliability, it should only contain alpha-numeric characters [no punctuation]. It is case sensitive.\', CAPTION, \'Team Password\',WIDTH,225);" onmouseout="return nd();" /></div><br /><div align="center"><input type="submit" name="createteam" value="Create Team" class="form" /></div></form>';
				} else {
					$page['text'] .= 'You\'re not meant to be here... Only Freelancers can start new teams...';
				}
			}
			
			///Leads to...
			
			if($_GET['mode'] == 'startteampart2'){
				if($player->team == 1){
					if(!$_GET['teamname'] OR !$_GET['teampass']){
						$page['text'] .= 'You missed one of the fields, all fields must be filled in completely.';
					} else {
						if(strtolower(trim($_GET['teamname'])) == 'admin' OR strtolower(trim($_GET['teamname'])) == 'freelance' OR strtolower(trim($_GET['teamname'])) == 'freelancer'){
							$page['text'] .= 'That teamname is reserved, you cannot call your team that.';
						} else {
							$_GET['teamname'] = stripslashes($_GET['teamname']);
							$_GET['teamname'] = htmlentities($_GET['teamname'],ENT_QUOTES);
							$_GET['teamname'] = nl2br($_GET['teamname']);
											
							//Check for no dups
							$sql = 'SELECT `teamid` FROM `ws_teams` WHERE `teamname`= "' . $_GET['teamname'] . '"';
							$query = queryme($sql);
							
							if(mysql_num_rows($query) == 0){
							
								//Add new team...
								$sql = 'INSERT INTO `ws_teams` ( gameid,teamname,teamleadershipid,teamleadertext,teampassword,teamallowlist) VALUES ("' . $_SESSION['gameid'] . '","' . $_GET['teamname'] . '","' . $player->shipid . '","' . $player->username . '","' . $_GET['teampass'] . '",1)';
								queryme($sql);
								
								//Get team id...
								$sql = 'SELECT `teamid` FROM `ws_teams` WHERE teamname = "' . $_GET['teamname'] . '"';
								$query = queryme($sql);
								
								$teamid = mysql_fetch_assoc($query);
								
								//Update player to be supreme leader...
								$sql = 'UPDATE `ws_ships` SET `team` = ' . $teamid['teamid'] . ' WHERE shipid = ' . $player->shipid;
								queryme($sql);
								
								$page['text'] .= 'Team Created Successfully. You can now invite some members [Make sure to tell them the join password].';
							
								$player->give_game_event('A new team has emerged... `' . $_GET['teamname'] . '`... It is reported that the leader is the mysterious ' . $player->username . '...');
							} else {
								$page['text'] .= 'A team already exists with that name.';
							}
						}
					}
				} else {
					$page['text'] .= 'You\'re not meant to be here... Only Freelancers can start new teams...';
				}
			}
			
			
			//Kick a member... Yessss.... all powerful kick...
			if($_GET['mode'] == 'kick'){
				if($player->shipid == $player->teamleadershipid){
					if($_GET['shipid']){
						//kick that guy...
						if($ships[$_GET['shipid']]){
							if($ships[$_GET['shipid']]->team == $player->team){
								//Local
								$ships[$_GET['shipid']]->team = 1;
								
								//Db
								$sql = 'UPDATE `ws_ships` SET team=1 WHERE shipid = ' . $_GET['shipid'];
								queryme($sql);
								
								$page['text'] .= '<span class="green">' . $ships[$_GET['shipid']]->username . ' was kicked from your team.</span><br /><br />';
							}
						}
					}
					//Info...
					$page['text'] .= 'As supreme leader of this team, you can kick any member from it. Anybody kicked will become a freelancer. Everybody in the team will get an event, as will the person who was kicked.<br /><br /><strong>Kick Member:</strong><br /><br />';
					
					//display list..
					$sql = 'SELECT `shipid` FROM `ws_ships` WHERE `team` = ' . $player->team . ' AND shipid != ' . $player->shipid;
					$query = queryme($sql);
					
					if(mysql_num_rows($query) > 0){
						$page['text'] .= '<small>';
					
						while($row =@ mysql_fetch_assoc($query)){
							$page['text'] .= '&nbsp;&raquo;&nbsp;' . $ships[$row['shipid']]->username . ' aboard the <em>' . $ships[$row['shipid']]->shipname . '</em> [<a href="team.php?mode=kick&shipid=' . $row['shipid'] . '">Kick?</a>]<br />';
						}
						
						$page['text'] .= '</small>';
						
					} else {
						$page['text'] .= 'There is nobody else to kick... and you can\'t kick yourself.';
					}
				} else {
					$page['text'] .= 'You\'re not meant to be here...';
				}
			} 
		}
	}
}

/////////CODE ABOVE////////////
//Set mode:
$page['mapmode'] = '1,0,0';

//Render the map
include 'map.php';

//Events
$page['minievent'] = get_events();

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