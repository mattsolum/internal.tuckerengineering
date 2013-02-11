<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_admin_links())); ?>
<div id="body_wrapper">
	<?PHP $this->load->view('sections/action_links', array('links' => $this->Navigation->build_user_admin_links())); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>