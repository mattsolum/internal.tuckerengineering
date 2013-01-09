<?PHP
	if(isset($search) && is_array($search))
	{
		$results = $search;
		$keywords = $id;
	}	
?>
<ul id="search_results">
	<?PHP 
	foreach ($results as $key => $result) {
	?>
	<li<?PHP if($key % 2 == 1) echo(' class="odd"'); ?>>
	<?PHP
		$this->load->view('search/result', array('keywords' => $keywords, 'result' => $result));
	?>
	</li>
	<?PHP
	}
	?>
</ul>