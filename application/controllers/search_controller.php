<?php defined('BASEPATH') OR exit('No direct script access allowed');

class search_controller extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Navigation');
		
		if(!$this->User->read_enabled(uri_string()))
		{
			redirect('user/auth');
		}
	}

	public function _remap($method)
	{
		$param_offset = 2;

		// Default to index
		if ( ! method_exists($this, $method))
		{
			// We need one more param
			$param_offset = 1;
			$method = 'index';
		}

		// Since all we get is $method, load up everything else in the URI
		$params = array_slice($this->uri->rsegment_array(), $param_offset);

		// Call the determined method with all params
		call_user_func_array(array($this, $method), $params);
	} 

	private function prepare($q)
	{
		if($q == '' && $this->input->post('q') !== FALSE)
		{
			redirect('search/' . urlencode($this->input->post('q')));
		}
		elseif($q != '')
		{
			return trim(urldecode($q));
		}
	}

	public function index($q = '')
	{
		$q = $this->prepare($q);

		echo($q);
	}

	public function clients($q = '')
	{
		$q = $this->prepare($q);

		if($q != '')
		{
			if(str_pos('[type:client]') === FALSE) 
			{
				$q .= ' [type:client]';
			}
		}

		echo($q);
	}

	public function jobs($q = '')
	{
		$q = $this->prepare($q);

		if($q != '')
		{
			if(str_pos('[type:job]') === FALSE) 
			{
				$q .= ' [type:job]';
			}
		}

		echo($q);
	}

	public function properties($q = '')
	{
		$q = $this->prepare($q);

		if($q != '')
		{
			if(str_pos('[type:property]') === FALSE) 
			{
				$q .= ' [type:property]';
			}
		}	

		echo($q);
	}
}