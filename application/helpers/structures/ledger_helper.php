<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructLedger
{
	public $ledger_id		= NULL;
	public $client_id		= NULL;
	public $job_id			= NULL;

	public $item			= NULL;
	public $amount			= 0.0;

	public $date_added		= NULL;
	public $date_updated	= NULL;
	
	public function amount()
	{
		return $this->amount;
	}
	
	public function stringify()
	{
		$spaces1 = '';
		for($i = 30 - strlen($this->item); $i > 0; $i--)
		{
			$spaces1 .= ' ';
		}
		
		$formatted = number_format($this->amount, 2);
		$spaces2 = '';
		for($i = 8 - strlen($formatted); $i > 0; $i--)
		{
			$spaces2 .= ' ';
		}
		
		return $this->ledger_id . ' ' . $this->item . $spaces1 . "= " . $spaces2 . $formatted;
	}
	
	public function __toString()
	{
		return number_format($this->amount, 2);
	}
}