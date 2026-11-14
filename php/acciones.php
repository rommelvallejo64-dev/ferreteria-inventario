<?php
require __DIR__.'/comun.php';
if($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); header('Allow: POST'); exit('Usa el formulario de la aplicación.'); }
$destino='../carrito.php'; $transaccion=false;
try {
    comprobar_csrf(isset($_POST['csrf'])?$_POST['csrf']:null);
    $accion=isset($_POST['accion'])?$_POST['accion']:null;
    if(!is_string($accion) || !in_array($accion,array('guardar','borrar','agregar','cantidad','precio','quitar','vaciar','cobrar'),true)) { throw new InvalidArgumentException('Acción no válida.'); }
    $carrito=isset($_SESSION['carrito'])?$_SESSION['carrito']:array();
    if($accion==='vaciar') { $carrito=array(); }
    elseif($accion==='quitar') { $id=entero(isset($_POST['id'])?$_POST['id']:null,1,2147483647); unset($carrito[$id]); }
    elseif($accion==='precio') {
        $id=entero(isset($_POST['id'])?$_POST['id']:null,1,2147483647);
        if(!isset($carrito[$id])) { throw new InvalidArgumentException('Producto fuera del carrito.'); }
        $carrito[$id]['precio_centavos']=centavos(isset($_POST['precio'])?$_POST['precio']:null);
    } else {
        require __DIR__.'/conexion.php';
        if($accion==='guardar') {
            $destino='../inventario.php?zona=Todo';
            $nombre=texto(isset($_POST['nombre'])?$_POST['nombre']:null,100,true);
            $marca=texto(isset($_POST['marca'])?$_POST['marca']:'',100);
            $medida=texto(isset($_POST['medida'])?$_POST['medida']:'',50);
            $cat=categoria(isset($_POST['categoria'])?$_POST['categoria']:null);
            $precio=dinero(centavos(isset($_POST['precio'])?$_POST['precio']:null));
            $cantidad=entero(isset($_POST['cantidad'])?$_POST['cantidad']:null,0);
            $valores=array($nombre,$marca,$medida,$cat,$precio,$cantidad);
            if(isset($_POST['id']) && $_POST['id']!=='') {
                $id=entero($_POST['id'],1,2147483647); producto($conn,$id); $valores[]=$id;
                consulta($conn,'UPDATE productos SET nombre=?,marca=?,medida=?,categoria=?,precio=?,cantidad=? WHERE id=?','sssssii',$valores)->close();
            } else { consulta($conn,'INSERT INTO productos(nombre,marca,medida,categoria,precio,cantidad) VALUES(?,?,?,?,?,?)','sssssi',$valores)->close(); }
        } elseif($accion==='borrar') {
            $destino='../inventario.php?zona=Todo'; $id=entero(isset($_POST['id'])?$_POST['id']:null,1,2147483647);
            consulta($conn,'DELETE FROM productos WHERE id=?','i',array($id))->close(); unset($carrito[$id]);
        } elseif($accion==='agregar' || $accion==='cantidad') {
            $id=entero(isset($_POST['id'])?$_POST['id']:null,1,2147483647); $p=producto($conn,$id);
            if($accion==='agregar') { $carrito=agregar($carrito,$p,isset($_POST['cantidad'])?$_POST['cantidad']:null); }
            else {
                if(!isset($carrito[$id])) { throw new InvalidArgumentException('Producto fuera del carrito.'); }
                $cantidad=entero(isset($_POST['cantidad'])?$_POST['cantidad']:null);
                if($cantidad>(int)$p['cantidad']) { throw new InvalidArgumentException('Cantidad superior a las existencias.'); }
                $carrito[$id]['cantidad']=$cantidad;
            }
        } elseif($accion==='cobrar') {
            if(!$carrito) { throw new InvalidArgumentException('El carrito está vacío.'); }
            $conn->begin_transaction(); $transaccion=true; $unidades=0; $total=0;
            ksort($carrito,SORT_NUMERIC);
            foreach($carrito as $id=>$item) {
                $id=entero($id,1,2147483647); $p=producto($conn,$id,true); $cantidad=entero($item['cantidad']);
                if($cantidad>(int)$p['cantidad']) { throw new InvalidArgumentException('El stock cambió. Revisa el pedido antes de cobrar.'); }
                $precio=centavos(dinero($item['precio_centavos']));
                $unidades+=$cantidad; $total+=$cantidad*$precio;
                if($unidades>2147483647 || $total>9999999999) { throw new InvalidArgumentException('La venta supera el límite permitido.'); }
                $s=consulta($conn,'UPDATE productos SET cantidad=cantidad-? WHERE id=? AND cantidad>=?','iii',array($cantidad,$id,$cantidad));
                if($s->affected_rows!==1) { throw new RuntimeException('No se pudo actualizar el stock.'); } $s->close();
            }
            consulta($conn,'INSERT INTO ventas(total_articulos,total_ingresos) VALUES(?,?)','is',array($unidades,dinero($total)))->close();
            $conn->commit(); $transaccion=false; $carrito=array();
        }
    }
    $_SESSION['carrito']=$carrito;
    $_SESSION['mensaje']=$accion==='cobrar'?'Venta registrada y existencias actualizadas.':'Cambio guardado.';
} catch(Exception $e) {
    if($transaccion) { $conn->rollback(); }
    $_SESSION['mensaje']=$e instanceof InvalidArgumentException?$e->getMessage():'No se guardó la operación. Revisa la configuración de demostración.';
}
header('Location: '.$destino,true,303); exit;
