<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_admin_links())); ?>
<section id="migration">
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

		$('html').click(function(){
			if(pause()) {
				$('#status').data('pause', false);
			}
			else
			{
				$('#status').data('pause', true);
			}
		});

		$.ajax({
			type: 		'GET',
			url: 		'<?PHP echo(base_url()); ?>/resources/migration/data/test.xml',
			dataType: 	'xml',
			success: 	function(xml){
				add_status('INFO', 'test.xml loaded.')
				$(xml).find('clients').each(function(){
					var rows = $(xml).find('row');
					var total_rows = rows.length;
					var i = 0;

					var doNext = null;
					doNext = function() {
						if(!pause())
						{
							var row = rows.eq(i);

							var client = new Object();
							client.location = new Object();
							client.contact = new Array();
							client.notes = new Array();

							client.name 					= row.find('CLNAME').text();
							client.id						= row.find('CLNTNO').text();

							client.location.route			= row.find('ADDR1').text();
							client.location.sub_premise		= row.find('ADDR2').text();
							client.location.locality		= row.find('CITY').text();
							client.location.admin_level_1	= row.find('STATE').text();
							client.location.postal_code		= row.find('ZIP').text();

							var phone 	= row.find('PHONE').text();
							var email 	= row.find('EMAIL').text();
							var fax	  	= row.find('FAX').text();
							var contact = row.find('CONTACT').text();

							if(phone != '')
							{
								var con = new Object();
								con.id 		= client.id;
								con.type 	= 'phone';
								con.info 	= phone;

								client.contact.push(con);
							}

							if(email != '')
							{
								var con = new Object();
								con.id 		= client.id;
								con.type 	= 'email';
								con.info 	= email;

								client.contact.push(con);
							}

							if(fax != '')
							{
								var con = new Object();
								con.id 		= client.id;
								con.type 	= 'fax';
								con.info 	= fax;

								client.contact.push(con);
							}

							if(contact != '')
							{
								var con = new Object();
								con.id 		= client.id;
								con.type 	= 'contact';
								con.info 	= contact;

								client.contact.push(con);
							}

							var note_message = "CLNTNO " + client.id + "; CLNAME " + client.name + "; ADDR1 " + client.location.route + "; ADDR2 " + client.location.sub_premise + "; CITY " + client.location.locality + "; STATE " + client.location.admin_level_1 + "; ZIP " + client.location.postal_code + "; PHONE " + phone + "; FAX " + fax + "; EMAIL " + email + "; CONTACT " + contact + "; CURBAL " + row.find('CURBAL').text() + "; BEGBAL " + row.find('BEGBAL').text();
							var note = new Object();
							note.type_id = client.id;
							note.type = 'client';
							note.user = new Object();
							note.user.id = 0;
							note.text = note_message;

							client.notes.push(note);

							var json_string = JSON.stringify(client);
							$.ajax({
								type: 		'POST',
								url: 		'<?PHP echo(base_url()); ?>api/v2/client/' + client.id + '.json',
								data: 		{data: json_string},
								async: 		false,
								success: 	function(returned) {
									if(returned.result == undefined)
									{
										add_status('error', returned);
									}

									if(returned.result == 'success')
									{
										set_progress((i / total_rows) * 100);
										add_status('success', "Client added with ID " + returned.data['id']);
									}
									else
									{
										add_status('error', returned.data.message);
									}
								},
								error: 		function(jqxhr) {
									var response = JSON.parse(jqxhr.responseText);
									
									if(response.data.message == undefined)
									{
										add_status('error', jqxhr.responseText);
									}
									else
									{
										add_status('error', response.data.message);
									}
								}
							});

							if(i < total_rows)
							{
								i++;
							}
						}

						setTimeout(doNext, 1);
					}

					doNext();
				});
			},
			error:  	function(){
				add_status('ERROR', 'An error occured loading test.xml');
			}
		});
	});

	function pause()
	{
		if($('#status').data('pause') == true)
		{
			return true;
		}
		else return false;
	}

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

		$('#progress').animate({marginLeft: offset + 'px'}, 250);
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