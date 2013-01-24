<?PHP
	$client = $job->client;
	$phone 		= array();
	$contacts 	= array();
	$email		= array();
	$fax		= array();

	$states = array('Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'District of Columbia', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming');

	foreach($client->contact AS $contact)
	{
		switch($contact->type)
		{
			case 'phone':
				$phone[] = $contact;
				break;
			case 'contact':
				$contacts[] = $contact;
				break;
			case 'email':
				$email[] = $contact;
				break;
			case 'fax':
				$fax[] = $contact;
		}
	}

	if(count($phone) == 0)
	{
		$phone[] = new StructContact();
	}

	if(count($contacts) == 0)
	{
		$contacts[] = new StructContact();
	}

	if(count($email) == 0)
	{
		$email[] = new StructContact();
	}

	if(count($fax) == 0)
	{
		$fax[] = new StructContact();
	}
?>
<form method="POST" action="<?PHP echo(site_url($action)); ?>" id="client_edit_form" class="edit_form">
	<h2>Client</h2>
	<ul>
		<li>
			<input type="text" name="cl_name" id="cl_name" title="Name" value="<?PHP echo($client->name); ?>" />
			<label class="hint" for="cl_name">Required.</label>
		</li>
		<li class="clear"></li>
	</ul>
	<h2>Client address</h2>
	<ul>
		<li>
			<input type="text" name="cl_addr_1" id="cl_addr_1" title="Address" value="<?PHP echo(trim($client->location->number . ' ' . $client->location->route)); ?>" />
			<label class="hint" for="cl_addr_1">Required.</label>
		</li>
		<li>
			<input type="text" name="cl_subpremise" id="cl_subpremise" title="Subpremise" value="<?PHP echo(trim($client->location->subpremise)); ?>" />
		</li>
		<li>
			<input type="text" name="cl_locality" id="cl_locality" title="City" value="<?PHP echo(trim($client->location->locality)); ?>" />
			<label class="hint" for="cl_locality">Required.</label>
		</li>
		<li>
			<select name="cl_admin_level_1" id="cl_admin_level_1" title="State" >
				<option value=""></option>
				<?PHP
				foreach($states AS $state)
				{
				?>
				<option value="<?PHP echo($state); ?>"<?PHP if(strtolower($client->location->admin_level_1) == strtolower($state)) echo(' selected="selected"'); ?>><?PHP echo($state); ?></option>
				<?PHP
				}
				?>
			</select>
			<label class="hint" for="cl_admin_level_1">Required.</label>
		</li>
		<li>
			<input type="text" name="cl_postal_code" id="cl_postal_code" title="Postal code" value="<?PHP echo(trim($client->location->postal_code)); ?>" />
			<label class="hint" for="cl_postal_code">Required. Format: 12345</label>
		</li>
		<li class="clear"></li>
	</ul>
	<h2>Client contact information</h2>
	<ul>
		<?PHP
		foreach($phone AS $key => $ph)
		{
		?>
		<li>
			<input type="text" name="cl_contact[phone][]" id="cl_contact[phone][<?PHP echo($key); ?>]" title="Phone" value="<?PHP echo($ph->info); ?>" />
			<label class="hint valid" for="cl_contact[phone][<?PHP echo($key); ?>]">Not required. Format: 123-456-7891</label>
		</li>
		<?PHP
		}
		?>
		<li class="new_contact type_phone">
			Add another phone number <a href="#" tabindex="-1" title="Add another phone number">+</a>
 		</li>
		<?PHP
		foreach($email AS $key => $em)
		{
		?>
		<li>
			<input type="text" name="cl_contact[email][]" id="cl_contact[email][<?PHP echo($key); ?>]" title="Email" value="<?PHP echo($em->info); ?>" />
			<label class="hint valid" for="cl_contact[email][<?PHP echo($key); ?>]">Not required. Format: [name]@[domain].com</label>
		</li>
		<?PHP
		}
		?>
		<li class="new_contact type_email">
			Add another email address <a href="#" tabindex="-1" title="Add another email address">+</a>
 		</li>
		<?PHP
		foreach($fax AS $key => $fa)
		{
		?>
		<li>
			<input type="text" name="cl_contact[fax][]" id="cl_contact[fax][<?PHP echo($key); ?>]" title="Fax" value="<?PHP echo($fa->info); ?>" />
			<label class="hint valid" for="cl_contact[fax][<?PHP echo($key); ?>]">Not required. Format: 123-456-7891</label>
		</li>
		<?PHP
		}
		?>
		<li class="new_contact type_fax">
			Add another fax number <a href="#" tabindex="-1" title="Add another fax number">+</a>
 		</li>
		<?PHP
		foreach($contacts AS $key => $co)
		{
		?>
		<li>
			<input type="text" name="cl_contact[contact][]" id="cl_contact[contact][<?PHP echo($key); ?>]" title="Contact name" value="<?PHP echo($co->info); ?>" />
		</li>
		<?PHP
		}
		?>
		<li class="new_contact type_contact">
			Add another contact <a href="#" tabindex="-1" title="Add another contact">+</a>
 		</li>
		<li class="clear"></li>
	</ul>
	<h2>Note</h2>
	<ul>
		<li>
			<input type="text" name="cl_note" id="cl_note" title="Add note" value="" />
		</li>
		<li class="clear"></li>
	</ul>
	<ul>
		<li class="submit_container">
			<input type="submit" value="Next - Requester" />
		</li>
	</ul>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		
		var form = $('#client_edit_form');

		$.getJSON('<?PHP echo(site_url()); ?>resources/validation/client_rules.json', function(json) {
			form.validate({
				rules: json,
				errorPlacement: function(error, element) {

				}
			});
		});


		/**
		 * Autocomplete
		 */
		$('#cl_admin_level_1').shadowComplete();
		$("#cl_name").shadowComplete({source: "<?PHP echo(site_url()); ?>api/v2/autocomplete/client/", sourceSuffix: ".json", dataContainer: 'data'});
		$("#cl_name").live('SCAccept', function(e){
			var clname = $(this).val();
			clname = clname.toLowerCase().replace(/[^a-z0-9 ]/, '').replace(/\s+/g, '_');
			
			$.getJSON('<?PHP echo(site_url()); ?>api/v2/client/' + clname + '.json', function(json){
				var e = jQuery.Event('blur');

				var addr_1 = json.data.location.route;
				if(json.data.location.number != null) {
					addr_1 = json.data.location.number + ' ' + addr_1;
				}

				$('#cl_addr_1').val(addr_1).trigger(e);
				$('#cl_addr_2').val(json.data.location.subpremise).trigger(e);
				$('#cl_locality').val(json.data.location.locality).trigger(e);
				$('#cl_admin_level_1').val(json.data.location.admin_level_1).trigger(e);
				$('#cl_postal_code').val(json.data.location.postal_code).trigger(e);

				var typeCount = new Array();

				for(var i = 0; i < json.data.contact.length; i++)
				{
					var type = json.data.contact[i].type.toLowerCase();
					var info = json.data.contact[i].info;

					if(typeCount[type] == undefined)
					{
						typeCount[type] = 0;
					}

					$.fn.MSDebug(type + ': ' + info + ' #' + typeCount[type]);

					if(typeCount[type] != 0)
					{
						var ev = jQuery.Event('mousedown');
						ev.target = $('li.type_' + type).eq(0).children('a').eq(0);

						addNew(ev);
					}

					var $target = $('li.type_' + type).eq(0).prev().children('input:not(SCHint)').eq(0);

					$target.val(info).trigger(e);

					typeCount[type] = typeCount[type] + 1;

				}
			});
		});


		/**
		 * Hiding and displaying hints.
		 */
		$('li').focusin(function(e){
			$(this).children('label.hint').each(function(){
				$(this).animate({opacity: 1.0}, 150);
			});
		});

		$('li').focusout(function(e){
			if($(e.target).valid() == true) {
				$(this).children('label.hint').each(function(){
					$(this).animate({opacity: 0}, 150);
				});
			}
		});

		$('input').keyup(function(e){
			if($(this).valid() == true)
			{
				$(this).siblings('label.hint').each(function(){
					$(this).addClass('valid');
				});
			}
			else
			{
				$(this).siblings('label.hint').each(function(){
					$(this).removeClass('valid');
				});
			}
		});

		/**
		 * Add additional
		 */
		$('.new_contact').mousedown(function(e){addNew(e)});

		Mousetrap.bind('tab+n', function(e){
			e.preventDefault();

			if($(e.target).prop('tagName') == 'INPUT' && $(e.target).parent().next().children('a').length > 0){
				alert('should fire');
				var ev = jQuery.Event('mousdown');
				ev.target = $(e.target).parent().next().children('a').eq(0);
				addNew(ev);
			}
		});
	});

	function addNew(e) {
		if($(e.target).prop('tagName') == 'A') {
			e.preventDefault();

			var $this = $(e.target).parent();

			var copy = $this.prev('li').clone();
			var name = $this.prev('li').children('input:not(.SCHint)').eq(0).attr('name');
			var number = $('input[name="' + name + '"]').length;

			copy.children('input:not(.SCHint)').each(function(){
				var id = $(this).attr('id').replace(/\[\d+\]/, '[' + number + ']');

				$(this).siblings('[for="' + $(this).attr('id') + '"]').attr('for', id);
				$(this).attr('id', id).val('');
			});

			copy.children('label.hint').css('opacity', 0.0);	

			copy.focus();
			$this.before(copy);

			if(copy.children('.SCHint').length > 0)
			{
				copy.children('.SCHint, .SCList, .SCButton').remove();

				if(copy.children('.job_item').length > 0)
				{
					copy.children('.job_item').shadowComplete({source: "<?PHP echo(site_url()); ?>api/v2/autocomplete/services/", sourceSuffix: ".json", dataContainer: 'data'});
				}
			}

			$.fn.formLabels();
		}
	}
</script>