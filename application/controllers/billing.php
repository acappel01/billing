<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Billing extends CI_Controller {

	public function index()
	{
		#phpinfo();
		echo "<h3>Main Controller</h3>";
		#$data['form'] = $this->load->view('form','',true);
		$data['test'] = 'test Data';
		$this->load->view('home',$data);
	}

}
