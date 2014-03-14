<?PHP $this->load->view('sections/header'); ?>
<div id="body_wrapper">
	<?PHP $this->load->view('sections/action_links', array('links' => $this->Navigation->build_admin_links())); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>