<?PHP
	$states = array('Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'District of Columbia', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming');

	if(!isset($user))
	{
		$user = new StructUser();
	}
?>
<form method="POST" action="<?PHP echo(site_url($action)); ?>" id="user_edit_form" class="edit_form">
	<ul>
		<li>
			<input type="text" name="user_email" id="user_email" title="Email" value="<?PHP echo($user->get_email()); ?>" />
			<label class="hint" for="user_email">Required. Format: [name]@[domain].com</label>
		</li>
		<li>
			<input type="password" name="user_password" id="user_password" title="Change password" value="" />
			<label class="hint" for="user_password">Required. Must be at least six characters.</label>
		</li>
		<li class="clear"></li>
	</ul>
	<h2>User address</h2>
	<ul>
		<li>
			<input type="text" name="user_addr_1" id="user_addr_1" title="Address" value="<?PHP echo(trim($user->location->number . ' ' . $user->location->route)); ?>" />
			<label class="hint" for="user_addr_1">Required.</label>
		</li>
		<li>
			<input type="text" name="user_subpremise" id="user_subpremise" title="Subpremise" value="<?PHP echo(trim($user->location->subpremise)); ?>" />
		</li>
		<li>
			<input type="text" name="user_locality" id="user_locality" title="City" value="<?PHP echo(trim($user->location->locality)); ?>" />
			<label class="hint" for="user_locality">Required.</label>
		</li>
		<li>
			<select name="user_admin_level_1" id="user_admin_level_1" title="State" >
				<option value=""></option>
				<?PHP
				foreach($states AS $state)
				{
				?>
				<option value="<?PHP echo($state); ?>"<?PHP if(strtolower($user->location->admin_level_1) == strtolower($state)) echo(' selected="selected"'); ?>><?PHP echo($state); ?></option>
				<?PHP
				}
				?>
			</select>
			<label class="hint" for="user_admin_level_1">Required.</label>
		</li>
		<li>
			<input type="text" name="user_postal_code" id="user_postal_code" title="Postal code" value="<?PHP echo(trim($user->location->postal_code)); ?>" />
			<label class="hint" for="user_postal_code">Required. Format: 12345</label>
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
		
		var form = $('#user_edit_form');

		$.getJSON('<?PHP echo(site_url()); ?>resources/validation/user_rules_2.json', function(json) {
			form.validate({
				rules: json,
				errorPlacement: function(error, element) {

				}
			});
		});

		/**
		 * Hiding and displaying hints.
		 */
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

		$('#user_admin_level_1').shadowComplete();

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