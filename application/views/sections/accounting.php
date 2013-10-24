<table id="receipt">
	<?PHP foreach($accounting->debits AS $debit): ?>
	<tr>
		<td>
			<?PHP echo($debit->item); ?>
		</td>
		<td>

		</td>
		<td>
			-$<?PHP echo(number_format(abs($debit->amount), 2)); ?>
		</td>
	</tr>
	<?PHP endforeach; ?>
	<tr class="total">
		<td></td>
		<td>
			Total debits:
		</td>
		<td>
			-$<?PHP echo(number_format(abs($accounting->debit_total()), 2)); ?>
		</td>
	</tr>
	<?PHP foreach($accounting->credits AS $credit): ?>
	<tr>
		<td>
			<?PHP echo($credit->item); ?>
		</td>
		<td>
			<?PHP if($credit->payment != null) echo($credit->payment->summary()); ?>
		</td>
		<td>
			$<?PHP echo(number_format($credit->amount, 2)); ?>
		</td>
	</tr>
	<?PHP endforeach; ?>
	<tr class="total">
		<td></td>
		<td>
			Total credits:
		</td>
		<td>
			$<?PHP echo(number_format($accounting->credit_total(), 2)); ?>
		</td>
	</tr>

</table>