<?PHP 

class TestAPI extends PrototypeAPI
{
	
	function __construct(&$API)
	{
		parent::__construct($API);
		$this->CI->load->model('Map');
		$this->CI->load->model('Property');
		$this->CI->load->helper('loader');
	}
	
	public function get()
	{
		return "Nothing to see here.";
	}
	
	public function extension_get()
	{
		$client = new StructClient('{"id":"4","name":"Brady Barnet","title":"Owner","location":{"id":"10","number":"13107","route":"Dessau Rd","subpremise":"400","locality":"Austin","admin_level_1":"Texas","admin_level_2":"","postal_code":"78754","neighborhood":"","latitude":"","longitude":"","info":{"foundation":"slab","reinforcement":"post-tension cables","roof_framing":"conventionally framed"},"assets":{},"notes":{},"date_added":"1345597698","date_updated":"1345607353"},"contact":[{"type":"email","info":"brady@superiorfoundationrepair.com"},{"type":"phone","info":"512-476-7700"}],"notes":null,"date_added":null,"date_updated":"1345607353"}');
		
		$search = new StructSearch($client);
		
		$search->description('brady austin texas');
		
		return 'Testing extensions and events!';
	}
	
	private function payment_test()
	{
		$this->CI->load->model('Payment');
		$this->CI->load->model('Job');
		
		$this->CI->load->helper('structures/payment');
		$this->CI->load->helper('structures/ledger');
		$this->CI->load->helper('structures/accounting');
		
		$ledger = array();
		
		$ledger[0] = new StructLedger();
		$ledger[0]->item = 'structural inspection';
		$ledger[0]->amount = 450.00;
		$ledger[0]->ledger_id = 1234;
		
		$ledger[1] = new StructLedger();
		$ledger[1]->item = 'written report';
		$ledger[1]->amount = 100.00;
		$ledger[1]->ledger_id = 1235;
		
		$ledger[2] = new StructLedger();
		$ledger[2]->item = 'travel fee';
		$ledger[2]->amount = 50.00;
		$ledger[2]->ledger_id = 1236;
		
		$payment = new StructPayment();
		$payment->type = 'Credit';
		$payment->number = '1234';
		$payment->ledger_id = '1534';
		$payment->amount = 300.00;

		$job = new StructJob('{"id":"1","service":"Structural Inspection Report","client":{"id":"2","name":"Tucker Engineering, Inc","title":null,"location":{"id":"1","number":"1311","route":"Chisholm Trail Rd","subpremise":"303","locality":"Round Rock","admin_level_1":"Texas","admin_level_2":"Williamson","postal_code":"78681","neighborhood":null,"latitude":null,"longitude":null,"info":{"foundation":"slab","reinforcement":"post-tension cables","roof_framing":"factory-built trusses"},"assets":{},"notes":{},"date_added":"1230811200","date_updated":"1343822400"},"contact":null,"notes":null,"date_added":"1275930000","date_updated":null},"requester":{"id":"2","name":"Tucker Engineering, Inc","title":null,"location":{"id":"1","number":"1311","route":"Chisholm Trail Rd","subpremise":"303","locality":"Round Rock","admin_level_1":"Texas","admin_level_2":"Williamson","postal_code":"78681","neighborhood":null,"latitude":null,"longitude":null,"info":{"foundation":"slab","reinforcement":"post-tension cables","roof_framing":"factory-built trusses"},"assets":{},"notes":{},"date_added":"1230811200","date_updated":"1343822400"},"contact":null,"notes":null,"date_added":"1275930000","date_updated":null},"location":{"id":"10","number":"13107","route":"Dessau Rd","subpremise":"400","locality":"Austin","admin_level_1":"Texas","admin_level_2":"","postal_code":"78754","neighborhood":"","latitude":"","longitude":"","info":{"foundation":"slab","reinforcement":"post-tension cables","roof_framing":"conventionally framed"},"assets":{},"notes":{},"date_added":"1345597698","date_updated":"1345607353"},"notes":null,"assets":null,"receivable":{"total":0},"payments":{"total":0},"date_added":"1345654800","date_updated":"1345654800","date_billed":null}');
		$job->debits->add($ledger[0]);
		$job->debits->add($ledger[1]);
		$job->debits->add($ledger[2]);
		
		$job->credits->add($payment);
		
		return $job;
	}
	
}