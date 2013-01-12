<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructDebit
{
	public $ledger_id		= NULL;
	public $client_id		= NULL;
	public $job_id			= NULL;
	
	public $item			= NULL;
	public $amount			= 0.0;

	public $date_added		= NULL;
	public $date_updated	= NULL;
	
	public function __construct($json = NULL)
	{
		if($json != NULL)
		{
			$this->set_from_json($json);
		}
	}
	
	public function set_from_json($json)
	{
		if(is_string($json))
		{
			$json = json_decode($json);
		}
		
		if($json !== NULL)
		{
			$this->ledger_id 	= $json->ledger_id;
			$this->client_id 	= $json->client_id;
			$this->job_id		= $json->job_id;
			
			$this->item			= $json->item;
			$this->amount		= $json->amount;
			
			$this->date_added	= $json->date_added;
			$this->date_updated	= $json->date_updated;
		}
	}
	
	public function is_valid($strict = FALSE)
	{
		if($strict == TRUE)
		{
			if($this->client_id == NULL || $this->job_id == NULL)
			{
				return FALSE;
			}
			
			//I just concatinate them. Probably not the fastest method, but the least
			//number of lines.
			if(!preg_match('/^[0-9]+$/', $this->client_id . $this->job_id))
			{
				return FALSE;
			}
		}

		if($this->item == NULL)
		{
			return FALSE;
		}
		
		//So... I had an issue making sure that all debits are negative.
		if($this->amount > 0)
		{
			$this->amount = $this->amount * -1;
		}
		
		return TRUE;
	}
	
	public function amount()
	{
		return $this->amount;
	}
	
	public function __toString()
	{
		return $this->item . ' $' . number_format($this->amount, 2); 
	}
}