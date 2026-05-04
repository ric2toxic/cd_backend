ALTER TABLE `oc_order_payment` CHANGE `payment_gateway` `payment_gateway` ENUM('citrus','razorpay','bank_transfer','cash','paytm','fedex','gati_kwe','gati_ltd', 'upi','paytabs') DEFAULT NULL;

ALTER TABLE `oc_order_payment` ADD `invoice_id` VARCHAR(20) DEFAULT NULL;


