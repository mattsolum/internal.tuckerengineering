<?PHP 
	$aggregate = 0;
	$totals = array();

	if(count($accounting) > 0)
	{
		$date_printed = $accounting[0]->date;
	}
	else
	{
		$date_printed = now();
	}
?>

<!doctype html>
<html>
	<head>
		<link rel="stylesheet" href="<?PHP echo base_url(); ?>resources/css/reset.css" media="all">
		<link rel="stylesheet" href="<?PHP echo base_url(); ?>resources/css/batchpay.css" media="all">
	</head>
	<body>
		<header>
			<img src="<?PHP echo base_url(); ?>resources/img/color_logo.svg" />
			<div id="company">
				<address>
					1311 Chisholm Trail, Suite 303, Round Rock, Texas 78681
				</address>
				<p>
					(512) 255-7477
				</p>
				<p>
					tuckerengineering.net
				</p>
			</div>
			<div class="six">
				Deposite Slip / Batch Payment Voucher
			</div>
			<div id="date" class="six">
				Printed: <span class="date"><?PHP echo(date('n/j/Y', $date_printed)); ?></date>
			</div>
			<div class="clear">&nbsp;</span>
		</header>
		<table id="items">
			<?PHP foreach($accounting AS $batch): ?>
			<tr>
				<td><?PHP echo(date('n/j/Y', $batch->payment->date_added)); ?></td>
				<td>
					<?PHP echo($batch->client->name); ?>
					<ul class="job_list">
						<?PHP foreach($batch->jobs AS $job): ?>
						<li>
						#<?PHP echo($job->id); ?>
						</li>
						<?PHP endforeach; ?>
					</ul>
				</td>
				<td>
					<?PHP echo($batch->payment->tender); ?> *<?PHP echo($batch->payment->number); ?>
				</td>
				<td class="number">$<?PHP echo(number_format($batch->payment->amount, 2)); ?></td>
				<?PHP
					$aggregate += $batch->payment->amount;

					if(!isset($totals[$batch->payment->tender]))
					{
						$totals[$batch->payment->tender] = 0;
					}

					$totals[$batch->payment->tender] += $batch->payment->amount;
				?>
			</tr>
			<?PHP endforeach; ?>
		</table>

		<table id="totals">
			<?PHP
				asort($totals);


				foreach($totals AS $tender => $total):
			?>
			<tr>
				<td></td>
				<td>
					<?PHP echo(ucwords($tender)); ?> Total
				</td>
				<td class="number">
					$<?PHP echo(number_format($total, 2)); ?>
				</td>
			</tr>
			<?PHP endforeach; ?>

			<tr class="due">
				<td></td>
				<td>
					Aggregate Total
				</td>
				<td class="number">
					$<?PHP echo(number_format($aggregate, 2)); ?>
				</td>
			</tr>
		</table>
	</body>
</html>