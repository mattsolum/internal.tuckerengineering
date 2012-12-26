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
		$default = array();
		$default['Home'] 		= site_url();
		$defualt['clients'] 	= site_url('clients');
		$defualt['jobs']		= site_url('jobs');
		$defualt['invoices']	= site_url('invoices');

		$package_links = $this->CI->Event->trigger('nav.build.top');


		$wrapper_start 	= '<nav id="top"><ul>';
		$wrapper_end	= '</ul></nav>';
	}
}