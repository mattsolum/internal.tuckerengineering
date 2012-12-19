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
	
	public function put()
	{
		$job = new StructJob('{"id":"3","client":{"id":"8","name":"Joe Bob","title":"","balance":0,"location":{"id":"14","number":"9500","route":"W Parmer Ln.","subpremise":"","locality":"Austin","admin_level_1":"Texas","admin_level_2":"","postal_code":"78717","neighborhood":"","latitude":"","longitude":"","info":{},"assets":{},"notes":{},"date_added":null,"date_updated":"1355970014"},"contact":null,"notes":null,"date_added":null,"date_updated":"1355970014"},"requester":{"id":"8","name":"Joe Bob","title":"","balance":0,"location":{"id":"14","number":"9500","route":"W Parmer Ln.","subpremise":"","locality":"Austin","admin_level_1":"Texas","admin_level_2":"","postal_code":"78717","neighborhood":"","latitude":"","longitude":"","info":{},"assets":{},"notes":{},"date_added":null,"date_updated":"1355970014"},"contact":null,"notes":null,"date_added":null,"date_updated":"1355970014"},"relation":null,"location":{"id":"14","number":"9500","route":"W Parmer Ln.","subpremise":"","locality":"Austin","admin_level_1":"Texas","admin_level_2":"","postal_code":"78717","neighborhood":"","latitude":"","longitude":"","info":{},"assets":{},"notes":{},"date_added":null,"date_updated":"1355970014"},"notes":[],"assets":null,"accounting":{"credits":[],"debits":[]},"date_added":"1355970014","date_updated":"1355970014","date_billed":null}');
		
		$job->client->id 					= NULL;
		$job->client->name					= 'John Smith';
		$job->client->location->id 			= NULL;
		$job->client->location->number 		= 3406;
		$job->client->location->route		= 'Meadow Brook Ln';
		$job->client->location->locality 	= 'Round Rock';
		$job->client->location->postal_code	= '78681';
		
		$debit_1 = new StructDebit();
		$debit_1->item 		= 'Structural Inspection';
		$debit_1->amount	= -550;
		
		$job->accounting->debits[] = $debit_1;
		
		var_dump($this->CI->Job->commit($job));
		
		return $job;
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