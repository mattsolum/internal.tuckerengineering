<?PHP

class NameRandomizer
{
	private $CI = NULL;
	
	function __construct()
	{
		$this->CI =& get_instance();
	}
	
	public function client_handler($e)
	{
		$e->name = str_shuffle($e->name);
		echo('client_handler called!' . "\n");
	}
	
	public function install()
	{
		echo("Install NameRandomizer");
		$this->CI->Event->register('clientCreate', 'client_handler');
	}
	
	public function uninstall()
	{
		$this->CI->Event->unregister();	
	}
}