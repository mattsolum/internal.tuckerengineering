<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_admin_links())); ?>
<div id="body_wrapper">
	<h1>Create user</h1>
	<?PHP $this->load->view('user/form', array('action' => 'admin/users/create')); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>