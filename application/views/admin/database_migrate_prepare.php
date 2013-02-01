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
			dataType: 	'csv',
			success: 	function(csv){
				add_status('INFO', 'te_billing.csv loaded.')
							
							$.ajax({
								type: 		'POST',
								url: 		'<?PHP echo(base_url()); ?>api/v2/migration/client/' + client.id + '.json',
								data: 		{data: json_string},
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

									if(i < total_rows)
									{
										i++;
									}
									setTimeout(doNext, 10);
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

									if(i < total_rows)
									{
										i++;
									}
									setTimeout(doNext, 10);
								}
							});
						}

						if(pause())
						{
							setTimeout(doNext, 10);
						}
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
		if($('#begin_migration').attr('value') == 0)
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
<?PHP $this->load->view('sections/footer') ?>