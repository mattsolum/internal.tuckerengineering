<?PHP $this->load->view('sections/header'); ?>
<div id="body_wrapper">
	<h1>Account Controls</h1>
	<?PHP $this->load->view('user/account_form', array('action' => 'user/account', 'user' => $this->User->get_current_user())); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>