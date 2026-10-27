-- Ejecutar una sola vez en phpMyAdmin sobre la base de datos de CreaContable.
-- Si la columna estado ya existe, no ejecutes este ALTER de nuevo.
ALTER TABLE consultas ADD COLUMN estado ENUM('Pendiente','Respondido') NOT NULL DEFAULT 'Pendiente' AFTER detalle;
