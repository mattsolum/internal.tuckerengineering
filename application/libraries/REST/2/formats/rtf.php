<?PHP
	
class ApiRtf
{
	public $mime 	= 'application/rtf';
	private $CI 	= NULL;
	private $API;
	private $class 	= 'API_Result';
		
	function __construct(&$API)
	{	
		$this->API =& $API;
		$this->CI =& get_instance();

		$this->CI->load->library('pd4ml/pd4ml.php');

		$this->PD4ML = new PD4ML();
		$this->CI->output->set_header('Content-disposition: attachment; filename=invoice_' . $this->API->id . '.rtf');
	}

	public function format($data, $error)
	{
		return $this->PD4ML->rtf_from_url(str_ireplace('.rtf', '.html', current_url() . '?view=' . $this->API->arguments->view));
	}
}