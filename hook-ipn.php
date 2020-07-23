<?php
require_once 'mp.php';

//    MercadoPago\SDK::setAccessToken($mpConfig['user-mp-app']['access_token']);
//if(count($_GET)>1 || count($_POST)>1){
if (isset($_POST["type"])) {


    switch ($_POST["type"]) {
        case "payment":
            $data = MercadoPago\Payment . find_by_id($_POST["id"]);
            break;
        case "plan":
            $data = MercadoPago\Plan . find_by_id($_POST["id"]);
            break;
        case "subscription":
            $data = MercadoPago\Subscription . find_by_id($_POST["id"]);
            break;
        case "invoice":
            $data = MercadoPago\Invoice . find_by_id($_POST["id"]);
            break;
    }

    if (empty($data) === false) {
//        $file_name = date('Y-m-d').'-ipn.txt';
        $file_name = 'ipn.txt';
        $fp = fopen($file_name, "a+");
        fwrite($fp, json_encode($data));
        fclose($fp);
    }
}

if (isset($_GET["topic"])) {

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
        if ($payment['status'] == 'approved') {
            $paid_amount += $payment['transaction_amount'];
        }
    }

    if ($paid_amount >= $merchant_order->total_amount) {
        if (count($merchant_order->shipments) > 0) { // El merchant_order tiene envíos
            if ($merchant_order->shipments[0]->status == "ready_to_ship") {
                print_r("Totalmente pagado Imprima la etiqueta y suelte su artículo.");
            }
        } else {
            print_r("Totalmente pagado Libera tu artículo.");
        }
    } else {
        print_r("Aún no pagado. No sueltes tu artículo.");
    }

    if(empty($merchant_order)===false){
//        $file_name = date('Y-m-d').'-ipn.txt';
        $file_name = 'ipn.txt';
        $fp = fopen($file_name, "a+");
        fwrite($fp, json_encode($data));
        fclose($fp);
    }
}

//}
$txt = file_get_contents('ipn.txt');
echo $txt;