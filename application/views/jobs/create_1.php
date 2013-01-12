<?PHP $this->load->view('sections/header'); ?>
<div id="body_wrapper">
	<h1>Create new job - step 1 of 3</h1>
	<?PHP $this->load->view('jobs/client_form', array('action' => 'jobs/create/requester', 'job' => $job)); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>
