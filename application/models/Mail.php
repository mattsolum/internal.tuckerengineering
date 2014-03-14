<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mail extends CI_Model {
	
	private $CI = NULL;

	private $email = NULL;
	private $system = 'no-reply';
	private $user = 'no-reply';
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		
		$this->CI->load->library('email');
		$this->CI->load->model('User');

		$this->email = '@' . /*site_url()*/ 'tuckerengineering.net';

		$system = $this->CI->User->get_user(0);

		$this->system = $system->name;

		$this->user = $this->CI->User->get_name();
	}

	public function get_sys_addr()
	{
		return $this->system . $this->email;
	}

	public function get_user_addr()
	{
		return $this->user . $this->email;
	}

	private function prepare($to, $system = TRUE)
	{
		if($system === TRUE)
		{
			$this->CI->email->from($this->get_sys_addr(), $this->system);
		}
		else
		{
			$this->CI->email->from($this->get_user_addr(), $this->user);
		}

		$this->CI->email->to($to);
	}

	public function send_invoice($to, $invoice)
	{
		
	}

	public function send_account_created($user, $password)
	{
		$this->prepare($user->get_email());
		$body = $this->CI->load->view('email/new_user', array('user' => $user, 'password' => $password), TRUE);

		$this->CI->email->message($body);
		$this->CI->email->subject($this->parse_subject(setting('mail.new_user.subject')));

		if(!$this->CI->email->send())
		{
			log_message('Error', 'Failed to send email from ' . $this->get_sys_addr() . ' to ' . $user->get_email());
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	private function parse_subject($subject, $data = NULL)
	{
		return $subject;
	}
}