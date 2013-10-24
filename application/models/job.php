<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Job extends CI_Model {
	
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->model('Accounting');
		$this->CI->load->model('Note');
		$this->CI->load->model('Client');
		$this->CI->load->model('Property');
		$this->CI->load->model('Checksum');
		$this->CI->load->model('Asset');
	}
	
	public function insert($job)
	{
		return $this->commit($job);
	}
	
	//Return the ID on success
	//and FALSE on failure
	public function commit($job)
	{
		//First ensure the job is valid
		if(!$job->is_valid())
		{
			log_message('error', 'Error commiting job: job is invalid.');
			return FALSE;
		}

		//Everything should be encapsulated in a transaction
		$this->CI->db->trans_start();
		
		//Are we updating or creating a job?
		$id = $this->exists($job);
		if($id !== FALSE)
		{
			$job->id = $id;
			//The job exists, has it changed?
			if($this->CI->Checksum->compare($job))
			{
				//Checksum will return true if the record is the same
				//as the record in the database
				//If they are the same, return true and stop; nothing to see here!
				
				return $id;
			}

			$this->CI->Event->trigger('job.commit.update', $job);
			//It exists, keep the date added and assign the old ID
			$data['job_id'] 	= $id;
			$data['date_added']	= $job->date_added;
		}
		else
		{
			$this->CI->Event->trigger('job.commit.create', $job);
			//It does not exist. Set date_added to now.
			$data['job_id'] 	= ($job->id != null)?$job->id:$this->get_next_index();
			$data['date_added']	= ($job->date_added != '')?$job->date_added:now();	
		}
		
		//The ID for client and property will be returned by their
		//respective commit functions.
		
		$data['client_id']				= $this->CI->Client->commit($job->client);
		$data['property_id']			= $this->CI->Property->commit($job->location);
		$data['requester_relationship']	= $job->relation;
		$data['date_billed']			= $job->date_billed;
		$data['date_updated']			= now();
		
		//Set job and client ID for accounting items
		$job->accounting->set_job_id($data['job_id']);
		$job->accounting->set_client_id($data['client_id']);
		
		//Commit accounting and store result
		$accounting = $this->CI->Accounting->commit($job->accounting);
		
		//If the requester is not set assume that the client is the requester
		if($job->requester->name == '' || $job->requester->name == $job->client->name)
		{
			$data['requester_id']	= $data['client_id'];
		}
		else
		{
			$data['requester_id']	= $this->CI->Client->commit($job->requester);
		}
		
		//Check for failures. Strict boolean FALSE because the ID returned might be 0.
		if($data['property_id'] === FALSE || $data['client_id'] === FALSE || $data['requester_id'] === FALSE || $accounting === FALSE)
		{
			$this->CI->db->trans_complete();
			log_message('error', 'Error in Job model method commit: failure in property, client, or account commit.');
			return FALSE;
		}
		
		//Insert assets
		foreach($job->assets AS $asset)
		{
			$this->CI->Asset->commit($asset, 'job', $data['job_id']);
		}

		//Insert the data into the jobs table
		if($id === FALSE)
		{
			$query = $this->CI->db->insert('jobs', $data);
		}
		else
		{
			$this->CI->db->where('job_id', $data['job_id']);
			$query = $this->CI->db->update('jobs', $data);	
		}
		
		$job->set_id($data['job_id']);
		$this->CI->Note->commit($job->notes);
		
		//End the transaction
		$this->CI->db->trans_complete();
		
		//Handle errors in the jobs transaction.
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Job method insert: transaction failed.');
			return FALSE;
		}
		else
		{
			//Update the stored checksum.
			$this->CI->Checksum->store($job);
			$this->CI->Event->trigger('job.dirty', $job->id);
			
			return $data['job_id'];
		}
	}
	
	public function delete($id)
	{
		$this->CI->Event('delete_job', $id);

		$id = preg_replace('/[^0-9]/', '', $id);
		
		$this->CI->db->trans_start();
		
		$this->CI->db->delete('jobs', array('job_id' => $id));
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Job method delete: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function get($id)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$query = $this->CI->db->get_where('jobs', array('job_id' => $id));
		
		$job = new StructJob();
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			$job->id			= $result->job_id;
			$job->date_added	= $result->date_added;
			$job->date_updated	= $result->date_updated;
			$job->date_billed	= $result->date_billed;
			$job->relation		= $result->requester_relationship;
			
			$job->client		= $this->CI->Client->get($result->client_id);

			if($result->client_id == $result->requester_id)
			{
				$job->requester = $job->client;
			}
			else
			{
				$job->requester		= $this->CI->Client->get($result->requester_id);
			}
			
			$job->accounting	= $this->CI->Accounting->get_by_job($id);
			
			$job->location		= $this->CI->Property->get($result->property_id);
			
			$job->notes			= $this->CI->Note->get_by_job($job->id);
			
			return $job;
		}
		else
		{
			log_message('Error', 'Error in Job method get: no data found with given ID.');
			return FALSE;
		}
	}

	public function get_by_client_id($client_id, $end = 10, $start = 0)
	{
		$client_id = $this->CI->Client->get_id($client_id);
		$jobs = array();

		$this->CI->db->limit($end);
		$this->CI->db->order_by('job_id', 'desc');
		$query = $this->CI->db->get_where('jobs', array('client_id' => $client_id));

		if($query->num_rows() > 0)
		{
			foreach($query->result() AS $row)
			{
				$jobs[] = $this->get($row->job_id);
			}
		}

		return $jobs;
	}

	public function get_unpaid_jobs_by_client_id($client_id)
	{
		$client_id = $this->CI->Client->get_id($client_id);
		$jobs = array();

		$where = array();
		$where['client_id'] = $client_id;
		$where['balance <'] = '0';

		$this->CI->db->order_by('job_id', 'desc');
		$query = $this->CI->db->get_where('jobs', $where);

		if($query->num_rows() > 0)
		{
			foreach($query->result() AS $row)
			{
				$jobs[] = $this->get($row->job_id);
			}
		}

		return $jobs;
	}

	public function number_of_jobs_for_client_id($client_id)
	{
		$client_id = $this->CI->Client->get_id($client_id);

		$query = $this->CI->db->get_where('jobs', array('client_id' => $client_id));

		return $query->num_rows();
	}
	
	//Return ID on success and FALSE on failure
	public function exists($job)
	{
		if($job->id != NULL)
		{		
			
			$where = array('job_id' => $job->id);
			$query = $this->CI->db->get_where('jobs', $where);
			
			if($query->num_rows() > 0)
			{
				$result = $query->row();
				
				return $job->id;
			}
		}
		
		return FALSE;
	}
	
	private function get_next_index()
	{
		$this->CI->db->from('jobs');
		$this->CI->db->order_by('job_id', 'DESC');
		$this->CI->db->limit(1);
		
		$query = $this->CI->db->get();
		
		if($query->num_rows() > 0)
		{
			$row = $query->row(0);
			
			return $row->job_id + 1;
		}
		else
		{
			return 1;
		}
	}
}