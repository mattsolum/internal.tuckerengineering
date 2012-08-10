<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Job {
	//CI base class
	private $base = NULL;
	//Job specific informatino
	private $id = 0;
	public $description = '';
	//Links to other information
	public $client = NULL;
	public $requester = NULL;
	public $location = NULL;
	//Amounts
	public $amount = 0;
	public $adjustment = 0;
	public $credits = 0;
	//Dates
	public $date_requested = NULL;
	public $date_billed = NULL;
	
	//Loads a job given a job_id
	function __construct($id = NULL)
	{
		$this->base =& get_instance();
		if ($id != NULL)
		{
			$this->load($id);
		}
	}
	
	/**
	 * Loads a job and supporting information into a job class
	 *
	 * @author Matthew Solum
	 * @param $id
	 * @return BOOL
	 */
	public function load($id)
	{
		//code
		if($this->exists($id))
		{
			$this->id = $id;
		}
	}
	
	/**
	 * Creates or Updates the job by need
	 *
	 * @author Matthew Solum
	 * @param 
	 * @return False on failure, ID on success
	 */
	public function commit()
	{
		//code
		$this->base->db->trans_start();
		
		$this->base->job->commit($this);
		$this->client->commit();
		$this->location->commit();
		
		if($this->requester != NULL) $this->requester->commit();
		
		$this->base->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->_error_message();
		} 
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
	public function exists($id = NULL)
	{
		if($id == NULL)
		{
			
		}
		else
		{
			
		}
	}
	
	/**
	 * Verifies that the Job has all the information required
	 *
	 * @author Matthew Solum
	 * @param 
	 * @return bool
	 */
	private function verify()
	{
		//code
	}
}