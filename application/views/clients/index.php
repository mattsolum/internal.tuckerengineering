<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_client_links())); ?>
<?PHP $this->load->view('sections/footer') ?>