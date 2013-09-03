<?PHP $total_due = 0; ?>

<form id="apply_payment_form" class="edit_form" action="<?PHP echo($action); ?>" method="POST">
	<table id="list">
		<thead>
			<tr>
				
			</tr>
		</thead>
		<tbody>
			<?PHP 
				foreach($jobs AS $key => $job)
				{
					$total_due += $job->balance();
			?>
			<tr<?PHP if($key % 2 == 1) echo(' class="odd"'); ?>>
				<td>
					<input type="checkbox" name="job[]" id="job[<?PHP echo($job->id); ?>]" value="<?PHP echo($job->id); ?>" checked="checked" />
				</td>
				<td>
					<label for="job[<?PHP echo($job->id); ?>]">#<?PHP echo($job->id); ?></label>
				</td>
				<td>
					<label for="job[<?PHP echo($job->id); ?>]"><?PHP echo($job->service() . ' at ' . $job->location->number . ' ' . $job->location->route); ?></label>
				</td>
				<td>
					<label id="<?PHP echo($job->id); ?>_amount"for="job[<?PHP echo($job->id); ?>]">$<?PHP echo(number_format($job->balance(),2)); ?></label>
				</td>
			</tr>
			<?PHP
				}
			?>
		</tbody>
	</table>

	<div id="amount_due">
		Amount due: <span id="amount_due_number">$0.00</span>
	</div>

	<ul>
		<li>
			<select name="tender" title="Tender" id="tender">
				<option></option>
				<option>Cash</option>
				<option>Check</option>
				<option>Manager's Discretion</option>
			</select>
			<input type="checkbox" name="number_required" id="number_required" class="hidden" />
			<label class="hint" for="tender">Required.</label>
		</li>
		<li id="payment_number">
			<input type="text" name="number" title="Number" id="number" />
			<label class="hint" for="number">Required for checks, must be a numeric.</number>
		</li>
		<li>
			<input type="text" name="amount" title="Amount" class="amount" id="amount" value="<?PHP echo(($this->input->post('amount') === false)?'$' . number_format(abs($total_due), 2):$this->input->post('amount')); ?>"/>
			<label class="hint" for="amount">Amount must be formatted as USD. Required.</label>
		</li>
	</ul>

	<ul>
		<li class="submit_container">
			<input type="submit" value="Submit" />
		</li>
	</ul>
</form>

<script type="text/javascript">
	$(document).ready(function(){
		
		Mousetrap.trigger('- - d e b u g');

		var form = $('#apply_payment_form');

		$.getJSON('<?PHP echo(site_url()); ?>resources/validation/payment_rules.json', function(json) {
			$.fn.MSDebug('Validation rules loaded.');
			form.validate({
				rules: json,
				errorPlacement: function(error, element) {
				}
			});
		});



		/**
		 * Autocomplete
		 */
		$('#tender').shadowComplete();

		$('ul').focusin(function(e){
			if($(e.target).hasClass('amount')) {

				if($(e.target).val() == '')
				{
					$(e.target).collapseSelection();
					$(e.target).val('$');

					setTimeout(function(){$(e.target).collapseSelection();}, 5);
				}
			}
		});

		$('ul').focusout(function(e){
			if($(e.target).hasClass('amount')) {
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
			else if($(e.target).prop('name') == 'tender')
			{
				if($(e.target).val().toLowerCase() == 'check')
				{
					$('#number_required').prop('checked', true);
				}
				else
				{
					$('#number_required').prop('checked', false);
				}
			}
		});

		/**
		 * Showing and hiding check number
		 */

		var original_height 				= $("#payment_number").css('height');
		var original_margin_top 			= $("#payment_number").css('margin-top');
		var original_margin_bottom 			= $("#payment_number").css('margin-bottom');
		var original_padding_top			= $("#payment_number").css('padding-top');
		var original_padding_bottom 		= $("#payment_number").css('padding-bottom'); 

		$("#payment_number").css('overflow', 'hidden');
		$("#payment_number").animate({
			'height': 			0,
			'margin-top': 		0,
			'margin-bottom': 	0,
			'padding-top': 		0,
			'padding-bottom': 	0
		});

		$('#number').attr('tabindex', -1);

		$('#tender').keyup(function(e) {

			if($('#tender').val().toLowerCase() == 'check' && parseInt($("#payment_number").css("height")) == 0)
			{
				MSDebug('Should appear!');
				$("#payment_number").animate({
					'height': 			original_height,
					'margin-top': 		original_margin_top,
					'margin-bottom': 	original_margin_bottom,
					'padding-top': 		original_padding_top,
					'padding-bottom': 	original_padding_bottom
				});

				$('#number').removeAttr('tabindex');
			}
			else if($('#tender').val().toLowerCase() != 'check' && $("#payment_number").css("height") != '0px')
			{
				$("#payment_number").animate({
					'height': 			0,
					'margin-top': 		0,
					'margin-bottom': 	0,
					'padding-top': 		0,
					'padding-bottom': 	0
				});

				$('#number').attr('tabindex', -1);
			}
		});



		/**
		 * Summing amount due
		 */
		
		$('input[type=checkbox]').click(function(e){

			var total = 0;

			$('input[type=checkbox]:checked').each(function(index, object){
				var id = $(object).prop('id').replace(/[^0-9]/g,'');
				var targetId = '#' + id + '_amount';

				var amount = parseFloat($(targetId).html().replace(/\$|,/g, ''));

				if($(targetId).html().indexOf('-') > 0)
				{
					amount = amount * -1;
				}

				total += amount;
			});

			//total = total * -1;

			$('#amount_due_number').html('$' + total.toFixed(2));

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

		$('input').focusin(function(e){
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