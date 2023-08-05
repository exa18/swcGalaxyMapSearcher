<?php set_time_limit(180);
require_once '_apk/config.php';
require_once '_apk/functions.php';
/*
	API links
*/
$url_sectors = _DB['sectors'];
$url_systems = _DB['systems'];
/*
	DATA files
*/
$fil_sectors = _PATH['sectors'];
$fil_systems = _PATH['systems'];
$fil_factions = _PATH['factions'];
/*

	UPDATE stage selection
	(DISABLED)

*/
$update = [
	'basic' => true,
	'extra' => false,
];

if ( $forceupdate = htmlspecialchars($_GET["forceupdate"]) ){
	if ( isset($update[$forceupdate]) ){
		foreach($update as $k => $v){
			if ( $k == $forceupdate ) {
				$switch = true;
			}else{
				$switch = false;
			}
			$update[$k] = $switch;
		}
	}
}

/*
	Read and write all sectors and factions inside
*/
$start_index = 1;
$item_count = 50;
$total = 0;
$sectors = [];
$factions = [];

$i = -1;
while($i<$total){
	$file = get_xml_from_url($url_sectors."?start_index=".$start_index."&item_count=".$item_count);
	$xml = simplexml_load_string($file);
	if ($total==0){
		$total = $xml->sectors['total'];
		$i = 0;
	}
	foreach($xml->sectors->sector as $v){
		$name = (string)$v['name'];
		$uid = swcGetUid($v['uid']);
		if ($name) {
			// SECTOR : name
			$sectors['name'][$uid] = $name;
			// SECTOR : population
			$sectors['population'][$uid] = intval($v->population);
			// SECTOR : knownsystems
			$sectors['knownsystems'][$uid] = intval($v->knownsystems);
			// SECTOR : controlledby
			$fid = swcGetUid($v->controlledby['uid']);
			if ($fid) {
				// FACTION : check and save
				$sectors['faction'][$uid] = $fid;
				if (!isset($factions['name'][$fid])){
					$factions['name'][$fid] = (string)$v->controlledby;
				}
			}
		}
		$i++;
	}
	$start_index += $item_count;
}
fileSave($fil_sectors,$sectors);
/*
	Read and write all systems
*/
$start_index = 1;
$item_count = 50;
$total = 0;
$systems = [];

$i = -1;
while($i<$total){
	$file = get_xml_from_url($url_systems."?start_index=".$start_index."&item_count=".$item_count);
	$xml = simplexml_load_string($file);
	if ($total==0){
		$total = $xml->systems['total'];
		$i = 0;
	}
	foreach($xml->systems->system as $v){
		$name = (string)$v['name'];
		$uid = swcGetUid($v['uid']);
		if ($name) {
			// SYSTEM : name
			$systems['name'][$uid] = $name;
			// SYSTEM : population
			$systems['population'][$uid] = intval($v->population);
			// SYSTEM : sector ID
			$sid = swcGetUid($v->location->sector['uid']);
			$systems['sector'][$uid] = $sid;
			// SYSTEM : controlledby
			$fid = swcGetUid($v->controlledby['uid']);
			if ($fid){
				// FACTION : check and save
				$systems['faction'][$uid] = $fid;
				if (!isset($factions['name'][$fid])){
					$factions['name'][$fid] = (string)$v->controlledby;
				}
			}
			// SYSTEM : location inside sector
			$c = $v->location->coordinates->galaxy;
			$systems['x'][$uid] = intval($c['x']);
			$systems['y'][$uid] = intval($c['y']);
		}
		$i++;
	}
	$start_index += $item_count;
}

fileSave($fil_systems,$systems);
fileSave($fil_factions,$factions);
/*

	Extend read

*/
/*
	First read TYPES

		planets
		stations

*/
/*
	Read each system in loop

		XML access number in loop
			-> systems
				-> planets
					-> cities
				-> stations

	foreach($systems['name'] as $sid => $name){

		> PASS 1 >
			~60s

		$file = get_xml_from_url($url_systems . swcMakeUid($sid, _CODE['systems']) );
		$xml = simplexml_load_string($file);

		[
		system UID

		planets[sid]
			uid	-> pid
			name
		stations[sid]
			uid
			name
		]
		(!) - no need to read and check if exists then skip
		PLANET name having {
			Sun			-> (!) Sun
			Moon		-> Moon
			Companion	-> Moon
			Hole		-> (!) BlackHole
			Comet		-> usaly have 1 city
			Belt		-> asteroid field

		}

		>> PASS 2 >>
			~180s

			/galaxy/planets/ID (with exeption of SUN and HOLE)
		From PLANETS read Cities and planet type
			planets
				type
			cities[pid]
				uid
				name
				x
				y
			/galaxy/stations/ID
		From STATIONS read type of station
			stations
				type
	}

*/

?>