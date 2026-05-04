ALTER TABLE `oc_order_payment` CHANGE `payment_gateway` `payment_gateway` ENUM('citrus', 'razorpay', 'bank_transfer', 'cash', 'paytm', 'upi');
