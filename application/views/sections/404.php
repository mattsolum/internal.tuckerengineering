<?PHP $this->load->view('sections/header'); ?>
<div id="fof">
	The area you have navigated to does not exist. Perhaps you can use the search box to locate what you are looking for.
</div>
<section id="search_section">
	<form method="POST" action="<?PHP echo(site_url('search')); ?>">
		<input type="text" name="q" title="Search" id="search" value="" />
		<input type="submit" class="hide" />
	</form>
</section>
<?PHP $this->load->view('sections/footer') ?>