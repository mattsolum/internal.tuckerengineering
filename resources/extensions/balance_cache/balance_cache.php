<?PHP

class balance_cache {
	private $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	
	public function dirty_handler($data)
	{
		$type = $this->get_type($data);

		$method = $type . '_balance';

		if(method_exists($this, $method))
		{
			$this->$method($data);
		}
	}

	private function get_type($data)
	{
		return str_replace('struct', strtolower(get_class($data)));
	}

	private function client_balance($data)
	{
		
	}

	private function job_balance($data)
	{
		
	}

	private function invoice_balance($data)
	{
		
	}
}