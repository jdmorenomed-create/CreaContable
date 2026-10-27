-- ================================================================
-- CreaContable: corregir relaciones entre tablas
-- Base de datos: creacontable (MariaDB/MySQL)
--
-- Ejecuta este archivo desde phpMyAdmin > creacontable > SQL.
-- El script NO borra ni modifica registros existentes.
-- Si hay registros huérfanos (por ejemplo, una cita cuyo usuario ya no
-- existe), informa y omite esa relación para evitar perder datos o fallar.
-- Corrige esos registros y vuelve a ejecutar este archivo.
-- Es seguro volver a ejecutarlo: no duplica claves foráneas existentes.
-- ================================================================

USE `creacontable`;

DELIMITER $$
DROP PROCEDURE IF EXISTS `creacontable_corregir_relaciones`$$
CREATE PROCEDURE `creacontable_corregir_relaciones`()
BEGIN
  DECLARE n BIGINT DEFAULT 0;
  DECLARE existe BIGINT DEFAULT 0;

  -- usuarios.rol_id -> roles.id
  SELECT COUNT(*) INTO n
    FROM usuarios u LEFT JOIN roles r ON r.id = u.rol_id
    WHERE u.rol_id IS NOT NULL AND r.id IS NULL;
  SELECT COUNT(*) INTO existe FROM information_schema.REFERENTIAL_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_usuarios_rol';
  IF existe = 0 AND n = 0 THEN
    SELECT COUNT(*) INTO existe FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND INDEX_NAME = 'idx_usuarios_rol_id';
    IF existe = 0 THEN ALTER TABLE usuarios ADD INDEX idx_usuarios_rol_id (rol_id); END IF;
    ALTER TABLE usuarios ADD CONSTRAINT fk_usuarios_rol
      FOREIGN KEY (rol_id) REFERENCES roles(id)
      ON UPDATE CASCADE ON DELETE SET NULL;
    SELECT 'OK: usuarios.rol_id -> roles.id creada.' AS resultado;
  ELSEIF existe > 0 THEN
    SELECT 'Ya existe: usuarios.rol_id -> roles.id.' AS resultado;
  ELSE
    SELECT CONCAT('OMITIDA usuarios.rol_id -> roles.id: ', n, ' usuario(s) tienen un rol inexistente.') AS resultado;
  END IF;

  -- citas.usuario_id -> usuarios.id
  SELECT COUNT(*) INTO n
    FROM citas c LEFT JOIN usuarios u ON u.id = c.usuario_id
    WHERE u.id IS NULL;
  SELECT COUNT(*) INTO existe FROM information_schema.REFERENTIAL_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_citas_usuario';
  IF existe = 0 AND n = 0 THEN
    SELECT COUNT(*) INTO existe FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'citas' AND INDEX_NAME = 'idx_citas_usuario_id';
    IF existe = 0 THEN ALTER TABLE citas ADD INDEX idx_citas_usuario_id (usuario_id); END IF;
    ALTER TABLE citas ADD CONSTRAINT fk_citas_usuario
      FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
      ON UPDATE CASCADE ON DELETE CASCADE;
    SELECT 'OK: citas.usuario_id -> usuarios.id creada.' AS resultado;
  ELSEIF existe > 0 THEN
    SELECT 'Ya existe: citas.usuario_id -> usuarios.id.' AS resultado;
  ELSE
    SELECT CONCAT('OMITIDA citas.usuario_id -> usuarios.id: ', n, ' cita(s) apuntan a usuarios inexistentes. Revisa los usuario_id antes de corregirlos.') AS resultado;
  END IF;

  -- consultas.usuario_id -> usuarios.id
  SELECT COUNT(*) INTO n
    FROM consultas c LEFT JOIN usuarios u ON u.id = c.usuario_id
    WHERE u.id IS NULL;
  SELECT COUNT(*) INTO existe FROM information_schema.REFERENTIAL_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_consultas_usuario';
  IF existe = 0 AND n = 0 THEN
    SELECT COUNT(*) INTO existe FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'consultas' AND INDEX_NAME = 'idx_consultas_usuario_id';
    IF existe = 0 THEN ALTER TABLE consultas ADD INDEX idx_consultas_usuario_id (usuario_id); END IF;
    ALTER TABLE consultas ADD CONSTRAINT fk_consultas_usuario
      FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
      ON UPDATE CASCADE ON DELETE CASCADE;
    SELECT 'OK: consultas.usuario_id -> usuarios.id creada.' AS resultado;
  ELSEIF existe > 0 THEN
    SELECT 'Ya existe: consultas.usuario_id -> usuarios.id.' AS resultado;
  ELSE
    SELECT CONCAT('OMITIDA consultas.usuario_id -> usuarios.id: ', n, ' consulta(s) apuntan a usuarios inexistentes. Revisa los usuario_id antes de corregirlos.') AS resultado;
  END IF;

  -- mensajes.remitente_id -> usuarios.id
  SELECT COUNT(*) INTO n
    FROM mensajes m LEFT JOIN usuarios u ON u.id = m.remitente_id
    WHERE u.id IS NULL;
  SELECT COUNT(*) INTO existe FROM information_schema.REFERENTIAL_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_mensajes_remitente';
  IF existe = 0 AND n = 0 THEN
    SELECT COUNT(*) INTO existe FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mensajes' AND INDEX_NAME = 'idx_mensajes_remitente_id';
    IF existe = 0 THEN ALTER TABLE mensajes ADD INDEX idx_mensajes_remitente_id (remitente_id); END IF;
    ALTER TABLE mensajes ADD CONSTRAINT fk_mensajes_remitente
      FOREIGN KEY (remitente_id) REFERENCES usuarios(id)
      ON UPDATE CASCADE ON DELETE CASCADE;
    SELECT 'OK: mensajes.remitente_id -> usuarios.id creada.' AS resultado;
  ELSEIF existe > 0 THEN
    SELECT 'Ya existe: mensajes.remitente_id -> usuarios.id.' AS resultado;
  ELSE
    SELECT CONCAT('OMITIDA mensajes.remitente_id -> usuarios.id: ', n, ' mensaje(s) tienen remitente inexistente.') AS resultado;
  END IF;

  -- mensajes.destinatario_id -> usuarios.id
  SELECT COUNT(*) INTO n
    FROM mensajes m LEFT JOIN usuarios u ON u.id = m.destinatario_id
    WHERE u.id IS NULL;
  SELECT COUNT(*) INTO existe FROM information_schema.REFERENTIAL_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_mensajes_destinatario';
  IF existe = 0 AND n = 0 THEN
    SELECT COUNT(*) INTO existe FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mensajes' AND INDEX_NAME = 'idx_mensajes_destinatario_id';
    IF existe = 0 THEN ALTER TABLE mensajes ADD INDEX idx_mensajes_destinatario_id (destinatario_id); END IF;
    ALTER TABLE mensajes ADD CONSTRAINT fk_mensajes_destinatario
      FOREIGN KEY (destinatario_id) REFERENCES usuarios(id)
      ON UPDATE CASCADE ON DELETE CASCADE;
    SELECT 'OK: mensajes.destinatario_id -> usuarios.id creada.' AS resultado;
  ELSEIF existe > 0 THEN
    SELECT 'Ya existe: mensajes.destinatario_id -> usuarios.id.' AS resultado;
  ELSE
    SELECT CONCAT('OMITIDA mensajes.destinatario_id -> usuarios.id: ', n, ' mensaje(s) tienen destinatario inexistente.') AS resultado;
  END IF;

  -- mensajes.consulta_id -> consultas.id, si la columna ya fue instalada.
  SELECT COUNT(*) INTO existe FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mensajes' AND COLUMN_NAME = 'consulta_id';
  IF existe > 0 THEN
    SELECT COUNT(*) INTO n
      FROM mensajes m LEFT JOIN consultas c ON c.id = m.consulta_id
      WHERE m.consulta_id IS NOT NULL AND c.id IS NULL;
    SELECT COUNT(*) INTO existe FROM information_schema.REFERENTIAL_CONSTRAINTS
      WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_mensajes_consulta';
    IF existe = 0 AND n = 0 THEN
      SELECT COUNT(*) INTO existe FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mensajes' AND INDEX_NAME = 'idx_mensajes_consulta_fk';
      IF existe = 0 THEN ALTER TABLE mensajes ADD INDEX idx_mensajes_consulta_fk (consulta_id); END IF;
      ALTER TABLE mensajes ADD CONSTRAINT fk_mensajes_consulta
        FOREIGN KEY (consulta_id) REFERENCES consultas(id)
        ON UPDATE CASCADE ON DELETE SET NULL;
      SELECT 'OK: mensajes.consulta_id -> consultas.id creada.' AS resultado;
    ELSEIF existe > 0 THEN
      SELECT 'Ya existe: mensajes.consulta_id -> consultas.id.' AS resultado;
    ELSE
      SELECT CONCAT('OMITIDA mensajes.consulta_id -> consultas.id: ', n, ' mensaje(s) apuntan a consultas inexistentes.') AS resultado;
    END IF;
  ELSE
    SELECT 'AVISO: mensajes.consulta_id no existe; ejecuta primero la migración del chat por consulta si usas esa función.' AS resultado;
  END IF;
END$$
DELIMITER ;

CALL `creacontable_corregir_relaciones`();
DROP PROCEDURE IF EXISTS `creacontable_corregir_relaciones`;

-- Relaciones del foro ya definidas en creacontable.sql / foro_contadores.sql:
-- foro_publicaciones.usuario_id -> usuarios.id
-- foro_comentarios.publicacion_id -> foro_publicaciones.id
-- foro_comentarios.usuario_id -> usuarios.id
-- La tabla notas no aparece en el esquema SQL de este proyecto y no se le
-- inventa una relación; si existe en tu instalación, revisa su estructura.
