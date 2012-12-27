<?php

class Admin extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Navigation');

		if(!$this->User->read_enabled(uri_string()))
		{
			redirect('user/auth');
		}
	}

	function index()
	{	
		$this->load->view('admin/index');
	}
	
	function config()
	{
		$this->load->model('Setting');
		
		$data = array('results' => null);
		
		$data['results'] = $this->Setting->get_all();
		
		$this->load->view('config', $data);
	}
}