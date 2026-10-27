-- CreaContable: métodos de pago por servicio y comprobantes manuales de contratación
-- Ejecutar una sola vez en la base de datos creacontable.
CREATE TABLE IF NOT EXISTS servicios_metodos_pago (
 id INT AUTO_INCREMENT PRIMARY KEY, servicio_id INT NOT NULL, metodo VARCHAR(80) NOT NULL, datos_cuenta VARCHAR(255) NOT NULL, titular VARCHAR(180) NULL, activo TINYINT(1) NOT NULL DEFAULT 1, creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_smp_servicio (servicio_id), CONSTRAINT fk_smp_servicio FOREIGN KEY (servicio_id) REFERENCES servicios_contables(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE PROCEDURE cc_add_payment_columns() BEGIN
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='metodo_pago_cliente') THEN ALTER TABLE contrataciones ADD COLUMN metodo_pago_cliente VARCHAR(80) NULL; END IF;
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='referencia_pago_cliente') THEN ALTER TABLE contrataciones ADD COLUMN referencia_pago_cliente VARCHAR(180) NULL; END IF;
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='fecha_pago_cliente') THEN ALTER TABLE contrataciones ADD COLUMN fecha_pago_cliente DATE NULL; END IF;
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='banco_origen') THEN ALTER TABLE contrataciones ADD COLUMN banco_origen VARCHAR(120) NULL; END IF;
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='comprobante_url') THEN ALTER TABLE contrataciones ADD COLUMN comprobante_url VARCHAR(255) NULL; END IF;
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='notas_pago') THEN ALTER TABLE contrataciones ADD COLUMN notas_pago TEXT NULL; END IF;
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='pago_enviado_en') THEN ALTER TABLE contrataciones ADD COLUMN pago_enviado_en DATETIME NULL; END IF;
END$$
CALL cc_add_payment_columns()$$
DROP PROCEDURE cc_add_payment_columns$$
DELIMITER ;
