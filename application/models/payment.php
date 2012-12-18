<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Model {
	
	private $CI = NULL;
	
	public function Payment()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}
	
	public function get($id)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$where = array('payment_id' => $id);
		
		$query = $this->CI->db->get_where('payments', $where);
		
		if($query->num_rows() > 0)
		{
			$row = $query->row(0);
			
			$payment = new StructPayment();
			
			$payment->id 			= $row->payment_id;
			$payment->client_id		= $row->client_id;
			
			$payment->tender		= $row->tender;
			$payment->number		= $row->number;
			$payment->amount		= $row->amount;
			
			$payment->date_added 	= $row->date_added;
			$payment->date_posted	= $row->date_posted;
			
			return $payment;
		}
		
		return FALSE;
	}
	
	public function get_client_last($client_id)
	{
		$this->db->select('*');
		$this->db->from('payments');
		$this->db->where('client_id', $client_id);
		$this->db->order_by('date_added', 'DESC');
		$this->db->limit(1);
		
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$row = $query->row(0);
			
			$payment = new StructPayment();
			
			$payment->id			= $row->payment_id;
			$payment->client_id		= $row->client_id;
			$payment->tender		= $row->tender;
			$payment->number		= $row->number;
			$payment->amount		= $row->amount;
			$payment->date_added	= $row->date_added;
			$payment->date_posted	= $row->date_posted;
			
			return $payment;
		}
		
		return FALSE;
	}
	
	public function commit($payment)
	{
	
	}
	
	public function delete($payment)
	{
	
	}
	
	public function apply_payment_to_jobs($payment, $jobs = NULL)
	{
		if(is_int($jobs))
		{
			$jobs = array($jobs);
		}
	}
}