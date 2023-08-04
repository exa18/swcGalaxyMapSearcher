<?php
/*
	Settings
*/
$url = explode(":",$_SERVER["SCRIPT_URI"]);
$url = $url[0] . "://". $_SERVER["HTTP_HOST"];
define('_BASEURI', $url);
define('_VERSION', '1.1');
define('_API',"https://www.swcombine.com/ws/v2.0/");
define('_ROOT', __DIR__ . '/' );
define('_DATA',"_db/");
define('_GALAXYMAP',"https://www.swcombine.com/rules/?Galaxy_Map&sectorID=");
define('_PATH',[
	'sectors' => _DATA . "sectors",
	'systems' => _DATA . "systems",
    'factions' => _DATA . "factions",
]);
define('_DB',[
		'sectors' => _API . "galaxy/sectors/",
		'systems' => _API . "galaxy/systems/"
]);
?>