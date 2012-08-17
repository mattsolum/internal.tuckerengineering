<?PHP
	
class ClientAPI extends PrototypeAPI
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get($data)
	{
		return 'ClientAPI Get';
	}
	
	public function post($data)
	{
	
	}
	
	public function put($data)
	{
		$this->post($data);
	}
	
	public function delete($data)
	{
		
	}
	
}