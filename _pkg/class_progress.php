<?php

class progressBar {
	
		var  $objID;
		var  $total;
		var  $status;
		var  $delay;
		var  $objSufix;
	
	function __construct($total,$status='',$objSufix = '_status',$delay=300,$objID='progress')
	{
		$this->objID = $objID;
		$this->objSufix = $objSufix;
		$this->total = $total;
		$this->status = $status;
		$this->delay = $delay;
	}
	
	function showBuild($s,$a=''){
		if ($s) {
			return $s.'=document.getElementById("'.$s.'");'.$s.'.innerHTML="<div>'.($a ? $a : '&nbsp;').'</div>";'.$s.'.style.display="block";'.$s.'.style.opacity=1;';
		}
	}
	function show(){
		$m = $this->showBuild($this->objID);
		$ms = $this->showBuild($this->objID.$this->objSufix, $this->statusBuild(0));
		echo '<script language="javascript">'.$m.($this->status ? $ms : '').'</script>';
		flush();
	}
	function hideBuild($s){
		if ($s) {
			return $s.'=document.getElementById("'.$s.'");'.$s.'.style.opacity=0;'.$s.'.style.display="none";';
		}
	}
	function hide(){
		$m = $this->hideBuild($this->objID);
		$ms = $this->hideBuild($this->objID.$this->objSufix);
		echo '<script language="javascript">'.$m.$ms.'</script>';
		flush();
	}
	function statusBuild($i){
		$s = $this->status;
		if($s){
			$p = $i . '/' . $this->total;
			return str_replace("%s",$p,$s);
		}
	}
    function update($i){
		$percent = intval($i/$this->total * 100);
		$st = 'document.getElementById("'.$this->objID.$this->objSufix.'").innerHTML="<div>'.$this->statusBuild($i).'</div>";';
		echo '<script language="javascript">
		document.getElementById("'.$this->objID.'").innerHTML="<div style=\"width:'.$percent.'%;\">&nbsp;</div>";'.($this->status ? $st : '').'
		</script>'; 
		flush();
		}
}

?>