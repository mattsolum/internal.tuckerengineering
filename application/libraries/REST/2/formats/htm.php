<?PHP
	
class ApiHtm
{
	public $mime = 'text/html';
	public $data = 'HTML module does not exist.';
	private $API;
			
	function __construct(&$API)
	{	
		$this->API =& $API;
	}

	public function format($data, $error)
	{	
		if(file_exists('html.php'))
		{
			include('html.php');
			
			if(class_exists('ApiHtml'))
			{
				$htm = new ApiHtml($this->API);
				return $htm->format($data, $error);
				
			}
		}
	}
}