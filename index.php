<?
require_once "_pkg/config.php";
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

	
	$swcuri = 'https://www.swcombine.com/rules/?Galaxy_Map';
	
	$map_level = array(
		0 => "sectorID",
		1 => "systemID",
		2 => "planetID",
		3 => "surface"
	);
?>

<html>
<head>
	<?=trim('
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
	')?>
	<style type="text/css">
		@import url('https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap&subset=latin-ext');
		*{margin:0;padding:0;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}
		body{background:<?=$c_background?>;font-family:'Open Sans',Arial,Helvetica,Sans-serif,Verdana,Tahoma;width:100%;height:100%}
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
		h1{color:<?=$c_base?>;font-size:24px;font-weight:400;text-align:center;margin-top:2rem;line-height:1.5}
		h1 a{color:<?=$c_color?>;font-size:16px}
		.accordion{width:100%;max-width:360px;margin:30px auto 20px;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;display:flex}
		.accordion li:last-child .link{border-bottom:0}
		.accordion li{opacity:unset;-webkit-transition:all .4s ease;-o-transition:all .4s ease;transition:all .4s ease;background-color:transparent}
		.accordion li:hover{background-color:<?=$c_color?>}
		.accordion li:hover a{color:<?=$c_base?>}
		#progress{height:8px;line-height:6px;width:100%;border:1px solid <?=$c_acclink?>;}
		#progress > div {width:0;background-color:<?=$c_color?>;}
	</style>
</head>
<body>

<div class="container">
	<div class="row">
    <div>
<?php
	echo '<div class="topic"><h1>'.$meta['site'].'&nbsp;&nbsp;<span class="badge">'. _VERSION .'</span><br><small>'.$meta['desc'].'</small></h1></div>';
	echo '<div id="progress" style="display:none;"><div>&nbsp;</div></div>';
	flush();
    
    /*
        Get all Sectors
    */
    $html = htmlGet($swcuri);
    preg_match_all('/href=\"(.*)\".*alt=\"(.*)\"\s/', $html, $sector);
    $u = $sector[1];
    $names = $sector[2];
    $sector=array();
    foreach($u as $k => $s){
        $name = $names[$k];
        preg_match('/(\d+)/', $s, $id);
        //echo "Sector: " . $name . " / " . $id[1] . " . ";
        $sector[$id[1]] = $name;
	}
	echo '<script language="javascript">$("#progress").show(300);</script>';
	flush();

	/*
		Search sector inside
		get: systemID
	*/
	$sector_total = count($sector);
	$system_total = 0;
	$planet_total = 0;
	$station_total = 0;
	$city_total = 0;

    $time = microtime(true);
	$i=0;
	$i_total=$sector_total;
	foreach($sector as $id => $sname){
	
		$level = 0;
	    $u = $swcuri . '&'.$map_level[$level] . '=' . $id;
	    $html = htmlGet($u);
	    $table = tableGet($html);
		
		$system[$id] = $table[0];
		$system_total += count($system[$id]);

		$percent = intval($i/$i_total * 100);
		echo '<script language="javascript">
		document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.'%;\">&nbsp;</div>";
		</script>'; 
		flush();

		//if ($percent>=100) { break; }
		$i++;
	}


	//	foreach($system as $sid => $sv){
	/*	
		
			$level = 1;
		    $u = $swcuri . '&'.$map_level[$level] . '=' . $sid;
		    $html = htmlGet($u);
		    $table = tableGet($html);
			
			$planet = $table[0];
			$station = $table[0];
			$planet_total += count($planet);
			$station_total += count($station);
		/*	
			foreach($planet as $pid => $pv){
				$level = 2;
			    $u = $swcuri . '&'.$map_level[$level] . '=' . $pid;
			    $html = htmlGet($u);
			    preg_match_all('/reg_pointCaption\((.*)\)/', $html, $surface);
			    $surface = $surface[1];
		        foreach($surface[1] as $v){
		            //echo $v . " // ";
				}
				$city_total += count($surface);
			}
		*/
	//	}

	echo '<script language="javascript">$("#progress").hide(200);</script>'; 
	flush();

	echo "Secotrs: " . $sector_total . "<br>";
	echo "Systems: " . $system_total . "<br>";
	echo "Planets: " . $planet_total . "<br>";
	echo "Stations: " . $station_total . "<br>";
	echo "Cities: " . $city_total . "<br>";
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
?>    
    

</div>
</div>
</div>
</body>
</html>