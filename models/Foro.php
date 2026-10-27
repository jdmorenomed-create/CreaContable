<?php
class Foro {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function obtenerPublicaciones($busqueda = '', $categoria = '') {
        $sql = "SELECT p.id, p.usuario_id, p.titulo, p.categoria, p.contenido, p.fecha_creacion,
                       u.nombre AS autor, u.apellido AS apellido,
                       (SELECT COUNT(*) FROM foro_comentarios c WHERE c.publicacion_id = p.id) AS total_comentarios
                FROM foro_publicaciones p
                INNER JOIN usuarios u ON u.id = p.usuario_id";
        $parametros = [];
        $condiciones = [];
        if (trim($busqueda) !== '') {
            $condiciones[] = "(p.titulo LIKE :titulo OR p.categoria LIKE :categoria_busqueda OR p.contenido LIKE :contenido OR u.nombre LIKE :nombre OR u.apellido LIKE :apellido)";
            $termino = '%' . trim($busqueda) . '%';
            $parametros[':titulo'] = $termino;
            $parametros[':categoria_busqueda'] = $termino;
            $parametros[':contenido'] = $termino;
            $parametros[':nombre'] = $termino;
            $parametros[':apellido'] = $termino;
        }
        if (trim($categoria) !== '' && in_array($categoria, ['Cambios normativos', 'Impuestos y DIAN', 'Facturación', 'Contabilidad', 'General'], true)) {
            $condiciones[] = 'p.categoria = :categoria_filtro';
            $parametros[':categoria_filtro'] = trim($categoria);
        }
        if ($condiciones) { $sql .= ' WHERE ' . implode(' AND ', $condiciones); }
        $sql .= " ORDER BY p.fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll();
    }


    public function obtenerPublicacion($id) {
        $stmt = $this->db->prepare(
            "SELECT p.id, p.usuario_id, p.titulo, p.categoria, p.contenido, p.fecha_creacion,
                    u.nombre AS autor, u.apellido AS apellido,
                    (SELECT COUNT(*) FROM foro_comentarios c WHERE c.publicacion_id = p.id) AS total_comentarios
             FROM foro_publicaciones p INNER JOIN usuarios u ON u.id = p.usuario_id WHERE p.id = :id LIMIT 1"
        );
        $stmt->execute([':id' => (int)$id]);
        return $stmt->fetch();
    }

    public function obtenerComentarios($publicacionId) {
        $stmt = $this->db->prepare(
            "SELECT c.id, c.usuario_id, c.contenido, c.fecha_creacion, u.nombre AS autor, u.apellido AS apellido
             FROM foro_comentarios c
             INNER JOIN usuarios u ON u.id = c.usuario_id
             WHERE c.publicacion_id = :id
             ORDER BY c.fecha_creacion ASC"
        );
        $stmt->execute([':id' => (int)$publicacionId]);
        return $stmt->fetchAll();
    }

    public function crearPublicacion($usuarioId, $titulo, $categoria, $contenido) {
        $stmt = $this->db->prepare(
            "INSERT INTO foro_publicaciones (usuario_id, titulo, categoria, contenido)
             VALUES (:usuario_id, :titulo, :categoria, :contenido)"
        );
        return $stmt->execute([
            ':usuario_id' => (int)$usuarioId,
            ':titulo' => $titulo,
            ':categoria' => $categoria,
            ':contenido' => $contenido
        ]);
    }

    public function agregarComentario($publicacionId, $usuarioId, $contenido) {
        $stmt = $this->db->prepare(
            "INSERT INTO foro_comentarios (publicacion_id, usuario_id, contenido)
             SELECT :publicacion_id, :usuario_id, :contenido
             FROM foro_publicaciones WHERE id = :id_publicacion"
        );
        return $stmt->execute([
            ':publicacion_id' => (int)$publicacionId,
            ':usuario_id' => (int)$usuarioId,
            ':contenido' => $contenido,
            ':id_publicacion' => (int)$publicacionId
        ]);
    }

    public function existePublicacion($id) {
        $stmt = $this->db->prepare("SELECT id FROM foro_publicaciones WHERE id = :id");
        $stmt->execute([':id' => (int)$id]);
        return (bool)$stmt->fetchColumn();
    }
}
