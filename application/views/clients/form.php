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
			<input type="text" name="cl_name" title="Name" value="<?PHP echo($client->name); ?>">
			<span class="hint">Required</span>
		</li>
		<li class="clear"></li>
	</ul>
	<h2>Client address</h2>
	<ul>
		<li>
			<input type="text" name="cl_addr_1" title="Address" value="<?PHP echo(trim($client->location->number . ' ' . $client->location->route)); ?>">
			<span class="hint">Required.</span>
		</li>
		<li>
			<input type="text" name="cl_subpremise" title="Subpremise" value="<?PHP echo(trim($client->location->subpremise)); ?>">
		</li>
		<li>
			<input type="text" name="cl_locality" title="City" value="<?PHP echo(trim($client->location->locality)); ?>">
			<span class="hint">Required.</span>
		</li>
		<li>
			<input type="text" name="cl_admin_level_1" title="State" value="<?PHP echo(trim($client->location->admin_level_1)); ?>">
			<span class="hint">Required.</span>
		</li>
		<li>
			<input type="text" name="cl_postal_code" title="Postal code" value="<?PHP echo(trim($client->location->postal_code)); ?>">
			<span class="hint">Required. Format: 12345</span>
		</li>
		<li class="clear"></li>
	</ul>
	<h2>Client contact information</h2>
	<ul>
		<?PHP
		foreach($phone AS $ph)
		{
		?>
		<li>
			<input type="text" name="cl_contact[phone]" title="Phone" value="<?PHP echo($ph->info); ?>">
			<span class="hint">Format: 123-456-7891</span>
		</li>
		<?PHP
		}
		?>
		<li>
			<input type="text" name="cl_contact[phone]" title="Phone" value="">
			<span class="hint">Format: 123-456-7891</span>
		</li>
		<?PHP
		foreach($email AS $em)
		{
		?>
		<li>
			<input type="text" name="cl_contact[email]" title="Email" value="<?PHP echo($em->info); ?>">
			<span class="hint">Format: [name]@[domain].com</span>
		</li>
		<?PHP
		}
		?>
		<li>
			<input type="text" name="cl_contact[email]" title="Email" value="">
			<span class="hint">Format: [name]@[domain].com</span>
		</li>
		<?PHP
		foreach($contacts AS $co)
		{
		?>
		<li>
			<input type="text" name="cl_contact[contact]" title="Contact name" value="<?PHP echo($co->info); ?>">
		</li>
		<?PHP
		}
		?>
		<li>
			<input type="text" name="cl_contact[contact]" title="Contact name" value="">
		</li>
		<li class="clear"></li>
	</ul>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		$('input').focusin(function(){
			$(this).siblings('span.hint').each(function(){
				$(this).animate({opacity: 1.0}, 150);
			});
		});

		$('input').focusout(function(){
			$(this).siblings('span.hint').each(function(){
				$(this).animate({opacity: 0}, 150);
			});
		});
	});

	var form = $('#client_edit_form').on("submit", function(){
		return false;
	});
</script>