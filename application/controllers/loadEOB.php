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
		$check{'name'} = 'develope check';
		$check{'claims'} = array();
		
		if($this->getE($a[$ln],0) == 'ISA'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'GS'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'ST'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'BPR'){
			$check{'date'} = $this->getE($a[$ln],16);
			$check{'amount'} = $this->getE($a[$ln],2);
			$ln++;
		}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'TRN'){
			$check{'number'} = $this->getE($a[$ln],2);
			$ln++;
		}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'REF'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'REF'){$ln++;}
		if($this->getE($a[$ln],0) == 'DTM'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'N1'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'N3'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'N4'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'PER'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'N1'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'N4'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'REF'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'LX'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'CLP'){
			$claim = array();
			$claim{'echo'} = $this->getE($a[$ln],1);
			$claim{'tcn'} = $this->getE($a[$ln],7);
			$claim{'chargeAmount'} = $this->getE($a[$ln],3);
			$claim{'paidAmount'} = $this->getE($a[$ln],4);
			$claim{'status'} = $this->getE($a[$ln],2);

			$check{'claims'}[] = $claim;
			$ln++;
		}else{$this->badseg($ln);}

		if($this->getE($a[$ln],0) == 'IEA'){$ln++;}else{$this->badseg($ln);}
		$line = $a[$ln]; echo "$line<br/>";

		#$db1 = $this->load->database('dentrix',true);
		#$db2 = $this->load->database('dw',true);

		#$json = json_encode($list);
		#echo "$json";
		$checkName = $check{'name'};
		$checkDate = $check{'date'};
		$checkAmount = $check{'amount'};
		$checkNumber = $check{'number'};
		$checkClaims = $check{'claims'};
		$claim1 = $check{'claims'}[0];
		$claim1echo = $claim1{'echo'};
		$claim1tcn = $claim{'tcn'};
		$claim1chargeAmount = $claim{'chargeAmount'};
		$claim1paidAmount = $claim{'paidAmount'};
		$claim1status = $claim{'status'};
		echo "-------------------<br/>";
		echo "Check Name: $checkName<br/>";
		echo "Check Date: $checkDate<br/>";
		echo "Check Amount: $checkAmount<br/>";
		echo "Check Number: $checkNumber<br/>";
		#echo "Check Claims: $checkClaims<br/>";
		echo "Claim1 Echo: $claim1echo<br/>";
		echo "Claim1 tcn: $claim1tcn<br/>";
		echo "Claim1 chargeAmount: $claim1chargeAmount<br/>";
		echo "Claim1 paidAmount: $claim1paidAmount<br/>";
		echo "Claim1 status: $claim1status<br/>";
		echo "-------------------<br/>";
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
