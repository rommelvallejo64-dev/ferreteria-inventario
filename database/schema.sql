-- Importar exclusivamente en una base de demostración nueva y vacía.
-- Esquema derivado de querys.txt del proyecto original; no contiene registros reales.
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    marca VARCHAR(100) NOT NULL,
    medida VARCHAR(50) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_articulos INT NOT NULL,
    total_ingresos DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
