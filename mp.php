<?php
require __DIR__ . '/vendor/autoload.php';
require_once 'mp-config.php';

//Init MP SDK
MercadoPago\SDK::setAccessToken($mpConfig['user-mp-app']['access_token']);
MercadoPago\SDK::setPublicKey($mpConfig['user-mp-app']['public_key']);
MercadoPago\SDK::setIntegratorId($mpConfig['user-mp-app']['integrator_id']);

// MP Config
$preference = new MercadoPago\Preference();
$preference->external_reference  = "info@e-om.com.ar";
$preference->auto_return = $mpConfig['preference']['auto_return'];
$preference->back_urls = $mpConfig['preference']['back_urls'];
$preference->notification_url = $mpConfig['preference']['notification_url'];
$preference->payment_methods = $mpConfig['preference']['payment_methods'];

// Emulamos usuario con session activa en el sitio modo test
if ($userLogin = true) {

    $payer = new MercadoPago\Payer();
    $payer->name = 'Lalo';
    $payer->surname = 'Landa';
    $payer->first_name = 'Lalo';
    $payer->last_name = 'Landa';
    $payer->email = 'test_user_63274575@testuser.com';
    $payer->date_created = '2020-07-23T12:58:41.425-04:00';
    $payer->phone = array(
        "area_code" => '11',
        "number" => '22223333'
    );
//    $payer->identification = array(
//        "type" => 'DNI',
//        "number" => '12345678'
//    );
    $payer->address = array(
        "street_name" => 'False',
        "street_number" => '123',
        "zip_code" => "1111"
    );

    $preference->payer = $payer;
}

// Post comprar index
if (isset($_POST['price'], $_POST['unit'], $_POST['title'], $_POST['img'])) {

    // Productos
    $item = new MercadoPago\Item();

    $item->id = '1234';
//    $item->currency_id = 'ARS';
    $item->title = $_POST['title'];
    $item->description = 'Dispositivo móvil de Tienda e-commerce';
    $item->picture_url = $domian.$_POST['img'];
//    $item->category_id = 'art';
    $item->quantity = 1;
    $item->unit_price = $_POST['price'];

    $preference->items = array($item);

    // Get URL or IDCheckOut
    $preference->save();

    $IdMpCheckOut = $preference->id;
    $linkDePagoUrl = $preference->init_point;
}
//print_r($linkDePagoUrl);
//print_r($IdMpCheckOut);
//echo'<pre>';
//print_r($preference);
//echo'</pre>';
//exit;
