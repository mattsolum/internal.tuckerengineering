<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Search_result
{	
	protected $id = 0;
	public $relevance = 0;
	public $text = '';
	public $link = '';
	public $type = '';
	public $type_id = '';
	public $date = 0;
	
	/**
	 * Outputs a description with keywords highlighted
	 *
	 * @author Matthew Solum
	 * @param $keywords
	 * @return string
	 */
	public function description($keywords)
	{
		//code
	}
}