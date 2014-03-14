<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_admin_links())); ?>
<div id="body_wrapper">
	<h1>Testbed Methods</h1>
	<ul id="testbed_methods">
		<?PHP foreach($links AS $link): ?>
		<li>
			<a href="<?PHP echo($base . $link); ?>" target="_blank"><?PHP echo($link); ?></a>
		</li>
		<?PHP endforeach; ?>
	</ul>
</div>
<?PHP $this->load->view('sections/footer') ?>