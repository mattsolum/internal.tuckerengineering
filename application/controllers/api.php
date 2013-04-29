<?php

class Api extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('User');
		$this->User->clear_for_this_session();
	}

	public function index()
	{	
		$this->error('No parameters provided');
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