<?PHP 

class TestAPI extends PrototypeAPI
{
	
	function __construct(&$API)
	{
		parent::__construct($API);
	}
	
	public function get()
	{
		$location = new StructProperty();
		$location->number 			= 3755;
		$location->route 			= 'Cap.TX Hwy. S';
		$location->locality			= 'Austin';
		$location->admin_level_1 	= 'Texas';
		$location->postal_code		= 78704;
		$location->id 				= 0;

		$client = new StructClient();
		$client->name 		= 'Keller Williams Realty (S)';
		$client->location 	= $location;

		$phone = new StructContact();
		$phone->type = 'phone';
		$phone->info = '512-448-4111';

		$contact = new StructContact();
		$contact->type = 'contact';
		$contact->info = 'Linda Schooley';

		$client->contact[] = $phone;
		$client->contact[] = $contact;
		//CLNTNO 18; CLNAME Keller Williams Realty (S); ADDR1 3755 Cap.TX Hwy. S.; ADDR2 0; CITY Austin; STATE TX; ZIP 78704; PHONE 512-448-4111; FAX ; EMAIL ; CONTACT Linda Schooley; CURBAL 0.00; BEGBAL 0.00 
		//CLNTNO 68; CLNAME Marilyn Wilson; ADDR1 221 Comanche Lane; ADDR2 0; CITY Leander; STATE TX; ZIP 78645; PHONE 267-3413; FAX ; EMAIL ; CONTACT Marilyn; CURBAL 0.00; BEGBAL 0.00

		var_dump($client->is_valid());

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

	public function checksum_get()
	{
		$this->CI->load->model('Client');
		$this->CI->load->model('Checksum');

		$data_1 = new StructClient('{"id":"1","name":"ACE FOUNDATION REPAIR","title":"","balance":0,"location":{"id":null,"route":"W Saint Johns Ave","subpremise":"","locality":"Austin","admin_level_1":"Texas","admin_level_2":"Travis","postal_code":"78752","neighborhood":"","latitude":"30.33884","longitude":"-97.71665","info":[],"assets":[],"notes":[],"date_added":0,"date_updated":0,"number":"624"},"contact":[{"id":"1","type":"phone","info":"512-451-8120","note":""},{"id":"1","type":"contact","info":"JERRY SALLAS","note":""}],"notes":[{"id":null,"type_id":"1","type":"Client","user":{"id":0,"name":null,"office_id":null,"email":null,"permissions":null,"password":null,"hash":null},"text":"Imported from the old database. The old information was: 1; ACE FOUNDATION REPAIR; 624 W. ST JOHN; 0; AUSTIN; TX; 78752; 512-451-8120; ; JERRY SALLAS; ; 0.00; 0.00; 0.00","date_added":0,"user_id":0}],"date_added":0,"date_updated":0}');
		$data_2 = new StructClient('{"id":"1","name":"ACE FOUNDATION REPAIR","title":"","balance":1500.00,"location":{"id":"1","number":"624","route":"W Saint Johns Ave","subpremise":"","locality":"Austin","admin_level_1":"Texas","admin_level_2":"Travis","postal_code":"78752","neighborhood":"","latitude":"30.33884","longitude":"-97.71665","info":[],"assets":{},"notes":[{"id":"1","type_id":"1","type":"property","user":{"id":"0","name":"Io","office_id":null,"location":{"id":null,"number":"","route":"","subpremise":"","locality":"","admin_level_1":"","admin_level_2":"","postal_code":"","neighborhood":"","latitude":"","longitude":"","info":[],"assets":{},"notes":[],"date_added":null,"date_updated":null}},"text":"I imported this property record.","date_added":"1368219554"}],"date_added":"1368219554","date_updated":"1368219554"},"contact":[{"id":null,"type":"contact","info":"JERRY SALLAS","note":""},{"id":null,"type":"phone","info":"512-451-8120","note":""}],"notes":[{"id":"2","type_id":"1","type":"client","user":{"id":"0","name":"Io","office_id":null,"location":{"id":null,"number":"","route":"","subpremise":"","locality":"","admin_level_1":"","admin_level_2":"","postal_code":"","neighborhood":"","latitude":"","longitude":"","info":[],"assets":{},"notes":[],"date_added":null,"date_updated":null}},"text":"Imported from the old database. The old information was: 1; ACE FOUNDATION REPAIR; 624 W. ST JOHN; 0; AUSTIN; TX; 78752; 512-451-8120; ; JERRY SALLAS; ; 0.00; 0.00; 0.00","date_added":"1368219554"},{"id":"3","type_id":"1","type":"client","user":{"id":"0","name":"Io","office_id":null,"location":{"id":null,"number":"","route":"","subpremise":"","locality":"","admin_level_1":"","admin_level_2":"","postal_code":"","neighborhood":"","latitude":"","longitude":"","info":[],"assets":{},"notes":[],"date_added":null,"date_updated":null}},"text":"I imported this client record.","date_added":"1368219554"}],"date_added":"1368219554","date_updated":"1368219554"}');
			
		echo($data_1 . "\n\n\n--------\n\n\n" . $data_2 . "\n\n\n");

		echo($this->CI->Checksum->hash($data_1) . "\n" . $this->CI->Checksum->hash($data_2));
	}

	public function dirty_get()
	{
		$this->CI->load->model('Event');
		$id = 261;
		$this->CI->Event->trigger('job.dirty', $id);
	}
}