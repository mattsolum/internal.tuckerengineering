<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_admin_links())); ?>
<section id="migration">
	<button id="begin_migration" value="0">Begin Migration</button>
	<div id="progress_bar">
		<span id="progress" />
	</div>
	<ul id="status">

	</ul>
</section>

<script type="text/javascript" src="<?PHP echo(site_url()); ?>resources/js/jquery.csv-0.71.min.js"></script>
<script type="text/javascript">
	var dup = new Array();
	var data_file = 'te_billing_parsed';
	var timeout = 600;
	var num_lines = 0;
	var last_position = 0;
	var i = 0;
	var sections = new Array('duplicate', 'client', 'job', 'payment');
	var current_section = 0;
	var time_last = new Date();
	var time_start = new Date();
	var start = 0;
	var end = 0;
	var length = 0;
	var doNext = null;

	$(document).ready(function(){
		$('#status').height(status_height());

		$(window).resize(function(){
			$('#status').height(status_height());
		});

		Mousetrap.trigger('- - d e b u g');

		$('#begin_migration').click(function(){
			if(pause()) {
				$(this).attr('value', 1);
				$(this).html('Pause migration');
			}
			else
			{
				$(this).attr('value', 0);
				$(this).html('Resume migration');
			}
		});

		$.ajax({
			type: 		'GET',
			url: 		'<?PHP echo(base_url()); ?>/resources/migration/data/' + data_file,
			dataType: 	'text',
			success: 	function(csv){
				$.fn.MSDebug(data_file + ' loaded.');

				num_lines = csv.split("\n").length;

				csv = "\n" + csv;

				doNext = function() {
					if(!pause()) {
						start = csv.indexOf("\n", last_position);
						end = csv.indexOf("\n", start + 1);
						length = end - start;

						var line = csv.substr(start + 1, length);
						//$.fn.MSDebug(line);

						if(line.substr(0, 8) == '--------') {
							$.fn.MSDebug('Next section');
							current_section++;
							next(1);
						} else {

							window[sections[current_section]](line);
							var time_current = new Date();
							var tDiff = ((time_current.getTime() - time_last.getTime()) / 1000);
							var avg = (time_current.getTime() - time_start.getTime()) / i;
							var time_left = Math.round((num_lines - i) * avg);

							var hours = Math.floor(time_left / 36e5),
								mins = Math.floor((time_left % 36e5) / 6e4),
								secs = Math.floor((time_left % 6e4) / 1000);
							time_last = time_current;

							$.fn.MSDebug('line #' + i + ' / ' + num_lines + ', ' + sections[current_section] + "; time left: " + hours + ':' + mins + ':' + secs);
						}

						set_progress(i/num_lines * 100);	
					}
					else {
						setTimeout(doNext, timeout);
					}
				}

				doNext();
			},
			error:  	function(){
				$.fn.MSDebug('An error occured loading te_billing.csv');
			}
		});
	});

	function next(time = null)
	{
		if(time == null)
		{
			time = timeout;
		}

		if(i < num_lines) {
			last_position = end;
			i++;
			setTimeout(doNext, time);
		}
	}

	function duplicate(line) {
		var cells = $.csv.toArray(line);
		dup[cells[0]] = cells[1];

		next(1);
	}

	function client(line) {
		var client = new Client();
		var cells = $.csv.toArray(line);

		client.id = cells[0];
		client.name = cells[1];
		client.location.number = cells[6];
		client.location.route = cells[7];
		client.location.subpremise = cells[8];
		client.location.locality = cells[9];
		client.location.admin_level_1 = expand_abbr(cells[10]);
		client.location.admin_level_2 = cells[11];
		client.location.postal_code = cells[12];

		client.location.latitude = cells[13];
		client.location.longitude = cells[14];

		//If there name is not set it will come back invalid. 
		//I have checked on every no-name record on the database and they
		//are all orphans; no connected data. It is safe to ignore them.
		if(client.name == '')
		{
			next(1);
			return false;
		}

		if(cells[3] != '') {
			var phone = new Contact();
			phone.set('phone', cells[3]);

			if(phone.is_valid(false))
			{
				client.add_contact_item('phone', cells[3]);
			}
		}

		if(cells[4] != '') {
			var fax = new Contact();
			fax.set('fax', cells[4]);

			if(fax.is_valid(false))
			{
				client.add_contact_item('fax', cells[4]);
			}
		}

		if(cells[5] != '') {
			var contact = new Contact();
			contact.set('contact', cells[5]);

			if(contact.is_valid(false))
			{
				client.add_contact_item('contact', cells[5]);
			}
		}

		if(cells[2] != '') {
			var email = new Contact();
			email.set('email', cells[2]);

			if(email.is_valid(false))
			{
				client.add_contact_item('email', cells[2]);
			}
		}

		client.add_note(0, "Imported from the old database. The old information was: " + cells[15]);

		if(!client.is_valid())
		{
			add_status('error', '[' + client.id + '] ' + client.name + ', ' + client.location.number + ' ' + client.location.route);	
		}
		else
		{
			$.ajax({
				type: 		'POST',
				url: 		'http://local/internal.tuckerengineering/api/v2/client/' + client.id + '.json',
				data: 		{data: JSON.stringify(client)},
				aync: 		true,
				success: 	function(returned) {
					if(returned.result == undefined)
					{
						add_status('error', returned);
					}

					if(returned.result == 'success')
					{
						//add_status('info', 'Success! returned id ' + returned.data['id'] + ' ' + client.name);
					}
					else
					{
						add_status('error', returned.data.message);
					}
				},
				error: 		function(jqxhr) {
					add_status('error', jqxhr.responseText);
				},
				complete: 	function() {
					next(1);
				}
			});
		}
	}

	function job(line) {
		var job = new Job();
		var cells = $.csv.toArray(line);

		if(dup[cells[0]] != undefined && dup[cells[0]] != null) {
			job.client.id = dup[cells[0]];
		}
		else
		{
			job.client.id = cells[0];
		}

		job.id = cells[1];
		job.location.number = cells[3];
		job.location.route = cells[4];
		job.location.subpremise = cells[5];
		job.location.locality = cells[6];
		job.location.admin_level_1 = cells[7];
		job.location.admin_level_2 = expand_abbr(cells[8]);
		job.location.postal_code = cells[9];

		job.location.latitude = cells[16];
		job.location.longitude = cells[17];

		job.date_added = job.date_billed = from_datetime(cells[13]);

		job.add_item(cells[2], cells[14]);

		job.add_note(0, 'Document file: ' + cells[12]);
		job.add_note(0, "Imported from the old database. The old information was: " + cells[15]);

		if(!job.is_valid())
		{
			add_status('error', '[' + job.id + '] ' + job.service() + ', ' + job.location.number + ' ' + job.location.route);
			return false;
		}

		$.ajax({
			type: 		'POST',
			url: 		'http://local/internal.tuckerengineering/api/v2/migration/job/' + job.id + '.json',
			data: 		{data: JSON.stringify(job)},
			aync: 		true,
			success: 	function(returned) {
				if(returned.result == undefined)
				{
					add_status('error', returned);
				}

				if(returned.result == 'success')
				{
					add_status('info', 'Success! returned id ' + returned.data['id']);
				}
				else
				{
					add_status('error', returned.data.message);
				}
			},
			error: 		function(jqxhr) {
				add_status('error', jqxhr.responseText);
			},
			complete: 	function() {
				next(1);
			}
		});
		//add_status('info', job.service() + '; $' + job.accounting.debit_total().toFixed(2) + ' {' + cells[2] + ' - ' + cells[14] + '}');
	}

	function payment(line) {
		var payment = new Credit();
		var cells = $.csv.toArray(line);

		if(cells[4] == 0)
		{
			return true;
		}

		payment.client_id = cells[0];
		payment.job_id = cells[1];

		payment.date_added = from_datetime(cells[2]);

		var tender = 'Cash';

		if(cells[3] != 0)
		{
			tender = 'Check';
		}

		payment.make_payment(cells[4], tender, cells[3]);

		payment.payment.date_posted = from_datetime(cells[5]);

		if(!payment.is_valid())
		{
			add_status('error', '[' + payment.client_id + ', ' + payment.job_id + '] ' + payment.amount);
		}
	}

	function expand_abbr(state) {
		if(state == undefined)
		{
			return 'Texas';
		}

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

		if(state != '')
		{
			return abbr[state.toUpperCase()];
		}
	}

	function pause()
	{
		if($('#begin_migration').attr('value') == 0)
		{
			return true;
		}
		else return false;
	}
	
	Mousetrap.bind('p', function(e){
		if(pause()) {
			$('#begin_migration').attr('value', 1);
			$('#begin_migration').html('Pause migration');
		}
		else
		{
			$('#begin_migration').attr('value', 0);
			$('#begin_migration').html('Resume migration');
		}
	});

	function status_height(){
		var window_height = parseInt($(window).height());
		var height_remaining = window_height - parseInt($('#status').offset().top);
		var height = ((Math.floor(height_remaining / 16) - 1) * 16);
		return Math.max(height, 128) + 'px';
	}

	function add_status(type, str)
	{
		var date = new Date();
		var message = date.toLocaleTimeString() + " " + type.toUpperCase() + " " + str;
		$('#status').prepend("<li>" + message + "</li>");
	}

	function set_progress(percent)
	{
		if(percent > 100)
		{
			percent = 99.999;
		}

		var percent 		= (percent % 100) / 100;
		var total_width 	= parseInt($('#progress').width());
		var positive_offset = Math.floor(total_width * percent);
		var offset 			= (total_width - positive_offset) * -1;

		$('#progress').css('margin-left', offset);
	}

	function get_progress()
	{
		var offset 			= parseInt($("#progress").css("margin-left"));
		var total_width 	= parseInt($('#progress').width());
		var positive_offset	= total_width + offset;
		var percent			= positive_offset / total_width;

		return percent * 100;
	}

	function from_datetime(datetime)
	{
		//1999-11-04 00:00:00
		//1992-07-21 00:00:00
		
		var year = datetime.substr(0, 4);
		var month = datetime.substr(5, 2) - 1;
		var day = datetime.substr(8, 2);

		var hour = datetime.substr(11, 2) + 5; //Get it into GMT
		var minute = datetime.substr(14, 2);
		var second = datetime.substr(17, 2);

		var date = new Date(year, month, day, hour, minute, second);

		return date.getTime() / 1000;
	}
</script>
<?PHP $this->load->view('sections/footer') ?>