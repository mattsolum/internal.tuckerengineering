<?php defined('BASEPATH') OR exit('No direct script access allowed');

class user_admin extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		if(!$this->User->is_authed() && uri_string() != 'user/auth')
		{
			redirect('user/auth');
		}
	}

	public function index()
	{	
		var_dump($this);
		$this->load->view('index');
	}

	public function auth($redirect = '')
	{
		$redirect = urldecode($redirect);

		if($this->User->is_authed())
		{
			$this->User->de_auth();
			redirect('user/auth');
		}
		else
		{
			if($this->input->post('email') !== FALSE && $this->input->post('password') !== FALSE)
			{
				if($this->User->auth($this->input->post('email'), $this->input->post('password')))
				{
					redirect($redirect);
				}
			}

			if($redirect != '' && substr($redirect, 0, 1) != '/')
			{
				$redirect = '/' . $redirect;
			}

			$this->load->view('log_in', array('redirect' => $redirect));
		}
	}
}