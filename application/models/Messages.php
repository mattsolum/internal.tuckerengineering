<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Messages extends CI_Model {
	
	private $CI = NULL;

	private $flashes = NULL;

	private $max_message_length = 64;
	private $cookie_name	 	= 'alerts';
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->helper('structures/flash_helper');

		$this->flashes = array();

		$this->load();
	}

	private function save()
	{
		$str = '';

		foreach($this->flashes AS $flash)
		{
			$str .= base64_encode($flash->get_type()) . ',' . base64_encode($flash->get_message()) . ';';
		}

	 	$str = substr($str, 0, -1);

	 	$this->CI->input->set_cookie($this->cookie_name, $str, 0);
	}

	private function load()
	{
		$str = $this->CI->input->cookie($this->cookie_name);

		if($str != '')
		{
			$messages = explode(';', $str);

			foreach($messages AS $msg)
			{
				if(strstr($msg, ','))
				{
					$data = explode(',', $msg);

					$this->flashes[] = new Flash(substr( base64_decode($data[1]),0,$this->max_message_length), base64_decode($data[0]));
				}
			}
		}
	}

	private function clear()
	{
		$this->CI->input->set_cookie($this->cookie_name, '', 1);
	}

	public function flash($msg, $type = 'alert')
	{
		$this->flashes[] = new Flash(substr($msg, 0, $this->max_message_length), $type);
		$this->save();
	}

	public function load_flash_messages()
	{
		$this->clear();
		return $this->CI->load->view('messages/flashes', array('messages' => $this->flashes), true);
	}

	public function post_system_message($to_id, $message)
	{
		return $this->post_message($to_id, 0, $message);
	}

	public function post_user_message($to_id, $message)
	{

	}

	private function post_message($to_id, $from_id, $message)
	{

	}
}