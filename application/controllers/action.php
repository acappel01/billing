<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Action extends CI_Controller {

	public function index(){
		echo "Default return from Action controller";
		#$this->load->view('name');
	}
#	public function getDB(){
#		$database = $this->load->database('default',true);
#		return $database;
#	}
	function getE($line,$i){
		$r = explode("*",$line);
		if(count($r)>$i){return $r[$i];}else{return false;}
	}
	public function readX12(){
		echo "getting ready to X12...<br/>";
		$file = 'X12/d.x12';
		$fh = fopen($file,'r');
		$data = fread($fh,filesize($file));
		$claims = explode("~",$data);
		echo "got file as array...<br/>";
		$ln = 0;
		$myTrans = array();
		#foreach($claims as $claim){ echo "$claim<br/>"; }

		# READ X12 FILE

		if($this->getE($claims[$ln],0)=='ISA'){
			$myTrans{'GSid'} = $this->getE($claims[$ln],13);
			$ln++;
		}else{ $this->xerror($claims,$ln); }

		if($this->getE($claims[$ln],0)=='GS'){ $ln++; }else{ $this->xerror($claims,$ln); }
		if($this->getE($claims[$ln],0)=='ST'){ 
			$myTrans{'STid'} = $this->getE($claims[$ln],2);
			$ln++;
		}else{ $this->xerror($claims,$ln); }
		if($this->getE($claims[$ln],0)=='BHT'){ $ln++; }else{ $this->xerror($claims,$ln); }
		if($this->getE($claims[$ln],0)=='NM1'){ $ln++; }else{ $this->xerror($claims,$ln); }
		if($this->getE($claims[$ln],0)=='PER'){ $ln++; }else{ $this->xerror($claims,$ln); }
		if($this->getE($claims[$ln],0)=='NM1'){ $ln++; }else{ $this->xerror($claims,$ln); }
		if($this->getE($claims[$ln],0)=='HL'){ $ln++; }else{ $this->xerror($claims,$ln); }
		if($this->getE($claims[$ln],0)=='NM1'){ $ln++; }else{ $this->xerror($claims,$ln); }
		if($this->getE($claims[$ln],0)=='N3'){ $ln++; }else{ $this->xerror($claims,$ln); }
		if($this->getE($claims[$ln],0)=='N4'){ $ln++; }else{ $this->xerror($claims,$ln); }
		if($this->getE($claims[$ln],0)=='REF'){ $ln++; }else{ $this->xerror($claims,$ln); }
		#LOOP through claims
		$myTrans{'claimList'} = array();
		while($this->getE($claims[$ln],0)=='HL'){
			$currentClaim = array();
			$currentClaim{'preAuth'} = '';
			$ln++;
			if($this->getE($claims[$ln],0)=='SBR'){ $ln++; }else{ $this->xerror($claims,$ln); }
			if($this->getE($claims[$ln],0)=='NM1'){
				$currentClaim{'first'} = $this->getE($claims[$ln],3);
				$currentClaim{'last'} = $this->getE($claims[$ln],4);
				$currentClaim{'medicaid'} = $this->getE($claims[$ln],9);
				$ln++;
			}else{ $this->xerror($claims,$ln); }
			if($this->getE($claims[$ln],0)=='DMG'){
				$currentClaim{'birthdate'} = $this->getE($claims[$ln],2);
				$currentClaim{'gender'} = $this->getE($claims[$ln],3);
				$ln++;
			}else{ $this->xerror($claims,$ln); }
			if($this->getE($claims[$ln],0)=='NM1'){ $ln++; }else{ $this->xerror($claims,$ln); }
			if($this->getE($claims[$ln],0)=='CLM'){
				$currentClaim{'echo'} = $this->getE($claims[$ln],1);
				$currentClaim{'billedAmt'} = $this->getE($claims[$ln],2);
				$ln++;
			}else{ $this->xerror($claims,$ln); }
			if($this->getE($claims[$ln],0)=='DTP'){
				$currentClaim{'claimDate'} = $this->getE($claims[$ln],3);
				$ln++;
			}else{ $this->xerror($claims,$ln); }
			if($this->getE($claims[$ln],0)=='REF'){
				$currentClaim{'preAuth'} = $this->getE($claims[$ln],2);
				$ln++;
			}#else{ $this->xerror($claims,$ln); }
			while($this->getE($claims[$ln],0)=='LX'){
				$claimLines = array();
				$ln++;
				if($this->getE($claims[$ln],0)=='SV3'){
					$claimLines{'adacode'} = $this->getE($claims[$ln],1);
					$claimLines{'lineAmt'} = $this->getE($claims[$ln],2);
					$ln++;
				}else{ $this->xerror($claims,$ln); }
				$currentClaim{'claimLines'}[] = $claimLines;
			}

			$myTrans{'claimList'}[] = $currentClaim;
		}
		#END claim loop
		if($this->getE($claims[$ln],0)=='SE'){ $ln++; }else{ $this->xerror($claims,$ln); }
		if($this->getE($claims[$ln],0)=='GE'){ $ln++; }else{ $this->xerror($claims,$ln); }
		if($this->getE($claims[$ln],0)=='IEA'){ $ln++; }else{ $this->xerror($claims,$ln); }
		echo "have claim file...<br/>";

		# END READ X12 FILE
		$this->showTransfile($myTrans);

	}
	public function showTransfile($thing){

		$GSid 	= $thing{'GSid'};
		$STid 	= $thing{'STid'};
		$claims = $thing{'claimList'};

		$db2 = $this->load->database('dw',true);

		$rs = $db2->query("select gsId from sentClaims where gsId = '$GSid'");
		$ct = 0;
		if($rs){
			foreach($rs->result_array() as $row){
				$ct++;
			}
		}
		if($ct == 0){
			foreach($claims as $claim){
				$first = $claim{'first'}; $last = $claim{'last'}; $medicaid = $claim{'medicaid'};
				$birth = $claim{'birthdate'};
				$birthdate = substr($birth,0,4).'-'.substr($birth,4,2).'-'.substr($birth,6,2);
				$gender = $claim{'gender'}; $echo = $claim{'echo'}; $billedAmt = $claim{'billedAmt'};
				$clmdate = $claim{'claimDate'};
				$claimdate = substr($clmdate,0,4).'-'.substr($clmdate,4,2).'-'.substr($clmdate,6,2);
				$preauth = $claim{'preAuth'};
				$rs = $db2->query("insert into sentClaims (
						gsId, stId, firstName, lastName, medicaid, birthdate,
						gender, echo, billedAmt, claimDate, preAuth
					) values (
						'$GSid', '$STid', '$first', '$last', '$medicaid', '$birthdate',
						'$gender', '$echo', $billedAmt, '$claimdate', '$preauth'
					)
				");
				#drop claim lines
				$claimLines = $claim{'claimLines'};
				foreach($claimLines as $line){
					$adacode = $line{'adacode'};
					$lineAmt = $line{'lineAmt'};
					$rs = $db2->query("insert into sentClaimLines (
							gsId,stId,echo,adacode,lineAmt
						) values (
							'$GSid','$STid','$echo','$adacode',$lineAmt
						)
					");
				}
			}
		}


		
		echo "-----------------<br/>";
		echo "printing Trans<br/>";
		echo "GSid is $GSid<br/>";
		echo "STid is $STid<br/>";
		echo "-----------------<br/>";
		#echo "first=$first, last=$last, medicaid=$medicaid, birthdate=$birthdate, gender=$gender<br/>";
		#echo "echo=$echo, billedAmt=$billedAmt, claimdate=$claimdate, preauth=$preauth<br/>";
		#echo "adacode=$adacode, lineAmt=$lineAmt<br/>";
		echo "-----------------<br/>";

	}
	public function xerror($claims,$ln){
		$x = $claims[$ln];
		$y = $claims[$ln-1];
		echo "privous segment<br/>$y<br/>";
		echo "bad segment at $ln<br/>";
		echo "$x<br/>";
		echo "--------------<br/>";
	}
	public function getapi(){
		$mydate = date('Y-m-d');
		$mytime = date('H:i');
		$a = file_get_contents('http://data.bter.com/api/1/tickers');
		$b = json_decode($a,true);
		$keys = array_keys($b);
		$db = $this->load->database('ticker',true);
		$rs = $db->query("insert into bter (lineDate,lineTime) values ('$mydate','$mytime')");
		foreach($keys as $line){
			$average = $b{$line}{'avg'};
			$pairs = explode('_',$line);
			$topC = $pairs[0];
			$botC = $pairs[1];
			if( $botC == 'btc' ){
				$rs = $db->query("update bter set $topC=$average where lineDate='$mydate' and lineTime = '$mytime'");
			}
		}
		# get btc price
		$jsonbtc = file_get_contents('https://coinbase.com/api/v1/prices/sell');
		$z = json_decode($jsonbtc,true);
		$btc_price = $z{'amount'};
		$rs = $db->query("update bter set btc=$btc_price where lineDate='$mydate' and lineTime = '$mytime'");
		# end btc price
		echo "data loaded $mydate $mytime\n";
		#if($rs){ foreach($rs->result_array() as $row){ $claimid = $row['CLAIMID']; } }
	}
	# Function to run a query and echo a json object
	public function add2wh(){
		$db2 = $this->load->database('dw',true);
# Check table for claim
		$rs = $db2->query("select ClaimID from myclaims where ClaimID = 1973122");
		$ct = 0;
		if($rs){
			foreach($rs->result_array() as $row){
				$ct++;
			}
		}
# end check
		echo "checking table $ct";
	}
	public function getList(){
		#putenv('FREETDSCONF=/usr/local/etc/freetds.conf');
		#$db1 = $this->getDB();

		$file = 'query/ProdClaim.sql';
		$fh = fopen($file,'r');
		$query = fread($fh,filesize($file));

		$db1 = $this->load->database('dentrix',true);
		$db2 = $this->load->database('dw',true);

		$headings = array(
			'ClaimID',
			'claimDate',
			'claimAmt',
			'PATID',
			'lastName',
			'firstName',
			'subscribID',
			'Birthdate',
			'sex',
			'insurance',
			'fClass',
			'procID',
			'procDate',
			'lineAmt',
			'code',
			'clinic'
		);
		$list[0] = $headings;

		$rs = $db1->query($query);
		if($rs){
			foreach($rs->result_array() as $row){
				$claimid = $row['CLAIMID'];
				$claimdate = $row['claimDate'];
				$claimamt = $row['claimAmt'];
				$patid = $row['PATID'];
				$lastname = $row['lastName'];
				$firstname = $row['firstName'];
				$subscribid = $row['subscribID'];
				$medicaid = $row['medicaid'];
				$birthdate = $row['Birthdate'];
				$sex = $row['sex'];
				$insurance = $row['insurance'];
				$fclass = $row['fClass'];
				$procid = $row['procID'];
				$procdate = $row['procDate'];
				$lineamt = $row['lineAmt'];
				$code = $row['code'];
				$clinic = $row['clinic'];
				$list[] = array(
					$claimid,
					$claimdate,
					$claimamt,
					$patid,
					$lastname,
					$firstname,
					$subscribid,
					$birthdate,
					$sex,
					$insurance,
					$fclass,
					$procid,
					$procdate,
					$lineamt,
					$code,
					$clinic,
				);

# Check table for claim
		$rs1 = $db2->query("select ClaimID from myclaims where ClaimID = $claimid and procID = $procid");
		$ct = 0;
		if($rs1){ foreach($rs1->result_array() as $row1){ $ct++; } }
		if($ct == 0){
			# ADD RECORD TO MY Warehouse
				$rs2 = $db2->query("
					insert into myclaims (
						ClaimID,
						claimDate,
						claimAmt,
						PATID,
						lastName,
						firstName,
						subscribID,
						medicaid,
						Birthdate,
						sex,
						insurance,
						fClass,
						procID,
						procDate,
						lineAmt,
						code,
						clinic
					) values (
						$claimid,
						'$claimdate',
						$claimamt,
						$patid,
						'$lastname',
						'$firstname',
						'$subscribid',
						'$medicaid',
						'$birthdate',
						'$sex',
						'$insurance',
						$fclass,
						$procid,
						'$procdate',
						$lineamt,
						'$code',
						'$clinic'
					)
				");
		}
# end check
			} # end for loop that is reading each record
		} # end if block for sussesful date pull from dentrix
		$json = json_encode($list);
		echo "$json";
	}

	# function to add a record to a table
#	public function addRecord(){
#		$d1 = $this->input->post('x');
#		$d2 = $this->input->post('y');
#		$db1 = $this->getDB();
#		$db1->query("INSERT INTO table (column1,column2) VALUES ('$d1','$d2')");
#		echo "creating account";
#	}

}
