<?php

class Admin extends CI_Controller {
	
	function index()
	{	
		$this->load->view('config');
	}
	
	function config()
	{
		$this->load->model('Setting');
		
		$data = array('results' => null);
		
		$data['results'] = $this->Setting->get_all();
		
		$this->load->view('config', $data);
	}
}