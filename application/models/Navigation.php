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
		$links['Clients'] 	= 'clients';
		$links['Jobs']		= 'jobs';
		$links['Invoices']	= 'invoices';
		$links['Admin']		= 'admin';

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

	public function build_user_links()
	{
		$links = array();
		$links['Account controls'] 	= 'user';
		$links['Sign out']			= 'user/auth';

		$package_links = $this->CI->Event->trigger('nav.build.user');

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

	public function build_admin_links()
	{
		$links = array();
		$links['User administration'] 			= 'admin/users';
		$links['Database administration']		= 'admin/database';
		$links['Logs']							= 'admin/logs';
		$links['Config']						= 'admin/config';

		$package_links = $this->CI->Event->trigger('nav.build.admin');

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

	public function build_job_links()
	{
		$links = array();
		$links['Create job'] 			= 'jobs/create';
		$links['Apply payment']			= 'jobs/payment';

		$package_links = $this->CI->Event->trigger('nav.build.jobs');

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

	public function build_client_links()
	{
		$links = array();
		$links['Create client'] 		= 'clients/create';
		$links['Apply payment']			= 'clients/payment';
		$links['Make invoice']			= 'invoice/client';
		$links['Merge clients']			= 'clients/payment';

		$package_links = $this->CI->Event->trigger('nav.build.clients');

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

	public function build_search_links()
	{
		$links = array();
		$links['Search clients'] 		= 'search/clients';
		$links['Search jobs']			= 'search/jobs';
		$links['Search properties']		= 'search/properties';

		$package_links = $this->CI->Event->trigger('nav.build.search');

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

	public function build_invoice_links()
	{
		$links = array();
		$links['Create invoice'] 		= 'invoice/create';
		$links['Apply Payment']			= 'payments/invoice';

		$package_links = $this->CI->Event->trigger('nav.build.invoice');

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

	/**
	 * Removes any link the user does not have permission to see.
	 * 
	 * @param  array $links
	 * @return array
	 */
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
		$uri = str_replace(base_url(), '', $link);

		$elements = explode('/', strtolower(trim($uri, '/')));

		$location = explode('/', strtolower(trim(uri_string(), '/')));

		for($i = 0; $i < count($elements); $i++)
		{
			if(!isset($location[$i]) || $elements[$i] != $location[$i])
			{
				return FALSE;
			}
		}

		return TRUE;
	}
}