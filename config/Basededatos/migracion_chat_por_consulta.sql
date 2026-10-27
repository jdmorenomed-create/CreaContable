-- Ejecutar una sola vez en phpMyAdmin > base creacontable > SQL.
-- Conserva mensajes antiguos (consulta_id NULL); los mensajes nuevos quedan separados por consulta.
ALTER TABLE mensajes ADD COLUMN consulta_id INT NULL AFTER destinatario_id;
CREATE INDEX idx_mensajes_consulta_id ON mensajes (consulta_id);
