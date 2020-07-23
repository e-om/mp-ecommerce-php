<?php
echo '<pre>';
print_r($_GET);
echo '</pre>';
?>

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
