<?PHP
	
class ApiXml
{
	public $mime 	= 'text/html';
	public $data 	= '';
	private $CI 	= NULL;
	
	function __construct($data, $error)
	{
		$this->CI =& get_instance();
			
		if(is_array($data) && isset($data['pagination'])) unset($data['pagination']);
		
		if($error == FALSE && isset($this->arguments->view) && view_exists($this->arguments->view))
		{
			
			$this->data = $this->CI->load->view($this->arguments->view, array($this->type => $data), TRUE);
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
			
			$this->data = $html;	
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