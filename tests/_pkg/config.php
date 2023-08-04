<?php
$db_host="localhost";
$db_user="cenkier_swcgs";
$db_pass="KWxcdMAXp43tjjz9yVjZ4vdU";
$db_db="cenkier_swcgs";

$url = explode(":",$_SERVER["SCRIPT_URI"]);
$url = $url[0] . "://". $_SERVER["HTTP_HOST"];

define('_DB_MAIN', "$db_host|$db_user|$db_pass|$db_db");
define('_DB_SALT', 'd39kQqyZNFAgGLWGCKNm95KR');
define('_BASEURI', $url);
define('_VERSION', '20210914');

/*
	start
*/
require_once "db.php";
require_once "session.php";
require_once "functions.php";

$session = Session::getInstance();
$session->swcgms_version = _VERSION;

?>