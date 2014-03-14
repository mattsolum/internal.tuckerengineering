<?PHP
	
class ApiPng
{
	public $mime 	= 'image/png';
	private $CI 	= NULL;
	private $API;
	private $class 	= 'API_Result';
		
	function __construct(&$API)
	{	
		$this->API =& $API;
		$this->CI =& get_instance();

		$this->CI->load->library('wkhtmltox/wkhtmltoimg');
		$this->lib = substr(BASEPATH, 0, -7) . APPPATH . 'libraries/';

		//$this->CI->output->set_header('Content-disposition: attachment; filename=invoice_' . $this->API->id . '.pdf');
	}

	public function format($data, $error)
	{
		$wkhtml = new WKHTMLTOIMG();
			
		if(is_array($data) && isset($data['pagination'])) unset($data['pagination']);
		
		if($error == FALSE && isset($this->API->arguments->view) && view_exists($this->API->arguments->view))
		{
			$html = $this->CI->load->view($this->API->arguments->view, array($this->API->type => $data, 'id' => trim($this->API->id)), TRUE);
			return $wkhtml->img_from_html($html);
		}
		else
		{
			$html  = '<span class="' . $this->class . '">';
			$html .= ($error)?'error</span>':'success</span>';
			
			$html .= '<ul>';
			
			foreach($data as $value)
			{
				$html .= $this->recurse($value);
			}
			
			$html .= '</ul>';
			
			return $wkhtml->pdf_from_html($html);	
		}
	}
	
	private function recurse($value)
	{
		$html = '<li>';
		$flag = false;
		
		if(is_array($value) || is_object($value))
		{
			$html .= '<ul>';
			
			foreach ($value as $value2) {
				$html .= $this->recurse($value2);
			}
			
			$html .= '</ul>';
		}
		else 
		{
			$html .= $value;	
		}
		
		$html .= '</li>';
		
		return $html;
	}
}