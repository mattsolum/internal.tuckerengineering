<?PHP
	
class ApiXml
{
	public $mime = 'text/xml';
	private $API;
	
	function __construct(&$API)
	{	
		$this->API =& $API;
	}
	
	public function format($data, $error)
	{
		$xml = '<APIResponse><result>';
		$xml .= ($error)?'error</result>':'success</result>';
		
		if(!is_array($data))
		{
			$data = array('message' => $data);
		}

		foreach($data as $key => $value)
		{
			//If the root array is numerically indexed, 
			//which would be improper for XML,
			//use the request type as the tag name
			if(preg_match('/^[0-9]/', $key))
			{
				$xml .= $this->recurse($this->API->type, $value );
			}
			else 
			{
				$xml .= $this->recurse($key, $value);
			}
		}
		
		$xml .= '</APIResponse>';
		
		return $xml;
	}
	
	private function recurse($key, $value)
	{
		$xml = "<$key>";
		$flag = false;
		
		if(is_array($value) || is_object($value))
		{
			foreach ($value as $key2 => $value2) {
				if(preg_match('/^[0-9]+$/', $key2))
				{
					if(!$flag)
					{
						$xml = '';
						$flag = true;
					}
					
					$xml .= $this->recurse($key, $value2);
				}
				else
				{
					$xml .= $this->recurse($key2, $value2);	
				}
			}
		}
		else 
		{
			if(preg_match('/&|</', $value))
			{
				$value = '<![CDATA[' . $value . ']]>';
			}

			$xml .= $value;	
		}
		
		if(!$flag) $xml .= "</$key>";
		
		return $xml;
	}
}