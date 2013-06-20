<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Invoice</title>

		<link rel="stylesheet" href="<?PHP echo base_url(); ?>resources/css/reset.css" media="all">
		<link rel="stylesheet" href="<?PHP echo base_url(); ?>resources/css/invoice.css" media="all">
	</head>
	<body>
		<header>
			<img src="<?PHP echo base_url(); ?>resources/img/color_logo.svg" />
			<div id="company">
				<address>
					1311 Chisholm Trail
					<br />Suite 303
					<br />Round Rock, Texas 78681
				</address>
				<p>
					(512) 255-7477
				</p>
				<p>
					tuckerengineering.net
				</p>
			</div>
			<div id="invoice_number" class="six">
				Invoice # <span class="number">12345</span>
			</div>
			<div id="client" class="six">
				<h1>
					Superior Foundation Repair
				</h1>
				<address>
					123 Duval St
					<br />Pflugerville, Texas 78783
				</address>
				<ul>
					<li>
						Attention Brady Barnet
					</li>
					<li>
						(123) 456-7890
					</li>
					<li>
						brady@superiorfoundationrepair.com
					</li>
				</ul>
			</div>
			<div id="date" class="six">
				Date issued: <span class="date">June 20th, 2013</date>
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
			<tr>
				<td>#10876</td>
				<td>
					Consultation, review of report / Letter
					<address>1009 Whispering Dr</address>
				</td>
				<td>$550.00</td>
			</tr>
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
					$550.00
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					Credit
				</td>
				<td>

				</td>
				<td>
					$0.00
				</td>
			</tr>
			<tr>
				<td>6/20/2013</td>
				<td>
					Payment
				</td>
				<td>
					Check *123
				</td>
				<td>
					$550.00
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					Due
				</td>
				<td>

				</td>
				<td>
					$0.00
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