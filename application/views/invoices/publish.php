<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_invoice_tools($invoice->slug()))); ?>
<div id="body_wrapper">
	<ul id="publish_options">
		<li>
			<a href="#print">Print</a>
		</li>
		<li>
			<a href="<?PHP echo(site_url()); ?>api/v2/invoice/<?PHP echo($invoice->slug()); ?>.pdf?view=invoices/invoice">Save as PDF</a>
		</li>
		<li>
			<a href="<?PHP echo(site_url()); ?>api/v2/invoice/<?PHP echo($invoice->slug()); ?>.rtf?view=invoices/invoice">Save as RTF</a>
		</li>
		<li>
			<a href="#email">Email</a>
		</li>
	</ul>
	<iframe id="invoice_frame" src="<?PHP echo(site_url()); ?>api/v2/invoice/<?PHP echo($invoice->slug()); ?>.html?view=invoices/invoice">

	</iframe>
</div>
<script type="text/javascript">
	$('#publish_options').click(function(e){
		if($(e.target).prop('href').match('#print') != null)
		{
			e.preventDefault();
			var frm = document.getElementById('invoice_frame').contentWindow;
            frm.focus();// focus on contentWindow is needed on some ie versions
            frm.print();
		}
	});
</script>
<?PHP $this->load->view('sections/footer') ?>