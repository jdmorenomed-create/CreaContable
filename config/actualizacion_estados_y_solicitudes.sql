-- Ejecutar en phpMyAdmin sobre la base de datos creacontable.
-- La tabla de perfiles ya usa pendiente/aprobado/rechazado.
CREATE TABLE IF NOT EXISTS solicitudes_explicacion_rechazo (
 id INT AUTO_INCREMENT PRIMARY KEY,
 contador_id INT NOT NULL,
 asunto VARCHAR(180) NOT NULL,
 mensaje TEXT NOT NULL,
 respuesta_admin TEXT NULL,
 estado ENUM('pendiente','respondida') NOT NULL DEFAULT 'pendiente',
 creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 respondido_en TIMESTAMP NULL,
 INDEX idx_solicitud_estado (estado),
 CONSTRAINT fk_ser_contador FOREIGN KEY (contador_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
