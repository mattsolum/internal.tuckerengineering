<?php

class Invoices extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Navigation');

		$this->User->check_auth();
	}

	public function index()
	{	
		$this->load->view('invoices/index');
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