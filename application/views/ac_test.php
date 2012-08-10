<?PHP $this->load->view('sections/header'); ?>

		<form>
			<ul>
				<li>
					<label for="client_name">Name</label>
					
					<input id="client_name" type="text" name="client_name" rel="autocomplete" />
				</li>
			</ul>
		</form>
	
<?PHP $this->load->view('sections/footer'); ?>

<script>
	$('input[rel="autocomplete"]').focus(function(){
		$('<input type="text" id="autocomplete_test" />').insertBefore(this);
		$(this).addClass('focus');
	});
	
	$('input[rel="autocomplete"]').blur(function(){
		$('#autocomplete_test').remove();
		$(this).removeClass('focus');
	});
	
		
	$('input[rel="autocomplete"]').keyup(function(){
		var text = $(this).attr("value");
		var url = "<?PHP echo BASE_URL; ?>index.php/autocomplete/client_name/" + text;
		
		if(text.length > 0) {
			$.get(url, function(data){
				ac_string = text + data.substr(text.length);
				$("#autocomplete_test").attr("value", ac_string);
			});
		} else { $("#autocomplete_test").attr("value", ""); }
	});
	
</script>