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

	public function build_client_tools($client_id = NULL)
	{
		$id = '';
		if($client_id != NULL)
		{
			$id = $client_id;
		}

		$links = array();
		$links['View'] 				= 'clients/' . $id;
		$links['Edit'] 				= 'clients/edit/' . $id;
		$links['Apply Payment']		= 'clients/apply_payment/' . $id;
		$links['Make Invoice']		= 'clients/make_invoice/' . $id;

		$package_links = $this->CI->Event->trigger('nav.build.client_tools');

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

	public function build_job_tools($job_id = NULL)
	{
		$id = '';
		if($job_id != NULL)
		{
			$id = $job_id;
		}

		$links = array();
		$links['View'] 				= 'jobs/' . $id;
		$links['Edit'] 				= 'jobs/edit/' . $id;
		$links['Apply Payment']		= 'jobs/apply_payment/'. $id;
		$links['View Invoice']		= 'jobs/invoice/' . $id;

		$package_links = $this->CI->Event->trigger('nav.build.job_tools');

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

	public function build_user_action_links()
	{
		$links = array();
		$links['Password'] 				= 'user/password';
		$links['Address']				= 'user/address';
		$links['View statistics']		= 'user/statistics';

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
		$links['User admin'] 					= 'admin/users';
		$links['Database admin']				= 'admin/database';
		$links['View logs']						= 'admin/logs';
		$links['Config']						= 'admin/config';
		$links['Testbed']						= 'admin/testbed';

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

	public function build_database_admin_links()
	{
		$links = array();
		$links['Backup'] 						= 'admin/database/backup';
		$links['Restore']						= 'admin/database/restore';
		$links['Migrate']						= 'admin/database/migrate';

		$package_links = $this->CI->Event->trigger('nav.build.database_admin');

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

	public function build_user_admin_links()
	{
		$links = array();
		$links['Create user'] 					= 'admin/users/create';

		$package_links = $this->CI->Event->trigger('nav.build.user_admin');

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
		$links['Search jobs']			= 'search/jobs';

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
		$links['Search clients']		= 'search/clients';

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
		$links['Apply payment']			= 'invoice/payment';
		$links['Batch pay']				= 'payments/batch';

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

	public function build_invoice_tools($invoice_id = NULL)
	{
		$id = '';
		if($invoice_id != NULL)
		{
			$id = $invoice_id;
		}

		$links = array();
		$links['View'] 				= 'invoices/' . $id;
		$links['Apply Payment']		= 'invoices/apply_payment/'. $id;
		$links['Publish']			= 'invoices/publish/' . $id;

		$package_links = $this->CI->Event->trigger('nav.build.invoice_tools');

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