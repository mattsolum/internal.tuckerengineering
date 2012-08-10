<?php

class Index extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{	
		$this->load->view('index');
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */