<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Model {
	
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		
		$this->CI->load->model('Job');
		$this->CI->load->model('Client');
	}

	/**
	 * Loads an invoice and supporting information into an invoice class
	 *
	 * @author Matthew Solum
	 * @param $invoice_id
	 * @return StructInvoice
	 */
	public function get($invoice_id, $client_id = NULL)
	{
		if($client_id == NULL)
		{
			$segments = explode('-', $invoice_id);

			$invoice_id = $segments[1];
			$client_id  = $segments[0];

		}

		$invoice 			= new StructInvoice();

		$invoice->id 		= $invoice_id;
		$invoice->client 	= $this->CI->Client->get($client_id);

		$where = array('client_id' => $client_id, 'invoice_id' => $invoice_id);
		$query = $this->CI->db->get_where('invoices', $where);

		if($query->num_rows() > 0)
		{
			$row = $query->row(0);

			$invoice->date_added = $row->date_added;
			$invoice->date_sent	 = $row->date_sent;

			foreach ($query->result() as $row)
			{
				$invoice->jobs[] = $this->CI->Job->get($row->job_id);
			}
		}

		return $invoice;
	}

	/**
	 * Marks an invoice as sent.
	 * @param  int $invoice_id
	 * @param  int $client_id 
	 * @return BOOL
	 */
	public function mark_sent($invoice_id, $client_id)
	{
		$this->CI->db->trans_start();

		$data['date_sent'] = now();

		$this->CI->db->where('invoice_id', $invoice_id);
		$this->CI->db->where('client_id', $client_id);
		$this->CI->db->update('invoices', $data);

		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Invoice method mark_sent: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * Creates or updates an invoice
	 * @param  StructInvoice $invoice
	 * @return BOOL FALSE on failure, Int on success
	 */
	public function commit($invoice)
	{
		if(get_class($invoice) != 'StructInvoice' || !$invoice->is_valid())
		{
			log_message('error', 'Error in Invoice method commit: invoice provided is invalid.');
			return FALSE;
		}

		$id = $this->exists($invoice);
		if($id !== FALSE)
		{
			//The exists function checks to see if
			//an invoice exists for the same client
			//with the same list of jobs.
			//If it does, may as well call it quits,
			//that's all this model handles.
			return $id;
		}
		else
		{
			return $this->create($invoice);
		}
	}

	/**
	 * Creates a new invoice.
	 * Returns the invoice ID on success
	 *
	 * @author Matthew Solum
	 * @param StructInvoice $invoice		
	 * @return BOOL FALSE on failure, Int on success
	 */
	private function create($invoice)
	{
		$this->CI->db->trans_start();

		$id = $this->get_next_invoice_id($invoice->client->id);

		foreach($invoice->jobs AS $job)
		{
			if(!$this->create_single($id, $invoice->client->id, $job->id))
			{
				break;
			}
		}

		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Invoice method create: transaction failed.');
			return FALSE;
		}
		else
		{
			return $id;
		}
	}
	
	/**
	 * Inserts a single record for an invoice
	 * 
	 * @param  int $invoice_id
	 * @param  int $client_id 
	 * @param  int $job_id
	 * @return BOOL
	 */
	private function create_single($invoice_id, $client_id, $job_id)
	{
		$this->CI->db->trans_start();

		$data = array();
		$data['invoice_id'] = $invoice_id;
		$data['client_id']	= $client_id;
		$data['job_id']		= $job_id;
		$data['date_added']	= now();

		$this->CI->db->insert('invoices', $data);

		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Invoice method create: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	/**
	 * Deletes an invoice given a client and invoice ID
	 * @param  int $invoice_id
	 * @param  int $client_id 
	 * @return BOOL
	 */
	public function delete($invoice_id, $client_id)
	{
		$this->CI->db->trans_start();

		$where = array();
		$where['client_id'] 	= $client_id;
		$where['invoice_id']	= $invoice_id;

		$this->CI->db->delete('invoices', $where);

		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Invoice method create: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	/**
	 * Checks to see if an invoice exists
	 *
	 * @author Matthew Solum
	 * @param $invoice
	 * @return bool
	 */
	private function exists($invoice)
	{
		//Assemble a sorted list of job ID's separated by spaces
		//into one string to be compared to a similarly constructed string
		//from a MySQL query.
		$jobs_str = '';

		$invoice->sort_jobs();
		foreach ($invoice->jobs as $job) {
			$jobs_str .= $job->id . ' ';
		}

		$jobs_str = substr($jobs_str, 0, strlen($jobs_str) - 1);

		$query = $this->CI->db->query("SELECT invoice_id, client_id, GROUP_CONCAT(job_id ORDER BY job_id ASC SEPARATOR ' ') AS jobs FROM invoices WHERE client_id = " . $invoice->client->id . " GROUP BY invoice_id, client_id");

		if($query->num_rows() > 0)
		{
			//MySQL was not allowing me to compare to the GROUP_CONCAT string
			//so I am sorting through the invoices that belong to the given
			//client manually
			foreach ($query->result() as $row)
			{
				if($row->jobs == $jobs_str)
				{
					return $row->invoice_id;
				}
			}
		}

		return FALSE;
	}

	/**
	 * Gets the next invoice ID given a client ID
	 * @param  Int $client_id
	 * @return Int
	 */
	private function get_next_invoice_id($client_id)
	{
		$this->CI->db->select('invoice_id');
		$this->CI->db->from('invoices');
		$this->CI->db->order_by('invoice_id', 'DESC');
		$this->CI->db->limit(1);

		$query = $this->CI->db->get();

		if($query->num_rows() > 0)
		{
			$row = $query->row(0);

			return $row->invoice_id + 1;
		}

		return 1;
	}
}