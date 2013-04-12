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

<script type="text/javascript">
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
			url: 		'<?PHP echo(base_url()); ?>/resources/migration/data/te_billing.csv',
			dataType: 	'text',
			success: 	function(csv){
				$.fn.MSDebug('te_billing.csv loaded.');

				var timeout = 0;
				var num_lines = csv.split("\n").length;
				var last_position = 0;
				var i = 0;
				var sections = new Array('client', 'job', 'payment');
				var current_section = 0;
				var time_last = new Date();
				var time_start = new Date();

				csv = "\n" + csv;

				var doNext = null;
				doNext = function() {
					if(!pause()) {
						var start = csv.indexOf("\n", last_position);
						var end = csv.indexOf("\n", start + 1);
						var length = end - start;

						var line = csv.substr(start + 1, length);

						if(line.substr(0, 8) == '--------') {
							current_section++;
						} else {
							window[sections[current_section]](line);
							var time_current = new Date();
							var tDiff = ((time_current.getTime() - time_last.getTime()) / 1000);
							var avg = (time_current.getTime() - time_start.getTime()) / i;
							var time_left = Math.round((num_lines - i) * avg);

							var hours = Math.floor(time_left / 36e5),
								mins = Math.floor((time_left % 36e5) / 6e4),
								secs = Math.floor((time_left % 6e4) / 1000);

							$.fn.MSDebug('line #' + i + ' / ' + num_lines + ', ' + sections[current_section] + "; time left: " + hours + ':' + mins + ':' + secs);
							time_last = time_current;
						}

						set_progress(i/num_lines * 100);

						if(i < num_lines) {
							last_position = end;
							i++;
							setTimeout(doNext, timeout);
						}	
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

	function client(line) {
		var client = new Client();
		var cells = line.split(',');
		for(var i = 0; i < cells.length; i++) {
			cells[i] = cells[i].replace(/^"(.*)"$/, "$1");
			cells[i] = cells[i].replace(/^\s+|\s+$/, '');
		}

		client.id = cells[0];
		client.name = cells[1];
		client.location.set_addr_1(cells[2]);
		client.location.subpremise = cells[3];
		client.location.locality = cells[4];
		client.location.set_admin_level_1(cells[5]);
		client.location.admin_level_2 = "United States";
		client.location.postal_code = cells[6];

		if(cells[7] != '') {
			client.add_contact_item('phone', cells[7]);
		}

		if(cells[8] != '') {
			client.add_contact_item('fax', cells[8]);
		}

		if(cells[9] != '') {
			client.add_contact_item('contact', cells[9]);
		}

		if(cells[10] != '') {
			client.add_contact_item('email', cells[10]);
		}

		client.add_note(0, "Imported from the old database. The old information was: " + line);
	}

	function job(line) {
		var cells = line.split(',');
		for(var i = 0; i < cells.length; i++) {
			cells[i] = cells[i].replace(/^"(.*)"$/, "$1");
			cells[i] = cells[i].replace(/^\s+|\s+$/, '');
		}

		var job = new Job();
		job.client.id = cells[0];
		job.id = cells[1];
		job.location.set_addr_1(cells[3] + ' ' + cells[4]);
		job.location.set_city_state(cells[5]);
		job.location.admin_level_2 = 'United States';

		
		$.fn.MSDebug(JSON.stringify(job));
	}

	function payment(line) {
		//$.fn.MSDebug(line);
	}

	function expand_abbr(a) {
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

		return abbr[a.toUpperCase()];

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
</script>
<?PHP $this->load->view('sections/footer') ?>