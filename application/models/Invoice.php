<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Model {
	
	$CI = NULL;
	
	public function __construct()
	{
		parent::construct();
		$this->CI =& get_instance();
		
		$this->CI->load->model('Job');
	}
	
	/**
	 * Loads an invoice and supporting information into an invoice class
	 *
	 * @author Matthew Solum
	 * @param $invoice_id
	 * @return Invoice
	 */
	public function get($invoice_id)
	{
		//code
		$invoice = $this->query();
	}
	
	
	
	/**
	 * Creates a new invoice
	 *
	 * @author Matthew Solum
	 * @param $invoice		
	 * @return BOOL
	 */
	public function create($invoice)
	{
		//code
	}
	
	/**
	 * Updates an invoice
	 *
	 * @author Matthew Solum
	 * @param $invoice
	 * @return bool
	 */
	public function update($invoice)
	{
		//code
	}
	
	/**
	 * Deletes an invoice given a invoice_id
	 *
	 * @author Matthew Solum
	 * @param $invoice_id
	 * @return bool
	 */
	public function delete($client_id, $invoice_id)
	{
		//code
	}
	
	/**
	 * Checks to see if an invoice exists
	 *
	 * @author Matthew Solum
	 * @param $invoice
	 * @return bool
	 */
	public function exists($invoice)
	{
		//code
	}
}