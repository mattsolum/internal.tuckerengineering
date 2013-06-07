<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Messages extends CI_Model {
	
	private $CI = NULL;

	private $flashes = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->helper('structures/flash_helper');

		$this->flashes = array();
	}

	public function flash($msg, $type = 'alert')
	{
		$this->flashes[] = new Flash($msg, $type);
	}

	public function load_flash_messages()
	{
		return $this->CI->load->view('messages/flashes', array('messages' => $this->flashes), true);
	}
}