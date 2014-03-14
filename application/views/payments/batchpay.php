<?PHP $this->load->view('sections/header'); ?>
<div id="body_wrapper">
	<form id="batchpay_form" method="POST" action="<?PHP echo(site_url('admin/logs')); ?>">
		<ul>
			<li>
				<select name="batchpay_file" title="Select older report" id="batchpay_file">
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
	<ul id="publish_options">
		<li>
			<a href="#print">Print</a>
		</li>
		<li>
			<a href="<?PHP echo(site_url()); ?>api/v2/accounting/batchpay.pdf?view=admin/batchpayslip">Save as PDF</a>
		</li>
		<!--<li>
			<a href="#email">Email</a>
		</li>-->
	</ul>
	<iframe id="batchpay_frame" src="<?PHP echo(site_url()); ?>api/v2/accounting/batchpay.html?view=admin/batchpayslip">

	</iframe>
</div>
<script type="text/javascript">
	$('#batchpay_file').shadowComplete();

	$('#batchpay_file').live('SCAccept', function() {
		$.fn.MSDebug("<?PHP echo(site_url()); ?>api/v2/admin/" + $(this).val() + ".html");

		$('#log').load("<?PHP echo(site_url()); ?>api/v2/admin/" + $(this).val() + ".html");
	});

	$('#batchpay_file').change(function(){
		$.fn.MSDebug('#batchpay_file changed!');
	});

	$('#publish_options').click(function(e){
		if($(e.target).prop('href').match('#print') != null)
		{
			e.preventDefault();
			var frm = document.getElementById('batchpay_frame').contentWindow;
            frm.focus();// focus on contentWindow is needed on some ie versions
            frm.print();

            $.ajax({url: "<?PHP echo(site_url()); ?>api/v2/accounting/batchpay_mark"});
		}
		else if($(e.target).prop('href').match('#copy') != null)
		{
			var url = $("#batchpay_frame").attr('src').replace('html', 'png');

		}
	});
</script>
<?PHP $this->load->view('sections/footer') ?>