<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Token fijo exclusivamente para pruebas de validación; producción usa random_bytes.
session_start();
$_SESSION['csrf'] = str_repeat('a',64);
require dirname(__DIR__).'/php/comun.php';
$casos=0;
function verificar($condicion,$mensaje) { global $casos; $casos++; if(!$condicion) { throw new RuntimeException($mensaje); } }
function rechazar($fn,$mensaje) { try { $fn(); } catch(InvalidArgumentException $e) { verificar(true,$mensaje); return; } verificar(false,$mensaje); }
verificar(centavos('10.25')===1025,'Conversión exacta a centavos');
verificar(centavos('0.1')===10,'Un decimal');
verificar(centavos('0')===0,'Precio cero');
verificar(dinero(1025)==='10.25','Formato monetario');
verificar(dinero(0)==='0.00','Formato de cero');
foreach(array('-1','1.001','1x20','1e3','NaN','9999999.99',array('1')) as $precio) {
    rechazar(function() use($precio) { centavos($precio); },'Rechazar precio inválido');
}
foreach(array('0','-1','1.5','1000001',array('2')) as $cantidad) {
    rechazar(function() use($cantidad) { entero($cantidad); },'Rechazar cantidad inválida');
}
verificar(entero('0',0)===0,'Inventario permite stock cero');
verificar(h('<script>"')==='&lt;script&gt;&quot;','Escape de HTML');
rechazar(function() { categoria('inexistente'); },'Categoría inválida');
rechazar(function() { comprobar_csrf('incorrecto'); },'Rechazo de token CSRF');
comprobar_csrf($_SESSION['csrf']); verificar(true,'Token correcto');
$p=array('id'=>1,'nombre'=>'Producto Demo','marca'=>'Demo','medida'=>'1 unidad','cantidad'=>5,'precio'=>'2.50');
$carrito=agregar(array(),$p,'3');
verificar($carrito[1]['cantidad']===3 && $carrito[1]['precio_centavos']===250,'Primer agregado');
$carrito=agregar($carrito,$p,'2');
verificar($carrito[1]['cantidad']===5,'Cantidad acumulada');
rechazar(function() use($carrito,$p) { agregar($carrito,$p,'1'); },'No exceder stock mediante agregados repetidos');
verificar($carrito[1]['cantidad']===5,'El rechazo no modifica el carrito');
echo $casos." comprobaciones correctas.\n";
