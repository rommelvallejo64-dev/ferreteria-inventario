# Ferretería: inventario y ventas

Aplicación académica en PHP y MySQL para administrar productos por categoría, preparar un carrito de venta y consultar ingresos mensuales.

## Funciones presentes en el código

- Alta, consulta, edición y eliminación de productos.
- Búsqueda por nombre, marca o medida y navegación por categorías.
- Carrito en sesión, cambio de cantidades y precios.
- Descuento de existencias y registro agregado de ventas.
- Reporte de artículos e ingresos por mes.

## Instalación local de demostración

1. Usar un entorno local PHP con extensión mysqli/mysqlnd y MySQL con tablas InnoDB. El código usa `get_result()`. La sintaxis fue revisada con PHP 7 instalado; para uso mantenido se debe verificar también con una versión de PHP soportada en el equipo de destino.
2. Crear una base nueva y vacía llamada `ferreteria_demo`; crear un usuario limitado a esa base. No usar la base del negocio.
3. Importar `database/schema.sql` y, opcionalmente, `database/seed-demo.sql` una sola vez. Los tres productos del ejemplo son ficticios.
4. Copiar `php/config.example.php` como `php/config.local.php` y configurar allí las credenciales locales. Ese archivo está excluido de Git.
5. Servir la carpeta mediante un servidor PHP local. Para un entorno con PHP en PATH: `php -S 127.0.0.1:8080 -t .` desde esta carpeta, y abrir `http://127.0.0.1:8080`. Usarlo únicamente en la máquina local.

## Estado actual

Prototipo académico para demostración local. Se retiraron las credenciales originales y el nombre comercial de la pantalla principal. Se incluyen esquema y datos ficticios, y se normalizó `inventario.php` para sus enlaces en Linux. La interfaz conserva la hoja de estilos del proyecto y se reorganizó para los nuevos formularios.

La revisión añadió consultas parametrizadas, escape de HTML, token CSRF y POST para acciones, validación de importes en centavos y comprobación acumulada del carrito. El cobro usa una transacción con bloqueo de productos, validación de stock y reversión si falla. Solo se vacía el carrito tras confirmar la transacción.

Estas mejoras fueron preparadas con asistencia de Codex sobre el proyecto encontrado en la laptop. La autoría original debe describirse según las contribuciones reales.

## Comprobaciones

Los diez archivos PHP pasan la revisión de sintaxis. `php tests/reglas.php` ejecuta 26 comprobaciones de precios, cantidades, carrito acumulado, escape de texto y tokens CSRF sin conectarse a MySQL. El token fijo de ese archivo solo se usa en pruebas por consola; la aplicación usa `random_bytes`.

Se incluye un flujo de GitHub Actions para repetir sintaxis y pruebas. No se ha ejecutado todavía en GitHub. Las comprobaciones unitarias no prueban el comportamiento transaccional de MySQL ni la interfaz completa.

## Pendientes antes de presentar una demo pública

- Probar en MySQL de demostración el alta, búsqueda, edición, carrito, cobro y reportes.
- Verificar con dos sesiones que una venta sin stock se revierte completa y no registra ingresos parciales.
- Probar recuperación ante fallos de base de datos, importes límite y un segundo envío del formulario de cobro.
- Incorporar autenticación y roles si se accede fuera de un entorno local aislado.
- Registrar líneas de venta si se requiere trazabilidad por producto: actualmente solo se guarda un resumen de cada venta.
- Verificar los flujos con una base de demostración y añadir capturas sin datos de clientes o del negocio.

Esta preparación no ejecutó ventas ni modificó la base original. La aplicación no implementa autenticación: usar solo con datos ficticios y servidor ligado a 127.0.0.1. Publicar el código del ejercicio no equivale a desplegarlo como un sistema listo para producción.

## Créditos

La autoría y los recursos utilizados deben documentarse antes de añadir una licencia. La existencia de los archivos en la laptop no implica que todo el diseño corresponda a una sola persona.
