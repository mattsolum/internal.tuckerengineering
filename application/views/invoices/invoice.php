<!doctype html>
<html>
	<head>
		<link rel="stylesheet" href="<?PHP echo base_url(); ?>resources/css/reset.css" media="all">
		<link rel="stylesheet" href="<?PHP echo base_url(); ?>resources/css/invoice.css" media="all">
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
			<div id="invoice_number" class="six">
				Invoice # <span class="number"><?PHP echo($invoice->slug()); ?></span>
			</div>
			<div id="client" class="six">
				<h1>
					<?PHP echo($invoice->client->name); ?>
				</h1>
				<address>
					<?PHP echo($invoice->client->location->location_string()); ?>
				</address>
				<ul>
					<?PHP foreach($invoice->client->contact AS $contact): ?>
					<li>
						<?PHP if($contact->type == 'contact') echo('Attention '); ?>
						<?PHP echo($contact->info); ?>
					</li>
					<?PHP endforeach; ?>
				</ul>
			</div>
			<div id="date" class="six">
				Date issued: <span class="date"><?PHP echo(date('n/j/Y', $invoice->date_added)); ?></date>
			</div>
			<div class="clear">&nbsp;</span>
		</header>
		<table id="items">
			<thead>
				<tr>
					<td>
						Job Number
					</td>
					<td>
						Description
					</td>
					<td>
						Subtotal
					</td>
				</tr>
			</thead>
			<?PHP foreach($invoice->jobs AS $job): ?>
			<tr>
				<td>#<?PHP echo($job->id); ?></td>
				<td>
					<?PHP echo($job->service()); ?>
					<address><?PHP echo($job->location->location_string()); ?></address>
				</td>
				<td>$<?PHP echo(number_format($job->accounting->debit_total() * -1, 2)); ?></td>
			</tr>
			<?PHP endforeach; ?>
		</table>

		<table id="totals">
			<tr>
				<td></td>
				<td>
					Billed
				</td>
				<td>

				</td>
				<td>
					$<?PHP echo(number_format(abs($invoice->debits_total()), 2)); ?>
				</td>
			</tr>
			<?PHP foreach($invoice->payments() AS $payment): ?>
			<tr>
				<td>
					<?PHP if($payment['date'] != 0) echo(date('n/j/Y', $payment['date'])); ?>
				</td>
				<td>
					<?PHP echo(ucwords($payment['type'])); ?>
				</td>
				<td>
					<?PHP echo(ucwords($payment['tender'])); ?> <?PHP echo(($payment['number'] != '')?' *' . $payment['number']:''); ?>
				</td>
				<td>
					$<?PHP echo(number_format($payment['amount'], 2)); ?>
				</td>
			</tr>
			<?PHP endforeach; ?>
			<tr>
				<td></td>
				<td>
					Due
				</td>
				<td>

				</td>
				<td>
					$<?PHP echo(number_format(abs($invoice->balance()), 2)); ?>
				</td>
			</tr>
		</table>
		<footer>
			<ul>
				<li>
					<p>
						Tucker Engineering, inc.
						<br />1311 Chisholm Trail, Suite 303, Round Rock, Texas 78681
					</p>
					<p>
						tuckerengineering.net
						<br />(512) 255-7477
					</p>
				</li>
				<li>
					<p>
						When making payment by check include the invoice number on the memo line.
					</p>
					<p>
						Any additional work, including construction inspections, will be an additional charge due at the time of the inspection.
					</p>
				</li>
			</ul>
		</footer>
	</body>
</html>