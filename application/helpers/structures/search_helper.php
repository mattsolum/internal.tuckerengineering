<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructSearch
{	
	public $id 				= NULL;
	public $type 			= NULL;
	public $keywords 		= NULL;
	public $date_added 		= 0;
	public $date_updated 	= 0;
	
	public function __construct($item)
	{
		if(is_string($item))
		{
			$json = json_decode($item);
			
			//json_decode returns NULL if the string is not formatted correctly.
			if($json !== NULL)
			{
				//Look, it *was* json! now we will overwrite $item and move along.
				//Programmers accustomed to strictly typed languages will cry
				$item = $json;
				unset($json);
			}
		}
		
		//If it is an object we run a function to populate this object based
		//on which one it is.
		//By using a switch statement rather than parsing a function out of the
		//classname we avoid possible security issues.
		switch(get_class($item))
		{
			case 'StructClient':
				$this->client($item);
				break;
			case 'StructJob':
				$this->job($item);
				break;
			default:
				$this->generic($item);
		}
	}
	
	private function client($item)
	{
		$this->id 		= $item->id;
		$this->type 	= 'client';
		$this->keywords = (string)$item;
	}
	
	private function job($item)
	{
		$this->$id 		= $item->id;
		$this->type 	= 'job';
		$this->keywords = (string)$item;
	}
	
	/**
	 * Outputs a description with keywords highlighted
	 *
	 * @author Matthew Solum
	 * @param $keywords
	 * @return string
	 */
	public function description($keywords)
	{
		//Set them case insensitive. I may decide to change this later.
		$sentences	= explode(';', strtolower($this->keywords));
		$keywords 	= explode(' ', strtolower($keywords));
		
		//Number of keywords per sentence
		$kcount = array();
		
		foreach($sentences AS $number => $sentence)
		{
			if(!isset($kcount[$number]))$kcount[$number] = 0;
			
			foreach($keywords AS $keyword)
			{
				if(strstr($sentence, $keyword))
				{
					$kcount[$number]++;
				}
			}
		}
		
		$sentence_density = array();
	}
}