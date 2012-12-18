<?PHP

class AccountingAPI extends PrototypeAPI
{
	public function __construct(&$API)
	{
		parent::__construct($API);
		
		$this->CI->load->model('Accounting');
	}
	
	public function payment_post()
	{
		
	}
	
	public function payment_put()
	{
	
	}
	
	public function job_get()
	{
		return $this->CI->Accounting->get_debits_by_job($this->API->id);
	}
	
	public function client_get()
	{
		$this->CI->load->model('Client');
		$id = $this->CI->Client->get_id($this->API->id);
				
		if($id != FALSE)
		{
			return $this->CI->Accounting->get_debits_by_client($id);
		}
		else 
		{
			$this->error = 'Client of ID \'' . $id .'\' not found.';
			return FALSE;
		}
	}
	
	public function client_balance_get()
	{
		$this->CI->load->model('Client');
		$id = $this->CI->Client->get_id($this->API->id);
				
		if($id != FALSE)
		{
			return $this->CI->Accounting->get_client_balance($id);
		}
		else 
		{
			$this->error = 'Client of ID \'' . $id .'\' not found.';
			return FALSE;
		}
	}
}