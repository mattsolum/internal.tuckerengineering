<?PHP
	$states = array('Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'District of Columbia', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming');
	$foundation_types = array('Slab', 'Pier and Beam', 'Combination');
	$property_types = array('Single Family', 'Duplex', 'Fourplex', 'Commercial');

	$foundation_type = '';
	$property_type = '';

	if(isset($job->location->info['foundation_type']))
	{
		$foundation_type = $job->location->info['foundation_type'];
	}

	if(isset($job->location->info['property_type']))
	{
		$property_type = $job->location->info['property_type'];
	}

	if(count($job->accounting->debits) == 0)
	{
		$job->accounting->debits[] = new StructDebit();
	}
?>
<form method="POST" action="<?PHP echo(site_url($action)); ?>" id="job_edit_form" class="edit_form">
	<h2>Property address</h2>
	<ul>
		<li>
			<input type="text" name="jb_addr_1" id="jb_addr_1" title="Address" value="<?PHP echo(trim($job->location->number . ' ' . $job->location->route)); ?>" />
			<label class="hint" for="jb_addr_1">Required.</label>
		</li>
		<li>
			<input type="text" name="jb_subpremise" id="jb_subpremise" title="Subpremise" value="<?PHP echo(trim($job->location->subpremise)); ?>" />
		</li>
		<li>
			<input type="text" name="jb_locality" id="jb_locality" title="City" value="<?PHP echo(trim($job->location->locality)); ?>" />
			<label class="hint" for="jb_locality">Required.</label>
		</li>
		<li>
			<select name="jb_admin_level_1" id="jb_admin_level_1" title="State" >
				<option value=""></option>
				<?PHP
				foreach($states AS $state)
				{
				?>
				<option value="<?PHP echo($state); ?>"<?PHP if(strtolower($job->location->admin_level_1) == strtolower($state)) echo(' selected="selected"'); ?>><?PHP echo($state); ?></option>
				<?PHP
				}
				?>
			</select>
			<label class="hint" for="jb_admin_level_1">Required.</label>
		</li>
		<li>
			<input type="text" name="jb_postal_code" id="jb_postal_code" title="Postal code" value="<?PHP echo(trim($job->location->postal_code)); ?>" />
			<label class="hint" for="jb_postal_code">Required. Format: 12345</label>
		</li>
		<li class="clear"></li>
	</ul>
	<h2>Property information</h2>
	<ul>
		<li>
			<select name="prop_type" id="prop_type" title="Property type">
				<option value=""></option>
				<?PHP 
				foreach($property_types AS $prop_type)
				{
				?>
				<option value="<?PHP echo($prop_type); ?>"<?PHP if(strtolower($prop_type) == strtolower($property_type)) echo(' selected="selected"') ?>><?PHP echo($prop_type) ?></option>
				<?PHP
				}
				?>
			</select>
		</li>
		<li>
			<select name="prop_foundation" id="prop_foundation" title="Foundation type">
				<option value=""></option>
				<?PHP 
				foreach($foundation_types AS $found_type)
				{
				?>
				<option value="<?PHP echo($found_type); ?>"<?PHP if(strtolower($found_type) == strtolower($foundation_type)) echo(' selected="selected"') ?>><?PHP echo($found_type) ?></option>
				<?PHP
				}
				?>
			</select>
		</li>
		<li class="clear"></li>
	</ul>
	<h2>Work requested</h2>
	<ul>
		<?PHP
			foreach($job->accounting->debits AS $key => $debit)
			{
		?>
		<li>
			<input type="text" name="jb_item[]" id="jb_item[<?PHP echo($key); ?>]" title="Item" value="<?PHP echo($debit->item); ?>" class="job_item" /><input type="text" name="jb_item_amount[]" id="jb_item_amount[<?PHP echo($key); ?>]" title="Amount" value="<?PHP if($debit->amount != 0) echo('$' . number_format(abs($debit->amount), 2)) ?>" class="job_amount mousetrap" />
			<label class="hint" for="jb_item[<?PHP echo($key); ?>]">Amount must be formatted as USD.</label>
		</li>
		<?PHP
			}
		?>
		<li class="new_contact">
			Add another item <a href="#" tabindex="-1" title="Add another item">+</a>
 		</li>
		<li class="clear"></li>
	</ul>
	<h2>Note</h2>
	<ul>
		<li>
			<input type="text" name="jb_note" id="jb_note" title="Add note" value="" />
		</li>
		<li class="clear"></li>
	</ul>
	<ul>
		<li class="submit_container">
			<input type="submit" value="Submit" />
		</li>
	</ul>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		
		var form = $('#job_edit_form');

		$.getJSON('<?PHP echo(site_url()); ?>resources/validation/job_rules.json', function(json) {
			$.fn.MSDebug('Validation rules loaded.');
			form.validate({
				rules: json,
				errorPlacement: function(error, element) {
				}
			});

			$('[name="jb_item_amount[]"]').rules("add", {
				required: function(element) {
					return jb_item_filled(element);
				}
			});
		});



		/**
		 * Autocomplete
		 */
		$('#jb_admin_level_1').shadowComplete();
		$('#prop_type').shadowComplete();
		$('#prop_foundation').shadowComplete();
		$('.job_item').shadowComplete({source: "<?PHP echo(site_url()); ?>api/v2/autocomplete/services/", sourceSuffix: ".json", dataContainer: 'data'});

		$('ul').focusin(function(e){
			if($(e.target).hasClass('job_amount')) {
				autofillAmount(e);

				if($(e.target).val() == '')
				{
					$(e.target).collapseSelection();
					$(e.target).val('$');

					setTimeout(function(){$(e.target).collapseSelection();}, 5);
				}
			}
		});

		$('ul').focusout(function(e){
			if($(e.target).hasClass('job_amount')) {
				if($(e.target).val() == '$')
				{
					$(e.target).val('');
				}
				else if($(e.target).val() != '') {
					var amount = $(e.target).val().replace(/[^0-9\.]/, '');

					amount = parseFloat(amount);

					$.fn.MSDebug('Amount after parsing: ' + amount);

					$(e.target).val('$' + amount.toFixed(2));
				}
			}
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

		Mousetrap.bind('+', function(e){
			e.preventDefault();

			if($(e.target).prop('tagName') == 'INPUT' && $(e.target).parent().next().children('a').length > 0){
				var ev = jQuery.Event('mousdown');
				ev.target = $(e.target).parent().next().children('a').eq(0);
				addNew(ev);
			}
		});
	});

	function autofillAmount(e) {
		if($(e.target).val() == '')
		{
			var item = encodeURIComponent($(e.target).siblings('.job_item').eq(0).val());

			$.getJSON("<?PHP echo(site_url()); ?>api/v2/accounting/price/" + item + ".json", function(json){

				$(e.target).val('$' + json.data.price).focus().setSelection(1, json.data.price.length + 1);
			});
		}
	}

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