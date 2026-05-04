ALTER TABLE `oc_transaction_logs` CHANGE `type` `type` ENUM('NEOGROWTH','WSB_CREDIT','LAZYPAY','RBL') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'NEOGROWTH';

ALTER TABLE `oc_transaction_logs` CHANGE `request_type` `request_type` ENUM('Purchased','Delivered','Cancelled','Return','GetOrderStatus','GetOTBL','EligibilityCheck','InitiatePreAuth','AuthorizedPreAuth','Refund','CancelReturn','ReleasedPreAuth','DisbursalRequest','Disbursed') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

