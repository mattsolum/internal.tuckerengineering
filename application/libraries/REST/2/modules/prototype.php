<?PHP

class PrototypeAPI
{
	public $error = 'Unknown.';
	public $CI;
	public $API;
	
	public function __construct(&$API)
	{
		$this->CI =& get_instance();
		$this->API =& $API;
	}
}