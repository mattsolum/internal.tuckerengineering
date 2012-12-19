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
	
	//Returns TRUE on success.
	public function commit($accounting)
	{
		if(!$accounting->is_valid())
		{
			log_message('error', 'Error in model Accounting method commit: accounting is invalid.');
			return FALSE;
		}
		
		$this->CI->db->trans_start();
		
		//Get rid of 'Positive Balance' items
		//They will be recreated later.
		//If an existing job is edited and the client is changed
		//we need to make sure that the positive balance 
		//from the previous client does not get applied.
		//Yes, I have trust issues. Users are always using me.
		$date_added = now();
		foreach($accounting->credits AS $key => $credit)
		{
			if($item->item = 'Positive Balance')
			{
				$date_added = $credit->date_added;
				unset($accounting->credits[$key]);
			}
		}
		
		if($this->job_exists($accounting->debits[0]->job_id))
		{
			$this->delete_by_job($accounting->debits[0]->job_id);
		}
		
		//Let's check their balance!
		$bal = $this->get_balance_by_client($accounting->debits[0]->client_id);
		$bal = $bal[0]['balance'];
		
		//Look, there is money in the account!
		if($bal > 0)
		{
			//Make a NEW positive balance credit
			$positive_balance = new StructCredit();
			
			$positive_balance->client_id 	= $accounting->debits[0]->client_id;
			$positive_balance->job_id 		= $accounting->debits[0]->job_id;
			$positive_balance->item			= 'Positive Balance';
			$positive_balance->date_added	= $date_added;
			$positive_balance->date_updated	= now();
			
			//Use whatever is lower: the positive balance or the total for this job.
			$positive_balance->amount		= min($bal, $accounting->total() * -1);
			
			//The payment being applied is assumed to be 
			//the last payment we received from this client.
			$positive_balance->payment 		= $this->CI->Payment->get_client_last($positive_balance->client_id);
			
			//add it to the credits
			$accounting->credits[] = $positive_balance;
		}
		
		//Put everything in one array so it can be looped through all at once
		$all = array_merge($accounting->debits, $accounting->credits);
		
		foreach($all as $item)
		{
			$this->create_ledger_item($item);
		}
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('error', 'Error in model Accounting committing object.');
			return FALSE;
		}
		else return TRUE;
	}
	
	private function create_ledger_item($ledger)
	{
		if(!$ledger->is_valid())
		{
			log_message('error', 'Model Accounting method create_ledger_item: ledger is not valid.');
			return FALSE;
		}
		
		$this->CI->db->trans_start();
		
		$data = array();
		
		if(isset($ledger->ledger_id))
		{
			$data['date_added'] = $ledger->date_added;
			$data['ledger_id']	= $ledger->ledger_id;
		}
		else
		{
			$data['date_added'] = now();
		}
		
		$data['date_updated'] = now();
		
		if(isset($ledger->payment_id))
		{
			$data['payment_id']	= $ledger->payment_id;
		}
		
		$data['job_id']		= $ledger->job_id;
		$data['client_id']	= $ledger->client_id;
		$data['item']		= $ledger->item;
		$data['amount']		= $ledger->amount;
		
		$this->CI->db->insert('ledger', $data);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('error', 'Error in model Accounting method create_ledger_item.');
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
		
		$credits 	= $this->get_credits_by_job($job_id);
		$debits		= $this->get_debits_by_job($job_id);
		
		if($credits !== FALSE)
			$accounting->credits	= $credits;
			
		if($debits !== FALSE)
			$accounting->debits		= $debits;
		
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
		$query = $this->CI->db->query('SELECT t1.client_id, balance, payments, other_payments, (IFNULL(balance, 0) + IFNULL(payments, 0) - IFNULL(other_payments, 0)) AS final_balance FROM (SELECT jobs.client_id, SUM(ledger.amount) AS balance FROM jobs JOIN ledger ON jobs.job_id = ledger.job_id WHERE ledger.amount < 0 GROUP BY jobs.job_id) t1 OUTER JOIN (SELECT payments.client_id, sum(amount) AS payments FROM payments GROUP BY client_id) t2 ON t1.client_id = t2.client_id OUTER JOIN (SELECT ledger.client_id AS client_id, SUM(ledger.amount) AS other_payments FROM ledger JOIN jobs ON ledger.job_id = jobs.job_id WHERE jobs.client_id != ledger.client_id GROUP BY ledger.client_id) t3 on (t2.client_id = t3.client_id) WHERE t1.client_id = ' . $where);
		
		if($query->num_rows() > 0)
		{
			$result = array();
			
			foreach($query->result() AS $key => $row)
			{
				$result[$key]['client_id'] 	= $row->client_id;
				$result[$key]['balance'] 	= $row->final_balance;
			}
			
			return $result;
		}
		
		//If nothing is found the most likely reason is
		//that the client is new and has no balance.
		return array(array('client_id' => $client_id[0], 'balance' => 0));
	}
	
	private function read()
	{
		
	}
		
	public function edit($accounting)
	{
		
	}
	
	public function delete_by_job($job_id)
	{
		$this->CI->db->trans_start();
		
		$where = array('job_id' => $job_id);
		$query = $this->CI->db->delete('ledger', $where);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('error', 'Error in model Accounting method delete_by_job.');
			return FALSE;
		}
		else return TRUE;
	}
	
	private function job_exists($job_id)
	{
		$where = array('job_id' => $job_id);
		
		$query = $this->CI->db->get_where('ledger', $where);
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		
		return FALSE;
	}
}