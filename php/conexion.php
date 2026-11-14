<?php
// Configuración local excluida de Git; nunca incluir credenciales reales en el repositorio.
$configFile = __DIR__ . '/config.local.php';
if (!is_file($configFile)) {
    http_response_code(503);
    exit('Falta la configuración local. Consulta las instrucciones de instalación del README.');
}
$config = require $configFile;
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($config['host'], $config['user'], $config['password'], $config['database'], $config['port']);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(503);
    exit('No se pudo conectar con la base de datos de demostración. Revisa la configuración local.');
}
