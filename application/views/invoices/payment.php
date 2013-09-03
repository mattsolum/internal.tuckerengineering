<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_invoice_tools($invoice->slug()))); ?>
<div id="body_wrapper">
	<h1 class="client"><?PHP echo($invoice->slug()); ?></h1>
	<h2><?PHP echo($invoice->client->name); ?></h2>
	<?PHP $this->load->view('payments/form', array('jobs' => $invoice->jobs, 'action' => site_url('invoice/apply_payment/' . $invoice->slug()))); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>