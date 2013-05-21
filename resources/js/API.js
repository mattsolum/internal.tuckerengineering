function API() {
	this.root = 'http://local/internal.tuckerengineering/';
	this.url = 'api/v2/';


	this.post = function() {
		this.property = function(property) {
			var end = '.json';
			if(property instanceof Property && property.is_valid()) {
				if(property.id != null) {
					end = '/' + property.id + '.json';
				}

				this.ajax('http://local/internal.tuckerengineering/api/v2/property' + end, property);
			}
		}

		this.job = function(job) {
			var end = '.json';
			if(job instanceof Job && job.is_valid()) {
				if(job.id != null) {
					end = '/' + job.id + '.json';
				}

				this.ajax('http://local/internal.tuckerengineering/api/v2/job' + end, job);
			}
		}

		this.payment = function(payment) {
			var end = '.json';
			if(payment instanceof Payment && payment.is_valid()) {
				if(payment.id != null) {
					end = '/' + payment.id + '.json';
				}

				this.ajax('http://local/internal.tuckerengineering/api/v2/payment' + end, payment);
			}
		}

		this.invoice = function(invoice) {
			var end = '.json';
			if(invoice instanceof Invoice && invoice.is_valid()) {
				if(invoice.id != null) {
					end = '/' + invoice.id + '.json';
				}

				this.ajax('http://local/internal.tuckerengineering/api/v2/invoice' + end, invoice);
			}
		}
	}

	this.post_client = function(client) {
		$.fn.MSDebug('API::post::client()');
		var end = '.json';
		if(client instanceof Client && client.is_valid()) {
			if(client.id != null) {
				end = '/' + client.id + '.json';
			}

			this.ajax(this.root + this.url + 'client' + end, client);
		}
	}

	this.ajax = function(url, data) {
			var message = '';
			var status = false;

			if(typeof data == 'string') {
				data = JSON.stringify(data);
			}

			$.fn.MSDebug('AJAXIFY! ' + url);

			$.ajax({
				type: 		'POST',
				url: 		url,
				data: 		{data: data},
				aync: 		false,
				success: 	function(returned) {
					if(returned.result == undefined)
					{
						$.fn.MSDebug(returned);
					}

					if(returned.result == 'success')
					{
						$.fn.MSDebug('Success! ' + url + ' returned id ' + returned.data['id']);
					}
					else
					{
						$.fn.MSDebug(returned.data.message);
					}
				},
				error: 		function(jqxhr) {
					var response = JSON.parse(jqxhr.responseText);
					
					if(response.data.message == undefined)
					{
						$.fn.MSDebug(jqxhr.responseText);
					}
					else
					{
						$.fn.MSDebug(response.data.message);
					}
				}
			});
		}

	this.get = function() {

	}
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
			$.fn.MSDebug('Invalid; name is not set or location is invalid.');
			return false;
		}

		for(var i = 0; i < this.contact.length; i++) {
			if(!this.contact[i].is_valid())
			{
				$.fn.MSDebug('Invalid; a contact method is invalid.');
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

	this.add_contact_item = function(type, info) {

		for(var i = 0; i < this.contact.length; i++) {
			if(this.contact[i].type.toLowerCase() == type.toLowerCase() && this.contact[i].info.toLowerCase() == info.toLowerCase()) {
				return false;
			}
		}

		var contact = new Contact();

		contact.id = this.id;

		contact.set(type, info);
		this.contact.push(contact);
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
		//$.fn.MSDebug('Property::is_valid() ' + JSON.stringify(this));
		return (this.location_valid() && this.meta_valid());
	}

	this.location_valid = function() {
		
		if(!this.is_pobox() && (this.number == '' || this.route == '')) {
			$.fn.MSDebug('Invalid; Not a po box and number and route are not set. [' + this.number + ' ' + this.route + ']');
			return false;
		}

		if(
			this.locality == '' ||
			this.admin_level_1 == '' ||
			this.postal_code == ''
		) {
			$.fn.MSDebug('Invalid; city, state or postal code is not set.');
			return false;
		}

		if(this.postal_code.match(/^[0-9-]+$/) == null) {
			$.fn.MSDebug('Invalid; postal code is invalid.');
			return false;
		}

		return true;
	}

	this.meta_valid = function() {
		
		var result = true;

		if(this.info.length > 0) {
			this.info.forEach(function(value, key, arr) {
				if(key.match(/^[a-zA-Z_]+$/) == null) {
					$.fn.MSDebug('Invalid; meta value has illegal characters.');
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

			if(matches != null && matches.length > 0)
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

	this.set_city_state = function(addr) {
		var sections = addr.split(',');

		this.admin_level_1 = sections[1].replace(/^\s+|\s+$/, '');
		this.locality = sections[0].replace(/^\s+|\s+$/, '');
	}

	this.set_admin_level_1 = function(a) {
		if(a.length == 2) {
			var abbr = new Array();
			abbr['AL'] = 'Alabama';
			abbr['AK'] = 'Alaska';
			abbr['AZ'] = 'Arizona';
			abbr['AR'] = 'Arkansas';
			abbr['CA'] = 'California';
			abbr['CO'] = 'Colorado';
			abbr['CT'] = 'Connecticut';
			abbr['DE'] = 'Delaware';
			abbr['FL'] = 'Florida';
			abbr['GA'] = 'Georgia';
			abbr['HI'] = 'Hawaii';
			abbr['ID'] = 'Idaho';
			abbr['IL'] = 'Illinois';
			abbr['IN'] = 'Indiana';
			abbr['IA'] = 'Iowa';
			abbr['KS'] = 'Kansas';
			abbr['KY'] = 'Kentucky';
			abbr['LA'] = 'Louisiana';
			abbr['ME'] = 'Maine';
			abbr['MD'] = 'Maryland';
			abbr['MA'] = 'Massachusetts';
			abbr['MI'] = 'Michigan';
			abbr['MN'] = 'Minnesota';
			abbr['MS'] = 'Mississippi';
			abbr['MO'] = 'Missouri';
			abbr['MT'] = 'Montana';
			abbr['NE'] = 'Nebraska';
			abbr['NV'] = 'Nevada';
			abbr['NH'] = 'Hampshire';
			abbr['NJ'] = 'Jersey';
			abbr['NM'] = 'Mexico';
			abbr['NY'] = 'York';
			abbr['NC'] = 'Carolina';
			abbr['ND'] = 'Dakota';
			abbr['OH'] = 'Ohio';
			abbr['OK'] = 'Oklahoma';
			abbr['OR'] = 'Oregon';
			abbr['PA'] = 'Pennsylvania';
			abbr['RI'] = 'Island';
			abbr['SC'] = 'Carolina';
			abbr['SD'] = 'Dakota';
			abbr['TN'] = 'Tennessee';
			abbr['TX'] = 'Texas';
			abbr['UT'] = 'Utah';
			abbr['VT'] = 'Vermont';
			abbr['VA'] = 'Virginia';
			abbr['WA'] = 'Washington';
			abbr['WV'] = 'Virginia';
			abbr['WI'] = 'Wisconsin';
			abbr['WY'] = 'Wyoming';

			this.admin_level_1 = abbr[a.toUpperCase()];
		}
		else
		{
			this.admin_level_1 = a;
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

	this.location 		= new Property();

	this.notes 			= new Array();
	this.assets 		= new Array();

	this.accounting 	= new Accounting();

	this.date_added 	= 0;
	this.date_updated 	= 0;
	this.date_billed 	= 0;

	this.is_valid = function(strict) {
		strict = typeof strict !== 'undefined' ? strict : true;

		if(/*!this.client.is_valid() || */!this.location.is_valid() || !this.accounting.is_valid(true))
		{
			$.fn.MSDebug('Invalid; client, location or accounting is invalid.');
			return false;
		}

		if(this.requester.name != '' && !this.requester.is_valid())
		{
			$.fn.MSDebug('Invalid; requester is invalid.');
			return false;
		}

		for(var i = 0; i < this.notes.length; i++) {
			if(!this.notes[i].is_valid()) {
				$.fn.MSDebug('Invalid; a note is invalid.');
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

	this.add_item = function(item_name, amount)
	{
		//Clean it up!
		item_name = item_name.replace(/\s*&\s*/, ' and ');
		item_name = item_name.replace(/([^a-zA-Z0-9, -]|^\s+|\s+$|written\s+)/ig, '');
		item_name = item_name.replace(/\s+/g, ' ');

		if(item_name == '')
		{
			item_name = 'Engineering service';
		}

		if(item_name.match(/verbal/i) && item_name.replace(/(and )?verbal( report)?/i, '') != '')
		{
			item_name = item_name.replace(/(and )?verbal( report)?/i, '');
		}
		else
		{
			if(item_name.match(/and report/i) && amount - 100 > 0)
			{
				amount -= 100;

				this.new_debit('Written report', 100.00);
			}
			else if(item_name.match(/and letter/i) && amount - 100 > 0)
			{
				amount -= 100;

				this.new_debit('Letter', 100.00);
			}
			else if(item_name.match(/and (construction |repair )?certification/i) && amount - 100 > 0)
			{
				amount -= 100;

				this.new_debit('Certification', 100.00);

				item_name = item_name.replace(/and (construction |repair )?certification/i, '');
			}

			item_name = item_name.replace(/and (report|letter)/i, '');
		}

		if(item_name.match(/structural inspection/i) && amount >= 450)
		{
			amount -= 450;

			this.new_debit('Structural Inspection', 450.00);

			if(amount > 0)
			{
				amount = 0;

				this.new_debit('Property type and travel surcharges', amount);
			}
		}

		if(amount > 0)
		{
			this.new_debit(item_name.replace(/\s+/, ' ').replace(/^\s+|\s+$|^\s*-\s*|\s*-\s*$/g, ''), amount);
		}

	}

	this.new_debit = function(name, amount)
	{
		var debit = new Debit();
		debit.item = name;
		debit.amount = amount;
		debit.job_id = this.id;
		debit.client_id = this.client.id;
		debit.date_added = this.date_billed;

		this.accounting.debits.push(debit);
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

	this.is_valid = function(strict)
	{
		strict = typeof strict !== 'undefined' ? strict : true;

		if(strict == true && this.id == null)
		{
			//$.fn.MSDebug('Invalid; strict is set and the ID is not.');
			return false;
		}
		
		if(this.type == '' || this.info == '')
		{
			//$.fn.MSDebug('Invalid; type or info is not set.');
			return false;
		}

		var method_name = this.type.toLowerCase() + '_valid';

		if(method_name in this)
		{
			return this[method_name]();
		}

		return true;
	}

	this.set = function(type, info, note) {
		note = typeof note !== 'undefined' ? note : '';

		type = type.replace(/[^a-zA-Z0-9 _-]/, '').toLowerCase();
		this.type = type;

		var method_name = 'prepare_' + type;

		if(method_name in this)
		{
			info = this[method_name](info);
		}

		this.info = info;
	}

	this.prepare_email = function(info) {
		return info.replace(/^\s+|\s+$/g, '');
	}

	this.prepare_phone = function(info) {
		info = info.replace(/[^0-9]/g, '');
		var phone = '';

		//$.fn.MSDebug('Contact::prepare_phone; ' + info);

		if(info.length == 7)
		{
			//TODO: Fix this so it pulls from the settings database
			info = '512' + info;
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
		if(this.info.match(/^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i) != null) {
			return true;
		}

		//$.fn.MSDebug('Invalid; email address "' + this.info + '" is not valid.');
		return false;
	}

	this.phone_valid = function() {
		if(this.info.match(/^[(]?\d{3}[)]?\s?-?\s?\d{3}\s?-?\s?\d{4}$/) != null) {
			return true;
		}

		//$.fn.MSDebug('Invalid; phone number "' + this.info + '" is not valid.');
		return false;
	}

	this.fax_valid = function() {
		return this.phone_valid();
	}
}

function Accounting() {
	this.credits 	= new Array();
	this.debits 	= new Array();

	this.is_valid = function(strict)
	{
		strict = typeof strict !== 'undefined' ? strict : true;

		for(var i = 0; i < this.credits.length; i++)
		{
			if(!this.credits[i].is_valid())
			{	
				$.fn.MSDebug('Invalid; credit is not valid.');
				return false;
			}
		}
		
		for(var i = 0; i < this.debits.length; i++)
		{
			if(!this.debits[i].is_valid(strict))
			{
				$.fn.MSDebug('Invalid; debit is not valid.');
				return false;
			}
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

	this.quicksort_by_property = function(arr, property, left, right)
	{
		left = typeof left !== 'undefined' ? left : 0;
		right = typeof right !== 'undefined' ? right : null;

		// when the call is recursive we need to change
		//the array passed to the function yearlier
		this.quicksort_by_property.array = new Array();
		if( right == null )
		{
			this.quicksort_by_property.array = arr;
			right = this.quicksort_by_property.array.length-1;//last element of the array
		}
		 
		var i = left;
		var j = right;
		 
		var tmp = this.quicksort_by_property.array[parseInt((left+right)/2)][property];
		 
		// partion the array in two parts.
		// left from tmp are with smaller values,
		// right from tmp are with bigger ones
		do
		{
			while( this.quicksort_by_property.array[i][property] < tmp )
			i++;
			 
			while( tmp < this.quicksort_by_property.array[j][property] )
			j--;
			 
			// swap elements from the two sides
			if( i <= j )
			{
				w = this.quicksort_by_property.array[i];
				this.quicksort_by_property.array[i] = this.quicksort_by_property.array[j];
				this.quicksort_by_property.array[j] = w;
				 
				i++;
				j--;
			}
		}while( i <= j );
		 
		// devide left side if it is longer the 1 element
		if( left < j )
		this.quicksort_by_property(null, property, left, j);
		 
		// the same with the right side
		if( i < right )
		this.quicksort_by_property(null, property, i, right);
		 
		// when all partitions have one element
		// the array is sorted
		 
		return this.quicksort_by_property.array;
	}

	this.sort_debits = function()
	{
		if(this.debits.length > 1)
		{
			this.debits = this.quicksort_by_property(this.debits, 'amount').reverse();
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
			total += parseFloat(this.debits[i].amount);
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
			$.fn.MSDebug('Invalid; either the job or client ID is not set or the amount is zero.');
			return false;
		}
		
		if(this.payment != null && !this.payment.is_valid()){
			$.fn.MSDebug('Invalid; payment is set but not valid.');
			return false;
		}
		//I just concatinate them. Probably not the fastest method, but the least
		//number of lines.
		
		var ids = this.client_id + this.job_id;
		if(ids.match(/^[0-9]+$/) == null){
			$.fn.MSDebug('Invalid; the client or job ID is invalid.');
			return false;
		}
		
		if(this.amount < 0){
			this.amount = this.amount * -1;
		}
		
		return true;
	}

	this.make_payment = function(amount, tender, number)
	{
		number = typeof number !== 'undefined' ? number : '';

		this.payment = new Payment();
		this.payment.client_id = this.client_id;
		this.payment.amount = amount;
		this.payment.tender = tender;
		this.payment.number = number;

		this.payment.date_added = this.date_added;
		this.amount = amount;
		this.item = 'Payment';
	}

	this.amount = function()
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

	this.is_valid = function(strict){
		strict = typeof strict !== 'undefined' ? strict : true;

		if(strict == true){
			if(this.client_id == null || this.job_id == null){
				$.fn.MSDebug('Invalid; client and/or job ID are not set.');
				return false;
			}
			
			//I just concatinate them. Probably not the fastest method, but the least
			//number of lines.
			var ids = this.client_id + this.job_id;
			if(ids.match(/^[0-9]+$/) == null){
				$.fn.MSDebug('Invalid; either the client or job ID is invalid. c' + this.client_id + ', j' + this.job_id + ', ids' + ids);
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

	this.amount = function()
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
	
	this.date_added		= 0;
	this.date_posted	= 0;

	this.is_valid = function() {
		if((this.tender == 'credit' || this.tender == 'check') && this.number == '')
		{
			$.fn.MSDebug('Invalid; the tender that is set requires a identifying number.');
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
			$.fn.MSDebug('Invalid; type ID, type, ID, user ID, or body text is not set.');
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
			$.fn.MSDebug('Invalid; email, name or hash is not set.');
			return false;
		}
	
		if(this.email.match(/^[a-z0-9!#$%&*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/) == null || this.hash.length < 20)
		{
			$.fn.MSDebug('Invalid; email address is invalid.');
			return false;
		}
		
		return true;
	}
}

function invoice() {
	var id 			= null;

	var client		= new Client();
	var jobs		= new Array();

	var date_added	= null;
	var date_sent	= null;

	this.is_valid = function() {
		if(!this.client.is_valid())
		{
			$.fn.MSDebug('Invalid; client is invalid.');
			return false;
		}

		if(this.jobs.length == 0)
		{
			$.fn.MSDebug('Invalid; no jobs are included in this invoice.');
			return false;
		}

		for(var i = 0; i < this.jobs.length; i++)
		{
			if(!this.jobs[i].is_valid())
			{
				$.fn.MSDebug('Invalid; a job is invalid.');
				return false;
			}
		}

		return true;
	}

	/**
	 * Sums the totals of all included jobs
	 * @return float
	 */
	this.debits_total = function()
	{
		var total = 0;

		for(var i = 0; i < this.jobs.length; i++) {
			total += this.jobs[i].accounting.debits_total();
		}

		return total;
	}

	/**
	 * Sums the balance of all included jobs
	 * @return float
	 */
	this.balance = function()
	{
		var total = 0;

		for(var i = 0; i < this.jobs.length; i++) {
			total += this.jobs[i].balance();
		}

		return total;
	}

	/**
	 * Gets the date, as GMT unix timestamp, of the last payment 
	 * applied to a job on this invoice. 
	 * 
	 * If no credits have been applied to any
	 * job in this invoice it will return FALSE.
	 * 
	 * @return BOOL FALSE on failure, int on success
	 */
	this.date_paid = function()
	{
		var date_paid = FALSE;

		for(var i = 0; i < this.jobs.length; i++) {
			for(var ii = 0; i < this.jobs[i].credits.length; ii++)
			{
				if(this.jobs[i].credits[ii].date_added > date_paid)
				{
					date_paid = this.jobs[i].credits[ii].date_added;
				}
			}
		}

		return date_paid;
	}

	this.sort_jobs = function()
	{
		this.jobs = this.quicksort_by_property(this.jobs, 'id');
	}

	this.quicksort_by_property = function(arr, property, left, right)
	{
		left = typeof left !== 'undefined' ? left : 0;
		right = typeof right !== 'undefined' ? right : null;

		// when the call is recursive we need to change
		//the array passed to the function yearlier
		quicksort_by_property.array = new Array();
		if( right == null )
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
		this.quicksort_by_property(null, property, left, j);
		 
		// the same with the right side
		if( i < right )
		this.quicksort_by_property(null, property, i, right);
		 
		// when all partitions have one element
		// the array is sorted
		 
		return quicksort_by_property.array;
	}
}