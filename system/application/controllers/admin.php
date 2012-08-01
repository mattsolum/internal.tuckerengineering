<?php

class Admin extends Controller {

	function admin()
	{
		parent::Controller();
	}
	
	function index()
	{	
		$this->load->view('config');
	}
	
	function config()
	{
		$this->load->model('Settings');
		
		$data = array('results' => null);
		
		$data['results'] = $this->Settings->get_all();
		
		$this->load->view('config', $data);
	}
}