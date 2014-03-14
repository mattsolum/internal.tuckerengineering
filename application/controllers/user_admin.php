<?php defined('BASEPATH') OR exit('No direct script access allowed');

class user_admin extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Navigation');

		if(!$this->User->is_authed() && uri_string() != 'user/auth')
		{
			redirect('user/auth');
		}
	}

	public function index()
	{
		$this->load->view('user/account_controls');
	}

	public function password()
	{
		if($this->input->post('current_password') !== FALSE)
		{
			if($this->User->check_password($this->input->post('current_password')))
			{
				if(strlen($this->input->post('new_password_1')) > 5 && $this->input->post('new_password_1') == $this->input->post('new_password_2'))
				{
					$user = $this->User->get_current_user();
					$user->set_password($this->input->post('new_password_1'));

					if($this->User->commit($user))
					{
						$this->Messages->flash('Your password has been changed.', 'success');
						redirect('user');
					}
					else
					{
						$this->Messages->flash('An internal error occured while attempting to update your password.', 'error');
						$this->load->view('user/change_password');
					}
				}
				else
				{
					$this->Messages->flash('The new passwords entered do not match.', 'error');
					$this->load->view('user/change_password');
				}
				
			}
			else
			{
				$this->Messages->flash('The password you entered was incorrect.', 'error');
				$this->load->view('user/change_password');
			}
		}
		else
		{
			$this->load->view('user/change_password');
		}
	}

	public function messages($alert_id = '')
	{
		$this->load->view('user/index');
	}

	public function auth($redirect = '')
	{
		$redirect = base64_decode(urldecode($redirect));

		if($this->User->is_authed())
		{
			$this->Messages->flash('You have been signed out.', 'success');
			$this->User->de_auth();
		}
		else
		{
			if($this->input->post('email') !== FALSE && $this->input->post('password') !== FALSE)
			{
				if($this->User->auth($this->input->post('email'), $this->input->post('password')))
				{
					$this->input->set_cookie('last_email', $this->input->post('email'), 60*60*24*7);
					redirect($redirect);
				}
				else
				{
					$this->Messages->flash('Username or password was invalid.', 'error');
				}
			}

			if($redirect != '' && substr($redirect, 0, 1) != '/')
			{
				$redirect = '/' . $redirect;
			}
		}

		$email_prefill = ($this->input->post('email') != '')?$this->input->post('email'):$this->input->cookie('last_email', TRUE);

		$this->load->view('log_in', array('redirect' => $redirect, 'email' => $email_prefill));
	}
}