SELECT client_id, SUM(ledger.amount) AS 'balance'
FROM
(
	(
		SELECT payments.client_id AS 'client_id', payments.ledger_id AS 'ledger_id'
		FROM payments
	)
	UNION 
	(
		SELECT jobs.client_id AS 'client_id', jobs_ledger.ledger_id AS 'ledger_id'
		FROM jobs INNER JOIN jobs_ledger 
		ON jobs.job_id = jobs_ledger.job_id
	)
) AS client_ledger, ledger
WHERE client_ledger.ledger_id = ledger.ledger_id
GROUP BY client_id
ORDER BY client_id;
