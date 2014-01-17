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
		#putenv('FREETDSCONF=/usr/local/etc/freetds.conf');
		#$database = $this->load->database('fl',true);
		#echo "db Loaded...";
		#$headings = array('first','second','third');
		#$list[0] = $headings;
		#$db1 = $this->getDB();
		#$rs = $db1->query('SELECT * FROM table');
		#if($rs){
		#	foreach($rs->result_array() as $row){
		#		$list[] = $row;
		#	}
		#}
		#$json = json_encode($list);
		#echo "$json";
		echo "hello from action function get list";
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
