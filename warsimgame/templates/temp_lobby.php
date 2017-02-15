<?php echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>-Warsim-</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="warsim.css" rel="stylesheet" type="text/css" />
</head>

<body class="background">
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
			<center><b>Open Games:</b></center>
			<?=$page['gamesavailable'] ?>
			</td>
		    <td width="1%">&nbsp;</td>
            <td align="center" valign="top" class="dispkey"><?php if($_GET['error']){ echo '<span class="red">Error: ' . $_GET['error'] . '</span>'; }?>&nbsp;</td>
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
