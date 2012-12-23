<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Model {
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}
	
	/**
	 * Loads a search result and supporting information into a search_result class
	 *
	 * @author Matthew Solum
	 * @param $id
	 * @return Invoice
	 */
	public function load($id)
	{
		//code
		$invoice = $this->query();
	}
	
	/**
	 * Creates or updates an entry
	 *
	 * @author Matthew Solum
	 * @param $search
	 * @return False on failure, ID on success
	 */
	public function commit($search)
	{
		//code
		if($this->exists($search))
		{
			$this->create($search);
		}
		else
		{
			$this->update($search);
		}
	}
	
	/**
	 * Adds a new search
	 *
	 * @author Matthew Solum
	 * @param $search		
	 * @return FALSE on failure, search_id on success
	 */
	private function create($search)
	{
		//code
	}
	
	/**
	 * Reads a search given a search_id
	 *
	 * @author Matthew Solum
	 * @param $search_id
	 * @return Array
	 */
	private function read($id)
	{
		//code
	}
	
	/**
	 * Updates a search
	 *
	 * @author Matthew Solum
	 * @param $search
	 * @return bool
	 */
	private function update($search)
	{
		//code
		$this->delete($search->id);
		$this->create($search);
	}
	
	/**
	 * Deletes a search given a search_id
	 *
	 * @author Matthew Solum
	 * @param $search_id
	 * @return bool
	 */
	public function delete($id)
	{
		//code
	}
	
	/**
	 * Checks to see if a search exists
	 *
	 * @author Matthew Solum
	 * @param $search
	 * @return bool
	 */
	public function exists($search)
	{
		//code
		if ($search instanceof Search_result)
		{
			
		}
		else if (is_number($search))
		{
			
		}
	}
	
	/**
	 * Full text search
	 *
	 * @author Matthew Solum
	 * @param $query, $page = 0, $per_page = NULL, $modifiers = NULL
	 * @return Array of search_results
	 */
	public function find($query, $page = 0, $modifiers = NULL)
	{
		//code
		
	}
	
	public function client_commit($client)
	{
		$search_result = new StructSearch($client);
		
		echo $search_result->description('');
	}
}