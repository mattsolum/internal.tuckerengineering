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
		}

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
}

function Contact() {
	this.id 			= null;
	this.type 			= '';
	this.info 			= '';
	this.note 			= '';
}

function Accounting() {
	this.credits 	= new Array();
	this.debits 	= new Array();
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
}

function Debit() {
	this.ledger_id		= null;
	this.client_id		= null;
	this.job_id			= null;
	
	this.item			= '';
	this.amount			= 0.0;

	this.date_added		= 0;
	this.date_updated	= 0;
}

function Payment() {
	this.id 			= null;
	this.client_id 		= null;
	
	this.tender			= '';
	this.number			= null;
	this.amount			= 0.0;
	
	this.date_added		0;
	this.date_posted	0;
}

function Note() {
	this.id				= NULL;
	this.type_id 		= NULL;
	this.type			= '';
	
	this.user			= 0;
	
	this.text			= '';
	
	this.date_added		= 0;
}