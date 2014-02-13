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
			while($this->getE($a[$ln],0) == 'CLP'){
				$claim = array();
				# Claim elements that may no be set
					$claim{'rarCode'} = '';
					$claim{'rateCode'} = '';

				$claim{'echo'} = $this->getE($a[$ln],1);
				$claim{'tcn'} = $this->getE($a[$ln],7);
				$claim{'chargeAmount'} = $this->getE($a[$ln],3);
				$claim{'paidAmount'} = $this->getE($a[$ln],4);
				$claim{'status'} = $this->getE($a[$ln],2);
				$ln++;
				while($this->getE($a[$ln],0) == 'CAS'){
					if($this->getE($a[$ln],1) == 'CO'){
						$claim{'adjustmentCode'} = $this->getE($a[$ln],2);
					}
					$ln++;
				}	
				if($this->getE($a[$ln],0) == 'NM1'){
					$claim{'lastName'} = $this->getE($a[$ln],3);
					$claim{'firstName'} = $this->getE($a[$ln],4);
					$claim{'medicaid'} = $this->getE($a[$ln],9);
					$ln++;
				}else{$this->badseg($ln);}
				while($this->getE($a[$ln],0) == 'NM1'){$ln++;}
				while($this->getE($a[$ln],0) == 'MIA'){$ln++;}
				if($this->getE($a[$ln],0) == 'MOA'){
					$claim{'rarCode'} = $this->getE($a[$ln],3);
					$ln++;
				}
				while($this->getE($a[$ln],0) == 'REF'){
					if($this->getE($a[$ln],1) == '9A'){
						$claim{'rateCode'} = $this->getE($a[$ln],2);
					}
					$ln++;
				}
				if($this->getE($a[$ln],0) == 'DTM'){
					$claim{'serviceDate'} = $this->getE($a[$ln],2);
					$ln++;
				}else{$this->badseg($ln);}
				while($this->getE($a[$ln],0) == 'DTM'){$ln++;}
				while($this->getE($a[$ln],0) == 'PER'){$ln++;}
				while($this->getE($a[$ln],0) == 'AMT'){$ln++;}
				if($this->getE($a[$ln],0) == 'SVC'){
					while($this->getE($a[$ln],0) == 'SVC'){
						$claimLine = array();

						$claimLine{'servAdj'} = ""; # may not be set
						$claimLine{'capAdd'} = ""; # may not be set
						$claimLine{'overPay'} = ""; # may not be set
						$claimLine{'apg'} = ""; # may not be set
						$claimLine{'maxAllowed'} = ""; # may not be set
						$claimLine{'apgPaid'} = ""; # may not be set
						$claimLine{'blendPaid'} = ""; # may not be set
						$claimLine{'apgWeight'} = ""; # may not be set
						$claimLine{'apgPercent'} = ""; # may not be set
						$claimLine{'servRARC'} = ""; # may not be set

						$claimLine{'adaCode'} = explode(":",$this->getE($a[$ln],1));
						$claimLine{'lineCharge'} = $this->getE($a[$ln],2);
						$claimLine{'linePaid'} = $this->getE($a[$ln],3);
						$ln++;
						if($this->getE($a[$ln],0) == 'DTM'){
							$claimLine{'serviceDate'} = $this->getE($a[$ln],2);
							$ln++;
						}else{$this->badseg($ln);}
						while($this->getE($a[$ln],0) == 'CAS'){
							if($this->getE($a[$ln],1) == 'PR'){
								$claimLine{'servAdj'} = $this->getE($a[$ln],2);
							}
							if($this->getE($a[$ln],1) == 'OA'){
								$claimLine{'servAdj'} = $this->getE($a[$ln],2);
							}
							if($this->getE($a[$ln],1) == 'CO'){
								if($this->getE($a[$ln],2) == '94'){
									$claimLine{'capAdd'} = $this->getE($a[$ln],3);
								}
								if($this->getE($a[$ln],2) == '45'){
									$claimLine{'overPay'} = $this->getE($a[$ln],3);
								}
								if($this->getE($a[$ln],5) == '45'){
									$claimLine{'overPay'} = $this->getE($a[$ln],6);
								}
								if($this->getE($a[$ln],2) <> '45' && $this->getE($a[$ln],2) <> '94'){
									$claimLine{'servAdj'} = $this->getE($a[$ln],2);
								}
							}
							$ln++;
						} # end while CAS
						while($this->getE($a[$ln],0) == 'REF'){
							if($this->getE($a[$ln],1) == '1S'){
								$claimLine{'apg'} = $this->getE($a[$ln],2);
							}
							$ln++;
						}
						while($this->getE($a[$ln],0) == 'AMT'){
							if($this->getE($a[$ln],1) == 'B6'){
								$claimLine{'maxAllowed'} = $this->getE($a[$ln],2);
							}
							if($this->getE($a[$ln],1) == 'ZK'){
								$claimLine{'apgPaid'} = $this->getE($a[$ln],2);
							}
							if($this->getE($a[$ln],1) == 'ZL'){
								$claimLine{'blendPaid'} = $this->getE($a[$ln],2);
							}
							$ln++;
						}
						while($this->getE($a[$ln],0) == 'QTY'){
							if($this->getE($a[$ln],1) == 'ZK'){
								$claimLine{'apgWeight'} = $this->getE($a[$ln],2);
							}
							if($this->getE($a[$ln],1) == 'ZL'){
								$claimLine{'apgPercent'} = $this->getE($a[$ln],2);
							}
							$ln++;
						}
						while($this->getE($a[$ln],0) == 'LQ'){
							if($this->getE($a[$ln],1) == 'HE'){
								$claimLine{'servRARC'} = $this->getE($a[$ln],2);
							}
							$ln++;
						}
						$claim{'claimLines'}[] = $claimLine;
					}
				} # END is SVC loop
				$check{'claims'}[] = $claim;
			}
		}else{$this->badseg($ln);} # END CLP Loop

		if($this->getE($a[$ln],0) == 'PLB'){$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'SE'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'GE'){$ln++;}else{$this->badseg($ln);}
		if($this->getE($a[$ln],0) == 'IEA'){$ln++;}else{$this->badseg($ln);}

		#$db1 = $this->load->database('dentrix',true);
		#$db2 = $this->load->database('dw',true);

		#$json = json_encode($list);
		#echo "$json";

		echo "-------------------<br/>";
		$james = array_keys($check{'claims'}[0]);
		print_r($james);echo "<br/>";
		$claimList = $check{'claims'};
		foreach($claimList as $cl){
			foreach($cl as $cc){
				echo "$cc :: ";
			}
			echo "<br/>";
			foreach($cl{'claimLines'} as $line){
				foreach($line as $ele){
					echo "$ele :: ";
				}
				echo "<br/>";
			}
			echo "<br/>";
		}
		echo "-------------------<br/>";

	}
	function badseg($x){
		echo "bad seg at $x<br/>";
	}
	function getE($line,$i){
		$r = explode("*",$line);
		if(count($r)>$i){return $r[$i];}else{return false;}
	}

}
