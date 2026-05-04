DROP TABLE IF EXISTS `oc_trxn_details`;

CREATE TABLE oc_trxn_details ( 
id int(11) NOT NULL AUTO_INCREMENT, 
trxn_for enum('CREDIT_NOTE','EXCESS_PAYMENT_BY_CUSTOMER','SELLER_DEBIT_NOTE','SELLER_INVOICE','WSB_SOR_PAYMENT', 'WSB_PURCHASE' , 'MISCELLANEOUS') DEFAULT 'MISCELLANEOUS', 
trxn_for_id int(11) NOT NULL, 
trxn_done enum('NOT_APPLICABLE','NOT_DONE','FAILURE','BANK_REQUESTED','BANK_PROCESSED','BANK_SUCCESS','BANK_FAILURE','INVALID_NO_GOODS','FAILED_ORDER') NOT NULL DEFAULT 'NOT_DONE', 
trxn_amount decimal(10,2) DEFAULT NULL, 
trxn_utr_internal varchar(128) DEFAULT NULL, 
trxn_utr varchar(128) DEFAULT NULL, 
trxn_utr_date date DEFAULT NULL, 
trxn_bank enum('icici','stanc','citrus','razorpay') DEFAULT NULL, 
trxn_response text, 
trxn_date_added datetime DEFAULT NULL, 
PRIMARY KEY (id) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


