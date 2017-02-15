<?php echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>-Warsim-</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="warsim.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="overlib_mini.js" ></script><!-- overLIB (c) Erik Bosrup -->
<script type="text/javascript" src="mapmaker.js"></script>
<script>
<!--	
var map_mode = "place_ship";

function load_functions(){
	//Map Mode:

	
	//Map:
	make_map();
	
	//Other stuff:
	<?php echo $page['map'] ?>
		
	//Finally, update teh map
	update_map();
}
function mousehasclicked(x,y){
	document.getElementById('x').value = x;
	document.getElementById('y').value = y;
}
function loadshipstats(name,img,classname,squad,hull,shield,ionic,crew,marines,at,weapons,tractorbeam,cost,speed)
{
	document.getElementById('shipimg').src = 'images/ships/' + img;

	document.getElementById('speed').innerHTML = speed;
	document.getElementById('shiptype').innerHTML = name;
	document.getElementById('shipclass').innerHTML = classname;
	document.getElementById('squadsize').innerHTML = squad;
	document.getElementById('hull').innerHTML = hull;
	document.getElementById('shield').innerHTML = shield;
	document.getElementById('ionic').innerHTML = ionic;
	document.getElementById('crew').innerHTML = crew;
	document.getElementById('marines').innerHTML = marines;
	document.getElementById('assaulttransport').innerHTML = at;
	document.getElementById('weapons').innerHTML = weapons;
	document.getElementById('tractor').innerHTML = tractorbeam;
	document.getElementById('cost').innerHTML = cost;
}
//-->
</script>
</head>

<body class="background" onload="load_functions();">
<div id="overDiv" style="position: absolute; visibility: hidden; z-index: 1000;"></div>
<div align="center" class="body"> 
<table width="735" class="layouttable">
  <tr>
    <td><div align="center"><strong>--Warsim--</strong></div></td>
  </tr>
</table>
<br />
<!-- Content Table -->
<table width="735" class="layouttable">
  <tr>
    <td>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
          <!--DWLayoutTable-->
          <tr> 
            <td width="100%" valign="top" class="dispkey">
			<div align="center"><a href="lobby.php">Lobby</a> | <a href="browsemaps.php">Browse Maps</a> | <a href="help.php">Rules</a> | <a href="logout.php">Logout</a></div>
			</td>
          </tr>
        </table>
		<br />
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		  <tr>
		    <td width="1">&nbsp;</td>
			<td width="53%" align="left" valign="top" class="dispkey">
			<center><strong>-Create Ship-</strong></center>
			<?php echo $page['p4s'] ?>
			<form name="form1" id="form1" method="get" action="">
			  <table width="100%"  class="norfont">
                <tr>
                  <td width="40%">Select Ship: </td>
                  <td width="60%"><select name="shipid" id="shipid">
                    <?php echo $page['forminput']; ?>
                  </select></td>
                </tr>
                <tr>
                  <td>Ship Name:</td>
                  <td><input name="shipname" type="text" id="shipname" /></td>
                </tr>
				<?php echo $page['p4scost'] ?>
                <?
				if($game['option_team'] == 1){
				?>
				<tr>
                  <td>Team:</td>
                  <td><select name="team" onmouseover="return overlib('Select which team you wish to join, if you choose a team, place the team password in the box below. If you get the password wrong, you will automatically be set as a freelancer. Team `Freelance` has no password.<br /><br />Teams can be created in game, and you can defect in game... although it isn`t recomended that you defect too often...', CAPTION, 'Team',WIDTH,225);" onmouseout="return nd();"><?php echo $teamstuff; ?></select></td>
                </tr>
				<tr>
                  <td>Team Password:</td>
                  <td><input name="teampass" type="password" id="teampass" value=""  onmouseover="return overlib('This box only applies if you preselect a team other than `Freelance`. If you choose a different team, then the password assigned by the creator will go in this box (You will have to ask what the password is over IRC/ICQ...) ', CAPTION, 'Team Password',WIDTH,225);" onmouseout="return nd();" /></td>
                </tr>
				<? 
				}
				?>
				
                <tr>
                  <td>X Co-ord:</td>
                  <td><select name="x" id="x">
                    <option value="0" selected="selected">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                  </select></td>
                </tr>
                <tr>
                  <td>Y Co-ord:</td>
                  <td><select name="y" id="y">
                    <option value="0" selected="selected">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15" onclick="stuff();" onchange="stuff();">15</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                  </select></td>
                </tr>
				<?
				if($game['option_gamepass']){
				?>
				<tr>
					<td>Game Password: </td>
					<td><input name="gamepass" type="password" id="teampass" value=""  onmouseover="return overlib('This game is a private game and is passworded. Do not attempt to enter it if you are not invited by the game creator. ', CAPTION, 'Game Password',WIDTH,225);" onmouseout="return nd();" />
				</tr>
				<?
				}
				?>
                <tr>
                  <td colspan="2"><div align="center">
                    <input name="game" type="hidden" id="game" value="<?php echo $_GET['game'] ?>" />
                    <input name="mode" type="hidden" id="mode" value="createship" />
                    <input type="submit" name="Submit" value="Submit" />
                  </div></td>
                </tr>
              </table>
			</form>
			<br />
			<center><strong>-Ships Stats-</strong><br /></center>
			<br />
				<table width="100%">
				<tr valign="top">
						<td>
							<small>
								<strong>Type:</strong> <span id="shiptype">None Selected</span><br />
								<strong>Class:</strong> <span id="shipclass">None Selected</span><br />
								<strong>Squad Size:</strong> <span id="squadsize">N/A</span><br />
								<strong>Speed:</strong> <span id="speed">N/A</span><br />
								<strong>Cost: </strong> <span id="cost">N/A</span><br /><br />							
								<strong>Hull:</strong> <span id="hull">N/A</span><br />
								<strong>Shield:</strong> <span id="shield">N/A</span><br />
								<strong>Ionic:</strong> <span id="ionic">N/A</span><br /><br />
								<strong>Crew:</strong> <span id="crew">N/A</span><br />
								<strong>Marines:</strong> <span id="marines">N/A</span><br />
								<strong>Assault Transport:</strong> <span id="assaulttransport">N/A</span><br /><br />
								<strong>Weapons:</strong> <span id="weapons">N/A</span><br />
								<strong>Tractor Beams:</strong> <span id="tractor">N/A</span>
							</small>
						</td>
						<td width="1%">
							<img src="images/ships/Acclamator.gif" id="shipimg" width="100" height="100" />
						</td>
					</tr>
				</table>
					
			</td>
		    <td width="1%">&nbsp;</td>
            <td align="center" valign="top" class="dispkey">
			<div id="container" class="bg" style="position: relative;"></div>
			<?php 
			if($_GET['error']){
				echo '<span class="red">Error: ' . $_GET['error'] . '</span>';
			}
			?></td>
		    <td width="1">&nbsp;</td>
		  </tr>
		</table>
    </td>
  </tr>
</table>
</div>
<!-- Start of StatCounter Code -->
<script type="text/javascript" language="javascript">
var sc_project=726797; 
var sc_partition=6; 
var sc_security="3b46b693"; 
var sc_invisible=1; 
</script>

<script type="text/javascript" language="javascript" src="http://www.statcounter.com/counter/counter.js"></script><noscript><a href="http://www.statcounter.com/" target="_blank"><img  src="http://c7.statcounter.com/counter.php?sc_project=726797&amp;java=0&amp;security=3b46b693&amp;invisible=1" alt="counter" border="0"></a> </noscript>
<!-- End of StatCounter Code -->
</body>
</html>
