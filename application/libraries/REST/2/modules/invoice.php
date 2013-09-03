<?PHP

class InvoiceAPI extends PrototypeAPI
{
	
	function __construct(&$API)
	{
		parent::__construct($API);
		
		$this->CI->load->model('Invoice');
		$this->CI->load->model('Client');
		$this->CI->load->model('Job');
	}
	
	public function get() 
	{
		$invoice = $this->CI->Invoice->get($this->API->id);
		
		if($invoice !== FALSE)
		{
			return $invoice;
		}
		else
		{
			$this->error = "No items found like '" . $this->API->id . "'.";
			return FALSE;	
		}
	}
}