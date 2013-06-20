<?PHP $this->load->view('sections/header'); ?>
<section id="search_section">
	<form method="POST" action="<?PHP echo(site_url('search')); ?>">
		<input type="text" name="q" title="Search" id="search" value="" />
		<input type="submit" class="hide" />
	</form>
</section>
<div id="body_wrapper">
	<table id="list">
		<thead>
			<tr></tr>
		</thead>
		<tbody>
			<?PHP 
				foreach($recent_activity AS $key => $row)
				{

				$link = base_url();

				switch($row->type)
				{
					case 'job':
						$link .= 'jobs/' . $row->id;
						break;
					case 'client':
						$link .= 'clients/' . $row->id;
						break;
					case 'property':
						$link .= 'properties/' . $row->id;
						break;
				}
			?>
			<tr<?PHP if($key % 2 == 1) echo(' class="odd"'); ?>>
				<td>
					<a href="<?PHP echo($link); ?>"><?PHP echo($row->type); ?></a>
				</td>
				<td>
					<a href="<?PHP echo($link); ?>">#<?PHP echo($row->id); ?></a>
				</td>
				<td>
					<a href="<?PHP echo($link); ?>"><?PHP echo(substr($row->note, 0, 45)); if(strlen($row->note) > 45) echo('...'); ?></a>
				</td>
				<td>
					<a href="<?PHP echo($link); ?>"><?PHP echo($row->name); ?></a>
				</td>
				<td>
					<a href="<?PHP echo($link); ?>"><?PHP echo(date('n-j-Y', gmt_to_local($row->date_added, 'HNC', false))); ?></a>
				</td>
			</tr>
			<?PHP
				}
			?>
		</tbody>
	</table>
<div id="body_wrapper">
<script type="text/javascript">
	<?PHP $api = ''; ?>
	$("#search").shadowComplete({source: "<?PHP echo(site_url()); ?>api/v2/autocomplete/<?PHP echo($api); ?>", sourceSuffix: ".json", dataContainer: 'data'});
</script>
<?PHP $this->load->view('sections/footer'); ?>