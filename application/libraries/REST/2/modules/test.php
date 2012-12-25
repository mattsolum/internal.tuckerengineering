<?PHP 

class TestAPI extends PrototypeAPI
{
	
	function __construct(&$API)
	{
		parent::__construct($API);
	}
	
	public function get()
	{
		return "Nothing to see here.";
	}
}