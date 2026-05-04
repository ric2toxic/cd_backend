-- Add a new field payment_cleared, similar to other tables. It will replace payment_done field --
ALTER TABLE `oc_wsb_purchase` ADD `payment_cleared` ENUM('NO','YES','NOT_APPLICABLE','PENDING_APPROVAL') 
CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'NO';

-- Moving data from oc_wsb_purchase to oc_trxn_details -- 
INSERT INTO `oc_trxn_details`(`trxn_for`, 
                              `trxn_for_id`, 
                              `trxn_done`, 
                              `trxn_amount`, 
                              `trxn_utr_internal`, 
                              `trxn_utr`, 
                              `trxn_utr_date`, 
                              `trxn_bank`, 
                              `trxn_response`, 
                              `trxn_date_added`) 

SELECT                        'WSB_PURCHASE',        
                              `purchase_id`, 
                              `trxn_done`, 
                              `trxn_amount`, 
                              `trxn_utr_internal`, 
                              `trxn_utr`, 
                              `trxn_utr_date`, 
                              `trxn_bank`, 
                              `trxn_response`, 
                              `trxn_date_added` 
FROM  `oc_wsb_purchase`;

-- Update payment_cleared field in oc_wsb_purchase table --
UPDATE oc_wsb_purchase 
SET payment_cleared = IF(payment_done = 0 AND trxn_done = 'NOT_DONE', 
                         'NO', 
                         IF(trxn_done = 'NOT_APPLICABLE', 'NOT_APPLICABLE', 'YES')
                        );
                        
-- DROP fields which are obsolete now -- 
ALTER TABLE `oc_wsb_purchase`
  DROP `trxn_done`,
  DROP `trxn_amount`,
  DROP `trxn_utr`,
  DROP `trxn_utr_internal`,
  DROP `trxn_utr_date`,
  DROP `trxn_bank`,
  DROP `trxn_response`,
  DROP `trxn_date_added`;
