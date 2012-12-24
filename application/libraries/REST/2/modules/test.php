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

	public function invoice_get()
	{
		$this->CI->load->model('Invoice');

		return $this->CI->Invoice->get(0, 4);
	}

	public function search_get()
	{
		$this->CI->load->model('Search');

		$query = $this->API->id;

		return $this->CI->Search->parse_query($query);
	}
}