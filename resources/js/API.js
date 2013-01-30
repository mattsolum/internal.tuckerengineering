function API() {

}

function Client() {
	this.id 			= null;

	this.name 			= '';
	this.title 			= '';

	this.balance		= 0.0;

	this.location 		= new Property();

	this.contact 		= new Array();
	this.notes 			= new Array();

	this.date_added 	= 0;
	this.date_updated 	= 0;

	this.is_valid = function() {
		if(this.name == '' || !this.location.is_valid()) {
			return false;
		}

		for(var i = 0; i < this.contact.length; i++) {
			if(!this.contact[i].is_valid())
			{
				return false;
			}
		}

		return true;
	}

	this.add_note = function(user_id, text) {
		var note = new Note();

		note.type 		= 'Client';
		note.type_id 	= this.id;

		note.user_id 	= user_id;
		note.text 		= text;

		this.notes.push(note);
	}

	this.add_contact = function(type, info) {

		for(var i = 0; i < this.contact.length; i++) {
			if(this.contact[i].type.toLowerCase == type.toLowerCase && this.contact[i].info.toLowerCase == info.toLowerCase) {
				return false;
			}

			var contact = new StructContact();

			contact.set(type, info);
			this.contact.push(contact);
		}
	}

	this.set_id = function(id)
	{
		if(id.match(/^[0-9]+$/) != null)
		{	
			for(var i = 0; i < this.notes.length; i++)
			{
				this.notes[i].type_id 	= id;
				this.notes[i].type 		= 'client';
			}
			
			for(var i = 0; i < this.contact.length; i++)
			{
				this.contact[i].id = id;
			}

			this.id = id;
			
			return true;
		}
		
		return false;
	}
}

function Property() {
	this.id 			= null;

	this.route			= '';
	this.subpremise 	= '';
	this.locality		= '';
	this.admin_level_1	= '';
	this.admin_level_2 	= '';
	this.postal_code	= '';

	this.neighborhood 	= '';

	this.latitude 		= '';
	this.longitude 		= '';

	this.info 			= new Array();
	this.assets 		= new Array();
	this.notes 			= new Array();

	this.date_added 	= 0;
	this.date_updated 	= 0;

	this.is_valid = function() {
		return (this.location_valid() && this.meta_valid());
	}

	this.location_valid = function() {
		if(!this.is_pobox() && (this.number == '' || this.route == '')) {
			return false;
		}

		if(
			this.locality == '' ||
			this.admin_level_1 == '' ||
			this.postal_code == ''
		) {
			return false;
		}

		if(this.postal_code.match(/^[0-9-]+$/) == null) {
			return false;
		}

		return true;
	}

	this.meta_valid = function() {
		var result = true;

		if(this.info.length > 0) {
			this.info.forEach(function(value, key, arr) {
				if(key.match(/^[a-zA-Z_]+$/) == null) {
					result = false;
				}
			});
		}

		return result;
	}

	this.is_pobox = function() {
		if(this.route.match(/(Postal|(P(ost|\.)?( |-)?O(ffice|\.)?))/i) == null) {
			return false;
		}

		return true;
	}

	this.add_note = function(user_id, text) {
		var note = new Note();

		note.type 		= 'Property';
		note.type_id 	= this.id;

		note.user_id 	= user_id;
		note.text 		= text;

		this.notes.push(note);
	}

	this.set_addr_1 = function(addr)
	{
		if(addr != '')
		{
			var matches = addr.match(/^([0-9]+)(?!st|nd|rd|th)-?([0-9]+|[a-zA-Z]+)?/);

			if(matches.length > 0)
			{
				if(matches[1] != null)
				{
					this.number = matches[1].replace(/^\s+|\s+$/, '');
				}

				if(matches[2] != null)
				{
					this.subpremise = matches[2][0].replace(/^\s+|\s+$/, '');
				}

				addr = addr.replace(matches[0], '');
			}

			this.route = addr.replace(/^\s+|\s+$/, '');
		}
	}

	this.set_id = function(id)
	{
		if(id.match(/^[0-9]+$/) != null)
		{	
			for(var i = 0; i < this.notes.length; i++)
			{
				this.notes[i].type_id 	= id;
				this.notes[i].type 		= 'property';
			}
			
			this.id = id;
			
			return true;
		}
		
		return false;
	}
 }

function Job() {
	this.id				= null;

	this.client 		= new Client();
	this.requester 		= new Client();
	
	this.relation 		= null;

	this.notes 			= new Array();
	this.assets 		= new Array();

	this.accounting 	= null;

	this.date_added 	= 0;
	this.date_updated 	= 0;
	this.date_billed 	= 0;

	this.is_valid = function(strict = true) {
		if(!this.client.is_valid() || !this.location.is_valid() || !this.accounting.is_valid(strict))
		{
			return false;
		}

		if(this.requester.name != '' && !this.requester.is_valid())
		{
			return false;
		}

		for(var i = 0; i < this.notes.length; i++) {
			if(!this.notes[i].is_valid()) {
				return false;
			}
		}

		return true;
	}

	this.add_note = function(user_id, text) {
		var note = new Note();

		note.type 		= 'Job';
		note.type_id 	= this.id;

		note.user_id 	= user_id;
		note.text 		= text;

		this.notes.push(note);
	}

	this.set_id = function(id)
	{
		if(id.match(/^[0-9]+$/) != null)
		{
			this.accounting.set_job_id(id);
			
			for(i = 0; i < this.notes.length; i++)
			{
				this.notes[i].type_id 	= id;
				this.notes[i].type 		= 'job';
			}
			
			this.id = id;
			
			return true;
		}
		
		return false;
	}

	this.set_client_id = function(id)
	{
		this.client.set_id(id);
		this.accounting.set_client_id(id);
		
		return true;
	}

	this.service = function()
	{
		service = '';
		
		this.accounting.sort_debits();
				
		service += (this.accounting.debits[0] != undefined)?this.accounting.debits[0].item:'';
		
		//TODO: This needs to be changed to be more generic/flexible. 
		//Right now it simply excludes "travel fee" from the service name.
		if(this.accounting.debits[1] != undefined && this.accounting.debits[1].item.toLowerCase() != 'travel fee')
		{
			service += ' and ' + this.accounting.debits[1].item;
		}
		
		return service;
	}
}

function Contact() {
	this.id 			= null;
	this.type 			= '';
	this.info 			= '';
	this.note 			= '';

	this.is_valid = function(strict = true)
	{
		if(strict == true && this.id == NULL)
		{
			return false;
		}
		
		if(this.type == '' || this.info == '')
		{
			return false;
		}

		var method_name = this.type.toLowerCase() + '_valid';

		if(method_name in this)
		{
			return this[method_name]();
		}

		return true;
	}

	this.set = function(type, info, note = '') {
		type = type.replace(/[^a-zA-Z0-9 _-]/, '').toLowerCase();
		this.type = type;

		var method_name = 'prepare_' . type;

		if(method_name in this)
		{
			info = this[method_name](info);
		}

		this.info = info;
	}

	this.prepare_email = function(info) {
		return info.replace(/^\s+|\s+$/, '');
	}

	this.prepare_phone = function(info) {
		info = preg_replace('/[^0-9]/', '', info);
		phone = '';

		if(strlen(info) == 7)
		{
			//TODO: Fix this so it pulls from the settings database
			info = '512' . info;
		}

		for(var i = 0; i < info.length; i++)
		{
			phone += info[i];

			if(i == 2 || i == 5)
			{
				phone += '-';
			}
		}

		return phone;
	}

	this.prepare_fax = function(info) {
		return this.prepare_phone(info);
	}

	this.email_valid = function() {
		if(this.info.match(/^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/) != null) {
			return true;
		}

		return false;
	}

	this.phone_valid = function() {
		if(this.info.match(/^[(]?\d{3}[)]?\s?-?\s?\d{3}\s?-?\s?\d{4}$/) != null) {
			return true;
		}

		return false;
	}

	this.fax_valid = function() {
		return this.phone_valid();
	}
}

function Accounting() {
	this.credits 	= new Array();
	this.debits 	= new Array();

	this.is_valid = function(strict = true)
	{
		for(var i = 0; i < this.credits.length; i++)
		{
			if(!this.credits[i].is_valid()) return false;
		}
		
		for(var i = 0; i < this.debits.length; i++)
		{
			if(!this.debits[i].is_valid(strict)) return false;
		}
		
		return true;
	}

	this.set_client_id = function(id)
	{	
		for(var i = 0; i < this.debits.length; i++)
		{
			this.debits[i].client_id = id;
		}
		
		return true;
	}
	
	this.set_job_id = function(id)
	{
		for(var i = 0; i < this.debits.length; i++)
		{
			this.debits[i].job_id = id;
		}
		
		return true;
	}

	this.quicksort_by_property = function(arr, property, left = 0, right = NULL)
	{
		// when the call is recursive we need to change
		//the array passed to the function yearlier
		quicksort_by_property.array = new Array();
		if( right == NULL )
		{
			quicksort_by_property.array = arr;
			right = quicksort_by_property.array.length-1;//last element of the array
		}
		 
		var i = left;
		var j = right;
		 
		var tmp = quicksort_by_property.array[parseInt((left+right)/2)][property];
		 
		// partion the array in two parts.
		// left from tmp are with smaller values,
		// right from tmp are with bigger ones
		do
		{
			while( quicksort_by_property.array[i][property] < tmp )
			i++;
			 
			while( tmp < quicksort_by_property.array[j][property] )
			j--;
			 
			// swap elements from the two sides
			if( i <= j )
			{
				w = quicksort_by_property.array[i];
				quicksort_by_property.array[i] = quicksort_by_property.array[j];
				quicksort_by_property.array[j] = w;
				 
				i++;
				j--;
			}
		}while( i <= j );
		 
		// devide left side if it is longer the 1 element
		if( left < j )
		this.quicksort_by_property(NULL, property, left, j);
		 
		// the same with the right side
		if( i < right )
		this.quicksort_by_property(NULL, property, i, right);
		 
		// when all partitions have one element
		// the array is sorted
		 
		return quicksort_by_property.array;
	}

	this.sort_debits = function()
	{
		if(this.debits.length > 1)
		{
			this.debits = this.quicksort_by_property(this.debits, 'amount');
		}
	}

	this.debits_total = function()
	{
		return this.debit_total();
	}
	
	this.debit_total = function()
	{
		var total = 0;
		
		for(var i = 0; i < this.debits.length; i++)
		{
			total += this.debits[i].amount;
		}
		
		return total;
	}
	
	this.credits_total = function()
	{
		return this.credit_total();
	}
	
	this.credit_total = function()
	{
		var total = 0;
		
		for(var i = 0; i < this.credits.length; i++)
		{
			total += this.credits[i].amount;
		}
		
		return total;
	}

	this.total = function()
	{
		return this.credit_total() + this.debit_total();
	}
}

function Credit() {
	this.client_id			= null;
	this.job_id				= null;
	this.ledger_id			= null;
	
	this.payment			= null;
	
	this.item				= '';
	
	this.date_added			= 0;
	this.date_updated		= 0;
	
	this.amount				= 0.0;

	this.is_valid = function(){
		if(	this.job_id == null || this.client_id == null || this.amount == 0){
			return false;
		}
		
		if(this.payment != null && !this.payment.is_valid()){
			return false;
		}
		//I just concatinate them. Probably not the fastest method, but the least
		//number of lines.
		
		var ids = this.client_id + this.job_id;
		if(ids.match('/^[0-9]+$/') == null){
			return false;
		}
		
		if(this.amount < 0){
			this.amount = this.amount * -1;
		}
		
		return true;
	}

	public function amount()
	{
		return this.amount;
	}
}

function Debit() {
	this.ledger_id		= null;
	this.client_id		= null;
	this.job_id			= null;
	
	this.item			= '';
	this.amount			= 0.0;

	this.date_added		= 0;
	this.date_updated	= 0;

	this.is_valid = function(strict = false){
		if(strict == true){
			if(this.client_id == null || this.job_id == null){
				return false;
			}
			
			//I just concatinate them. Probably not the fastest method, but the least
			//number of lines.
			var ids = this.client_id + this.job_id;
			if(ids.match('/^[0-9]+$/') == null){
				return false;
			}
		}

		if(this.item == null){
			return false;
		}
		
		//So... I had an issue making sure that all debits are negative.
		if(this.amount > 0) {
			this.amount = this.amount * -1;
		}
		
		return true;
	}

	public function amount()
	{
		return this.amount;
	}
}

function Payment() {
	this.id 			= null;
	this.client_id 		= null;
	
	this.tender			= '';
	this.number			= null;
	this.amount			= 0.0;
	
	this.date_added		0;
	this.date_posted	0;

	this.is_valid = function() {
		if((this.tender == 'credit' || this.tender == 'check') && this.number == '')
		{
			return false;
		}

		return true;
	}
}

function Note() {
	this.id				= null;
	this.type_id 		= null;
	this.type			= '';
	
	this.user			= new User();
	
	this.text			= '';
	
	this.date_added		= 0;

	this.is_valid = function(){
		if(this.type_id == null || this.type == null || this.user.id === null || this.text == null)
		{
			return false;	
		}
		
		return true;
	}
}

function User() {
	this.id				= 0;
	this.name			= null;
	this.office_id		= null;
	
	this.email			= null;
	this.permissions	= null;
	this.password		= null;
	this.hash			= null;

	this.is_valid = function() {
		if(this.email == null || this.name == null || this.hash == null)
		{
			return false;
		}
	
		if(this.email.match(/^[a-z0-9!#$%&*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/) == null || this.hash.length < 20)
		{
			return false;
		}
		
		return true;
	}
}