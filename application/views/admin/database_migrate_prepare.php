<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_admin_links())); ?>

<section id="migration">
	<form id="migration_form" method="post" action="<?PHP echo(site_url('admin/database/migration')); ?>" enctype="multipart/form-data">
		<p>
			Load...
		</p>
		<input type="file" name="data" id="data" />
		<p>
			And interpret with...
		</p>
		<select>
			<option>TEBilling</option>
		</select>
	</form>
</section>