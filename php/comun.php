<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(0, '/', '', !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', true);
    session_start();
}
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function csrf() { return '<input type="hidden" name="csrf" value="'.h($_SESSION['csrf']).'">'; }
function comprobar_csrf($v) {
    if (!is_string($v) || !hash_equals($_SESSION['csrf'],$v)) { throw new InvalidArgumentException('Formulario caducado. Recarga la página.'); }
}
function entero($v, $min=1, $max=1000000) {
    if (!is_scalar($v) || filter_var($v,FILTER_VALIDATE_INT)===false || (int)$v<$min || (int)$v>$max) { throw new InvalidArgumentException('Cantidad o identificador fuera de rango.'); }
    return (int)$v;
}
function centavos($v) {
    if (!is_scalar($v) || !preg_match('/^(0|[1-9][0-9]{0,5})(?:\.([0-9]{1,2}))?$/D',(string)$v,$m)) { throw new InvalidArgumentException('Precio no válido: admite hasta dos decimales.'); }
    return (int)$m[1]*100+(int)str_pad(isset($m[2])?$m[2]:'',2,'0');
}
function dinero($v) { return sprintf('%d.%02d',intdiv($v,100),$v%100); }
function texto($v,$max,$obligatorio=false) {
    if (!is_string($v) || strlen($v)>$max || ($obligatorio && trim($v)==='')) { throw new InvalidArgumentException('Campo de texto vacío o demasiado largo.'); }
    return trim($v);
}
function categorias() { return array('Griferia','Fontaneria','Tubos','Conexiones','Electricidad','Herramientas','Fijacion','Otros'); }
function categoria($v) {
    if (!is_string($v) || !in_array($v,categorias(),true)) { throw new InvalidArgumentException('Categoría no válida.'); }
    return $v;
}
function agregar($carrito,$p,$cantidad) {
    $id=(int)$p['id']; $cantidad=entero($cantidad);
    $cantidad=entero($cantidad+(isset($carrito[$id])?(int)$carrito[$id]['cantidad']:0));
    if ($cantidad>(int)$p['cantidad']) { throw new InvalidArgumentException('El pedido acumulado supera las existencias.'); }
    $precio=isset($carrito[$id])?$carrito[$id]['precio_centavos']:centavos($p['precio']);
    $carrito[$id]=array('id'=>$id,'nombre'=>$p['nombre'],'marca'=>$p['marca'],'medida'=>$p['medida'],'cantidad'=>$cantidad,'precio_centavos'=>$precio);
    return $carrito;
}
function consulta($conn,$sql,$tipos='',$valores=array()) {
    $s=$conn->prepare($sql);
    if ($tipos!=='') {
        $refs=array($tipos);
        foreach($valores as $k=>$v) { $refs[]=&$valores[$k]; }
        call_user_func_array(array($s,'bind_param'),$refs);
    }
    $s->execute(); return $s;
}
function producto($conn,$id,$bloquear=false) {
    $s=consulta($conn,'SELECT * FROM productos WHERE id=?'.($bloquear?' FOR UPDATE':''),'i',array($id));
    $p=$s->get_result()->fetch_assoc(); $s->close();
    if (!$p) { throw new InvalidArgumentException('El producto ya no existe.'); } return $p;
}
function inicio($titulo) {
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.h($titulo).'</title><link rel="stylesheet" href="css/estilos.css"></head><body><main class="container"><nav><a href="index.php">Inicio</a> · <a href="inventario.php?zona=Todo">Inventario</a> · <a href="carrito.php">Carrito</a> · <a href="reportes.php">Reportes</a></nav><h1>'.h($titulo).'</h1>';
    if(isset($_SESSION['mensaje'])) { echo '<p role="status">'.h($_SESSION['mensaje']).'</p>'; unset($_SESSION['mensaje']); }
}
function fin() { echo '</main></body></html>'; }
function campo($nombre,$etiqueta,$valor,$tipo='text',$atributos='') {
    echo '<p><label for="'.h($nombre).'">'.h($etiqueta).'</label><input id="'.h($nombre).'" name="'.h($nombre).'" type="'.h($tipo).'" value="'.h($valor).'" '.$atributos.'></p>';
}
function post_inicio($accion,$id=null) {
    echo '<form action="php/acciones.php" method="post">'.csrf().'<input type="hidden" name="accion" value="'.h($accion).'">';
    if ($id!==null) { echo '<input type="hidden" name="id" value="'.h($id).'">'; }
}
function boton($texto) { echo '<button class="btn btn-edit">'.h($texto).'</button></form>'; }
