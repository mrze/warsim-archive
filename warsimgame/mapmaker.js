//Map Making (c) CS 04-05
//FIREFOX VERSION
//Commented Edition...

var position_planet = new Array();
var position_fleet = new Array();
var position_caption = new Array();
var position_movement = new Array();
var position_scanner = new Array();
var position_weapon = new Array();
var weapon_array = new Array();
var count = 0;

function distance(x1,y1,x2,y2)
{
	return Math.sqrt(Math.pow((x2 - x1),(2)) + Math.pow((y2 - y1),(2)))
}

function make_map()
{
	var lotsofas = ""; //Pronounced as "Lots of A's"
	var x;
	var y;
	
	//Loop through the X Values [x => 0 => 20]
	for (x = 0; x <= 20; x++) {
		//Loop through the Y Values [y => 0 => 20]
		for (y = 0; y <= 20; y++) {
			//Render the div for each Square [id = "x-y"
			lotsofas += "<a class='mousepointer' id='a" + x + "-" + y + "' style='position: absolute; left: " + (x * 20) + "px; top: " + (y * 20) + "px; height: 20px; width: 20px;z-index: 8;'  onclick=\"mousehasclicked(" + x + "," + y + ")\" onmouseover=\"generate_popup(" + x + "," + y + ");\" onmouseout=\"close_popup();\"></a>";
		}
	}
	
	//Place the big chunck of text onto the map
	document.getElementById('container').innerHTML = lotsofas;
}

function place_planet(x,y,name,image)
{
	var nid = "";
	var aid = "";
	var temp = "";
	
	//Make the Position ID...
	nid = x + "-" + y;

	//Make the <A> ID...
	aid = "a" + x + "-" + y;

	//Add a Planet to the Array
	position_planet[nid] = name;

	//Planets are the top... for now...
	document.getElementById(aid).innerHTML += "<img src='images/planets/" + image + "' id='p" + nid + "' border='0' style='position: absolute; left: 0px; top: 0px; height: 20px; width: 20px;z-index: 1;'>";

}

function place_fleets(x,y,number,image)
{
	//A Little bit of error checking ;)
		var nid = "";
		var aid = "";
		
		//Make the Position ID
		nid = x + "-" + y;
		
		//Make the <A> ID...
		aid = "a" + x + "-" + y;
		
		//Add a Fleet to the Array
		position_fleet[nid] = "Ships: " + number;
		
		//Add the Fleet Image :D
		document.getElementById(aid).innerHTML += "<img src='images/planets/" + image + "' id='f" + nid + "' border='0' style='position: absolute; left: 0px; top: 0px; height: 20px; width: 20px;z-index: 2;'>";
}

function get_bearing(x,y,to_x,to_y)
{
	var run;
	var rise;
	var bearing = 0; 
	var gradient;
	var angle;
	
	run = to_x - x;
	rise  = to_y - y;
	
	if(run == 0){
		if(rise > 0){
			bearing = 360;
		} else {
			bearing = 180;
		}
	}
	if(rise == 0){
		if(run > 0){
			bearing = 90;
		} else {
			bearing = 270;
		}
	}
	
	if(bearing == 0){
		gradient = rise / run;
		angle = Math.atan(gradient) * 180 / Math.PI;
		
		if(rise > 0){
			if(run > 0){
				bearing = 90 - angle;
			} else {
				bearing = 360 - (90+angle);
			}
		} else {
			if(run > 0){
				bearing = 180-(90+angle);
			} else {
				bearing = 270-angle;
			}
		}
	}
	
	return bearing;
}

function is_between(target,number1,number2)
{
	if(target <= number1 && target >= number2){
		return true;
	} 
	if(target >= number1 && target <= number2){
		return true;
	}
	
	return false;
}

function sensor_radius(posx,posy,dist)
{
	var x;
	var y;
	var i;
	var continued;
	var difference;
	var blocked;
	var s_to_p;
	var p_to_x;
	
	//Loop through the X Values [x => 0 => 20]
	for (x = (posx - dist); x <= (posx + dist); x++) {
		//Loop through the Y Values [y => 0 => 20]
		for (y = (posy - dist); y <= (posy + dist); y++) {
		///////////
			blocked = false;
			if(distance(x,y,posx,posy) <= dist){
				if(!position_scanner[x + '-' + y]){	//HMMPH, didnt work with an "And"
					//Loop through all planets
					for(i = 3; i < sensor_radius.arguments.length; i=i+2){
						
						if(sensor_radius.arguments[i] == posx && sensor_radius.arguments[i+1] == posy){
						
						} else {
							continued = false;			
							
							if(sensor_radius.arguments[i] == x && sensor_radius.arguments[i+1] == y){
								continued = false;
							} else {
								
								if(is_between(sensor_radius.arguments[i],x,posx) && is_between(sensor_radius.arguments[i+1],y,posy)){
									continued = true;	
								}
							
							}
							
							if(continued){
								//If Ship->Planet is within 45 degrees of Planet->Square
								s_to_p = Math.round(get_bearing(posx,posy,sensor_radius.arguments[i],sensor_radius.arguments[i+1]));
								p_to_x = Math.round(get_bearing(sensor_radius.arguments[i],sensor_radius.arguments[i+1],x,y));
							
								difference = Math.abs(s_to_p - p_to_x)
								
								if(difference >= 325){
									blocked = true;
								}
								if(difference <= 35){
									blocked = true;
								}
							}
						}
					}
					
					if(blocked == false){
						place_sensor(x,y);
					}
				}
			}
		///////////		
		}
	}
}

function spawn_radius(posx,posy,dist)
{
	var x;
	var y;
	
	var nid;
	
	
	
	//Loop through the X Values [x => 0 => 20]
	for (x = (posx - dist); x <= (posx + dist); x++) {
		//Loop through the Y Values [y => 0 => 20]
		for (y = (posy - dist); y <= (posy + dist); y++) {
		///////////
			if(distance(x,y,posx,posy) <= dist){
				place_spawn(x,y);
			}
		///////////		
		}
	}
}

function place_sensor(x,y)
{
	var nid = "";
	var sid;
	var image;	
	
	//Make IDs
	nid = x + "-" + y;
	sid = "s" + nid;

	position_scanner[nid] = "In Scan Range";

	//Make sure you don't get hit by IE's alpha transparency bug
	if(navigator.appName == "Microsoft Internet Explorer"){
		//MUAHAHAH No extra functionality for you!
	} else {
		image = 'scannerBG.png';
		//Insert scanner img....
		if(document.getElementById('a' + nid)){
			document.getElementById('a' + nid).innerHTML += "<img src='images/planets/" + image + "' id='" + sid + "' border='0' style='position: absolute; left: 0px; top: 0px; height: 20px; width: 20px;z-index: 2;' />";	
		}
	}
}

function place_range(x,y)
{
	var nid = "";
	nid = x + "-" + y;
	
	position_weapon[nid] = "In Firing Range";
	weapon_array[count] = nid;
	count++;
	
	if(navigator.appName == "Microsoft Internet Explorer"){
		//MUAHAHAH No extra functionality for you! [Compatibility error]
	} else {
		document.getElementById("s" + nid).src = "images/planets/inrange.png";
	}
}

function place_spawn(x,y)
{
	var nid = "";
	nid = x + "-" + y;
	
	position_weapon[nid] = "Can NOT Spawn Here";
	weapon_array[count] = nid;
	count++;
	
	if(navigator.appName == "Microsoft Internet Explorer"){
		//MUAHAHAH No extra functionality for you! [Compatibility error]
	} else {
		document.getElementById('a' + nid).innerHTML += "<img src='images/planets/inrange.png' id='s" + nid + "' border='0' style='position: absolute; left: 0px; top: 0px; height: 20px; width: 20px;z-index: 2;' />";	
	}
}

function place_movement(x,y,image,title)
{
	var nid = "";
	
	//Make IDs
	nid = x + "-" + y;
	mid = "m" + nid;
	
	//Insert image
	document.getElementById('a' + nid).innerHTML += "<img src='images/planets/" + image + "' id='" + mid + "' border='0' style='position: absolute; left: 0px; top: 0px; height: 20px; width: 20px;z-index: 4;' />";

	//Add label
	if(title){
		position_movement[nid] = title;
	}
}

function place_movement2(nid,image,title)
{
	mid = "m" + nid;
	
	//Insert image
	document.getElementById('a' + nid).innerHTML += "<img src='images/planets/" + image + "' id='" + mid + "' border='0' style='position: absolute; left: 0px; top: 0px; height: 20px; width: 20px;z-index: 4;' />";

	//Add label
	if(title){
		position_movement[nid] = title;
	}
}

function update_map()
{
var x;
var y;
var nid;
var message;
	
	//Loop through the X Values [x => 0 => 20]
	for (x = 0; x <= 20; x++) {
		//Loop through the Y Values [y => 0 => 20]
		for (y = 0; y <= 20; y++) {
			
			//OK! Here we need to define the caption for the specific square...
			nid = x + "-" + y;
			message = "";
			if(position_movement[nid]){
				message += position_movement[nid] + "<br />";
			}
			if(position_planet[nid]){
				message += position_planet[nid] + "<br />";
			}
			if(position_fleet[nid]){
				message += position_fleet[nid] + "<br />";
			}
			if(position_scanner[nid]){
				message += position_scanner[nid] + "<br />";
			}
			if(position_weapon[nid]){
				message += position_weapon[nid] + "<br />";
			}
			position_caption[nid] = message;
		}
	}
}

function load_range(range,pos_x,pos_y)
{
	var x;
	var y;
	var nid;
	count = 0;
	
	//Clear any other weapons from the map
	for (var i = 0; i < weapon_array.length; i++){
		document.getElementById("s" + weapon_array[i]).src = "images/planets/scannerBG.png";
		position_weapon[weapon_array[i]] = "";
	}
	
	if(range >= 0){
		//Loop through the X Values [x => 0 => 20]
		for (x = 0; x <= 20; x++) {
			//Loop through the Y Values [y => 0 => 20]
			for (y = 0; y <= 20; y++) {
				nid = x + "-" + y;
				
				//Check out the distance between X,Y and Pos_x,Pos_y
				if(distance(x,y,pos_x,pos_y) <= range){
					if(position_scanner[nid]){
						place_range(x,y);
					}
				}
			}
		}
	}
	
	update_map();
}

function generate_popup(x,y)
{
	var nid = x + "-" + y;
	
	var stuff = '';
	
	if(map_mode == 'scanner'){
		stuff = 'Click to Scan this Square';
	}
	if(map_mode == 'weapons'){
		stuff = 'Click to Target this Square';
	}
	if(map_mode == 'travel'){
		stuff = 'Click to Fly to this Square';
	}
	if(map_mode == 'place_ship'){
		stuff = 'Set Entry location as ' + x + ',' + y;
	}
	
	return overlib(position_caption[nid] + stuff, CAPTION, x + "," + y);
}

function close_popup()
{
	return nd();
}