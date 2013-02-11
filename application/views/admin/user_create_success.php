<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_admin_links())); ?>
<div id="body_wrapper">
	<h1>New user created!</h1>
</div>
<?PHP $this->load->view('sections/footer') ?>