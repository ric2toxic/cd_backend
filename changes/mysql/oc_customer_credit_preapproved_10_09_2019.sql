ALTER TABLE `oc_customer_credit_preapproved` CHANGE `cif_creation_status` `cif_creation_status` ENUM('Pending','SentForCifCreation','DiscrepencyStatus','CifCreated','JourneyCompleted') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'Pending';

#Need to add new ENUM('JourneyCompleted') value in field `oc_customer_credit_preapproved`.`cif_creation_status`


