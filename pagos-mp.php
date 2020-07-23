<?php
echo "<pre>DEBUG: \n";
print_r($_GET);
?>

<?php if(isset($_GET['collection_id']) && empty($_GET['collection_id'])===false){
    $d = @file_get_contents('https://api.mercadopago.com/v1/payments/' . $_GET['collection_id'] . '?access_token=APP_USR-6317427424180639-042414-47e969706991d3a442922b0702a0da44-469485398');
    echo $d;
}?>

<?php echo '</pre>'; ?>

<?php switch ($_GET['estado-mp']) {
    case 'failure':
        $msj = 'No finalizo su pago o se genero un problema con el medio de pago, intente luego por favor';
        break;
    case 'pending':
        $msj = 'Gracias por el pago, lo estamos procesando, cuando se acredite le enviamos el pedido';
        break;
    case 'success':
        $msj = 'Gracias por el pago, en 24hs le estamos mandando el producto';
        break;
    default:
        break;
};?>

<h1><?php echo $msj; ?></h1>
<br>
<a href="/">Volver a la tienda</a>
