<?php
//Session Handling
session_start();

//GZipping
ob_start('ob_gzhandler');

include 'inc/connect.php';

?>
<center></center>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>--<?= $page['title']; ?>--</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="warsim.css" rel="stylesheet" type="text/css" />
</head>

<body class="background" onLoad="">
<div align="center" class="body"> 
<!-- Header Table -->
<table width="735" class="layouttable">
  <tr>
    <td><div align="center"><strong>War Simulator Title </strong></div></td>
  </tr>
</table>
<br />
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
			<div align="center"><?php
			if($_SESSION['shipid']){
				echo '<a href="game.php">Overview</a> | <a href="travel.php">Travel</a> 
                | <a href="scanner.php">Scanner</a> | <a href="weapons.php">Weapons</a> 
                | <a href="events.php">Events</a> | <a href="help.php">Help</a> | <a href="lobby.php">Lobby</a>/<a href="logout.php">Logout</a>';
			} else {
				if($_SESSION['username']){
					echo ' <a href="lobby.php">Lobby</a>| <a href="help.php">Rules </a> | <a href="logout.php">Logout</a>';
				} else {
					echo ' <a href="index.php">Index</a>| <a href="help.php">Rules </a>';
				}
			}
			?>
			</div>
			</td>
          </tr>
        </table>
	    <br />
	    <!-- Content Table 1 -->
		<!-- Map/Summary/Key -->
	  	  
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dispkey">
          <!--DWLayoutTable-->
          <tr> 
            <td> <div align="center"><strong>Warsim Help</strong></div>
              <br />
              Welcome to Warsim, an online combat simulator based around rules 
              from the Star Wars Combine Universe. This file will help explain 
              how to get started in Warsim, including creating an account, the 
              rules involved, and how to use Warsim. If you wish to learn about 
              how to play Warsim, you can go straight to the <a href="#playing">How 
              to Play</a> Section.<br /> <br />		
			<div align="center"><strong>Setting Up Your Account and Logging 
                In</strong></div>
              <br /> 
              <strong><em>Creating An Account</em></strong><br /> 
              <br />
              To play Warsim, you first need an account. Accounts must be linked 
              to a valid Star Wars Combine handle. This is to ensure that everyone 
              only has one account and the game is fair for all players. <br />
              <br />
              Please ensure your computer meets the System requirements for Warsim. 
              Warsim uses Javascript to speed up map downloading and to reduce 
              datatransfer, because of this, only Firefox and Opera are supported, 
              and you need a sufficiently fast CPU to run it.   <br /><br />
              <span class="uld">Requirements:</span> 
              <ul>
                <li> 350mhz or faster Processor</li>
                <li> 56k Internet or faster</li>
                <li> Firefox or Opera Browser [IE NOT supported]</li>
                <li> Javascript Enabled</li>
                <li> Cache Turned on [So to Save Server Bandwith]
                  <a name="settingup"></a> </li>
              </ul>
<em><strong>XP and Skill Points [Not Implemented]<br />
              <br />
              </strong></em>To make Warsim fair for all players, XP has been introduced 
              to give a limit to the size of ship you can pilot. When you first 
              create your account, you don't have any XP. To gain XP, you have 
              to join and participate in games. As your XP rises, you will have 
              access to a variety of ships. Low XP will enable you to only fly 
              Light Fighter Squadrons. As you progress, you will gain access to 
              Heavy Fighters, Freighters, Cruisers, Capital ships and finally 
              Uniques. XP can be lost for certain reasons, including exploiting 
              bugs/cheating, and losing too many ships in battle.<br /> <br /> 
              &raquo; Click Here to Events that Generate XP<br /> &raquo; Click 
              Here to View all the Ships in Warsim, their Weapons, and their XP 
              requirements<br /> <br />
              Skill points and XP level in SWC <span class="uld">do not</span> 
              count in Warsim. Warsim has its own skill system, based around the 
              Space Skills in SWC. You will gain skill points as your XP level 
              grows, and you can also gain skill points through competing/winning 
              certain Scenarios/Tournaments that are held. Skill levels range 
              from 0 to 5. A 0 in a certain catagory does not mean that you can't 
              fly a certain type of ship, it just means that you won't fly it 
              as effectivly. For Example; A Captain with 0 points in Capital Ship 
              Piloting Can fly an ISD, however, he lacks refined skills in battle, 
              and because of this, the ship will be less agile than one Commanded 
              by a Pilot with 5 points in Capital Ship Piloting.<br /> <br /> 
              &raquo; Click Here to View the Skill sheet used in Warsim<br /> 
              <br /> 
              <em><strong>Joining A Game</strong></em><br /> 
              <br />
              Warsim is divided into games, you can participate in as many games 
              as you want at any time, however, joining multiple games is not 
              recomended, since you will only be able to 'play' in one game at 
              any time. To join a game, first you must have an account. Login 
              to your account, and you will be greeted with the lobby screen. 
              This screen shows several important features of your account, and 
              lets you edit your preferences. The lobby screen will also list 
              any games looking for players, or games that are open for new players. 
              It is advised that you look at your XP level and look at the games 
              XP mean/cutoff before Joining. The XP cutoff is the max amount of 
              XP available for pilots to use in that specific game, if you have 
              an XP level higher than that, your still able to join, however you 
              will be limited by the XP cutoff. If your XP level is significantly 
              lower than the cutoff, then you may be at a disadvantage, since 
              you will only be able to access smaller ships.<br /> <br />
              Once you have decided which game/s you wish to join, click the link 
              below the game name 'Join This Game'. You will procede to a Team/Ship 
              Selection screen where you choose which team you wish to be on, 
              and what ship you will fly. It is advised that you get into contact 
              with other people in the game, either through Warsim messages or 
              IRC and check to make sure that you are all picking ships that will 
              complement each other. For Example: It is no use everyone picking 
              ISDs, because they have very little fighter defence alone.<br /> 
              <br /> &raquo; Click here to view a series of screenshots showing 
              how to join a Game.<br /> <br /> 
              <em><strong>Setting up a Game [Admin Only Right Now]</strong></em><br /> <br />
              If there are no good games available, then you can create a game, 
              which will be hosted on the Warsim Server. There are a number of 
              options applicable that must be set, including: Teams, Ships, XP 
              Cutoffs, Max Players, spawn points, Max lives, and Winning Objectives. 
              This help guide does not go into depth about setting up a game.<br /> 
              <br /> &raquo; Click here to view the rules on setting up games<br /> 
              <br /> <a name="playing"></a> <div align="center"><strong>Playing 
                Warsim</strong></div>
              <br />
              This section deals with playing Warsim. It is assumed that you have 
              already created an account and have joined a game. <br /> <br /> 
              <em><strong>Moving Your Ship</strong></em><br /> <br />
              The first thing we will look at, is how to move your ship. Moving 
              your ship is simple, it is done through the 'Travel' page, which 
              is can be accessed through the top menu once logged in. If your 
              ship is stationary, all you need to do is click a square to set 
              the destination, then click 'Start Travelling' when the path is 
              displayed. An ETA [Estimated time of Arrival] is displayed, and 
              your ship will begin to move. Please be patient, as moving speed 
              depends on the speed of the specific game, and the speed of your 
              ship.<br />
              <br />
              <em><strong>Scanning Other Ships</strong></em><br />
              <br />
              In Warsim, your not alone, there are probibly several other ships 
              on the battlefield with you. To see these other ships, you can look 
              at the map on any of the pages, however this gives you only a very 
              small amount of information. To see what type of ships are out there, 
              and what sort of stats they have, you have to use your scanners.<br />
			  <br />
              To use your scanners, you must be on the 'Scanner' page, which is 
              accessed through clicking the link on the top menu. When you first 
              click the link, you will see a list of all ships in scan range, 
              which includes your own ships and any ships on your team.<br />
              <br />
              &raquo; Click here to view what the scan list looks like<br />
              <br />
              To narrow down your scan results, use your mouse to select a square 
              on the map. This will only display the ships located in that specific 
              square. <br />
              <br />
              Ships will show their 'Name' and their relative FoF [Friend or Foe] 
              indicator. If the ship is friendly, a green 'Ally' note will be 
              displayed next to its name. Friendly ships are ships that are on 
              your team, as well as ships that your team has 'Alliances' with 
              [Not Implemented]. <br />
              <br />
              To get more information about a target, you must use your sensors 
              to do a 'Focus Scan'. This is done by clicking on any ships Name. 
              It will lead you to a new page, where several specific stats will 
              be displayed about the target ship. These stats include 'Team', 
              'Movement Status', 'Hull', 'Shields', and 'Ionic Capacity'. Any 
              intel you have gathered, such as that ships kills will also be displayed. 
              <em>Any ship that you scan, will get an event telling them your 
              Ships name, Team, and position.This is because, when you 'Focus 
              Scan', your using active sensors and they can be picked up by the 
              other ship. You should limit your focus scanning, because it can 
              give away your position. </em><br />
              <br />
              &raquo; Click here to view what the focus scan looks like<br />
              <br />
              <em><strong>Understanding Scan Blocking</strong></em><br /><br />
			  If a planet is between you and a square, then you cannot scan that square, because the planet blocks any active or passive scans you send. You can see the effects of scanner blocking when you view the map, and take note of any discrepancies. To work out wether a square is blocked or not, use the following tests:<br /><br />
			  <ul>
			  <li>If the Planet is above the ship, and the square being scanned is above the planet, Square may be blocked. </li>
			  <li>If the Planet is below the ship, and the square being scanned is below the planet, Square may be blocked. </li>
			  <li>If the Planet is to the right of the ship, and the square being scanned is to the right of the planet, Square may be blocked. </li>
			  <li>If the Planet is to the left of the ship, and the square being scanned is to the left of the planet, Square may be blocked. </li></ul>
			  For A square to successfully be blocked, two of those conditions must be met. If those two conditions are met, a further test is performed:<ul>
			  <li>If Bearing<small><small>(Ship to Planet)</small></small> is within 30 degrees of Bearing<small><small>(Planet to Square)</small></small> Then, Square is blocked.</li></ul>
			  Visible Code for this can be seen in 'mapmaker.js' under the function 'scanner_radius'. The two arguments are the players x and y coordinates, and the third is the scanning range of the ship. Every argument after that is a planets coordinates, so argument four would be planet1's x, and argument 5 would be planet1's y. There can be an unlimited number of planet arguments. <br />
			  <br />
			  &raquo; Click here to see an Example of Scan blocking<br /><br />
              <em><strong>The Map </strong></em><br />
              <br />
              The map is displayed on every page of warsim when your logged into 
              a game. It shows a variety of data, including scanner data, and 
              movement data. To interpret it, you can look at this key, which 
              shows what all the different symbols mean. The map uses advanced 
              'Alpha' Blending to render, and thus doesn't work in old, incompatible 
              browsers like IE6. Use Firefox or Opera for max compatibility.<br />
              <br />
              The map is interactive, and responds to clicks. It is context sensitive, 
              and the action that occours when you click will be shown in the 
              Caption Box that pops up. <br />
              <br />
              &raquo; Click here to view the Key<br />
              <br />
              <em><strong>Weapons, Reloading And Repairing</strong></em><br />
              <br />
              The whole point of warsim is to create war, so Weapons were included 
              very early in production. To use weapons, you first must be on the 
              'Weapons' page, which can be accessed through the top menu. To see 
              the status of any weapons you have, visit the 'Overview' page, which 
              can be accessed through the top menu.<br />
              <br />
              To fire weapons at any ship, you first must Target that ship, to 
              do this, on the Weapons page, click the square which that ship is 
              located on. This will bring up a list of ships on that square which 
              you can target, which looks oddly like the scanner page. When you 
              have found the ship you wish to shoot at, press the 'Target Ship' 
              button below it. <br />
              <br />
              &raquo; Click here to view the ship listing<br />
              <br />
              When you have targetted a ship, you will be shown a page which shows 
              the enemy ship and its systems, much like the focus scan. Below 
              the scan, you can see a listing of your own ships weapons, and how 
              many you can shoot. If they are in range, a dropdown box will be 
              shown, if the enemy ship is too far away, then a red 'Out of Range' 
              marker will be shown. Select how many weapons you wish to fire, 
              and click the 'Fire!' button. <br />
              <br />
              &raquo; Click here to view the Weapons display and Fire button<br />
              <br />
              You will then be shown a screen showing you the results of your 
              attack. A listing of weapons is shown, including how many was fired, 
              how many hit, and how much damage was done. Below this, is the actual 
              damage done to the ship, shown as a total off all the weapon hits. 
              Any other information will be shown here, including how many ships 
              were shot down if you were attacking a squadron, or if you destroyed 
              the enemys shied generators, or disabled the enemy ship. <br />
              <br />
              &raquo; Click here to view the Combat Results Screen.<br />
              <br />
              After you have fired, you may have to wait for your weapons to reload. 
              This is done automatically for capital ships and Freighters, which 
              will be reloaded after a certain amount of time [Shown by hovering 
              over the Weapon Image]. Fighters however, carry a far more limited 
              supply of warheads, and must return to a friendly capital ship to 
              reload when they run out [Not Implemented Yet] [See Docking]. You 
              will be given an event when your weapons are reloaded.<br />
              <br />
              Ships also can sustain Combat damage when fighting. Damage to Shields 
              and Electronics [Ionic Cap] is repaired over time slowly. Shields 
              will only repair if your shields are still up, and the 'Shield Generator' 
              is still functional. Ionic Capitance is repaired by your engineers, 
              as long as your ship is not completely disabled [See Ship Status]. 
              Shield generator damage, and completly disabled ships cannot be 
              repaired in the short time that battles occour in. You will get 
              an event when your ships have been repaired slightly. <br />
              <br />
              Fighters do not repair by themselves, they have to dock with a capital 
              ship to be repaired. Fighters that have been destroyed in combat 
              will not be replaced. <br />
              <br />
              &raquo; Click here to view Repairing information<br />
              <br />
              <em><strong>Docking<br />
              </strong></em>
              <br />
              Docking can be performed only by Fighters. Ships 
              that have docking bays are classed as Capitals, <em>regardless</em> 
              of SWC rules. Ships that have docking bays have a 'docked' quota, 
              which says how many ships they can have docked at any one time. 
              The quota is in 'Squadrons', however, the following rule is used 
              to caluclate the amount of Freighters that can dock: <br />
              <br />
              <div align="center">2 Freighters = 1 Squadron [Not Implemented yet] </div>
              <br />
              When a squadron is docked, it can be 'Repaired and Reloaded' [Not implemented yet] by 
              the capital ship, this may take a certain amount of time depending 
              on the game speed. If the capital ship is destroyed when a squadron 
              is docked inside, the Fighters escape, taking minor damage from 
              the proximity of the destruction. <br />
              <br />
              <em><strong>Capturing Ships [In Testing]</strong></em><br />
              <br />
              When you disable a ship, or stop it with Tractor Beams, you can 
              then send in Assault Transports to 'Capture' it. To capture a ship, 
              you must successfully attach a Assault Transport to the Hull of 
              the ship, then when your ready, somebody on your team can give the 
              'Capture' command, to make the Marines aboard the transports attempt 
              to secure the bridge of the enemy craft. <br />
              <br />
              To successfully capture it, you must overpower the enemys marines 
              aboard the ship. If you manage to do that, then the ship is captured, 
              it changes teams, and becomes under the command of the Assault Transport 
              Pilot [Pilots Old Ship is left unattended]. <br />
              <br />
              Any Freighter with the 'Assault Transport' Stat is thought of as 
              carrying a full load of marines [Passenger Capacity - 1 = Number 
              of Marines]. Capital ships and Freighters without the 'Assault Transport' 
              rating are thought of as carrying a small security force [Defending 
              Marines = 20% of Passenger Capacity]. Full Military Class capital 
              ships, such as the ISD have more defending marines aboard [30-40%].<br />
              <br />
              Specific Scenarios may give certain teams bonuses towards capturing. 
              For Example, a 'Pirate' based Scenario may give the team 'Eidola' 
              a 25%+ bonus when capturing, because they are skilled at it.<br />
              <br />
              Failure to capture a ship can be the result of:
              <ul><li>Not having enough Marines</li>
              <li>Target Ship is not Fully Immobile</li>
              <li>Target Ship provides enough suppressing fire to hold off Assault Transports</li></ul>
             
              <em><strong>Ship Status </strong></em><br />
              <br />
              Ships carry one of a few status that classifies it at any moment:<br />
              <br />
              <strong>Normal:</strong> Ship is alive and not Moving. Ships Hull 
              &gt; 0, Ships Ionic &gt; 0. Can perform all tasks as usual.<br />
              <br />
              <strong>Travelling:</strong> Ship is alive and moving. Ships Hull 
              &gt; 0, Ships Ionic &gt; 0. Can shoot while moving.<br />
              <br />
              <strong>Docked: </strong>Ship is alive and docked inside a larger 
              ship. It cannot shoot, but can access scanners. Can get Reloaded 
              and Repaired through the larger ship. Moves with the larger ship.<br />
              <br />
              <strong>Disabled: </strong>Ship is disabled, and cannot move or 
              shoot. Ships Hull &gt; 0, Ships Ionic = 0. When your at this stage, 
              you can opt to respawn and leave your old ship in the battle, or 
              you can wait and see if you'll be captured/destroyed. Your Ships 
              engineers <span class="uld">May</span> be able to bring your ships 
              systems back online, to 20%, however, the chance is only very slight 
              [1 in 4 chance every repair cycle].<br />
              <br />
              <strong>Destroyed: </strong>Ship is destroyed, cannot move, shoot, 
              scan... Ships Hull = 0. Ship image is shown as a wreck, and you 
              can respawn in a new ship, Wreck will remain.</td>
          </tr>
        </table>
  </table>
  Welcome to Warsim | Page Gen Time: 0.01 seconds | SQL Count: 0 | Current Time: <?= date("F j, g:i a") ?><br />
Game Concept from <a href="http://www.swcombine.com/">SWC</a> | Images: <a href="http://www.swcombine.com/">SWC</a> &amp; <a href="http://enigma.xrz.nl/">Xearz</a> &amp; Andrew Vellen &amp; Sogekihei</div>
</body>
</html>
