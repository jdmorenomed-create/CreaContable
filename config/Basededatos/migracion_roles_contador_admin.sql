-- Migración de roles para CreaContable (ejecutar una sola vez en la base creacontable)
-- IDs: 1 = contador, 2 = cliente, 3 = admin. Ejecutar una sola vez.
START TRANSACTION;
UPDATE roles SET nombre = 'contador' WHERE id = 1;
INSERT INTO roles (id, nombre) VALUES (2, 'cliente') ON DUPLICATE KEY UPDATE nombre = 'cliente';
INSERT INTO roles (id, nombre) VALUES (3, 'admin') ON DUPLICATE KEY UPDATE nombre = 'admin';
ALTER TABLE roles AUTO_INCREMENT = 4;
-- Sincroniza el campo legado rol con rol_id para los usuarios existentes.
ALTER TABLE usuarios MODIFY rol ENUM('cliente','contador','admin') DEFAULT 'cliente';
UPDATE usuarios SET rol = CASE rol_id WHEN 1 THEN 'contador' WHEN 3 THEN 'admin' ELSE 'cliente' END;
-- Mantiene sincronizados ambos campos de rol, tanto si se modifica rol como rol_id.
DROP TRIGGER IF EXISTS usuarios_rol_insert_sync;
DROP TRIGGER IF EXISTS usuarios_rol_update_sync;
DELIMITER $$
CREATE TRIGGER usuarios_rol_insert_sync BEFORE INSERT ON usuarios FOR EACH ROW
BEGIN
  SET NEW.rol = CASE NEW.rol_id WHEN 1 THEN 'contador' WHEN 3 THEN 'admin' ELSE 'cliente' END;
END$$
CREATE TRIGGER usuarios_rol_update_sync BEFORE UPDATE ON usuarios FOR EACH ROW
BEGIN
  IF NOT (NEW.rol <=> OLD.rol) THEN
    SET NEW.rol_id = CASE NEW.rol WHEN 'contador' THEN 1 WHEN 'admin' THEN 3 ELSE 2 END;
  ELSEIF NOT (NEW.rol_id <=> OLD.rol_id) THEN
    SET NEW.rol = CASE NEW.rol_id WHEN 1 THEN 'contador' WHEN 3 THEN 'admin' ELSE 'cliente' END;
  END IF;
END$$
DELIMITER ;
COMMIT;
