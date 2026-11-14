<?php
require __DIR__.'/php/comun.php';
$carrito=isset($_SESSION['carrito'])?$_SESSION['carrito']:array(); $total=0;
inicio('Resumen de pedido');
if(!$carrito) { echo '<p>El carrito está vacío.</p>'; fin(); exit; }
?>
<div style="overflow-x:auto"><table><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio unitario</th><th>Subtotal</th><th>Acción</th></tr></thead><tbody>
<?php foreach($carrito as $id=>$p): $subtotal=$p['cantidad']*$p['precio_centavos']; $total+=$subtotal; ?>
<tr><td><?=h($p['nombre'])?><br><?=h($p['marca'])?> / <?=h($p['medida'])?></td><td>
<?php post_inicio('cantidad',$id); campo('cantidad','Cantidad',$p['cantidad'],'number','min="1" max="1000000" step="1" required'); boton('Actualizar'); ?>
</td><td>
<?php post_inicio('precio',$id); campo('precio','Precio',dinero($p['precio_centavos']),'number','min="0" max="999999.99" step="0.01" required'); boton('Actualizar'); ?>
</td><td>$<?=h(dinero($subtotal))?></td><td><?php post_inicio('quitar',$id); boton('Quitar'); ?></td></tr>
<?php endforeach; ?>
</tbody></table></div><p><strong>Total: $<?=h(dinero($total))?></strong></p>
<?php post_inicio('cobrar'); boton('Registrar venta'); post_inicio('vaciar'); boton('Vaciar pedido'); fin(); ?>
