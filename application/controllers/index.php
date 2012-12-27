<?php

class Index extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		
		if(!$this->User->is_authed())
		{
			redirect('user/auth/' . urlencode(uri_string()));
		}

		$this->load->model('Navigation');
	}
	
	function index()
	{	
		$this->load->view('index');
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */