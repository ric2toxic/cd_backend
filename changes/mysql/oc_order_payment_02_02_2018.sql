ALTER TABLE `oc_order_payment` CHANGE `payment_gateway` `payment_gateway` ENUM('citrus','razorpay','bank_transfer','cash','paytm','fedex','gati_kwe','gati_ltd','coupon','cashback','neogrowth','upi','paytabs','dotzot','connect-india','bluedart','lazypay') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;




