<?php
require __DIR__ . '/vendor/autoload.php';
require_once 'mp-config.php';

//Init MP SDK
MercadoPago\SDK::setAccessToken($mpConfig['user-mp-app']['access_token']);
//$_POST["type"]='payment';
//$_POST["id"]='1615978805';
if (isset($_POST["type"], $_POST["id"])) {

    switch ($_POST["type"]) {
        case "payment":
            $data = MercadoPago\Payment::find_by_id($_POST["id"]);
            break;
        case "plan":
            $data = MercadoPago\Plan::find_by_id($_POST["id"]);
            break;
        case "subscription":
            $data = MercadoPago\Subscription::find_by_id($_POST["id"]);
            break;
        case "invoice":
            $data = MercadoPago\Invoice::find_by_id($_POST["id"]);
            break;
    }

    if (empty($data) === false) {
//        $file_name = date('Y-m-d').'-ipn.txt';
        $file_name = 'ipn.txt';
        $fp = fopen($file_name, "a+");
        fwrite($fp, json_encode($data)."\n");
        fclose($fp);
        header("HTTP/1.1 200 OK");
        exit(0);
    }
}
//$_GET["topic"]='merchant_order';
//$_GET["id"]='1615978805';
if (isset($_GET["topic"], $_GET["id"])) {

    $merchant_order = null;

    switch ($_GET["topic"]) {
        case "payment":
            $payment = MercadoPago\Payment::find_by_id($_GET["id"]);
            // Get the payment and the corresponding merchant_order reported by the IPN.
            $merchant_order = MercadoPago\MerchantOrder::find_by_id($payment->order->id);
            break;
        case "merchant_order":
            $merchant_order = MercadoPago\MerchantOrder::find_by_id($_GET["id"]);
            break;
    }

    $paid_amount = 0;
    foreach ($merchant_order->payments as $payment) {
        if (isset($payment->status) && $payment->status === 'approved') {
            $paid_amount += $payment->transaction_amount;
        }
    }

    if ($paid_amount >= $merchant_order->total_amount) {
            print_r("Totalmente pagado Libera tu artículo.");
    } else {
        print_r("Aún no pagado. No sueltes tu artículo.");
    }

    if(empty($merchant_order)===false){
//        $file_name = date('Y-m-d').'-ipn.txt';
        $file_name = 'ipn.txt';
        $fp = fopen($file_name, "a+");
        fwrite($fp, json_encode($merchant_order)."\n");
        fclose($fp);
        header("HTTP/1.1 200 OK");
        exit(0);
    }
}

//}
$txt = file_get_contents('ipn.txt');
echo $txt;
