<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Model {
	
	private $CI = NULL;
	
	public function Payment()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}
	
	
	//Return the ID on success
	//and FALSE on failure
	public function insert($payment)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$this->CI->db->trans_start();
		
		$id = $this->exists();
		if($id !== FALSE)
		{
			$data['<id>'] = $id;
			$this->delete();
		}
		else
		{
				
		}
		
		$query = $this->CI->db->insert('payments', $data);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Payment method insert: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function delete($id)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$this->CI->db->trans_start();
		
		$this->CI->db->delete('payments', array('<id>' => $id));
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Payment method delete: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function get($id)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$query = $this->CI->db->get_where('payments', array('<id>' => $id));
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			
		}
		else
		{
			log_message('Error', 'Error in Payment method get: no data found with given ID.');
			return FALSE;
		}
	}
	
	//Return ID on success and FALSE on failure
	public function exists($id,$type,$name)
	{
		$where = array();
		
		$where[''] = 0;
		
		$query = $this->CI->db->get_where('payments', $where);
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			return TRUE;
		}
		else return FALSE;
	}

	public function apply_payment_by_client($payment, $client_id)
	{

	}

	public function apply_payment_by_job($payment, $jobs)
	{
		if(is_int($jobs))
		{
			$jobs = array($jobs);
		}

		foreach($jobs AS $job_id)
		{

		}
	}

	public function apply_payment_by_invoice($payment, $invoice_id)
	{

	}
	
	public function get_client_balance($client_id)
	{
		
	}

	public function get_balance_by_job($job_id)
	{
		
	}
	
	//Sums all the jobs this client has ever requested, payed or not
	private function get_client_total_costs($client_id)
	{
		$this->CI->db->select_sum('price', 'total');
		$this->CI->db->from('payments');
		$this->CI->db->join('ledger', 'payments.ledger_id = ledger.ledger_id', 'right');
		$this->CI->db->where('client_id', $client_id);
		$query = $this->CI->db->get('jobs');
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			return floatval($result->total);
		}
		else return FALSE;
	}
	
	//Sums all the payments we have received from a given client
	private function get_client_total_payments($client_id)
	{
		$this->CI->db->select_sum('ledger.amount', 'total');
		$this->CI->db->where('client_id', $client_id);
		$query = $this->CI->db->get('jobs');
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			return floatval($result->total);
		}
		else return FALSE;
	}
}