<?PHP

class JobAPI extends PrototypeAPI
{
	
	public function __construct(&$API)
	{
		parent::__construct($API);
		
		$this->CI->load->model('Map');
		$this->CI->load->model('Property');
		$this->CI->load->model('Client');
		$this->CI->load->model('Job');
	}
	
	public function post()
	{
		$job = new StructJob($this->CI->input->post('data'));
		
		if($this->CI->Job->commit($job))
		{
			return TRUE;
		}
		else
		{
			$this->error = 'Failed to commit job.';
			return FALSE;
		}
	}
	
	public function put()
	{
		return $this->post();
	}
	
	public function get()
	{
		$job = $this->CI->Job->get($this->API->id);
		
		if($job !== FALSE)
		{
			return $job;
		}
		else
		{
			$this->error = 'Job with ID #' . $this->API->id . ' does not exist.';
			return FALSE;
		}
	}
}