<ul id="search_results">
	<?PHP 
	foreach ($results as $key => $result) {
	?>
	<li<?PHP if($key % 2 == 0) echo(' class="odd"'); ?>>
	<?PHP
		$this->load->view('search/result', array('q' => $q, 'result' => $result));
	?>
	</li>
	<?PHP
	}
	?>
</ul>