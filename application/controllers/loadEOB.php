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
		$mycheck = $this->readEOBfile($a);

		$number = $mycheck{'number'};
		$date   = $mycheck{'date'};
		$myDate = substr($date,0,4) . '-' . substr($date,4,2) . '-' . substr($date,6,2);
		$amount = $mycheck{'amount'};
		$status = $mycheck{'status'};
		$claims = $mycheck{'claims'};

		echo "-------------------<br/>";
		echo "number: $number<br/>";
		echo "mydate: $myDate<br/>";
		echo "amount: $amount<br/>";
		echo "status: $status<br/>";
		echo "-------------------<br/>";

		#$db1 = $this->load->database('dentrix',true);
		$db2 = $this->load->database('dw',true);
		$rs = $db2->query("select checkNumber from checks where checkNumber = '$number'");
		$ct = 0; if($rs){ foreach($rs->result_array() as $row){echo "add $ct"; $ct++; } }
		if($ct == 0){
			$rs = $db2->query("
				insert into checks (checkNumber,checkDate,checkAmount,status)
				values ('$number','$myDate',$amount,'$status')
			");
			foreach($claims as $claim){
				$tcn = $claim{'tcn'};
				$echo = $claim{'echo'};
				$lastName = $claim{'lastName'};
				$firstName = $claim{'firstName'};
				$medicaid = $claim{'medicaid'};
				$rateCode = $claim{'rateCode'};
				$chargeAmount = $claim{'chargeAmount'};
				$paidAmount = $claim{'paidAmount'};
				$status = $claim{'status'};
				$adjustmentCode = $claim{'adjustmentCode'};
				$rarCode = $claim{'rarCode'};
				$serviceDate = $claim{'serviceDate'};
				$rs1 = $db2->query("
					insert into claims (
						checkNumber,
						tcn,
						echo,
						lastName,
						firstName,
						medicaid,
						rateCode,
						chargeAmount,
						paidAmount,
						Xstatus,
						adjustmentCode,
						rarCode,
						serviceDate
					) values (
						'$number',
						'$tcn',
						'$echo',
						'$lastName',
						'$firstName',
						'$medicaid',
						'$rateCode',
						$chargeAmount,
						$paidAmount,
						'$status',
						'$adjustmentCode',
						'$rarCode',
						'$serviceDate'
					)
				");
			}
		}

		#$json = json_encode($list);
		#echo "$json";

	}
	public function readEOBfile($a){
		#Function takes EOB file array and converts to EOB object
		$ln = 0;
		$check{'status'} = 'OK';
		$check{'claims'} = array();
		
		if($this->getE($a[$ln],0) == 'ISA'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO'; $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'GS'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'ST'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'BPR'){
			$check{'date'} = $this->getE($a[$ln],16);
			$check{'amount'} = $this->getE($a[$ln],2);
			$ln++;
		}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'TRN'){
			$check{'number'} = $this->getE($a[$ln],2);
			$ln++;
		}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'REF'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'REF'){$ln++;}
		if($this->getE($a[$ln],0) == 'DTM'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'N1'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'N3'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'N4'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'PER'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'N1'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'N4'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'REF'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'LX'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'CLP'){
			while($this->getE($a[$ln],0) == 'CLP'){
				$claim = array();
				$claim{'rarCode'} = ''; # Claim element may no be set
				$claim{'rateCode'} = '';# Claim element may no be set
				$claim{'adjustmentCode'} = ''; # Claim element may not be set

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
				}else{$this->badseg($ln); $check{'status'} = 'NO';}
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
				}else{$this->badseg($ln); $check{'status'} = 'NO';}
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

						$tempAda = explode(":",$this->getE($a[$ln],1));
						$claimLine{'adaCode'} = $tempAda[1];
						$claimLine{'lineCharge'} = $this->getE($a[$ln],2);
						$claimLine{'linePaid'} = $this->getE($a[$ln],3);
						$ln++;
						if($this->getE($a[$ln],0) == 'DTM'){
							$claimLine{'serviceDate'} = $this->getE($a[$ln],2);
							$ln++;
						}else{$this->badseg($ln); $check{'status'} = 'NO';}
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
		}else{$this->badseg($ln); $check{'status'} = 'NO';} # END CLP Loop

		if($this->getE($a[$ln],0) == 'PLB'){$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'SE'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'GE'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		if($this->getE($a[$ln],0) == 'IEA'){$ln++;}else{$this->badseg($ln); $check{'status'} = 'NO';}
		return $check;
	}#END readEOBfile
	function badseg($x){
		echo "bad seg at $x<br/>";
	}
	function getE($line,$i){
		$r = explode("*",$line);
		if(count($r)>$i){return $r[$i];}else{return false;}
	}

}
