SELECT t1.client_id, balance, payments, other_payments, (IFNULL(balance, 0) + IFNULL(payments, 0) - IFNULL(other_payments, 0)) AS final_balance
FROM
(
	SELECT jobs.client_id, SUM(ledger.amount) AS balance
	FROM jobs
	JOIN ledger ON jobs.job_id = ledger.job_id
	WHERE ledger.amount < 0
	GROUP BY jobs.job_id
) t1 LEFT JOIN
(
	SELECT payments.client_id, sum(amount) AS payments
	FROM payments 
	GROUP BY client_id
) t2 ON t1.client_id = t2.client_id
LEFT JOIN
(
	SELECT ledger.client_id AS client_id, SUM(ledger.amount) AS other_payments
	FROM ledger
	JOIN jobs ON ledger.job_id = jobs.job_id 
	WHERE jobs.client_id != ledger.client_id 	GROUP BY ledger.client_id
) t3 on (t2.client_id = t3.client_id)