<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'libraries/Job.php'

class Job_model extends Model {
	
	static $base = NULL;
	
	public function Job_model()
	{
		parent::construct();
		self::$base =& get_instance();	
	}
	
	/**
	 * Loads a job and supporting information into a job class
	 *
	 * @author Matthew Solum
	 * @param $id
	 * @return Job
	 */
	public function load($id)
	{
		//code
	}
	
	/**
	 * Creates or updates an entry
	 *
	 * @author Matthew Solum
	 * @param $job
	 * @return False on failure, ID on success
	 */
	public function commit($job)
	{
		//code
		if($this->exists($job))
		{
			$this->create($job);
		}
		else
		{
			$this->update($job);
		}
	}
	
	/**
	 * Adds a new job
	 *
	 * @author Matthew Solum
	 * @param $job		
	 * @return FALSE on failure, job_id on success
	 */
	private function create($job)
	{
		//code
	}
	
	/**
	 * Reads a job given a job_id
	 *
	 * @author Matthew Solum
	 * @param $job_id
	 * @return Array
	 */
	private function read($id)
	{
		//code
	}
	
	/**
	 * Updates a job
	 *
	 * @author Matthew Solum
	 * @param $job
	 * @return bool
	 */
	private function update($job)
	{
		//code
		$this->delete_job($job->id);
		$this->create_job($job);
	}
	
	/**
	 * Deletes a job given a job_id
	 *
	 * @author Matthew Solum
	 * @param $job_id
	 * @return bool
	 */
	public function delete($id)
	{
		//code
	}
	
	/**
	 * Checks to see if a job exists
	 *
	 * @author Matthew Solum
	 * @param $job
	 * @return bool
	 */
	public function exists($job)
	{
		//code
		if ($job instanceof Job)
		{
			
		}
		else if (is_number($job))
		{
			
		}
	}
	
	/**
	 * Generates search terms
	 *
	 * @author Matthew Solum
	 * @param $job
	 * @return Search_result if $return_search is set to TRUE, String otherwise
	 */
	private function search_terms($job, $return_search = TRUE)
	{
		//code
	}
	
}