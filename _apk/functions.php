<?php
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
function swcMakeUid($uid,$code){
		return $code . "%3A" . $uid;
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

?>