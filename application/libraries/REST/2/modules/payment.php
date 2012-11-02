<?PHP

class PaymentAPI extends PrototypeAPI
{
	public function __construct(&$API)
	{
		parent::__construct($API);
		
		$this->CI->load->model('Payment');
	}	
}