<?PHP

class balance_cache {
	private $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('Accounting');
	}

	
	public function dirty_handler($e)
	{
		//the dirty event should pass an ID. 
		//The type of object is encapsulated as the first segment in the event name.
		//i.e. job.dirty passes 123, client.dirty passes 456, etc.
		
		$type = $e->segment(0);

		$method = $type . '_balance';

		if(method_exists($this, $method))
		{
			$this->$method($e->data);
		}
	}

	private function client_balance($id)
	{
		$client_balance = $this->CI->Accounting->get_balance_by_client($id);

		$this->CI->db->where('client_id', $id);
		$this->CI->db->update('clients', array('balance' => $client_balance));
	}

	private function job_balance($id)
	{
		//The client should be updated too.
		$query = $this->CI->db->get_where('jobs', array('job_id' => $id));

		if($query->num_rows() > 0)
		{
			$row = $query->row(0);

			$client_id = $row->client_id;

			$this->client_balance($client_id);
		}

		$job_balance = $this->CI->Accounting->get_balance_by_job($id);

		$this->CI->db->where('job_id', $id);
		$this->CI->db->update('jobs', array('balance' => $job_balance));
	}

	private function invoice_balance($id)
	{
		
	}
}