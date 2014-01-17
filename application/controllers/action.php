<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Action extends CI_Controller {

	public function index(){
		echo "Default return from Action controller";
		#$this->load->view('name');
	}
	public function getDB(){
		$database = $this->load->database('default',true);
		return $database;
	}
	# Function to run a query and echo a json object
	public function getList(){
		putenv('FREETDSCONF=/usr/local/etc/freetds.conf');
		$db1 = $this->load->database('dentrix',true);
		$headings = array('first','second','third');
		$list[0] = $headings;
		#$db1 = $this->getDB();
		$rs = $db1->query('
			SELECT
				p.FIRSTNAME,
				p.LASTNAME,
				p.PATID
			FROM
				DDB_PAT as p
			WHERE
				p.PATID = 869704
		');
		if($rs){
			foreach($rs->result_array() as $row){
				$first = $row['FIRSTNAME'];
				$last  = $row['LASTNAME'];
				$id    = $row['PATID'];
				$list[] = array($first,$last,$id);
			}
		}
		$json = json_encode($list);
		echo "$json";
	}
	# function to add a record to a table
	public function addRecord(){
		$d1 = $this->input->post('x');
		$d2 = $this->input->post('y');
		$db1 = $this->getDB();
		$db1->query("INSERT INTO table (column1,column2) VALUES ('$d1','$d2')");
		echo "creating account";
	}

}
