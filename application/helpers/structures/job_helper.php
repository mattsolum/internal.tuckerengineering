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
	public $amount 			= 0.0;
	public $travel_fee 		= 0.0;
	public $adjustment 		= 0.0;
	public $payed			= 0.0;
	
	public $payments;
	//Dates
	public $date_added;
	public $date_updated;
	public $date_billed;
	
	public function __construct()
	{
		$this->client 		= new StructClient();
		$this->requester 	= new StructClient();
		$this->location		= new StructProperty();
	}
	
	public function set_from_json($json)
	{
		if(is_string($json))
		{
			$json = json_decode($json);
		}
		
		$this->id			= $json->id;
		$this->service		= $json->service;
		
		$this->amount		= $json->amount;
		$this->travel_fee	= $json->travel_fee;
		$this->adjustment	= $json->adjustment;
		
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
			$this->requester = new StructRequester();
			$this->requester->set_from_json($json->requester);
		}
		
		if(isset($json->location))
		{
			$this->location = new StructProperty();
			$this->location->set_from_json($json->location);
		}
	}
	
	public function is_valid()
	{
		return TRUE;
	}
	
	public function __toString()
	{
		$str = '#' . $this->id . ' :: ';
		$str .= 'Location: ' . "\n" . (string)$this->location;
		$str .= "\n\n";
		$str .= 'Client: ' . "\n" . (string)$this->client;
	
	
		return $str;
	}
}