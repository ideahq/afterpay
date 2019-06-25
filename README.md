# Afterpay support for Payum

## Configuration

When adding the gateway, include parameters for Afterpay `merchant_id` & `secret_key`.

    // config.php

    $payum = (new PayumBuilder())
        ->addDefaultStorages()
        ->addGateway('gatewayName', [
            'factory' => 'afterpay',
            'merchant_id' => 'xxxxxx',
            'secret_key' => 'xxxxxx'
        ])
        ->getPayum();
        
        
## Payment Details

Orders will come though with the name "Jane Doe" unless you define these extra payment details.

    // prepare.php
    
    $payment->setDetails(
        $details['consumer'] = [
            'email' => 'fred@fish.com',
            'givenNames' => 'Fred',
            'surname' => 'Fish'
        ]
    );