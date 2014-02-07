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
						procID
					) values (
						$claimid,
						$procid
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
