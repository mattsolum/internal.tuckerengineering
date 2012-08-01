<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'libraries/Service.php'
require_once APPPATH . 'libraries/Location.php'
require_once APPPATH . 'libraries/Invoice.php'
require_once APPPATH . 'libraries/Contact.php'

class Invoice_model extends Model {
	
	$base = NULL;
	
	public function Invoice_model()
	{
		parent::construct();
		$self->base =& get_instance();	
	}
	
	/**
	 * Loads an invoice and supporting information into a invoice class
	 *
	 * @author Matthew Solum
	 * @param $id
	 * @return Invoice
	 */
	public function load($id)
	{
		//code
		$invoice = $this->query();
	}
	
	/**
	 * Adds a new invoice
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
	 * Reads an invoice given a invoice_id
	 *
	 * @author Matthew Solum
	 * @param $invoice_id
	 * @return Array
	 */
	public function read($id)
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
		$this->delete_invoice($invoice->id);
		$this->create_invoice($invoice);
	}
	
	/**
	 * Deletes an invoice given a invoice_id
	 *
	 * @author Matthew Solum
	 * @param $invoice_id
	 * @return bool
	 */
	public function delete($id)
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
		if ($invoice instanceof Invoice)
		{
			
		}
		else if (is_number($invoice))
		{
			
		}
	}
	
	/**
	 * Full text search of invoices
	 *
	 * @author Matthew Solum
	 * @param $query, $date_from = NULL, $date_to = NULL
	 * @return Array
	 */
	public function search($query, $date_from = NULL, $date_to = NULL, $page = 0, $per_page = NULL)
	{
		//code
	}
	
	
}