<?PHP 

class TestAPI extends PrototypeAPI
{
	
	function __construct(&$API)
	{
		parent::__construct($API);
		$this->CI->load->model('Map');
		$this->CI->load->model('Property');
		$this->CI->load->helper('loader');
	}
	
	public function get()
	{
		return "Nothing to see here.";
	}
}