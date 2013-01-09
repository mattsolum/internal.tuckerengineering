<?PHP
	if(!isset($q))
	{
		$q = '';
	}

	$search_type = '';
	$post_uri = 'search';

	if(isset($type))
	{
		$search_type = ' ' . $type;
		$post_uri .= '/' . $type;
	}
?>

<?PHP $this->load->view('sections/header') ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_search_links())); ?>
<section id="search_section">
	<form method="POST" action="<?PHP echo(site_url($post_uri)); ?>">
		<input type="text" name="q" title="Search<?PHP echo($search_type); ?>" id="search" value="<?PHP echo($q); ?>" />
		<input type="submit" class="hide" />
	</form>
</section>
<script type="text/javascript">
	var prev_val = '';
	$("#search").shadowComplete({source: "<?PHP echo(site_url()); ?>api/v2/autocomplete/", sourceSuffix: ".json", dataContainer: 'data'});
</script>
<?PHP $this->load->view('search/result_list', array('keywords' => $keywords, 'results' => $results)); ?>
<?PHP $this->load->view('sections/footer') ?>