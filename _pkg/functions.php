<?php
function removeSpaces($text){
		return preg_replace('/\s+/', '', $text);
}
function htmlGet($uri){
	$htmlContent = @file_get_contents($uri);
	if ($htmlContent!==false){
		$htmlContent = mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8');
	}
	return $htmlContent;
}
function tableGet($htmlContent){
/*
	source: https://openclassrooms.com/forum/sujet/dom-conversion-html-table-to-array-php
*/
	if ($htmlContent) {
		$e=0;
		$dom = new DOMDocument();
		$html = $dom->loadHTML($htmlContent);
		$dom->preserveWhiteSpace = false;
		
		$tables = $dom->getElementsByTagName('table');
		$tableexist = $tables->length;
		$table = array();
		
		while ($e < $tableexist) {
			//get all rows from the table
			$rows = $tables->item($e)->getElementsByTagName('tr');
			
			// get each column by tag name
			$cols = $rows->item($e)->getElementsByTagName('th');
			$row_headers = NULL;
			foreach ($cols as $node) {
			    //print $node->nodeValue."\n";
			    $row_headers[] = $node->nodeValue;
			}
		
			//get all rows from the table
			//$rows = $tables->item($e)->getElementsByTagName('tr');
			foreach ($rows as $row) {
			    // get each column by tag name
			    $cols = $row->getElementsByTagName('td');
			    $row = array();
	
			    $i=0;
			    $id=0;
			    foreach ($cols as $node) {
			        # code...
			        //print $node->nodeValue."\n";
			        if($row_headers===NULL) {
			            $row[] = $node->nodeValue;
			        }else{
			            $row[str_replace(' ','',$row_headers[$i])] = $node->nodeValue;
						$i++;
					}
			    	$h = $node->getElementsByTagName('a');
					if ( $h->length ) {
							$h = $h->item(0)->getAttribute('href');
							preg_match('/.*\&(.*)\=(\d*)/', $h, $h);
							$row[ $h[1] ] = $h[2];
					}
			    }
			    if (count($row)) {
			        foreach($row as $kr=>$kv){
			            if (strrpos($kr,'ID')!==FALSE) {
			                $id=$kv;
			                unset($row[$kr]);
			                break;
			            }
			        }
			        if ($id AND $e==0){
			            $table[$e][$id] = $row;
			        }else{
			            $table[$e][]= $row;
					}
			    }
			}
		    $e++;
		}
	}
	return $table;
}

?>