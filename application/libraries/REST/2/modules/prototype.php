<?PHP

class PrototypeAPI
{
	public $error = 'Unknown.';
	public $CI;
	public $API;
	
	public function __construct($API)
	{
		$this->CI =& get_instance();
		$this->API =& $API;
	}
	
	public function get()
	{
		$this->error = 'This HTTP request method is not available on this module.';
		return FALSE;
	}
	
	public function set()
	{
		$this->error = 'This HTTP request method is not available on this module.';
		return FALSE;
	}
	
	public function put()
	{
		$this->error = 'This HTTP request method is not available on this module.';
		return FALSE;
	}
	
	public function delete()
	{
		$this->error = 'This HTTP request method is not available on this module.';
		return FALSE;
	}
}