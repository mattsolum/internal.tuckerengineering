<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_admin_links())); ?>
<div id="body_wrapper">
	<form id="logs_form" method="POST" action="<?PHP echo(site_url('admin/logs')); ?>">
		<ul>
			<li>
				<select name="log_file" title="Select log" id="log_file">
					<option value=""></option>
					<?PHP
					foreach(array_reverse($files) as $file)
					{
						if(is_string($file) && !preg_match('/^(\.|index| )/', $file))
						{
							$file = preg_replace('/log-|\.php/', '', $file);

					?>
					<option value="<?PHP echo($file); ?>"<?PHP if($date == $file) echo(' selected="selected"'); ?>><?PHP echo($file); ?></option>
					<?PHP
						}
					}
					?>
				</select>
			</li>
		</ul>
	</form>

	<ul id="log"><li><?PHP echo(str_replace("\n", '</li><li>', $log)); ?></li></ul>
</div>
<script type="text/javascript">
	$('#log_file').shadowComplete();

	$('#log_file').live('SCAccept', function() {
		$.fn.MSDebug("<?PHP echo(site_url()); ?>api/v2/admin/" + $(this).val() + ".html");

		$('#log').load("<?PHP echo(site_url()); ?>api/v2/admin/" + $(this).val() + ".html");
	});

	$('#log_file').change(function(){
		$.fn.MSDebug('#log_file changed!');
	});
</script>
<?PHP $this->load->view('sections/footer') ?>