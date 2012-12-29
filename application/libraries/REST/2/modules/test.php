<?PHP 

class TestAPI extends PrototypeAPI
{
	
	function __construct(&$API)
	{
		parent::__construct($API);
	}
	
	public function get()
	{
		return "Nothing to see here.";
	}

	public function migrate_get()
	{
		$this->CI->load->model('Map');
		$this->CI->load->model('Client');
		$json = '{"location":{"route":"2106 B Kenneth Avenue","sub_premise":"0","locality":"Austin","admin_level_1":"TX","postal_code":"78741"},"contact":[{"id":"333","type":"phone","info":"633-3727"}],"notes":[{"type_id":"333","type":"client","user":{"id":0},"text":"CLNTNO 333; CLNAME Kevin Tucker; ADDR1 2106 B Kenneth Avenue; ADDR2 0; CITY Austin; STATE TX; ZIP 78741; PHONE 633-3727; FAX ; EMAIL ; CONTACT ; CURBAL 0.00; BEGBAL 0.00"}],"name":"Kevin Tucker","id":"333"}';
		$client = new StructClient($json);
		$client->name = ucwords(strtolower($client->name));
		
		$geocoded_location = $this->CI->Map->parse_address($client->location);
		if($geocoded_location != FALSE)
		{
			$client->location = $geocoded_location;
		}

		return $this->CI->Client->commit($client) . "\n" . $client;
	}
}