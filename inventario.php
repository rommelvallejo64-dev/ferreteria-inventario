<?php
require __DIR__.'/php/comun.php';
require __DIR__.'/php/conexion.php';
try {
    $busqueda=texto(isset($_GET['busqueda'])?$_GET['busqueda']:'',100);
    $zona=isset($_GET['zona'])?$_GET['zona']:'Todo';
    if($busqueda!=='') {
        $like='%'.$busqueda.'%';
        $s=consulta($conn,'SELECT * FROM productos WHERE nombre LIKE ? OR marca LIKE ? OR medida LIKE ? ORDER BY nombre','sss',array($like,$like,$like));
    } elseif($zona==='Todo') { $s=consulta($conn,'SELECT * FROM productos ORDER BY categoria,nombre'); }
    else { $zona=categoria($zona); $s=consulta($conn,'SELECT * FROM productos WHERE categoria=? ORDER BY nombre','s',array($zona)); }
    $rows=$s->get_result()->fetch_all(MYSQLI_ASSOC); $s->close();
} catch(InvalidArgumentException $e) { http_response_code(400); inicio('Consulta no válida'); echo '<p>'.h($e->getMessage()).'</p>'; fin(); exit; }
inicio($busqueda!==''?'Resultados: '.$busqueda:'Inventario: '.$zona);
?>
<p><a href="formulario.php" class="btn btn-add">Agregar producto</a></p>
<div style="overflow-x:auto"><table><thead><tr><th>Producto</th><th>Categoría</th><th>Marca / medida</th><th>Precio</th><th>Stock</th><th>Pedido</th><th>Acciones</th></tr></thead><tbody>
<?php foreach($rows as $p): ?>
<tr><td><?=h($p['nombre'])?></td><td><?=h($p['categoria'])?></td><td><?=h($p['marca'])?> / <?=h($p['medida'])?></td><td>$<?=h($p['precio'])?></td><td><?=h($p['cantidad'])?></td><td>
<?php if((int)$p['cantidad']>0) { post_inicio('agregar',$p['id']); campo('cantidad','Cantidad',1,'number','min="1" max="'.h($p['cantidad']).'" required'); boton('Agregar'); } else { echo 'Agotado'; } ?>
</td><td><a class="btn btn-edit" href="formulario.php?id=<?=h($p['id'])?>">Editar</a>
<?php post_inicio('borrar',$p['id']); boton('Eliminar'); ?></td></tr>
<?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="7">No se encontraron productos.</td></tr><?php endif; ?>
</tbody></table></div>
<?php fin(); ?>
