<?PHP
	
class ApiTxt
{
	public $mime = 'text';
	private $API;
	
	function __construct(&$API)
	{	
		$this->API =& $API;
	}

	public function format($data, $error)
	{	
		$str = $this->recurse($data);
		
		$str = trim($str);
		
		return $str;
	}
	
	private function recurse($data)
	{
		$str = '';
		if(is_array($data))
		{
			foreach ($data as $key => $value) {
				$str .= "\n";
				
				if(!preg_match('/^[0-9]+$/', $key))
				{
					//'message' is used by default for the output of a string
					//Just ignore it. It adds no information to a text file
					if($key != 'message')
					{
						$str .= $key . ' : ';
					}
				}
				
				$str .= $this->recurse($value);
			}
		}
		else @$str .= (string)$data;
		
		return $str;
	}
}