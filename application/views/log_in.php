<?PHP
	if(!isset($redirect))
	{
		$redirect = '';
	}
?>

<!DOCTYPE html>
<html id="log_in">
	<head>
		<meta charset="utf-8">
		<title><?PHP echo(setting('company.name')); ?> Billing System</title>

		<script src="<?PHP echo base_url(); ?>resources/js/jquery.js" type="text/javascript"></script>
		<script src="<?PHP echo base_url(); ?>resources/js/aggregate.js" type="text/javascript"></script>
		<script src="<?PHP echo base_url(); ?>resources/js/jquery.formLabels1.0.js" type="text/javascript"></script>
		<link rel="icon" type="image/ico" href="<?PHP echo base_url(); ?>resources/icon.ico">

		<script type="text/javascript">
		$(function(){
			$.fn.formLabels();
			$.fn.FlashMessages();
		});

		$(document).ready(function(){
			$("#login_form").keypress(function (e) {
				if(e.which == 13) {
					$("#login_form").submit();
				}
			});

			if($("#email").val() != '')
			{
				$("#password").focus();
			}
		});
		
		</script>

		<link rel="stylesheet" href="<?PHP echo base_url(); ?>resources/css/reset.css">
		<link rel="stylesheet" href="<?PHP echo base_url(); ?>resources/css/style.css">
	</head>
	<body><?PHP echo($this->Messages->load_flash_messages()); ?>
		<span id="company_name"><?PHP echo(setting('company.name')); ?></span>
		<form name="login" id="login_form" method="POST" action="<?PHP echo(site_url('user/auth' . $redirect)) ?>" autocomplete="off">
			<h1>Sign In</h1>
			<ul>
				<li>
					<input type="text" name="email" id="email" title="Email Address" value="<?PHP echo($email) ?>" autocomplete="off" />
				</li>
				<li>
					<input type="password" name="password" id="password" title="Password" value="" autocomplete="off" /> 
				</li>
				<button type="submit" value="submit" class="hide"></button>
			</ul>
		</form>
	</body>
</html>
