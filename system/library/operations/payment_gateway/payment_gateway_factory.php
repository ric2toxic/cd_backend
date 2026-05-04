<?php

class PaymentGatewayFactory {

    public static function getPaymentGateway($registry) {
        // if (PAYMENT_GATEWAY == 'razorpay')
        //     return new RazorPay($registry);
        // else
            return new Citrus($registry);
    }
}