<?php
require __DIR__ . '/vendor/autoload.php';
require_once 'mp-config.php';

if(empty($_POST)===false){
    $file_name = 'ipn.txt';
    $fp = fopen($file_name, "a+");
    fwrite($fp, 'POST:'."\n");
    fwrite($fp, json_encode($_POST)."\n\n");
    fclose($fp);
//    header("HTTP/1.1 200 OK");
//    exit(0);
}

//Init MP SDK
MercadoPago\SDK::setAccessToken($mpConfig['user-mp-app']['access_token']);
//$_POST["type"]='payment';
//$_POST["id"]='1615978805';
if (isset($_POST["type"])) {

//    switch ($_POST["type"]) {
//        case "payment":
//            $data = MercadoPago\Payment::find_by_id($_POST["id"]);
//            break;
//        case "plan":
//            $data = MercadoPago\Plan::find_by_id($_POST["id"]);
//            break;
//        case "subscription":
//            $data = MercadoPago\Subscription::find_by_id($_POST["id"]);
//            break;
//        case "invoice":
//            $data = MercadoPago\Invoice::find_by_id($_POST["id"]);
//            break;
//    }

    $data = controlIPN(json_encode($_POST), $mpConfig['user-mp-app']['access_token']);

    if (empty($data) === false) {

        $file_name = 'ipn.txt';
        $fp = fopen($file_name, "a+");
        fwrite($fp, 'POST GET IPN:'."\n");
        fwrite($fp, json_encode($data)."\n\n");
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
        $file_name = 'ipn.txt';
        $fp = fopen($file_name, "a+");
        fwrite($fp, 'GET:'."\n");
        fwrite($fp, json_encode($merchant_order)."\n\n");
        fclose($fp);
        header("HTTP/1.1 200 OK");
        exit(0);
    }
}

function controlIPN($obj_json_query_ipn = null, $key=null)
{
    if (empty($obj_json_query_ipn)) {
        return false;
    }

    $obj_json_ipn = (object)json_decode($obj_json_query_ipn, true);

    if (is_object($obj_json_ipn) === false) {
        return false;
    }

    switch (true) {
        case (isset($obj_json_ipn->id, $obj_json_ipn->topic) && $obj_json_ipn->topic === 'payment'):
            $d = @file_get_contents('https://api.mercadopago.com/v1/payments/' . $obj_json_ipn->id . '?access_token=' . $key);
            break;
        case (isset($obj_json_ipn->data_id, $obj_json_ipn->type) && $obj_json_ipn->type === 'payment'):
            $d = @file_get_contents('https://api.mercadopago.com/v1/payments/' . $obj_json_ipn->data_id . '?access_token=' . $key);
            break;
        case (isset($obj_json_ipn->collection_id)):
            $d = @file_get_contents('https://api.mercadopago.com/v1/payments/' . $obj_json_ipn->collection_id . '?access_token=' . $key);
            break;
        default:
            return null;
            break;
    }

    if (empty($d) === false) {

        $obj_mp = (object)json_decode($d, true);

        if (isset($obj_mp->status)) {
            return $obj_mp;
        }
    }
    return null;
}

$txt = file_get_contents('ipn.txt');
echo'<pre>';
echo $txt;
echo'</pre>';
