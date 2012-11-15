<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructJob {
	//Job specific informatino
	public $id 				= NULL;
	public $service 		= '';
	
	//Links to other information
	public $client;
	public $requester;
	public $location;
	
	public $notes;
	public $assets;
	
	//Amounts
	public $debits;
	public $credits;
	
	//Dates
	public $date_added;
	public $date_updated;
	public $date_billed;
	
	public function __construct($json = NULL)
	{
		$this->client 		= new StructClient();
		$this->requester 	= new StructClient();
		
		$this->location		= new StructProperty();

		$this->debits	= new StructAccounting();
		$this->credits		= new StructAccounting();
		
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
		
		$this->id			= $json->id;
		$this->service		= $json->service;
		
		$this->date_added	= $json->date_added;
		$this->date_updated	= $json->date_updated;
		$this->date_billed	= $json->date_billed;
		
		if(isset($json->client))
		{
			$this->client = new StructClient();
			$this->client->set_from_json($json->client);
		}
		
		if(isset($json->requester))
		{
			$this->requester = new StructClient();
			$this->requester->set_from_json($json->requester);
		}
		
		if(isset($json->location))
		{
			$this->location = new StructProperty();
			$this->location->set_from_json($json->location);
		}
	}

	public function balance()
	{
		return $this->debits->total - $this->credits->total;
	}
	
	public function is_valid()
	{
		//TODO
		return TRUE;
	}
	
	public function __toString()
	{
		$str = '#' . $this->id . ' :: ';
		$str .= 'Location: ' . "\n" . (string)$this->location;
		$str .= "\n\n";
		$str .= 'Client: ' . "\n" . (string)$this->client;
	
		$str .= "\n\n";
		$str .= 'Debits: ' . "\n";
		$str .= (string)$this->debits;
		$str .= "\n\n";
		$str .= 'Credits: ' . "\n";
		$str .= (string)$this->credits;
		$str .= "\n\n";
		
		$total = number_format($this->debits->total - $this->credits->total, 2);
		
		for($i = 8 - strlen($total); $i > 0; $i--)
		{
			$total = ' ' . $total;
		}
		
		$str .= 'Balance due:                         ' . $total . "\n";
		
		
		return $str;
	}
}