<?php
require __DIR__.'/php/comun.php';
$p=array('id'=>'','nombre'=>'','marca'=>'','medida'=>'','categoria'=>'Otros','precio'=>'0.00','cantidad'=>0);
try {
    if(isset($_GET['id'])) { require __DIR__.'/php/conexion.php'; $p=producto($conn,entero($_GET['id'],1,2147483647)); }
} catch(InvalidArgumentException $e) { http_response_code(404); inicio('Producto no encontrado'); echo '<p>'.h($e->getMessage()).'</p>'; fin(); exit; }
inicio($p['id']===''?'Agregar producto':'Editar producto');
post_inicio('guardar',$p['id']);
campo('nombre','Nombre',$p['nombre'],'text','maxlength="100" required');
campo('marca','Marca',$p['marca'],'text','maxlength="100"');
campo('medida','Medida',$p['medida'],'text','maxlength="50"');
?>
<p><label for="categoria">Categoría</label><select id="categoria" name="categoria">
<?php foreach(categorias() as $c): ?><option value="<?=h($c)?>" <?=$p['categoria']===$c?'selected':''?>><?=h($c)?></option><?php endforeach; ?>
</select></p>
<?php
campo('precio','Precio',$p['precio'],'number','min="0" max="999999.99" step="0.01" required');
campo('cantidad','Existencias',$p['cantidad'],'number','min="0" max="1000000" step="1" required');
boton('Guardar producto'); fin();
