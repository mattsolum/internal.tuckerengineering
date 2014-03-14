<?PHP
	$num_jobs = count($jobs);
	$date_format = setting('application.date_format');
?>

<?PHP $this->load->view('sections/header'); ?>
<div id="body_wrapper">
	<h1 class="client"><?PHP echo($property->location_string()); ?></h1>
	
	<table id="list">
		<thead>
			<tr>
				
			</tr>
		</thead>
		<tbody class="show_limited">
			<img id="map" src="http://maps.googleapis.com/maps/api/staticmap?center=<?PHP echo($property->latitude . ',' . $property->longitude);?>&zoom=14&size=640x284&sensor=false&scale=1&markers=color:blue|<?PHP echo($property->latitude . ',' . $property->longitude);?>">
			<?PHP 
				foreach($jobs AS $key => $job)
				{
			?>
			<tr<?PHP if($key % 2 == 1) echo(' class="odd"'); ?>>
				<td>
					<a href="<?PHP echo(base_url() . 'jobs/' . $job->id); ?>">#<?PHP echo($job->id); ?></a>
				</td>
				<td>
					<a href="<?PHP echo(base_url() . 'jobs/' . $job->id); ?>"><?PHP echo($job->service() . ' at ' . $job->location->number . ' ' . $job->location->route); ?></a>
				</td>
				<td>
					<a href="<?PHP echo(base_url() . 'jobs/' . $job->id); ?>"><?PHP echo(date('n-j-Y', gmt_to_local($job->date_added, 'HNC', false))); ?></a>
				</td>
			</tr>
			<?PHP
				}
			?>
		</tbody>
	</table>
	<a href="#" id="showAllButton" class="<?PHP if($num_jobs <= 10) echo('deactivate'); ?>"><?PHP echo(($num_jobs <= 10)?'Showing all ':'Show all ' . $num_jobs); ?> jobs</a>
	<?PHP $this->load->view('sections/notes', array('notes' => $property->notes, 'uri' => 'notes/client/' . $property->slug())); ?>
	<div class="clear"></div>
</div>
<?PHP $this->load->view('sections/footer') ?>