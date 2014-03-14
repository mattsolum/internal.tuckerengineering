<?PHP $this->load->view('sections/header'); ?>
<div id="body_wrapper">
	<h1>Change password</h1>
<form method="POST" action="<?PHP echo(site_url('user/password')); ?>" id="user_password_form" class="edit_form">
	<ul>
		<li>
			<input type="password" name="current_password" id="current_password" title="Current Password" value="" />
			<label class="hint" for="current_password">Required.</label>
		</li>
		<li>
			<input type="password" name="new_password_1" id="new_password_1" title="Password" value="" />
			<label class="hint" for="new_password_1">Required. Must be at least six characters.</label>
		</li>
		<li>
			<input type="password" name="new_password_2" id="new_password_2" title="Re-enter Password" value="" />
			<label class="hint" for="new_password_2">Required. Must match password box above.</label>
		</li>
		<li class="clear"></li>
	</ul>
	<ul>
		<li class="submit_container">
			<input type="submit" value="Change Password" />
		</li>
	</ul>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		
		var form = $('#user_edit_form');

		$.getJSON('<?PHP echo(site_url()); ?>resources/validation/user_rules.json', function(json) {
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
</script>
</div>
<?PHP $this->load->view('sections/footer') ?>