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
		$this->load->model('History');

		$recent = $this->History->recent_activity();

		$this->load->view('index', array('recent_activity' => $recent));
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */