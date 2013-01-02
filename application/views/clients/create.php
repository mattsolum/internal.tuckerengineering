<?PHP $this->load->view('sections/header'); ?>
<div id="body_wrapper">
	<h1>Create new client</h1>
	<?PHP $this->load->view('clients/form', array('action' => 'clients/create', 'client' => $client)); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>