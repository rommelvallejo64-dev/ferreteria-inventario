<?php
require __DIR__.'/php/comun.php'; require __DIR__.'/php/conexion.php';
$s=consulta($conn,"SELECT DATE_FORMAT(fecha,'%Y-%m') AS mes,SUM(total_articulos) AS articulos,SUM(total_ingresos) AS ingresos FROM ventas GROUP BY mes ORDER BY mes DESC");
$rows=$s->get_result()->fetch_all(MYSQLI_ASSOC); $s->close(); inicio('Reporte mensual de ventas');
?>
<table><thead><tr><th>Mes</th><th>Artículos vendidos</th><th>Ingresos</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?=h($r['mes'])?></td><td><?=h($r['articulos'])?></td><td>$<?=h($r['ingresos'])?></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="3">Todavía no hay ventas registradas.</td></tr><?php endif; ?>
</tbody></table>
<?php fin(); ?>
