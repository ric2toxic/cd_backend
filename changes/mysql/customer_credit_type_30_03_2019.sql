ALTER TABLE `oc_customer_credit` CHANGE `type` `type` ENUM('Neogrowth','Lazypay','RBL') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Neogrowth';
