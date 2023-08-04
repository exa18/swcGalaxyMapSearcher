<?
require_once "_pkg/config.php";
require_once "_pkg/class_progress.php";
$db = new mydb( _DB_MAIN );

define('_VERSIONdev', '(dev) api 2.0');
use const _VERSIONdev as _VERSION;

/*

API 2.0

1 / +50
https://www.swcombine.com/ws/v2.0/galaxy/sectors/?start_index=1
2
https://www.swcombine.com/ws/v2.0/galaxy/systems/?start_index=1
	https://www.swcombine.com/ws/v2.0/galaxy/systems/9:33
3
https://www.swcombine.com/ws/v2.0/galaxy/planets/?start_index=1
	https://www.swcombine.com/ws/v2.0/galaxy/planets/8:411
4
https://www.swcombine.com/ws/v2.0/galaxy/cities/
https://www.swcombine.com/ws/v2.0/galaxy/stations/
	https://www.swcombine.com/ws/v2.0/galaxy/stations/5:6329

*/
	$maps = [
		"normal" => "high&amp;political=0",
		"political" => "high&amp;political=1",
		"mono" => "gray",
	];
	/*
		COLORs
	*/
	$c_background = '#456';
	$c_subbackground = '#567';
	$c_base = '#fff';
	$c_color = '#0bf';
	$c_subcolor = '#ccc';
	$c_acclink = '#345';
	
	$links = array(
		'css' => 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css',
		'fontawesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/fontawesome.min.css',
		'jquery' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js'
	);

	$meta=array(
		"fbapi" => '',
		"tags" => '',
		//"img" => _BASEURI .'/'.'',
		"img" => 'http://holocron.swcombine.com/images/thumb/d/da/GalaxyMap.gif/600px-GalaxyMap.gif',
		"desc" => 'prints all galaxy entries at once',
		"site" => 'swcGalaxySearcher'
	);
	
	$url = _BASEURI . $_SERVER['REQUEST_URI'];
	
	$meta["title"] = $meta['site'] . ' : ' . _VERSION . ' :: ' . $meta['desc'];
	if ($meta["tags"]) {
		$tags = ' ( #'.implode(' #',$meta["tags"]) . ' )';
	}

	$swcuri = 'https://www.swcombine.com/';
	$swcuri_map = $swcuri . 'rules/?Galaxy_Map';

if ($_GET['update']) {

	$html = htmlGet($swcuri . 'rules/?Races');
	preg_match_all('/href=\"\?.*ID=(\d+)"\stitle=\".*\:(.*)\"\>/U', $html, $races);
	$races_list = array();
	foreach($races[1] as $k => $id){
		$name = $races[2][$k];
		$query = "SELECT * FROM `races` WHERE raceID={$id}";
		$q= $db->fetch($query);
		if ( empty($q) ) {
			$query = "INSERT `races` (raceID,raceName) VALUES ({$id},\"{$name}\")";
		}else{
			$query = "UPDATE `races` SET raceName=\"{$name}\" WHERE raceID={$id}";
		}
		$db->query($query);
		$races_list[$id] = $name;
	}
	/*
	Check and clean DB
	*/
	$query = 'SELECT * FROM `races`';
	$q= $db->fetch($query);
	foreach ($q as $n) {
		$id = $n['raceID'];
		$name = $n['raceName'];
		if ( !isset($races_list[$id]) ){
			$query = "DELETE FROM `races` WHERE raceID={$id}";
			$db->query($query);
		}
	}
}

		$query = 'SELECT * FROM `races`';
		$q= $db->fetch($query);
		$races_list = array();
		foreach ($q as $k => $n) {
			$id = $n['raceID'];
			$name = $n['raceName'];
			$races_list[$k]['id'] = (int)$id;
			$races_list[$k]['name'] = $name;
		}
		$r = rand( 0,count($races_list)-1 );
		$race['id'] = $races_list[$r]['id'];
		$race['uri'] = 'https://img.swcombine.com/races/' . $race['id'] .'/main.jpg';
		$race['name'] = $races_list[$r]['name'];
		$race['url'] = $swcuri . 'rules/?Races&amp;ID=' . $race['id'];
			
	$map_level = array(
		0 => "sectorID",
		1 => "systemID",
		2 => "planetID",
		3 => "surface"
	);
	
	$progress = 'progress';
	$progress_status = '_status';

	echo trim('<html><head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta property="fb:app_id" content="'.$meta['fbapi'].'"/>
	<meta property="og:title" content="'.$meta['title'].'"/>
	<meta property="og:url" content="'. $url .'"/>
	<meta property="og:type" content="article"/>
	<meta property="og:image" content="'.$meta['img'].'" />
	<meta property="og:site_name" content="'.$meta['site'].'"/>
	<meta property="og:description" content="'.$meta['desc']. $tags . ' )"/>
	<title>'.$meta['title'].'</title>
	<script type="text/javascript" src="'.$links['jquery'].'"></script>
	<link rel="stylesheet" href="'.$links['fontawesome'].'" />
	<link rel="stylesheet" href="'.$links['css'].'" />
	');
	?>
	<style type="text/css">
		@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap&subset=latin-ext);
		*{margin:0;padding:0;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}
		body{background:<?=$c_background?>;font-family:Open Sans,Arial,Helvetica,Sans-serif,Verdana,Tahoma;width:100%;height:100%}
		.row{display:flex;justify-content:center;align-items:center;height:100%;margin:auto}
		.row>div{text-align:center}
		h1 small{color:<?=$c_subcolor?>}
		.username{bottom:0;color:<?=$c_base?>;padding:30px 15px 4px;width:100%}
		.accordion .well{border-radius:0;background-color:transparent;border-color:<?=$c_subbackground?>;border-style:dashed;padding:1rem 2.5rem;margin:0;-web-kit-box-shadow:none;box-shadow:none}
		.accordion .well:first-child{border-width:0 1px 0 0}
		.accordion .well:last-child{border-width:0 0 0 1px}
		.username > h2{font-family:oswald;font-size:27px;font-weight:lighter;position:relative;text-transform:uppercase}
		.username > h2 small{color:<?=$c_base?>;font-family:open sans;font-size:13px;font-weight:400;position:relative;line-height:1.1;top:.5rem}
		.username .fa{color:<?=$c_base?>;font-size:14px;margin:0 0 0 4px;position:static}
		.tags{background:rgba(255,255,255,0.1) none repeat scroll 0 0;border:1px solid rgba(255,255,255,0.1);border-radius:0;display:inline-block;font-size:13px;margin:4px 0 0;padding:2px 5px;-webkit-transition:all .4s ease;-o-transition:all .4s ease;transition:all .4s ease}
		.tags:hover{background:rgba(255,255,255,0.3) none repeat scroll 0 0;border:1px solid rgba(255,255,255,0.5);border-radius:0;display:inline-block;font-size:13px;margin:4px 0 0;padding:2px 5px}
		ul{list-style-type:none}
		a,a:hover,a:focus,a:active{text-decoration:none;outline:none;border:0;outline-offset:0}
		h1{font-size:24px;font-weight:400;text-align:center;margin-top:2rem;line-height:1.5}
		h1,h2,h3,h4,h5,h6{color:<?=$c_base?>}
		h1 a{color:<?=$c_color?>;font-size:16px}
		.accordion{width:100%;max-width:360px;margin:30px auto 20px;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;display:flex}
		.accordion li:last-child .link{border-bottom:0}
		.accordion li{opacity:unset;-webkit-transition:all .4s ease;-o-transition:all .4s ease;transition:all .4s ease;background-color:transparent}
		.accordion li:hover{background-color:<?=$c_color?>}
		.accordion li:hover a{color:<?=$c_base?>}
		.swc_race {border-bottom:1px solid <?=$c_acclink?>}
		.swc_race img{width:100px}
		.inputfield{margin:1.5em auto;width:18em}
		#<?=$progress?>{height:8px;line-height:6px;width:100%;border:1px solid <?=$c_acclink?>;-webkit-transition: all 0.3s ease;transition: all 0.3s ease;
		}
		#<?=$progress?> > div {width:0;background-color:<?=$c_color?>;}
		#<?=$progress?><?=$progress_status?>{color:<?=$c_acclink?>;-webkit-transition: all 0.3s ease;transition: all 0.3s ease;}
	</style>
<?php
	echo '</head><body><div class="container"><div class="row"><div>';
	echo '<div class="swc_race"><a href="' . $race['url'] . '"><img src="' . $race['uri'] . '" alt="' . $race['name'] . '" /><h6>' . $race['name'] . '</h6></a></div>';
	echo '<div class="topic"><h1>'.$meta['site'].'&nbsp;&nbsp;<span class="badge">'. _VERSION .'</span><br><small>'.$meta['desc'].'</small></h1></div>';
	echo '<div class="inputfield"><input type="text" class="form-control" id="mySearchable" onkeyup="mySearchableList()" placeholder="Search . . ."></div>';
	echo '<div id="progress" style="display:none;opacity:0;"><div>&nbsp;</div></div>';
	echo '<div id="progress_status" style="display:none;opacity:0;">&nbsp;</div>';
	echo '<div id="search_area"></div>';
	flush();
    
	$time = microtime(true);

if ($_GET['update']) {
	/*
		Maps image generate
	*/
	$status_bar = new progressBar(count($maps),"Generating map images");
	$status_bar->show();
	$mapurl='https://www.swcombine.com/rules/the_universe/galaxy_map/galaxyMap.php?mode=';
	$i=0;
	foreach($maps as $k=>$v){
		imageSave($mapurl.$v,'./_maps/'.$k.'.gif');
		$status_bar->update($i);
		$i++;
	}
}
if ($_GET['update']) {
    /*
        Sectors check and update
    */
    $html = htmlGet($swcuri_map);
    preg_match_all('/href=\"(.*)\".*alt=\"(.*)\"\s/', $html, $sector);
    $u = $sector[1];
    $names = $sector[2];
	$sector=array();
	$level = 0;

	$status_bar = new progressBar(count($u),"Updating sector");
	$status_bar->show();
	$i=0;
    foreach($u as $k => $s){
        $name = $names[$k];
		preg_match('/(\d+)/', $s, $id);
		$id = $id[1];
		$sector[$id] = $name;

		$query = 'SELECT * FROM `sector` WHERE sectorID='.$id;
		$q= $db->fetch($query);
		if ( empty($q) ) {
			$query = "INSERT `sector` (sectorID, sectorName) VALUES ({$id},\"{$name}\")";
		}else{
			$query = "UPDATE `sector` SET sectorName=\"{$name}\" WHERE sectorID={$id}";
		}
		$db->query($query);
		$status_bar->update($i);
		$i++;
	}
	/*
		Check and clean DB
	*/
	$query = 'SELECT * FROM `sector`';
	$q= $db->fetch($query);
	$qs=array();
	foreach ($q as $n) {
		$id = $n['sectorID'];
		if ( isset($sector[$id]) ){
			$qs[$id] = $n['sectorName'];
		}else{
			$query = "DELETE FROM `sector` WHERE sectorID={$id}";
			$db->query($query);
		}
	}
	$sector=$qs;

	/*
		Search sector inside
		get: systemID and system stats

		$system = array(
			sectorID => array(
				systemID => array(
					Name => string
					Position => string
					Suns => string
					Planets => string
					Moons => string
					AsteroidFields => string
					Stations => string
					Population => string
					ControlledBy => string
				)
			)
		)
	*/
	$sector_total = count($sector);
	$system_total = 0;
	$planet_total = 0;
	$station_total = 0;
	$city_total = 0;
	
	$moon_total = 0;
	$asteroid_total = 0;
	$suns_total = array();
	$nosuns_total = 0;

	$status_bar = new progressBar($sector_total,"Sector: %s");
	$status_bar->show();
	
	$i=0;
	$system=array();
	foreach($sector as $id => $sname){
	
	    $u = $swcuri_map . '&'.$map_level[$level] . '=' . $id;
	    $html = htmlGet($u);
	    $table = tableGet($html);
		if ( !empty($table[0]) ) {
			$system[$id] = $table[0];
		/*
			Counts statistics
		*/
			foreach ($system[$id] as $tid => $tv) {

				$query = "SELECT * FROM `system` WHERE sectorID={$id} AND systemID={$tid}";
				$q= $db->fetch($query);

				$planet = (int)$tv['Planets'];
				$station = (int)$tv['Stations'];
				$moon = (int)$tv['Moons'];
				$asteroid = (int)$tv['AsteroidFields'];
				$suns = intval($tv['Suns']);
				$pos = removeSpaces($tv['Position']);

				if ( empty($q) ) {
					$query = "INSERT `system` (sectorID,systemID,systemName,systemPosition,systemSuns,systemPlanets,systemMoons,systemAsteroidFields,systemStations,systemPopulation,systemControlledBy) VALUES ({$id},{$tid},\"{$tv['Name']}\",\"{$pos}\",\"{$suns}\",\"{$planet}\",\"{$moon}\",\"{$asteroid}\",\"{$station}\",\"{$tv['Population']}\",\"{$tv['ControlledBy']}\")";
				}else{
					$query = "UPDATE `system` SET systemName=\"{$tv['Name']}\", systemPosition=\"{$pos}\", systemSuns=\"{$suns}\", systemPlanets=\"{$planet}\", systemMoons=\"{$moon}\", systemAsteroidFields=\"{$asteroid}\", systemStations=\"{$station}\", systemPopulation=\"{$tv['Population']}\", systemControlledBy=\"{$tv['ControlledBy']}\" WHERE sectorID={$id} AND systemID={$tid}";
				}
				$db->query($query);

				$planet_total += (int)$tv['Planets'];
				$station_total += (int)$tv['Stations'];
				$moon_total += (int)$tv['Moons'];
				$asteroid_total += (int)$tv['AsteroidFields'];

				if ( $suns>1 ) {
					if ( array_key_exists($suns, $suns_total) ) {
						$suns_total[$suns] += 1;
					}else{
						$suns_total[$suns] = 1;
					}
				}
				if ( $suns<1 ) {
					$nosuns_total++;
				}
			}
			$system_total += count($system[$id]);
		}
		$status_bar->update($i);
		//if ($i>100) {break;}
		$i++;
	}
	/*
		Check and clean DB
	*/
	$query = 'SELECT * FROM `system`';
	$q= $db->fetch($query);
	$qs=array();
	foreach ($q as $n) {
		$id = $n['sectorID'];
			unset($n['sectorID']);
		$tid = $n['systemID'];
			unset($n['systemID']);
		if ( isset($system[$id][$tid]) ){
			$qs[$id][$tid] = $n;
		}else{
			$query = "DELETE FROM `system` WHERE sectorID={$id} AND systemID={$tid}";
			$db->query($query);
		}
	}
	$system=$qs;
}
	
	/*
		Read and convert to array
	*/
	$query = 'SELECT * FROM `sector`';
	$q= $db->fetch($query);
	$qs=array();
	foreach ($q as $n) {
		$id = $n['sectorID'];
		$qs[$id] = $n['sectorName'];
	}
	$sector=$qs;
	$sector_total = count($sector);
	
	$query = 'SELECT * FROM `system`';
	$q= $db->fetch($query);
	$qs=array();
	foreach ($q as $n) {
		$id = $n['sectorID'];
			unset($n['sectorID']);
		$tid = $n['systemID'];
			unset($n['systemID']);
		$qs[$id][$tid] = $n;
	}
	$system=$qs;
	$system_total = count($q);

if ($_GET['update']) {
	/*
		Level 3: Planets and Stations
	*/
	$level = 1;
	$status_bar = new progressBar($system_total,"Planets and stations: %s");
	$status_bar->show();
	$i=0;
	$planets=array();
	$stations=array();
	$prefix = 'system';
	foreach($sector as $id => $sname){
		foreach ($system[$id] as $tid => $tv) {
			$planet = (int)$tv[$prefix.'Planets'] + (int)$tv[$prefix.'Moons'] + (int)$tv[$prefix.'AsteroidFields'] + (int)$tv[$prefix.'Suns'];
			$station = (int)$tv[$prefix.'Stations'];
			if ($planet>0 or $station>0) {
				$u = $swcuri_map . '&'.$map_level[$level] . '=' . $tid;
				$html = htmlGet($u);
				$table = tableGet($html);
				if ($planet>0 or count($table)>1 ) {
					$planet = $table[0];
					if ($station>0) {
						$station = $table[1];
					}
				}else{
					if ($station>0) {
						$station = $table[0];
					}			        
				}
				if ( !empty($planet) ) {
					foreach($planet as $pid => $pv){
						$planets[$tid][$pid] = $pv;
						$homeworld = trim($pv['Homeworld']);
						$controlled = trim($pv['ControlledBy']);
						if ( strlen($homeworld)<2 ) {
							$homeworld = '';
						}
						if ( strlen($controlled)<2 ) {
							$controlled = '';
						}
						$pos = removeSpaces($pv['Position']);
						if ( strlen($pos)<2 ) {
							$pos = '';
						}
						$query = "SELECT * FROM `planet` WHERE systemID={$tid} AND planetID={$pid}";
						$q= $db->fetch($query);
						if ( empty($q) ) {
							$query = "INSERT `planet` (systemID,planetID,planetName,planetPosition,planetType,planetSize,planetPopulation,planetControlledBy,planetHomeworld) VALUES ({$tid},{$pid},\"{$pv['Name']}\",\"{$pos}\",\"{$pv['Type']}\",\"{$pv['Size']}\",\"{$pv['Population']}\",\"{$controlled}\",\"{$homeworld}\")";
						}else{
							$query = "UPDATE `planet` SET planetName=\"{$pv['Name']}\", planetPosition=\"{$pos}\", planetType=\"{$pv['Type']}\", planetSize=\"{$pv['Size']}\", planetPopulation=\"{$pv['Population']}\", planetControlledBy=\"{$controlled}\", planetHomeworld=\"{$homeworld}\" WHERE systemID={$tid} AND planetID={$pid}";
						}
						$db->query($query);
					}
				}
				if ( !empty($station) ) {
					$si = 0;
					foreach($station as $sv){
						$sname = (string)$sv[1];
						$spos = removeSpaces($sv[2]);
						$stype = (string)$sv[3];
						$sowner = strip_tags((string)$sv[4]);
						$sid = hash( 'sha256', $si . $tid . $sname . $spos . $stype . $sowner );
						$stations[$tid][] = array( $sname, $spos, $stype, $sowner);
						$query = "SELECT * FROM `station` WHERE stationID=\"{$sid}\" AND systemID={$tid}";
						$q= $db->fetch($query);
						if ( empty($q) ) {
							$query = "INSERT `station` (systemID,stationID,stationName,stationPosition,stationType,stationOwner) VALUES ({$tid},\"{$sid}\",\"{$sname}\",\"{$spos}\",\"{$stype}\",\"{$sowner}\")";
							$si++;
						}else{
							$query = "UPDATE `station` SET stationName=\"{$sname}\", stationType=\"{$stype}\", stationOwner=\"{$sowner}\" WHERE stationID=\"{$sid}\" AND systemID={$tid}";
						}
						$db->query($query);
					}
				}
			}
		$status_bar->update($i);
		$i++;
		}
	}
	$status_bar->hide();
}


	//	foreach($system as $sid => $sv){
	/*	
		
			$level = 1;
		    $u = $swcuri_map . '&'.$map_level[$level] . '=' . $sid;
		    $html = htmlGet($u);
		    $table = tableGet($html);
			
			$planet = $table[0];
			$station = $table[0];
			$planet_total += count($planet);
			$station_total += count($station);
		/*	
			foreach($planet as $pid => $pv){
				$level = 2;
			    $u = $swcuri_map . '&'.$map_level[$level] . '=' . $pid;
			    $html = htmlGet($u);
			    //preg_match_all('/reg_pointCaption\((.*)\)/', $html, $surface);
			    preg_match_all('/reg_pointCaption\(\"(.*)\".*(\d+\,\d+)\)/', $html, $surface);
			    $caption = $surface[1];
			    $position = $surface[2];
			    if (count($caption)>0) {
		        foreach($caption as $k=>$v){
		        $sid = hash( 'sha256', $k . $v . $position[$k]);
		            //echo $v . " // ";
				}
				}
				$city_total += count($surface);
			}
		*/
	//	}
	$suns=0;
	$suns_temp=array();
	ksort($suns_total);
	foreach($suns_total as $sk => $sv){
		$suns += $sv;
		$suns_temp[] = $sk . ' s. =' . $sv;
	}
	$suns_temp = implode(', ',$suns_temp);
	
	echo "Secotrs: " . $sector_total . "<br>";
	echo "Systems: " . $system_total . "<br>";
	echo "Planets: " . $planet_total . "<br>";
	echo "Stations: " . $station_total . "<br>";
	echo "Moons: " . $moon_total . "<br>";
	echo "Asteroid Fields: " . $asteroid_total . "<br>";
	echo "MultiSun systems: " . $suns . ' where is ' . $suns_temp . "<br>";
	echo "NO-Sun systems: " . $nosuns_total . "<br>";
    $time = number_format(microtime(true) - $time, 5, '.','');
	echo "<br>Execute in $time seconds<br>";

	flush();
	/*
		Search system inside
		get: planetID, stations
	*/
	
	
	/*
		Search surface of planet
	*/
/* 	$time = microtime(true);
	
	$testuri = 'https://www.swcombine.com/rules/?Galaxy_Map&planetID=1573';
	$html = htmlGet($testuri);
    
    preg_match_all('/reg_pointCaption\((.*)\)/', $html, $surface);
        foreach($surface[1] as $v){
            echo $v . " // ";
		}    
    echo "<br>Surface: " . count($surface[1]);
    $time = number_format(microtime(true) - $time, 5, '.','');
	echo "<br>Execute in $time seconds<br>"; */

echo '</div></div></div></body></html>';

?>