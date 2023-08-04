<?php
require_once '_apk/config.php';

/*
	API links
*/
$url_sectors = _DB['sectors'];
$url_systems = _DB['systems'];
$fil_sectors = _PATH['sectors'];
$fil_systems = _PATH['systems'];
$fil_factions = _PATH['factions'];
/*
	HTML SWC Galaxy Map link
*/
$galaxymap = _GALAXYMAP;
/*
	Define update intervals or do force update
	?forceupdate=all -> do it all
	?forceupdate=sectors
	?forceupdate=systems
*/
$updateinterval = 365;
$updatesectors = false;
$updatesystems = false;
if ( $forceupdate = htmlspecialchars($_GET["forceupdate"]) ){
	switch($forceupdate){
		case "all":
			$updatesectors = true;
			$updatesystems = true;
			break;
		case "sectors":
			$updatesectors = true;
			break;
		case "systems":
			$updatesystems = true;
			break;
		default: break;
	}
}
/*

	FUNTIONS

*/
function get_xml_from_url($url){
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

	$xmlstr = curl_exec($ch);
	curl_close($ch);

	return $xmlstr;
}

function swcGetUid($uid){
	if ($uid) {
		$uid = explode(':',$uid);
		$uid = array_pop($uid);
		return intval($uid);
	}
	return NULL;
}
function absLong($a,$b){
	return abs( max($a,$b) - min($a,$b) );
}
function fileLoad($file){
	$c = file_get_contents($file);
	$json = json_decode($c,true);
	return $json;
}
function fileSave($file,$c){
	$json = json_encode($c);
	file_put_contents($file,$json);
}
function fileCleanName($string, $is_filename = TRUE) {
	$string = preg_replace('/[^\w\-'. ($is_filename ? '~_\.' : ''). ']+/u', '-', $string);
	return mb_strtolower(preg_replace('/--+/u', '-', $string), 'UTF-8');
}
function fileHowOld($file){
	if (file_exists($file)) {
		return date('Ymd') - date('Ymd',filemtime($file));
	}
	return -1;
}
function flieDownloadCSV( $array, $filename = "export.csv", $delimiter="\t" )
{
	$filename = fileCleanName($filename) . ".csv";
	header( 'Content-Type: application/csv' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '";' );

	// clean output buffer
	ob_end_clean();
	
	$handle = fopen( 'php://output', 'w' );

	// use keys as column titles
	//fputcsv( $handle, array_keys( $array['0'] ), $delimiter );

	foreach ( $array as $value ) {
		fputcsv( $handle, $value, $delimiter );
	}

	fclose( $handle );

	// flush buffer
	ob_flush();
	
	// use exit to get rid of unexpected output afterward
	exit();
}
/*
	Read and write all sectors and factions inside
*/
$lastupdatesectors = fileHowOld($fil_sectors);
if ( (!file_exists($fil_sectors) OR !file_exists("factions")) OR $lastupdatesectors>$updateinterval ){
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
				// SECTOR : url
				$sectors['u'][$uid] = (string)$v['href'];
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
						$factions['u'][$fid] = (string)$v->controlledby['href'];
					}
				}
			}
			$i++;
		}
		$start_index += $item_count;
	}
	fileSave($fil_sectors,$sectors);
	fileSave($fil_factions,$factions);
}else{
	$sectors = fileLoad($fil_sectors);
	$factions = fileLoad($fil_factions);
}
/*
	Read and write all systems
*/
$lastupdatesystems = fileHowOld($fil_systems);
if ( !file_exists($fil_systems) OR $lastupdatesystems>$updateinterval){
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
				// SYSTEM : href
				$systems['u'][$uid] = (string)$v['href'];
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
						$factions['u'][$fid] = (string)$v->controlledby['href'];
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
}else{
	$systems = fileLoad($fil_systems);
}

/*

	Download file when ?getmap=UID

*/
if ( $uid = htmlspecialchars($_GET["getmap"]) ){
	if ( isset($sectors['name'][$uid]) ){

		$file = get_xml_from_url($sectors['u'][$uid]);
		$xml = simplexml_load_string($file);

		/*
			Get Sector Systems
		*/
		$system = [];
		$i=0;
		$c=$xml->sector->systems['count'];
		if ($c) {
			while($i < $c){
				$v=$xml->sector->systems->system[$i];
				$sid = swcGetUid($v['uid']);
				if (isset($systems['name'][$sid])){
					$system[$sid] = [
						'x' => $systems['x'][$sid],
						'y' => $systems['y'][$sid],
					];
				}
				$i++;
			}
		}

		$c=$xml->sector->coordinates['count'];
		$i=0;
		$cords =[];
		$border = [
			'min'=>[
				'x'=>1000,
				'y'=>1000
			],
			'max'=>[
				'x'=>-1000,
				'y'=>-1000
			]
		];
		while($i<$c){
			$p = $xml->sector->coordinates->point[$i];
			$x = intval($p['x']);
			$y = intval($p['y']);
			$cords[$i]['x'] = $x;
			$cords[$i]['y'] = $y;
			$border['min']['x'] = min($border['min']['x'],$x);
			$border['min']['y'] = min($border['min']['y'],$y);
			$border['max']['x'] = max($border['max']['x'],$x);
			$border['max']['y'] = max($border['max']['y'],$y);
			$i++;
		}
		// Add square space around
		$border['min']['x'] -=1;
		$border['min']['y'] -=1;
		$border['max']['x'] +=1;
		$border['max']['y'] +=1;

		// WIDTH
		$w = absLong($border['max']['x'],$border['min']['x']) +1;
		// HIGTH
		$h = absLong($border['max']['y'],$border['min']['y']) +1;
		// MARKS
		$val = 1;	// area
		$valborder = 2;	// border -> space between sectors
		$valsystem = 's';	// known systems
		
		$map = [];
		$rowx = [];
		$rowy = [];
		
			$iy = 0;
			$ix = 0;
			$m = [];
			// create first row
			$rowx[] = "x->";
			while($ix < $w){
				$cix = $ix + $border['min']['x'];
				$rowx[] = $cix;
				$m[$cix] = '';
				$ix++;
			}
			// add galaxy map link
			$rowx[] = $galaxymap . $uid;
			// create empty rows
			while($iy<$h){
				$ciy = $border['max']['y'] - $iy;
				$rowy[] = $ciy;
				$map[$ciy] = $m;
				$iy++;
			}
		/*
			Fill Border between points
		*/
		$anch = [];
		$i = 0;
		// add first also as last
		$cords[] = [
			'x' => $cords[0]['x'],
			'y' => $cords[0]['y']	
			];
		foreach($cords as $cc){
			$x = $cc['x'];
			$y = $cc['y'];
			$map[$y][$x] = $val;
		
				$anch[] = [
					'x' => $x,
					'y' => $y
					];
		
			if (count($anch)==2){
				$x1 = $anch[0]['x'];
				$x2 = $anch[1]['x'];
				$y1 = $anch[0]['y'];
				$y2 = $anch[1]['y'];
				$lx = absLong($x1,$x2);
				$ly = absLong($y1,$y2);
				if ( $ly OR $lx ){
					if ($lx>1 AND $ly==0){
						$z = min($x1,$x2);
						$e = max($x1,$x2);
						$y = $y1;
						while($z<$e){
							$map[$y][$z] = $val;
							$z++;
						}
					}
					if ($ly>1 AND $lx==0){
						$z = min($y1,$y2);
						$e = max($y1,$y2);
						$x = $x1;
						while($z<$e){
							$map[$z][$x] = $val;
							$z++;
						}
					}
					
				}
				array_shift($anch);
			}
			$i++;
		}
		/*
			Flood fill 4x
   			algorythm idea:
   			source: https://takeuforward.org/graph/flood-fill-algorithm-graphs/
		*/
		$stack = [];
		// find first free point inside
			$x = $cords[0]['x'];
			$y = $cords[0]['y'];
		while(true){
			if ($map[$y][$x]){
				$x++;
			}else{
				break;
			}
		}
		$stack[] = [
			'x' => $x,
			'y' => $y	
		];
		$map[$y][$x] = $val;
		while(count($stack) ){
			foreach($stack as $s){
				$x = $s['x'];
				$y = $s['y'];
				array_shift($stack);
				if ( empty($map[$y][$x-1]) ){
					$stack[] = [
						'x' => $x-1,
						'y' => $y	
					];
					$map[$y][$x-1] = $val;
				}
				if ( empty($map[$y][$x+1]) ){
					$stack[] = [
						'x' => $x+1,
						'y' => $y	
					];
					$map[$y][$x+1] = $val;
				}
				if ( empty($map[$y+1][$x]) ){
					$stack[] = [
						'x' => $x,
						'y' => $y+1	
					];
					$map[$y+1][$x] = $val;
				}
				if ( empty($map[$y-1][$x]) ){
					$stack[] = [
						'x' => $x,
						'y' => $y-1	
					];
					$map[$y-1][$x] = $val;
				}
			}
		}
		/*
			Borders
		*/
		foreach($map as $y => $va){
			foreach($va as $x => $v){
				if ($v==$val){
					if (empty($map[$y][$x-1])){
						$map[$y][$x-1] = $valborder;
					}
					if (empty($map[$y][$x+1])){
						$map[$y][$x+1] = $valborder;
					}
					if (empty($map[$y+1][$x])){
						$map[$y+1][$x] = $valborder;
					}
					if (empty($map[$y-1][$x])){
						$map[$y-1][$x] = $valborder;
					}
				}
			}
		}
		// add extra cross border
		foreach($cords as $cc){
			$x = $cc['x'];
			$y = $cc['y'];
			if (empty($map[$y-1][$x-1])){
				$map[$y-1][$x-1] = $valborder;
			}
			if (empty($map[$y-1][$x+1])){
				$map[$y-1][$x+1] = $valborder;
			}
			if (empty($map[$y+1][$x-1])){
				$map[$y+1][$x-1] = $valborder;
			}
			if (empty($map[$y+1][$x+1])){
				$map[$y+1][$x+1] = $valborder;
			}
		}
		// add systems
		foreach($system as $s) {
			$y=$s['y'];
			$x=$s['x'];
			$map[$y][$x] = $valsystem;
		}

		// prepare CSV
			$csv = [];
			// add first row
			$csv[] = $rowx;
			$x=0;
			foreach($rowy as $v){
				$m = $map[$v];
				// add first col
				array_unshift($m, $v);
				$csv[] = $m;
			}
		flieDownloadCSV( $csv, $sectors['name'][$uid] );
	}
}

/*

	HTML (show website)

*/
	$c_background = '#456';
	$c_subbackground = '#567';
	$c_base = '#fff';
	$c_color = '#0bf';
	$c_subcolor = '#ccc';
	$c_acclink = '#345';
	
	$links = array(
		'css' => 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css',
		'fontawesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
		'jquery' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js'
	);

	$meta=array(
		"fbapi" => '',
		//"tags" => '',
		"footage" => '<a class="btn btn-xs btn-primary" href="https://github.com/exa18/swcGalaxyMapSearcher"><i class="fab fa-github"></i> Learn more</a>',
		"download" => 'swcGalaxyMapSearcherTEMPLATE.xlsx',
		"img" => './_img/swcmapgen.png',
		"desc" => 'prepare sector map to paste in sheet<br><small>tab delimited and openable as text, despite CSV</small>',
		"site" => 'swcSectorMap'
	);

	$url = _BASEURI . $_SERVER['REQUEST_URI'];
	
	$html_desc = strip_tags(str_replace("<br>",", ",$meta['desc']));
	$meta["title"] = $meta['site'] . ' : ' . _VERSION ;
/* 	if ($meta["tags"]) {
		$tags = ' ( #'.implode(' #',$meta["tags"]) . ' )';
	}
*/
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
<meta property="og:description" content="'.$html_desc. '"/>
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
	.list-box.list-group-item span {display:block;font-size:70%;}
	.list-box.list-group-item {padding:0.5em;line-height:1.35;border: 1px solid <?=$c_subcolor?>;border-radius:0;}
	.inputfield{margin:1.5em auto;}
	.btn{border:0;}
	#search_area{height: calc(100vh / 2);overflow-y: auto;}
	#mySearchable {margin-bottom:2.5em;height:3em;text-align:center;float: unset;margin: 0 auto;border-radius:0;}
</style>
<script>
	var swc = {
		search_input : 'mySearchable',
		search_target : 'search_area',
		search_element : 'list-group-item'
	};
	function mySearchableList() {
		var input, filter, ul, li, a, i;
		input = document.getElementById(swc.search_input);
		filter = input.value.toUpperCase();
		ul = document.getElementById(swc.search_target);
		li = ul.getElementsByClassName("list-box");

		for (i = 0; i < li.length; i++) {

				allow = li[i].innerHTML.toUpperCase().indexOf(filter);

			if (allow > -1) {
				li[i].style.display = "";
			} else {
				li[i].style.display = "none";
			}
		}
	}
</script>
<?php
	$api = array_reverse(explode('/',_API));
	$api = $api[1];

	echo '</head><body><div class="container"><div class="row"><div>';
	echo '<div class="swc_logo"><div class="swc_img"><img src="' . $meta['img'] . '" alt="' . $meta['site'] . '" /></div><h6><b>api ' . $api .'</b><br>'. ($updateinterval - min($lastupdatesectors,$lastupdatesystems)) . ' days till update '.$meta['footage'].'</h6></div>';
	echo '<div class="topic"><h1>'.$meta['site'].'&nbsp;&nbsp;<span class="badge">'. _VERSION . '</span><br><small>'.$meta['desc'].'</small></h1></div>';
	echo '<a href="'.$meta['download'].'" type="button" class="btn btn-success btn-sm btn-block">Download Template to work with</a>';
	echo '<div class="inputfield"><input type="text" class="form-control" id="mySearchable" onkeyup="mySearchableList()" placeholder="Search . . .">
	</div>';
	echo '<div id="search_area">';
	
		$list = $sectors['name'];
		asort($list);
		foreach($list as $k => $v) {
			$i = $sectors['faction'][$k];
			$controlled = $factions['name'][$i];
			echo '<a href="?getmap='.$k.'" class="list-box list-group-item">' . $v . '<span class="faction"'.($controlled?'':' style="display:none"').'>/' . $controlled . '/</span></a>';

		}
	echo '</div>';
	flush();

echo '</div></div></div></body></html>';
