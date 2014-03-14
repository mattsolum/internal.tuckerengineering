<?PHP $this->load->view('sections/header'); ?>
<div id="body_wrapper">
	<h1>Account Controls</h1>
	<?PHP $this->load->view('sections/action_links', array('links' => $this->Navigation->build_user_action_links())); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>