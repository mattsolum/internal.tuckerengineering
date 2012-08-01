		<script>
				$("#client_name").change(function(){
					var current_value = $("#client_name").attr("value");
					var response = $.ajax({url: "<?PHP echo BASE_URL; ?>index.php/autocomplete/client_name/" + current_value});
					
					$("#client_name").attr("value") = response.responseText;
					$("#client_name").attr("color") = "#ff0000";
				});
		</script>
		
		<form id="Job" method="POST">
			
			<fieldset>
				<legend>Client</legend>
				<ul>
					<li>
						<label for="client_name">Name</label>
						<input id="client_name" type="text" name="client_name" />
					</li>
				</ul>
				<?PHP $this->load->view('forms/address', array('prefix' => 'client')); ?>
			</fieldset>
			
			<fieldset>
				<legend>Requester</legend>
				<ul>
					<li>
						<label for="requester_name">Name</label>
						<input id="requester_name" type="text" name="requester_name" />
					</li>
				</ul>
				<?PHP $this->load->view('forms/address', array('prefix' => 'requester')); ?>
			</fieldset>
			
			<fieldset>
				<legend>Location</legend>
				<?PHP $this->load->view('forms/address', array('prefix' => 'job')); ?>
			</fieldset>
			
			<fieldset>
				<legend>Service</legend>
				<label for="service_required">Service Required</label>
				<input type="text" id="service_required" name="service_required" />
			</fieldset>
			
			<fieldset>
				<legend>Asset</legend>
				<?PHP $this->load->view('forms/asset'); ?>
			</fieldset>
		</form>