<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructJob {
	//Job specific informatino
	public $id 				= NULL;
	
	//Links to other information
	public $client;
	public $requester;
	public $relation;
	public $location;
	
	public $notes;
	public $assets;
	
	//Accounting
	public $accounting;
	
	//Dates
	public $date_added;
	public $date_updated;
	public $date_billed;
	
	public function __construct($json = NULL)
	{
		$this->client 		= new StructClient();
		$this->requester 	= new StructClient();
		
		$this->location		= new StructProperty();

		$this->accounting	= new StructAccounting();
		
		$this->notes = array();
		
		if($json != NULL)
		{
			$this->set_from_json($json);
		}
	}
	
	public function set_id($id)
	{
		if(preg_match('/^[0-9]+$/', $id))
		{
			$this->accounting->set_job_id($id);
			$this->id = $id;
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function set_client_id($id)
	{
		$this->client->set_id($id);
		$this->accounting->set_client_id($id);
		
		return TRUE;
	}
	
	public function set_from_json($json)
	{
		if(is_string($json))
		{
			$json = json_decode($json);
		}
		
		$this->id			= $json->id;
		
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
		
		if(isset($json->accounting))
		{
			$this->accounting->set_from_json($json->accounting);
		}
	}
	
	public function service()
	{
		$service = '';
		
		$this->accounting->sort_debits();
				
		$service .= (isset($this->accounting->debits[0]))?$this->accounting->debits[0]->item:'';
		
		//TODO: This needs to be changed to be more generic/flexible. 
		//Right now it simply excludes "travel fee" from the service name.
		if(isset($this->accounting->debits[1]) && strtolower($this->accounting->debits[1]->item) != 'travel fee')
		{
			$service .= ' and ' . $this->accounting->debits[1]->item;
		}
		
		return $service;
	}

	public function balance()
	{
		return $this->debits->total() - $this->credits->total();
	}
	
	public function is_valid()
	{
		//TODO
		return TRUE;
	}
	
	public function __toString()
	{
		$str = '#' . $this->id . ' ' . $this->service() . " ::\n";
		$str .= "Location:\n\t" . str_replace("\n", "\n\t", (string)$this->location) . "\n";
		
		$str .= "Client:\n\t" . str_replace("\n", "\n\t", (string)$this->client) . "\n";
		
		$str .=  (string)$this->accounting . "\n";
		
		$total = ($this->accounting->debit_total() + $this->accounting->credit_total()) * -1;
		
		$str .= 'Balance due $' . number_format($total, 2);
		
		if(count($this->notes) > 0)
		{
			$str .= "\nNotes:";
			
			foreach($this->notes AS $note)
			{
				$str .= "\n$note";
			}
		}
		
		return $str;
	}
}