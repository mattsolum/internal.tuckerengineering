<?PHP

class NameRandomizer
{
	private $CI = NULL;
	
	function __construct()
	{
		$this->CI =& get_instance();
		
		//$this->CI->Event->register('clientCreate', 'client_handler');
	}
	
	public function client_handler($e)
	{
		$e->name = str_shuffle($e->name);
		echo('client_handler called!' . "\n");
	}
}