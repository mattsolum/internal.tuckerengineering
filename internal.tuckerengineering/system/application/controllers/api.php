<?php

class Api extends Controller {

	function api()
	{
		parent::Controller();
	}
	
	function index($version = FALSE)
	{	
		//Clean any malicious content
		$version = ($version == FALSE)?1:preg_replace('/[^0-9]/', '', $version);
		if($version != '')
		{
			$this->load->library('REST_' . $version);
		}
		$this->load->view('api_test');
	}
}

/* End of file api.php */
/* Location: ./system/application/controllers/api.php */