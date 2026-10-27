-- CreaContable: conectar citas, consultas y mensajes con usuarios.
-- Ejecutar en phpMyAdmin > base de datos creacontable > pestaña SQL.
-- Antes de ejecutar, haz una exportación/copia de seguridad de creacontable.
--
-- Para no borrar ni reasignar citas, consultas o mensajes, este script crea
-- registros marcados como inactivos y sin acceso SOLO para IDs de usuario
-- referenciados por estas tablas que ya no existan. Así se conservan las
-- referencias históricas y se pueden crear las claves foráneas.
-- Estos registros NO representan usuarios reales; puedes depurarlos después
-- de identificar al propietario correcto de cada registro histórico.

USE `creacontable`;

-- Crear un usuario de referencia por cada ID faltante usado por citas,
-- consultas o mensajes. INSERT IGNORE evita duplicar IDs existentes.
INSERT IGNORE INTO `usuarios`
  (`id`, `rol_id`, `nombre`, `apellido`, `email`, `password`, `estado`, `telefono`, `rol`, `created_at`)
SELECT refs.user_id, NULL, 'Usuario de referencia (sin acceso)', NULL,
       CONCAT('referencia_', refs.user_id, '@invalid.local'),
       '!NO_LOGIN_REFERENCE_ONLY!', 'inactivo', NULL, 'cliente', CURRENT_TIMESTAMP
FROM (
  SELECT `usuario_id` AS user_id FROM `citas`
  UNION SELECT `usuario_id` FROM `consultas`
  UNION SELECT `remitente_id` FROM `mensajes`
  UNION SELECT `destinatario_id` FROM `mensajes`
) AS refs
LEFT JOIN `usuarios` u ON u.`id` = refs.user_id
WHERE u.`id` IS NULL;

-- Índices: se crean solo si no existen.
SET @ddl = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='citas' AND INDEX_NAME='idx_citas_usuario_id')=0,
 'ALTER TABLE `citas` ADD INDEX `idx_citas_usuario_id` (`usuario_id`)', 'SELECT ''Índice de citas ya existe'' AS resultado');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @ddl = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='consultas' AND INDEX_NAME='idx_consultas_usuario_id')=0,
 'ALTER TABLE `consultas` ADD INDEX `idx_consultas_usuario_id` (`usuario_id`)', 'SELECT ''Índice de consultas ya existe'' AS resultado');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @ddl = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='mensajes' AND INDEX_NAME='idx_mensajes_remitente_id')=0,
 'ALTER TABLE `mensajes` ADD INDEX `idx_mensajes_remitente_id` (`remitente_id`)', 'SELECT ''Índice de remitente ya existe'' AS resultado');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @ddl = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='mensajes' AND INDEX_NAME='idx_mensajes_destinatario_id')=0,
 'ALTER TABLE `mensajes` ADD INDEX `idx_mensajes_destinatario_id` (`destinatario_id`)', 'SELECT ''Índice de destinatario ya existe'' AS resultado');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- Claves foráneas: comprobar por columna para no duplicarlas si ya existen.
SET @has_fk = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE CONSTRAINT_SCHEMA=DATABASE() AND TABLE_NAME='citas' AND COLUMN_NAME='usuario_id' AND REFERENCED_TABLE_NAME='usuarios' AND REFERENCED_COLUMN_NAME='id');
SET @ddl = IF(@has_fk=0, 'ALTER TABLE `citas` ADD CONSTRAINT `fk_citas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT', 'SELECT ''Relación citas -> usuarios ya existe'' AS resultado');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @has_fk = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE CONSTRAINT_SCHEMA=DATABASE() AND TABLE_NAME='consultas' AND COLUMN_NAME='usuario_id' AND REFERENCED_TABLE_NAME='usuarios' AND REFERENCED_COLUMN_NAME='id');
SET @ddl = IF(@has_fk=0, 'ALTER TABLE `consultas` ADD CONSTRAINT `fk_consultas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT', 'SELECT ''Relación consultas -> usuarios ya existe'' AS resultado');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @has_fk = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE CONSTRAINT_SCHEMA=DATABASE() AND TABLE_NAME='mensajes' AND COLUMN_NAME='remitente_id' AND REFERENCED_TABLE_NAME='usuarios' AND REFERENCED_COLUMN_NAME='id');
SET @ddl = IF(@has_fk=0, 'ALTER TABLE `mensajes` ADD CONSTRAINT `fk_mensajes_remitente` FOREIGN KEY (`remitente_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT', 'SELECT ''Relación mensajes.remitente_id -> usuarios ya existe'' AS resultado');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @has_fk = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE CONSTRAINT_SCHEMA=DATABASE() AND TABLE_NAME='mensajes' AND COLUMN_NAME='destinatario_id' AND REFERENCED_TABLE_NAME='usuarios' AND REFERENCED_COLUMN_NAME='id');
SET @ddl = IF(@has_fk=0, 'ALTER TABLE `mensajes` ADD CONSTRAINT `fk_mensajes_destinatario` FOREIGN KEY (`destinatario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT', 'SELECT ''Relación mensajes.destinatario_id -> usuarios ya existe'' AS resultado');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- Comprobación final: debe listar las cuatro relaciones creadas.
SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE CONSTRAINT_SCHEMA=DATABASE()
  AND TABLE_NAME IN ('citas','consultas','mensajes')
  AND REFERENCED_TABLE_NAME='usuarios'
ORDER BY TABLE_NAME, COLUMN_NAME;
