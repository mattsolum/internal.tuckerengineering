<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Accounting extends CI_Model
{
	private $CI = NULL;
	
	public function Accounting()
	{
		parent::__construct();
		$this->CI =& get_instance();
		
		$this->CI->load->model('Payment');
	}
	
	public function commit($accounting)
	{
		$this->CI->db->trans_start();
		
		if($this->exists($accounting))
		{
			$this->delete($accounting);
		}
		
		//Get rid of 'Positive Balance' items
		//They will be recreated later.
		//Yes, I have trust issues. Users are always using me.
		foreach($accounting->items AS $key => $item)
		{
			if($item->item = 'Positive Balance')
			{
				unset($accounting->items[$key]);
			}
		}
		
		$this->create($accounting);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_satus() === FALSE)
		{
			log_message('error', 'Error in model Accounting committing object.');
			return FALSE;
		}
		else return TRUE;
	}
	
	public function job_balance($job_id)
	{
		$accounting = $this->get_by_job($job_id);
		
		return $accounting->credits->total() + $accounting->debits->total();
	}
	
	public function get_by_job($job_id)
	{
		$accounting = new StructAccounting();
		
		$accounting->credits 	= $this->get_credits_by_job($job_id);
		$accounting->debits		= $this->get_debits_by_job($job_id);
		
		return $accounting;
	}
	
	private function get_credits_by_job($job_id)
	{
		$this->CI->db->select('*');
		$this->CI->db->from('ledger');
		
		$this->CI->db->where('job_id', $job_id);
		$this->CI->db->where('amount >', 0);
		
		$query = $this->CI->db->get();
		
		if($query->num_rows() > 0)
		{
			$result = array();
			
			foreach($query->result() AS $row)
			{
				$credit = new StructCredit();
				
				$credit->client_id 		= $row->client_id;
				$credit->job_id			= $row->job_id;
				$credit->ledger_id		= $row->ledger_id;
				
				$credit->item			= $row->item;
				
				$credit->date_added 	= $row->date_added;
				$credit->date_updated	= $row->date_updated;
				
				$credit->amount			= $row->amount;
				
				if($row->payment_id != NULL)
				{
					$credit->payment = $this->CI->Payment->get($row->payment_id);
				}
				
				$result[] = $credit;
				
				unset($credit);
			}
			
			return $result;
		} 
		
		return FALSE;
	}
	
	private function get_debits_by_job($job_id)
	{
		$where = array('amount <' => '0', 'job_id' => $job_id);
		
		$query = $this->CI->db->get_where('ledger', $where);
		
		if($query->num_rows() > 0)
		{
			$result = array();
			
			foreach($query->result() AS $row)
			{
				$ledger = new StructDebit();
				
				$ledger->ledger_id 	= $row->ledger_id;
				$ledger->client_id 	= $row->client_id;
				$ledger->job_id		= $job_id;
				
				$ledger->item		= $row->item;
				$ledger->amount		= $row->amount;
				
				$ledger->date_added	= $row->date_added;
				
				$result[] = $ledger;
				
				unset($ledger);
			}
			
			return $result;
		}
		
		return FALSE;
	}
	
	public function get_balance_by_client($client_id)
	{
		//If it isn't an array, make it an array.
		//Makes it simpler to handle arrays or single values.
		if(!is_array($client_id))
		{
			$client_id = array($client_id);	
		}
		
		//Now validate it all. Can't have a sneaky developer trying to 
		//get something into the database that shouldn't be.
		foreach($client_id AS $id)
		{
			if(!preg_match('/^[0-9]+$/', $id))
			{
				log_message('error', 'Error in accounting model method get_balance_by_client: numeric ID required, "' . $id . '" given.');
				return FALSE;
			}
		}
		
		//Compile our where statement.
		$where = implode(' OR t1.client_id = ', $client_id);
		
		//Have each job summed individualy
		//Then sum up the payments that a client has made
		//subtract the two.
		
		//Annoyingly complex query ensures that if more than one person pays for a job
		//somewhere in the client's past then it will be properly counted.
		//Maybe this will never happen, but if it does the system will not collapse on itself
		$query = $this->db->query('SELECT t1.client_id, balance, payments, other_payments, (IFNULL(balance, 0) + IFNULL(payments, 0) - IFNULL(other_payments, 0)) AS final_balance
		FROM
		(
			SELECT jobs.client_id, SUM(ledger.amount) AS balance
			FROM jobs
			JOIN ledger ON jobs.job_id = ledger.job_id
			WHERE ledger.amount < 0
			GROUP BY jobs.job_id
		) t1 LEFT JOIN
		(
			SELECT payments.client_id, sum(amount) AS payments
			FROM payments 
			GROUP BY client_id
		) t2 ON t1.client_id = t2.client_id
		LEFT JOIN
		(
			SELECT ledger.client_id AS client_id, SUM(ledger.amount) AS other_payments
			FROM ledger
			JOIN jobs ON ledger.job_id = jobs.job_id 
			WHERE jobs.client_id != ledger.client_id 	GROUP BY ledger.client_id
		) t3 on (t2.client_id = t3.client_id) WHERE t1.client_id = ' . $where);
		
		if($query->num_rows() > 0)
		{
			$result = array();
			
			foreach($query->result() AS $key => $row)
			{
				$result[$key]['client_id'] 	= $row->client_id;
				$result[$key]['balance'] 	= $row->balance;
			}
			
			return $result;
		}
		
		log_message('error', 'Error in accounting model, method get_balance_by_client: no results found for given ID.');
		return FALSE;
	}
	
	private function read()
	{
		
	}
	
	private function create($accounting)
	{
		foreach($accounting->items AS $item)
		{
			
		}
	}
	
	public function edit($accounting)
	{
		
	}
	
	public function delete()
	{
		
	}
	
	private function exists($accounting)
	{
		
	}
}