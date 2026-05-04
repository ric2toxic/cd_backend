ALTER TABLE `oc_order_payment` 
CHANGE COLUMN `payment_gateway` `payment_gateway` ENUM('citrus', 'razorpay', 'bank_transfer', 'cash', 'paytm', 'fedex', 'gati_kwe', 'gati_ltd', 'coupon', 'cashback', 'neogrowth', 'upi', 'paytabs', 'dotzot', 'connect-india', 'bluedart', 'epay_later', 'lazypay', 'wsb_credit', 'rbl') NULL DEFAULT NULL ;

