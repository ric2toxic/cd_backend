ALTER TABLE
    `oc_franchise_data` ADD `franchise_status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `franchise_prefix`;

UPDATE
    oc_franchise_data fd
JOIN
    oc_customer c
ON
    c.customer_id = fd.franchise_id
SET
    fd.franchise_status = c.franchise_status;

ALTER TABLE `oc_customer`
  DROP `is_franchise`,
  DROP `franchise_status`;
