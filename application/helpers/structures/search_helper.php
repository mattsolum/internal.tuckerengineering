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
	 * I just want to make this clear: I am embarrased of this code,
	 * but no one else has published a better method of doing this.
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
		
		//Maximum number of characters for the description
		$max_length = 256;
		
		
		foreach($sentences AS $number => $sentence)
		{
			//I kept getting an "index does not exist" error. This solved it.
			if(!isset($kcount[$number]))$kcount[$number] = 0;
			
			foreach($keywords AS $keyword)
			{
				if(strstr($sentence, $keyword))
				{
					//The number of keywords contained in sentence $number
					//is stored in $kcount at index $number
					$kcount[$number]++;
				}
			}
		}
		
		
		//Now we parse out the two sentences with the highest density of keywords.
		$sentence_density = array();
		
		for($i = 0; $i < 2; $i++)
		{
			$sentence_density[$i] = array('number' => 0, 'density' => 0);
			
			foreach($kcount AS $key => $value)
			{
				if($value > $sentence_density[$i]['density'])
				{
					$sentence_density[$i]['density'] = $value;
					$sentence_density[$i]['number'] = $key;
				}
			}
			
			//Remove from our list before we go searching for the next lowest.
			unset($kcount[$sentence_density[$i]['number']]);
		}
		
		//We are now going to reorganize the two sentences so that the earlier sentence comes first
		$sentence_1 = $sentences[min($sentence_density[0]['number'], $sentence_density[1]['number'])];
		$sentence_2 = $sentences[max($sentence_density[0]['number'], $sentence_density[1]['number'])];
		
		//Now ship them off to get the excerpts.
		$sentence_1_excerpt = $this->excerpt($sentence_1, $keywords);
		$sentence_2_excerpt = $this->excerpt($sentence_2, $keywords);
		
		return $sentence_1_excerpt . '...' . $sentence_2_excerpt;
	}
	
	//This appears to work well enough to move on.
	//It should be changed to use a better heuristic for choosing an excerpt from the sentence.
	public function excerpt($sentence, $keywords)
	{
		$earliest_keyword_position = strlen($sentence);
		$earliest_keyword = '';
		$max_length = 64;
		
		if(strlen($sentence) <= $max_length)
		{
			return $sentence;
		}
		
		foreach($keywords AS $keyword)
		{
			
			$pos = stripos($sentence, $keyword);
			if($pos !== FALSE && $pos < $earliest_keyword_position + strlen($keyword) / 2)
			{
				$earliest_keyword_position = $pos + strlen($keyword) / 2;
			}
		}
		
		//If the first keyword is close enough to the begining of the sentence 
		//just return the first $max_length characters of the sentence
		$start = $earliest_keyword_position - $max_length / 2;
		
		//TODO: Change this function to attempt to include as many keywords as it can
		//Assuming that they are greatly separated in the sentence.		
		return substr($sentence, $start, $max_length);
	}
}