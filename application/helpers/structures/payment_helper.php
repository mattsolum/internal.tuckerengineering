<?php defined('BASEPATH') OR exit('No direct script access allowed');

//Stores information on a Payment
class StructPayment
{
	public $id;
	public $client_id;
	
	public $tender;
	public $number;
	public $amount;
	
	public $date_added;
	public $date_posted;
	
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
		
		if($json != NULL)
		{
			$this->id 			= $json->id;
			$this->client_id	= $json->client_id;
			$this->tender		= $json->tender;
			$this->number		= $json->number;
			$this->amount		= $json->amount;
			$this->date_added	= $json->date_added;
			$this->date_posted	= $json->date_posted;
		}
	}
	
	public function __toString()
	{
		$string  = $this->tender;
		$string .= ($this->number != 0)?' *' . $this->number:'';
		$string .= ' $' . number_format($this->amount, 2);
		
		return $string;
	}

	public function is_valid() {
		if(($this->tender == 'credit' || $this->tender == 'check') && $this->number == '')
		{
			return FALSE;
		}

		if($this->amount == 0)
		{
			return FALSE;
		}

		return TRUE;
	}
}