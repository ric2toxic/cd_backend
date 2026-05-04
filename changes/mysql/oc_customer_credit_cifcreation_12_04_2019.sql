ALTER TABLE `customer_credit_cifcreation` ADD `cif_creation_status` ENUM('Pending','SentForCifCreation','DiscrepencyStatus','CifCreated') NULL DEFAULT 'Pending' AFTER `tc_acceptance_flag`;
