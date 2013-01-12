<?php

class Api extends CI_Controller {
	
	public function index()
	{	
		$this->error('No parameters provided');
		$this->User->clear_for_this_session();
	}
	
	public function v1($auth, $method)
	{	
		//The library handles everything from parsing to output.
		$this->load->library('REST/1/RestApi.php');
	}
	
	public function v2()
	{
		$this->load->library('REST/2/RestApi.php');
	}
	
}

/* End of file api.php */
/* Location: ./system/application/controllers/api.php */