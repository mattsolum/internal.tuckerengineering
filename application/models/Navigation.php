<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Navigation extends CI_Model {
	
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->helper('url');
	}

	public function build_top_level()
	{
		$links = array();
		$links['Home'] 		= '';
		$links['clients'] 	= 'clients';
		$links['jobs']		= 'jobs';
		$links['invoices']	= 'invoices';
		$links['admin']		= 'admin';

		$package_links = $this->CI->Event->trigger('nav.build.top');

		if($package_links != NULL)
		{
			foreach($package_links AS $return)
			{
				$links = array_merge($links, $return);
			}
		}

		$this->censor($links);

		return $links;
	}

	private function censor(&$links)
	{
		foreach($links AS $title => $link)
		{
			if(!$this->CI->User->read_enabled('/' . $link))
			{
				unset($links[$title]);
			}
		}
	}

	public function here($link)
	{
		//TODO
		return FALSE;
	}
}