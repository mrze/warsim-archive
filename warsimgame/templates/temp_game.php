<center></center>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>--<?= $page['title']; ?>--</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="javascript">
<!--
function change_map_mode(combat,travel,sensors){
	if(combat){
		map_mode = 'weapons';
		document.getElementById('img1').src = 'images/gui/combat_on.png';
		document.getElementById('img2').src = 'images/gui/travel_off.png';
		document.getElementById('img3').src = 'images/gui/scanners_off.png';
	}
	if(travel){
		map_mode = 'travel';
		document.getElementById('img1').src = 'images/gui/combat_off.png';
		document.getElementById('img2').src = 'images/gui/travel_on.png';
		document.getElementById('img3').src = 'images/gui/scanners_off.png';
	}
	if(sensors){
		map_mode = 'scanner';
		document.getElementById('img1').src = 'images/gui/combat_off.png';
		document.getElementById('img2').src = 'images/gui/travel_off.png';
		document.getElementById('img3').src = 'images/gui/scanners_on.png';
	}
}

var popUpWin=0;
function popUpWindow(URLStr, left, top, width, height)
{
  if(popUpWin)
  {
    if(!popUpWin.closed) popUpWin.close();
  }
  popUpWin = open(URLStr, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menub ar=no,scrollbar=no,resizable=no,copyhistory=yes,width='+width+',height='+height+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
}

//-->
</script>
<script type="text/javascript" src="overlib_mini.js" ></script><!-- overLIB (c) Erik Bosrup -->
<script type="text/javascript" src="mapmaker.js"></script>
<link href="warsim.css" rel="stylesheet" type="text/css" />
<?= $page['map']; ?>
</head>

<body class="background" onLoad="load_functions();">
<div id="overDiv" style="position: absolute; visibility: hidden; z-index: 1000;"></div>
<div align="center" class="body"> 
<!-- Content Table -->
  <table width="735" class="layouttable">
    <!--DWLayoutTable-->
    <tr> 
      <td width="100%" height="20" valign="top">
	  
	  <!-- Menu Table -->
	  <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <!--DWLayoutTable-->
          <tr> 
            <td width="100%" valign="top" class="dispkey">
			<div align="center"><a href="game.php">Overview</a> | <a href="travel.php">Travel</a> 
                | <a href="dock.php">Docking</a> | <a href="scanner.php">Scanner</a> | <a href="weapons.php">Weapons</a> 
                | <a href="team.php">Teams</a> | <a href="events.php">Events</a> | <a href="goto-lobby.php">Lobby</a>/<a href="logout.php">Logout</a></div>
			</td>
          </tr>
        </table>
	    <br />
	    <!-- Content Table 1 -->
		<!-- Map/Summary/Key -->
	  	  
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
          <!--DWLayoutTable-->
          <tr> 
            <td width="400" valign="top"><table width="400" height="560">
			<tr><td class="dispmap">
<div id="container" class="bg" style="position: relative;"></div><table cellpadding="0" cellspacing="0" width="100%"><td><div align="center"><img id="img1" src="images/gui/combat_on.png" onclick="change_map_mode(1,0,0);"  onmouseover="return overlib('Switch to Combat Mode', CAPTION, 'Combat QuickSwitch');" onmouseout="return nd();" /></div></td><td><div align="center"><img id="img2" src="images/gui/travel_off.png" onclick="change_map_mode(0,1,0);" onmouseover="return overlib('Switch to Travel Mode', CAPTION, 'Travel QuickSwitch');" onmouseout="return nd();" /></div></td><td><div align="center"><img id="img3" src="images/gui/scanners_off.png"  onclick="change_map_mode(0,0,1);" onmouseover="return overlib('Switch to Scanner Mode', CAPTION, 'Scanner QuickSwitch');" onmouseout="return nd();" /></div></td></table></td></tr>
			<tr><td class="dispkey"><?=$page['minievent']?></td></tr></table></td>
			<td width="2%">&nbsp;</td>
            <td valign="top" class="dispkey"><div class="dispkeyscn"><?= $page['text']; ?></div></td>
          </tr>
        </table>
    </table>
Welcome to <?= $page['title']; ?> | Page Gen Time: <? echo $totaltime; ?> 
  | SQL Count: <?= $sql_count; ?> | Current Time: <?= date("F j, g:i a") ?><br />
  Game Concept from <a href="http://www.swcombine.com/">SWC</a> 
  | Images: <a href="http://www.swcombine.com/">SWC</a> &amp; 
  <a href="http://enigma.xrz.nl/">Xearz</a> &amp; Andrew Vellen &amp; Sogekihei | Users Online In Game: <a href="#" onclick="popUpWindow('list_users.php', 50, 50, 300, 300)" onmouseover="return overlib('Click to See List of people online in this game<br />[Opens in a new Window]', CAPTION, 'Users Online',ABOVE);" onmouseout="return nd();" ><?=$total_online; ?></a>
  <?php if($_GET['debug'] == 1){ 
  	echo '<div align="left">Count: ', $sql_count ,'<br />', $sql_log,'<br />Player Stats: <pre>', $player->debug(),'</pre>Ships Loaded: ' , count($ships) , '<br />Session ID: ' , session_id() , '<br />User ID: ' , $_SESSION['userid'] , '<br />Ship ID: ' , $_SESSION['shipid'] , '<br />Game ID: ' , $_SESSION['gameid'] , '</div>'; 
  	queryme('INSERT INTO `breach_logs` (time,note) VALUES (' . time() . ',"Debug Mode Used by ' . $player->username . '.")'); 
  } ?>
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

