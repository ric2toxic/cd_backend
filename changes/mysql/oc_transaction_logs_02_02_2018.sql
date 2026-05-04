
ALTER TABLE `oc_transaction_logs` CHANGE `request_type` `request_type` ENUM('Purchased','Delivered','Cancelled','Return','GetOrderStatus','GetOTBL','EligibilityCheck','InitiatePreAuth','AuthorizedPreAuth','Refund','CancelReturn') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `oc_transaction_logs` ADD `lpTxnId` VARCHAR(100) NULL DEFAULT NULL AFTER `transaction_id`;


ALTER TABLE `oc_transaction_logs` CHANGE `type` `type` ENUM('NEOGROWTH','WSB_CREDIT','LAZYPAY') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'NEOGROWTH';
