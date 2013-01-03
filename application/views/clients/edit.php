<?PHP
	$client_slug = url_title($client->name, '_', TRUE);
?>

<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_client_tools($client_slug))); ?>
<div id="body_wrapper">
	<h1>Edit Client</h1>
	<?PHP $this->load->view('clients/form', array('action' => 'clients/edit/' . $client_slug, 'client' => $client)); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>