-- Foro profesional de CreaContable
-- Ejecuta este archivo una sola vez en phpMyAdmin sobre la base de datos creacontable.

CREATE TABLE IF NOT EXISTS foro_publicaciones (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    titulo VARCHAR(160) NOT NULL,
    categoria ENUM('Cambios normativos','Impuestos y DIAN','Facturación','Contabilidad','General') NOT NULL DEFAULT 'General',
    contenido TEXT NOT NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_foro_publicaciones_fecha (fecha_creacion),
    KEY idx_foro_publicaciones_usuario (usuario_id),
    CONSTRAINT fk_foro_publicaciones_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS foro_comentarios (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    publicacion_id INT UNSIGNED NOT NULL,
    usuario_id INT NOT NULL,
    contenido TEXT NOT NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_foro_comentarios_publicacion_fecha (publicacion_id, fecha_creacion),
    KEY idx_foro_comentarios_usuario (usuario_id),
    CONSTRAINT fk_foro_comentarios_publicacion
        FOREIGN KEY (publicacion_id) REFERENCES foro_publicaciones(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_foro_comentarios_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Temas iniciales de ejemplo para que el foro no aparezca vacío.
-- Se asignan a una cuenta existente; no crea usuarios falsos.
INSERT INTO foro_publicaciones (usuario_id, titulo, categoria, contenido)
SELECT u.id, '¿Cómo están preparando la implementación de los últimos cambios normativos?', 'Cambios normativos',
       'Abrimos este tema para compartir fuentes oficiales, fechas de aplicación y dudas prácticas sobre los cambios normativos. Por favor, indiquen la norma y el artículo correspondiente para mantener la conversación verificable.'
FROM usuarios u
WHERE NOT EXISTS (SELECT 1 FROM foro_publicaciones)
ORDER BY u.id ASC
LIMIT 1;

INSERT INTO foro_publicaciones (usuario_id, titulo, categoria, contenido)
SELECT u.id, 'Dudas frecuentes sobre facturación electrónica', 'Facturación',
       '¿Qué errores de validación están encontrando con mayor frecuencia y qué pasos les han servido para solucionarlos? Evitemos publicar datos personales o información de clientes.'
FROM usuarios u
WHERE NOT EXISTS (SELECT 1 FROM foro_publicaciones WHERE titulo = 'Dudas frecuentes sobre facturación electrónica')
ORDER BY u.id ASC
LIMIT 1;

INSERT INTO foro_publicaciones (usuario_id, titulo, categoria, contenido)
SELECT u.id, 'Fuentes oficiales para consultar novedades tributarias', 'Impuestos y DIAN',
       'Compartamos enlaces a comunicados, resoluciones y conceptos oficiales de la DIAN que sean útiles para el trabajo contable. Incluyan la fecha y el enlace de la fuente cuando sea posible.'
FROM usuarios u
WHERE NOT EXISTS (SELECT 1 FROM foro_publicaciones WHERE titulo = 'Fuentes oficiales para consultar novedades tributarias')
ORDER BY u.id ASC
LIMIT 1;
