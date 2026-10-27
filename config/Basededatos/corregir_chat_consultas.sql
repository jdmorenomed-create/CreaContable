-- Ejecutar una sola vez en phpMyAdmin sobre la base de datos creacontable.
-- Si consulta_id ya existe, no volver a ejecutar la línea ALTER TABLE.
ALTER TABLE mensajes ADD COLUMN consulta_id INT NULL AFTER destinatario_id;
CREATE INDEX idx_mensajes_consulta_id ON mensajes (consulta_id);
