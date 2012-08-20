<?PHP
	
class ClientAPI extends PrototypeAPI
{
	
	public function __construct(&$API)
	{
		parent::__construct($API);
		$this->CI->load->model('Client');
	}
	
	public function get()
	{	
		$result = $this->CI->Client->get($this->API->id);
		
		if($result !== FALSE)
		{
			return $result;
		}
		else
		{
			$this->error = 'Client with ID "' . $this->API->id . '" does not exist.';
			return FALSE;	
		}
		
		return FALSE;
	}
	
	public function post()
	{
		
	}
	
	public function put()
	{
		$this->post($this->API);
	}
	
	public function delete()
	{
		
	}
	
}