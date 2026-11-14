<?php
require __DIR__.'/php/comun.php';
inicio('Ferretería: inventario y ventas');
?>
<p>Gestiona productos, prepara pedidos y consulta los ingresos mensuales.</p>
<form action="inventario.php" method="get"><label for="busqueda">Producto, marca o medida</label><input id="busqueda" name="busqueda" maxlength="100" required><button class="btn btn-edit">Buscar</button></form>
<p><a class="btn btn-add" href="formulario.php">Agregar producto</a></p>
<div class="zonas-grid">
<?php foreach(categorias() as $c): ?>
<a class="zona-card" href="inventario.php?zona=<?=h(rawurlencode($c))?>"><?=h($c)?></a>
<?php endforeach; ?>
</div>
<?php fin(); ?>
