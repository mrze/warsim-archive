<?php echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>-Warsim-</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="warsim.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="overlib_mini.js" ></script><!-- overLIB (c) Erik Bosrup -->
</head>
<body class="background">
<div align="center" class="body"> 
<!-- Header Table -->
<div id="overDiv" style="position: absolute; visibility: hidden; z-index: 1000;"></div>
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
			<div align="center"><a href="index.php">Index</a> | <a href="help.php" id="navlink">Rules</a> 
           </div>
			</td>
          </tr>
        </table>
		<br />
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		  <tr>
		    <td width="3%">&nbsp;</td>
          <td width="46%" align="left" valign="top" class="dispkey"><strong>Create a new Account:</strong><br />
            <br />
			<form name="createaccount" action="index.php" method="post">
			<div align="center">
            <table width="90%" border="0">
              <tr>
                <td>Handle:</td>
                <td><input type="text" name="handle" class="form"></td>
				<td width="5%" onmouseover="return overlib('This is your SWC handle, it will be used to identify you within the Game. Please make sure it is a Valid Handle.', CAPTION, 'Handle',WIDTH,225);" onmouseout="return nd();"><b>?</b></td>
              </tr>
              <tr>
                <td>Password:</td>
                <td><input type="password" name="password" class="form"></td>
				<td onmouseover="return overlib('This is the the password that you want for this website.It is encrypted and stored in the Database. Nobody has access to it.', CAPTION, 'Password',WIDTH,225);" onmouseout="return nd();"><b>?</b></td>
              </tr>
              <tr>
                <td>Email:</td>
                <td><input type="text" name="email" class="form"></td>
				<td onmouseover="return overlib('This is your Email Address, It is used if you forget your password and need a new one emailed to you.', CAPTION, 'Email',WIDTH,225);" onmouseout="return nd();"><b>?</b></td>
              </tr>
			  <tr>
                <td colspan="2"><div align="center">
                      <input name="Submit" type="submit" id="Submit" value="Submit" class="form">
                      <input name="do" type="hidden" value="createaccount" />
                    </div></td>
              </tr>
            </table> 
			</div>
			</form>
            <?php echo $page['tableone']; ?>
          </td>
		    <td width="2%">&nbsp;</td>
          <td width="46%" align="left" valign="top" class="dispkey"><strong>Login 
            to your Account:</strong><br />
            <br />
			<form name="loginaccount" action="index.php" method="post">
			<div align="center">
			<table width="90%" border="0">
              <tr>
                <td>Handle:</td>
                <td><input type="text" name="handle" class="form"></td>
				</tr>
			  <tr>
                <td>Password:</td>
                <td><input type="password" name="password" class="form"></td>
				</tr>
			  <tr>
                <td colspan="2"><div align="center">
                      <input name="Submit" type="submit" id="Submit" value="Submit" class="form">
                      <input name="do" type="hidden" value="login" />
                    </div></td>
              </tr>
            </table>
			</div>
			</form>
            <?php echo $page['tabletwo']; ?>
          </td>
		    <td width="3%">&nbsp;</td>
		  </tr>
		</table>
    </td>
  </tr>
</table>
<br />
<table width="735" class="layouttable">
<td>
<strong>Whats new?</strong><br />
&raquo; <strong>!!!!!!!IN AN EFFORT TO REDUCE SPAWN KILLS [When you continually spawn ontop of people] THE FOLLOWING HAS BEEN ADDED!!!!!! </strong><br />
<em>&raquo; You will not be allowed to spawn within 4 squares radially from your last death location! However, you will be able to spawn within a friendly cap ship, if it is within your `No Spawn` Location, and has space in its docking bay. [Can't see them tho - Will be coded soon]<br />
&raquo; If you have waited 10 mins from your last death, the square limit does not apply. [I don't recommend waiting 10 mins, this is just to stop people from being locked out from playing the night before]<br />
&raquo; The Join page now has a `Red Scanner Shroud` showing places where you can`t spawn. This is only available in <a href="http://getfirefox.com">Firefox</a>/Opera. Not in IE. In IE, hover over images for the alt text to see...<br /><br />
&raquo; <em>Capturing Ships</em> is nearing completion...<br />
&raquo; <strong>You Should NOT be recieving popups or ads from within Warsim</strong> - If you see any specifically coming from the Warsim page, please email the creator asap. [Some of the stats code might be showing ads...]<br />
&raquo; <strong>Internet Explorer is no longer supported, differences in Javascript and HTML Processing as well as Cookie handling means Warsim will _ONLY_ work properly in browsers like <a href="http://getfirefox.com/">Firefox</a> or Opera. Use IE at your OWN risk, as unexpected things may occour on the `Game` and `Join Game` pages.</strong><br />
&raquo; Images are being moved off server to reduce data transfer and to increase speeds and stability. Sometime tonight, you may recieve missing images, 404`s, and the map may not load properly. Please be patient while this is happening. Thankyou. <br />
&raquo; Check out the <a href="shiplist.php">Ship Stats</a> and <a href="http://my.statcounter.com/project/standard/stats.php?project_id=726797&guest=1">User/Website Hits Stats</a> [Has a very dodgy Hitcounter] for Warsim. <br />
</table>
<br />
<table width="735" class="layouttable">
<td class="dispkey"><strong>Note:</strong> It is recomended you use Firefox or Opera to play this game. Some of the games functionality will be removed if you use IE (Scanner and Weapons overlay). IE has several stability issues with this game, due to its heavy reliance on Javascript. Quick! <a href="http://www.getfirefox.com/">Download Firefox</a>.<br />
  <br />
  <strong>Note:</strong> It is also recomended that you join #warsim while playing the game, this is to ensure you know when any outages may occour, or for new features/suggestions - as well as allowing you to gloat over your Dead Enemies!</td>
</table>
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
