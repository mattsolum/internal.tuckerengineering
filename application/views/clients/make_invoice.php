<?PHP
	$client_slug = url_title($client->name, '_', TRUE);
?>

<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_client_tools($client_slug))); ?>
<div id="body_wrapper">
	<h1 class="client"><?PHP echo($client->name); ?></h1>
	<?PHP $this->load->view('invoices/form', array('jobs' => $jobs, 'action' => site_url('clients/make_invoice/' . $client_slug))); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>