INSERT INTO billing_copy
SELECT
	parsed.sequence AS sequence_number,
	billing.CLNTNO AS client_id,
	billing.RPTNO AS job_id,
	billing.WORKPERFORMED AS workperformed,
	'1311' AS number,
	'Chisholm Trail' AS route,
	'303' AS subpremise,
	'Round Rock' AS locality,
	'TX' AS admin_level_1,
	'Williamson' AS admin_level_2,
	'78681' AS postal_code,
	billing.REQUESTBY AS requester,
	billing.REALTOR AS realtor,
	billing.DOCFILE AS asset,
	billing.BILLDATE AS date_billed,
	billing.BILLED AS amount,
	CONCAT(billing.CLNTNO, '; ', billing.RPTNO, '; ', billing.WORKPERFORMED, '; ', billing.NUMBER, '; ', billing.STREET, '; ', billing.LOCATION, '; ', billing.REQUESTBY, '; ', billing.REALTOR, '; ', billing.DOCFILE, '; ', billing.BILLDATE, '; ', billing.BILLED, '; ', billing.PAID, '; ', billing.ADJ, '; ', billing.CR, '; ', billing.BALANCE) AS note,
	parsed.latitude,
	parsed.longitude
FROM 
	parsed
		JOIN address_elements
			ON parsed.sequence = address_elements.id
		JOIN billing 
			ON parsed.id = billing.RPTNO
WHERE address_elements.number = '' 
AND route = ''
AND locality = ''
AND admin_level_1 = ''
AND postal_code = ''
AND parsed.type = 'billing';

INSERT INTO billing_copy
SELECT
	parsed.sequence AS sequence_number,
	billing.CLNTNO AS client_id,
	billing.RPTNO AS job_id,
	billing.WORKPERFORMED AS workperformed,
	address_elements.number AS number,
	TRIM(CONCAT(address_elements.predirection, ' ', address_elements.route, IF(address_elements.postdirection, address_elements.postdirection, ''), ' ', address_elements.street_suffix)) AS route,
	address_elements.subpremise,
	address_elements.locality,
	address_elements.admin_level_1,
	parsed.CountyName AS admin_level_2,
	address_elements.postal_code,
	billing.REQUESTBY AS requester,
	billing.REALTOR AS realtor,
	billing.DOCFILE AS asset,
	billing.BILLDATE AS date_billed,
	billing.BILLED AS amount,
	CONCAT(billing.CLNTNO, '; ', billing.RPTNO, '; ', billing.WORKPERFORMED, '; ', billing.NUMBER, '; ', billing.STREET, '; ', billing.LOCATION, '; ', billing.REQUESTBY, '; ', billing.REALTOR, '; ', billing.DOCFILE, '; ', billing.BILLDATE, '; ', billing.BILLED, '; ', billing.PAID, '; ', billing.ADJ, '; ', billing.CR, '; ', billing.BALANCE) AS note,
	parsed.latitude,
	parsed.longitude
FROM 
	parsed
		JOIN address_elements
			ON parsed.sequence = address_elements.id
		JOIN billing 
			ON parsed.id = billing.RPTNO
WHERE address_elements.number != '' 
AND route != ''
AND locality != ''
AND admin_level_1 != ''
AND postal_code != ''
AND parsed.type = 'billing';

TRUNCATE TABLE billing_copy;
UPDATE address_elements, address_elements_copy
SET address_elements.id = address_elements_copy.id, 
address_elements.number = address_elements_copy.number, 
address_elements.route = address_elements_copy.route, 
address_elements.subpremise = address_elements_copy.subpremise, 
address_elements.locality = address_elements_copy.locality, 
address_elements.admin_level_1 = address_elements_copy.admin_level_1, 
address_elements.admin_level_2 = address_elements_copy.admin_level_2, 
address_elements.postal_code = address_elements_copy.postal_code, 
address_elements.predirection = address_elements_copy.predirection, 
address_elements.postdirection = address_elements_copy.postdirection, 
address_elements.street_suffix = address_elements_copy.street_suffix
WHERE address_elements.address_id = address_elements_copy.address_id
AND address_elements.address_type = address_elements_copy.address_type
AND address_elements.address_type != NULL;
	
INSERT INTO clients_parsed
SELECT
	clients.CLNTNO AS client_id,
	clients.CLNAME AS `name`,
	clients.EMAIL AS email,
	clients.PHONE AS phone,
	clients.FAX AS fax,
	clients.CONTACT AS contact,
	ideal.number,
	ideal.route,
	ideal.subpremise,
	ideal.locality,
	ideal.admin_level_1,
	ideal.admin_level_2,
	ideal.postal_code,
	CONCAT(clients.CLNTNO, '; ', clients.CLNAME, '; ', clients.ADDR1, '; ', clients.ADDR2, '; ', clients.CITY, '; ', clients.STATE, '; ', clients.ZIP, '; ', clients.PHONE, '; ', clients.FAX, '; ', clients.CONTACT, '; ', clients.EMAIL, '; ', clients.CURBAL, '; ', clients.MISC, '; ', clients.BEGBAL) AS note
FROM 
	ideal
		JOIN clients
			ON ideal.id = clients.CLNTNO
WHERE ideal.type = 'client';

INSERT INTO ideal
SELECT
	everything.sequence AS sequence,
	everything.`[id]` AS id,
	everything.`[type]` AS type,
	'1311'AS `number`,
	'Chisholm Trail' AS route,
	'303' AS subpremise,
	'Round Rock' AS locality,
	'TX' AS admin_level_1,
	'Williamson' AS admin_level_2,
	'78681' AS postal_code,
	'' AS latitude,
	'' AS longitude
FROM 
	everything
		JOIN components
			ON everything.sequence = components.sequence
WHERE components.primarynumber = '' 
OR components.streetname = ''
OR everything.city = ''
OR everything.state = ''
OR everything.zipcode = ''
ON DUPLICATE KEY UPDATE
sequence = everything.sequence;

SELECT clients_parsed.client_id, t1.client_id AS 'final_id', t1.name
FROM clients_parsed
 JOIN
(
SELECT client_id, name
FROM clients_parsed 
GROUP BY name
HAVING COUNT(name) > 1
AND name != ''
ORDER BY client_id ASC
) AS t1 ON t1.name = clients_parsed.name
WHERE clients_parsed.client_id != t1.client_id
ORDER BY t1.name ASC, clients_parsed.client_id ASC;