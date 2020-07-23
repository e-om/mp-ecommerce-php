<?php
$domian = 'https://sergio-eom.herokuapp.com/';
$domian = 'http://localhost/mp/';
$mpConfig = [
    'user-mp-app'=>[
      'platform_id'=>null,
      'integrator_id'=>'dev_24c65fb163bf11ea96500242ac130004',
      'corporation_id'=>null,
      'collector_id'=>'469485398',
      'public_key'=>'APP_USR-7eb0138a-189f-4bec-87d1-c0504ead5626',
      'access_token'=>'APP_USR-6317427424180639-042414-47e969706991d3a442922b0702a0da44-469485398',
    ],
    'preference' => [
        'external_reference' => 'info@e-om.com.ar',
        'notification_url' => $domian . 'hook-ipn.php',
//        'notification_url' => $domian . 'hook-ipn.php?source_news=webhooks', // solo webhook sin IPN
//        'notification_url' => $domian . 'hook-ipn.php?source_news=ipn', // solo webhook sin IPN
//        'auto_return' => 'all', // approved
        'auto_return' => 'approved',
        'back_urls' => [
            'success' => $domian . 'pagos-mp.php?estado-mp=success',
            'failure' => $domian . 'pagos-mp.php?estado-mp=failure',
            'pending' => $domian . 'pagos-mp.php?estado-mp=pending',
        ],
        'payment_methods' => [
            'installments' => 6,
            'excluded_payment_methods' => [
                ['id' => 'amex']
            ],
            'excluded_payment_types' => [
                ['id' => 'atm']
            ],
        ],
//        'expires' => false,
//        'expiration_date_from' => null,
//        'expiration_date_to' => null,
    ],
];
