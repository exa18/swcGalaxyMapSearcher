<?php

class progressBar {
	
		var  $objID;
		var  $total;
		var  $status;
		var  $delay;
		var  $objSufix;
	
	function __construct($total,$status='',$objSufix = '_status',$objID='progress',$delay=300)
	{
		$this->objID = $objID;
		$this->objSufix = $objSufix;
		$this->total = $total;
		$this->status = $status;
		$this->delay = $delay;
	}
	public function show(){
		$m = $this->buildShow($this->objID);
		$ms = $this->buildShow($this->objID.$this->objSufix, $this->buildStatus(0));
		echo '<script language="javascript">'.$m.($this->status ? $ms : '').'</script>';
		flush();
	}
	public function hide(){
		$m = $this->buildHide($this->objID);
		$ms = $this->buildHide($this->objID.$this->objSufix);
		echo '<script language="javascript">'.$m.$ms.'</script>';
		flush();
	}
    public function update($i){
		$percent = intval($i/$this->total * 100);
		$st = 'document.getElementById("'.$this->objID.$this->objSufix.'").innerHTML="<div>'.$this->buildStatus($i).'</div>";';
		echo '<script language="javascript">
		document.getElementById("'.$this->objID.'").innerHTML="<div style=\"width:'.$percent.'%;\">&nbsp;</div>";'.($this->status ? $st : '').'
		</script>'; 
		flush();
	}
	
	private function buildShow($s,$a=''){
		if ($s) {
			return $s.'=document.getElementById("'.$s.'");'.$s.'.innerHTML="<div>'.($a ? $a : '&nbsp;').'</div>";'.$s.'.style.display="block";'.$s.'.style.opacity=1;';
		}
	}

	private function buildHide($s){
		if ($s) {
			return $s.'=document.getElementById("'.$s.'");'.$s.'.style.opacity=0;'.$s.'.style.display="none";';
		}
	}

	private function buildStatus($i){
		$s = $this->status;
		if($s){
			$p = $i . '/' . $this->total;
			return str_replace("%s",$p,$s);
		}
	}

}

?>