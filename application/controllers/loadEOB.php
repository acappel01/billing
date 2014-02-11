<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LoadEOB extends CI_Controller {

	public function index(){
		echo "Default return from get eob controller controller";
		#$this->load->view('name');
	}

	public function go(){

		echo "getting ready to load EOB...<br/>";
		$file = 'EOB/a.x12';
		$fh = fopen($file,'r');
		$EOB = fread($fh,filesize($file));
		$a = explode("~",$EOB);
		$ln = 0;
		
		$line = $a[$ln]; echo "$line<br/>";
		if($this->getE($a[$ln],0) == 'ISA'){$ln++;}else{$this->badseg($ln);}

		$line = $a[$ln]; echo "$line<br/>";
		if($this->getE($a[$ln],0) == 'IEA'){$ln++;}else{$this->badseg($ln);}

		#$db1 = $this->load->database('dentrix',true);
		#$db2 = $this->load->database('dw',true);

		#$json = json_encode($list);
		#echo "$json";
		echo "load complete...";
	}
	function badseg($x){
		echo "bad seg at $x<br/>";
	}
	function getE($line,$i){
		$r = explode("*",$line);
		if(count($r)>$i){return $r[$i];}else{return false;}
	}

}
