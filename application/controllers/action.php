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
		if($this->getE($data,0)=='ISA'){
			$myTrans{'id'} = $this->getE($data,13);
			$x = $claims[$ln];
			echo "$x<br/>";
			$ln++;
		}
		if($this->getE($data,0)=='IEA'){ $x = $claims[$ln]; echo "$x<br/>"; }else{ $this->xerror($claims,$ln); }
		echo "-----------------<br/>";
		echo "printing Trans<br/>";
		$id = $myTrans{'id'};
		echo "printing Trans<br/>";
		echo "Id is $id<br/>";
	}
	public function xerror($claims,$ln){
		$x = $claims[$ln];
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
