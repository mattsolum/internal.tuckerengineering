<?php

class Index extends Controller {

	function __construct()
	{
		parent::Controller();	
	}
	
	function index()
	{	
		$this->load->view('index');
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */