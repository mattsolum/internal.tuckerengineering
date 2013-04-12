<?php

class Invoices extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Navigation');

		$this->User->check_auth();
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

	public function index($invoice_id = NULL)
	{	
		if($invoice_id == NULL)
		{
			$this->load->view('invoices/index');
		}
		else
		{
			$this->load->view('invoices/invoice');
		}
	}
	
	/**
	 * View... view. Displays a single record
	 *
	 * @author Matthew Solum
	 * @param $invoice_id
	 * @return NULL
	 */
	public function view($invoice_id)
	{
		//code
	}
	
	/**
	 * Displays a single record for editing
	 *
	 * @author Matthew Solum
	 * @param $invoice_id
	 * @return null
	 */
	public function edit($invoice_id)
	{
		//code
	}
	
}

/* End of file invoice.php */
/* Location: ./system/application/controllers/invoice.php */