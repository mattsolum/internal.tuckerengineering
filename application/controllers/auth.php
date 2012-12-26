<?php defined('BASEPATH') OR exit('No direct script access allowed');

class auth extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		if(!$this->User->is_authed() && uri_string() != 'auth/log_in')
		{
			redirect('auth/log_in');
		}
	}

	public function index()
	{	
		$this->load->view('index');
	}

	public function log_in($redirect = '')
	{
		$this->load->view('log_in');
	}
}