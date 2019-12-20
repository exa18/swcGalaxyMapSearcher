<?php
$db_host="localhost";
$db_user="cenkier_swc";
$db_pass="pNEh7X-6JPWAFpzmTeHabCFDxy";
$db_db="cenkier_swc";

$url = explode(":",$_SERVER["SCRIPT_URI"]);
$url = $url[0] . "://". $_SERVER["HTTP_HOST"];

define('_DB_MAIN', "$db_host|$db_user|$db_pass|$db_db");
define('_DB_SALT', 'LPijEJaiua0jwG8DNpxZ3uT0dh');
define('_BASEURI', $url);
define('_VERSION', '20191220');

/*
	start
*/
require_once "db.php";
require_once "session.php";
require_once "functions.php";

$session = Session::getInstance();
$session->swcgms_version = _VERSION;
//$db = new mydb( _DB_MAIN );

?>