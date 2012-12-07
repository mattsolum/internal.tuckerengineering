<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructSearch
{	
	public $id 				= NULL;
	public $type 			= NULL;
	public $title			= NULL;
	public $body			= NULL;
	public $link			= NULL;
	public $date_added 		= 0;
	public $date_updated 	= 0;
	
	private $lines			= 3;
	private $words			= 7;
	
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
		
		$lines = setting('application.search.excerpt.lines');
		$words = setting('application.search.excerpt.words');
		
		$this->lines = ($lines != FALSE)?$lines:3;
		$this->words = ($words != FALSE)?$words:7;
	}
	
	private function client($item)
	{
		$this->id 			= $item->id;
		$this->type 		= 'client';
		$this->body			= (string)$item;
		$this->title		= $item->name;
		$this->link			= BASE_URL . 'clients/' . $item->id;
	}
	
	private function job($item)
	{
		$this->$id 		= $item->id;
		$this->type 	= 'job';
		$this->body		= (string)$item;
		$this->title	= $item->service . ' at ' . $item->property->route;
		$this->link		= BASE_URL . 'jobs/' . $item->id;
	}
	
	public function excerpt($keywords)
	{
		$words 		= explode(' ', str_replace("\n", ' ', $this->body));
		$keywords 	= explode(' ', preg_replace('/\W\s*/', ' ', $keywords));
		
		$matched	= array();
		
		$excerpts 	= array();
		
		$num_excerpt = 2 * $this->words - 1;
		
		foreach($words AS $index => $word)
		{
			foreach($keywords AS $keyword)
			{
				//Does the keyword exist within the part of the text we are checking?
				if(stristr($word, $keyword))
				{
					$excerpt = $words[$index];
					$offset = 0;
					
					for($i = 1; $i < $this->words; $i++)
					{
						if(isset($words[$index - $i]))
						{
							$excerpt = $words[$index - $i] . ' ' . $excerpt;
						}
					
						$excerpt = (isset($words[$index + $i]))?$excerpt . ' ' . $words[$index + $i]:'';
					}
					
					$numkeywords = $this->count_keywords($excerpt, $keywords);
					
					if(isset($excerpts[count($excerpts) - 1]))
					{
						//There is previous item in the excerpts array
						if(($index - $excerpts[count($excerpts) - 1]['offset'] < $this->words) && $numkeywords > $excerpts[count($excerpts) - 1]['keywords'])
						{
							//Our segment covers the same block of text, but includes more keywords so the previous excerpt is replaced
							$excerpts[count($excerpts) - 1] = array('text' => $excerpt, 'keywords' => $numkeywords, 'offset' => $index);
						}
						elseif($index - $excerpts[count($excerpts) - 1]['offset'] > $this->words)
						{
							//Our segment covers a different block of text, so it is included
							$excerpts[] = array('text' => $excerpt, 'keywords' => $numkeywords, 'offset' => $index);
						}
						else
						{
							//Our segment covers the same block of text but does not
							//capture any more keywords than the previous one, so it is excluded
						}
					}
					else
					{ 
						//Our segment is the first one and is included.
						$excerpts[] = array('text' => $excerpt, 'keywords' => $numkeywords, 'offset' => $index);
					}
				}
			}
		}
		
		//Sort it by keywords
		$excerpts = $this->sort_by_keywords($excerpts);
		
		//It was sorted in ascending order so get the last $lines number of elements
		//So that we have the $lines highest keyword density segments
		$excerpts = array_slice($excerpts, $this->lines * -1);
		
		//Sort it again by offset, so that it appears in the right order.
		$excerpts = $this->sort_by_offset($excerpts);
		
		$return = '';
		
		//Each excerpt is 2*$this->words - 1 long so that it will capture all additional keywords in its range
		//Now that we have pared everything down to a managable number
		//We will brute force it into giving us the highest keyword density
		//$words number of words in the excerpt
		foreach($excerpts AS $exc)
		{
			$excerpt_text = explode(' ', $exc['text']);
			$most_keywords = 0;
			$most_keywords_text = '';
			
			for($i = 0; $i < count($excerpt_text); $i++)
			{
				$string = implode(' ', array_slice($excerpt_text, $i, $this->words));
				$nkw = $this->count_keywords($string, $keywords);
				
				if($nkw == $exc['keywords'])
				{
					$return .= '...' . $string;
					continue 2;
				}
				elseif ($nkw >= $most_keywords)
				{
					$most_keywords = $nkw;
					$most_keywords_text = $string;
				}
			}
			
			$return .= '...' . $most_keywords_text;
		}
		
		$return .= '...';
		
		$return = preg_replace('/\W+\.\.\./', '...', $return);
		return $return;
	}
	
	//Wraps search terms in a wrapper stored in the object
	public function highlight_search_terms($text, $keywords)
	{
		//Now we can accept strings and arrays!
		if(is_string($keywords))
		{
			$keywords = explode(' ', $keywords);
		}
		
		$keywords = implode('|', $keywords);
		return preg_replace("/($keywords)[a-zA-Z]*/i", $this->wrapper_open . '$0' . $this->wrapper_close, $text);
	}	
	
	private function count_keywords($text, $keywords)
	{
		$matches = array();
		$keywords = implode('|', $keywords);
		
		preg_match_all("/$keywords/i", $text, $matches);
		
		return count($matches[0]);
	}
	
	private function quicksort_by_property($arr, $property, $left = 0, $right = NULL)
	{
		// when the call is recursive we need to change
		//the array passed to the function yearlier
		static $array = array();
		if( $right == NULL )
		{
			$array = $arr;
			$right = count($array)-1;//last element of the array
		}
		 
		$i = $left;
		$j = $right;
		 
		$tmp = $array[(int)(($left+$right)/2)][$property];
		 
		// partion the array in two parts.
		// left from $tmp are with smaller values,
		// right from $tmp are with bigger ones
		do
		{
			while( $array[$i][$property] < $tmp )
			$i++;
			 
			while( $tmp < $array[$j][$property] )
			$j--;
			 
			// swap elements from the two sides
			if( $i <= $j )
			{
				$w = $array[$i];
				$array[$i] = $array[$j];
				$array[$j] = $w;
				 
				$i++;
				$j--;
			}
		}while( $i <= $j );
		 
		// devide left side if it is longer the 1 element
		if( $left < $j )
		$this->quicksort_by_property(NULL, $property, $left, $j);
		 
		// the same with the right side
		if( $i < $right )
		$this->quicksort_by_property(NULL, $property, $i, $right);
		 
		// when all partitions have one element
		// the array is sorted
		 
		return $array;
	}
	
	private function sort_by_offset($arr)
	{
		return $this->quicksort_by_property($arr, 'offset');
	}
	
	private function sort_by_keywords($arr)
	{
		return $this->quicksort_by_property($arr, 'keywords');
	}
	
}