<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructCredit
{
	//LOTS OF ID's
	//Maybe we could get away with fewer?
	public $client_id		= NULL;
	public $job_id			= NULL;
	public $ledger_id		= NULL;
	
	public $payment			= NULL;
	
	public $item			= NULL;
	
	public $date_added		= NULL;
	public $date_updated	= NULL;
	
	public $amount			= 0.0;
	
	public function __construct($json = NULL)
	{
		if(is_string($json))
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
		
		if($json != NULL)
		{
			$this->client_id	= $json->client_id;
			$this->job_id		= $json->job_id;
			
			$this->item			= $json->item;
			$this->amount		= $json->amount;
			
			if(isset($json->payment))
			{
				$this->payment = new StructPayment($json->payment);
			}
			
			$this->date_added	= $json->date_added;
			$this->date_posted	= $json->date_posted;
		}
	}
	
	
	public function is_valid()
	{
		if(	!$this->client_id || !$this->type || $this->amount == 0)
		{
			return FALSE;
		}
		
		if( ($this->type == 'credit' || $this->type == 'check') && $this->number == '')
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	public function amount()
	{
		return $amount;
	}
	
	public function __toString()
	{
		$string  = $this->item;
		
		if($this->payment != NULL)
		{
			$string .= ' ' . $this->payment->tender;
			$string .= ($this->payment->number != 0)?' *' . $this->payment->number:'';
		}
		
		$string .= ' $' . number_format($this->amount, 2);
		
		return $string;
	}
}