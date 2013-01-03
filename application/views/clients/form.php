<?PHP
	$phone 		= array();
	$contacts 	= array();
	$email		= array();

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
		}
	}
?>
<form method="POST" action="<?PHP echo(site_url($action)); ?>" id="client_edit_form">
	<ul>
		<li>
			<input type="text" name="cl_name" id="cl_name" title="Name" value="<?PHP echo($client->name); ?>">
			<label class="hint" for="cl_name">Required.</label>
		</li>
		<li class="clear"></li>
	</ul>
	<h2>Client address</h2>
	<ul>
		<li>
			<input type="text" name="cl_addr_1" id="cl_addr_1" title="Address" value="<?PHP echo(trim($client->location->number . ' ' . $client->location->route)); ?>">
			<label class="hint" for="cl_addr_1">Required.</label>
		</li>
		<li>
			<input type="text" name="cl_subpremise" id="cl_subpremise" title="Subpremise" value="<?PHP echo(trim($client->location->subpremise)); ?>">
		</li>
		<li>
			<input type="text" name="cl_locality" id="cl_locality" title="City" value="<?PHP echo(trim($client->location->locality)); ?>">
			<label class="hint" for="cl_locality">Required.</label>
		</li>
		<li>
			<input type="text" name="cl_admin_level_1" id="cl_admin_level_1" title="State" value="<?PHP echo(trim($client->location->admin_level_1)); ?>">
			<label class="hint" for="cl_admin_level_1">Required.</label>
		</li>
		<li>
			<input type="text" name="cl_postal_code" id="cl_postal_code" title="Postal code" value="<?PHP echo(trim($client->location->postal_code)); ?>">
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
			<input type="text" name="cl_contact[phone]" id="cl_contact[phone][<?PHP echo($key + 1); ?>]" title="Phone" value="<?PHP echo($ph->info); ?>">
			<label class="hint valid" for="cl_contact[phone][<?PHP echo($key + 1); ?>]">Not required. Format: 123-456-7891</label>
		</li>
		<?PHP
		}
		?>
		<li>
			<input type="text" name="cl_contact[phone]" id="cl_contact[phone][0]" title="Phone" value="">
			<label class="hint valid" for="cl_contact[phone][0]">Not required. Format: 123-456-7891</label>
		</li>
		<?PHP
		foreach($email AS $key => $em)
		{
		?>
		<li>
			<input type="text" name="cl_contact[email]" id="cl_contact[email][<?PHP echo($key + 1); ?>]" title="Email" value="<?PHP echo($em->info); ?>">
			<label class="hint valid" for="cl_contact[email][<?PHP echo($key + 1); ?>]">Not required. Format: [name]@[domain].com</label>
		</li>
		<?PHP
		}
		?>
		<li>
			<input type="text" name="cl_contact[email]" id="cl_contact[email][0]" title="Email" value="">
			<label class="hint valid" for="cl_contact[email][0]">Not required. Format: [name]@[domain].com</label>
		</li>
		<?PHP
		foreach($contacts AS $key => $co)
		{
		?>
		<li>
			<input type="text" name="cl_contact[contact]" id="cl_contact[contact][<?PHP echo($key + 1); ?>]" title="Contact name" value="<?PHP echo($co->info); ?>">
		</li>
		<?PHP
		}
		?>
		<li>
			<input type="text" name="cl_contact[contact]" id="cl_contact[contact][0]" title="Contact name" value="">
		</li>
		<li class="clear"></li>
	</ul>
	<h2>Note</h2>
	<ul>
		<li>
			<input type="text" name="cl_note" id="cl_note" title="Add note" value="">
		</li>
		<li class="clear"></li>
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

		$('input').focusin(function(){
			$(this).siblings('label.hint').each(function(){
				$(this).animate({opacity: 1.0}, 150);
			});
		});

		$('input').focusout(function(){
			$(this).siblings('label.hint').each(function(){
				$(this).animate({opacity: 0}, 150);
			});
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
	});
</script>