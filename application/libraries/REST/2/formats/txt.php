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
		
		$str = str_replace('; ', ";\n", $str);
		$str = str_replace(':: ', "::\n", $str);
		
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
					$str .= $key . ' : ';
				}
				
				$str .= $this->recurse($value);
			}
		}
		else @$str .= (string)$data;
		
		return $str;
	}
}