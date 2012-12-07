<?php defined('BASEPATH') OR exit('No direct script access allowed');

//Stores a list of ledger items
class StructAccounting
{
	//Itemized
	public $items = array();
	
	public function __construct($json = NULL)
	{
		if($json !== NULL)
		{
			$json = json_decode($json);
			
			if($json !== NULL && get_classname($json) == 'StructAccounting')
			{
				$this->items = $json->items;
			}
		}
	}
		
	public function total()
	{
		$total = 0;
		
		foreach($items AS $item)
		{
			$total += $item->amount;
		}
		
		return $total;
	}
	
	public function is_valid()
	{
		//todo
		return TRUE;
	}
}