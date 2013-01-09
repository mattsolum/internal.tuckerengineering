<?PHP
	
class ApiHtml
{
	public $mime 	= 'text/html';
	private $CI 	= NULL;
	private $API;
		
	function __construct(&$API)
	{	
		$this->API =& $API;
		$this->CI =& get_instance();
	}

	public function format($data, $error)
	{
			
		if(is_array($data) && isset($data['pagination'])) unset($data['pagination']);
		
		if($error == FALSE && isset($this->API->arguments->view) && view_exists($this->API->arguments->view))
		{
			return $this->CI->load->view($this->API->arguments->view, array($this->API->type => $data, 'id' => trim($this->API->id)), TRUE);
		}
		else
		{
			$html  = '<span class="result">';
			$html .= ($error)?'error</span>':'success</span>';
			
			$html .= '<ul>';
			
			foreach($data as $value)
			{
				$html .= $this->recurse($value);
			}
			
			$html .= '</ul>';
			
			return $html;	
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