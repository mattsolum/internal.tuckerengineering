<?PHP
	
class ApiHtm
{
	public $mime = 'application/html';
	public $data = 'HTML module does not exist.';
	
	function __construct($data, $error)
	{	
		if(file_exists('html.php'))
		{
			include('html.php');
			
			if(class_exists('ApiHtml'))
			{
				return new ApiHtml($data, $error);
			}
		}
	}
}