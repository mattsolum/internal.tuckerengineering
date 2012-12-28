<?PHP
	if(!isset($q))
	{
		$q = '';
	}

	if(!isset($post_uri))
	{
		$post_uri = 'search';
	}

	$search_type = '';

	$segments = explode('/', trim(uri_string(), '/'));
	if(isset($segments[1]))
	{
		$search_type = ' ' . $segments[1];
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