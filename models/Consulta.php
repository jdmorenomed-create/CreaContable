<?php
class Consulta {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
        // Compatibilidad con bases de datos creadas antes del chat por consulta.
        // La columna se crea solo si falta; los mensajes antiguos quedan sin consulta asociada.
        try {
            $check = $this->db->query("SHOW COLUMNS FROM mensajes LIKE 'consulta_id'");
            if (!$check || !$check->fetch(PDO::FETCH_ASSOC)) {
                $this->db->exec("ALTER TABLE mensajes ADD COLUMN consulta_id INT NULL AFTER destinatario_id");
            }
            $idx = $this->db->query("SHOW INDEX FROM mensajes WHERE Key_name = 'idx_mensajes_consulta_id'");
            if ($idx && !$idx->fetch(PDO::FETCH_ASSOC)) {
                $this->db->exec("CREATE INDEX idx_mensajes_consulta_id ON mensajes (consulta_id)");
            }
        } catch (PDOException $e) {
            // El error concreto se registra; guardarMensajeDB devolverá un error visible si falta la columna.
            error_log('No se pudo preparar el esquema del chat: ' . $e->getMessage());
        }
    }

    // LEER PARA CONTADOR / ADMIN (Obtener todas las consultas unidas con los datos del usuario)
    public function obtenerTodasConUsuario() {
        try {
            $sql = "SELECT consultas.*, 
                           usuarios.nombre, 
                           usuarios.apellido, 
                           usuarios.email, 
                           usuarios.telefono 
                    FROM consultas 
                    LEFT JOIN usuarios ON consultas.usuario_id = usuarios.id 
                    ORDER BY consultas.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerTodasConUsuario: " . $e->getMessage());
            return [];
        }
    }

    // Método alternativo de respaldo
    public function obtenerTodas() {
        return $this->obtenerTodasConUsuario();
    }

    // LEER (Obtener todas las consultas de un usuario cliente)
    public function obtenerPorUsuario($usuario_id) {
        try {
            // El cliente ve "Respondido" cuando su último movimiento fue una respuesta suya;
            // para el contador se conserva consultas.estado (que pasa a Pendiente).
            $stmt = $this->db->prepare("SELECT c.*,
                CASE WHEN (SELECT m.remitente_id FROM mensajes m WHERE m.consulta_id = c.id ORDER BY m.fecha DESC, m.id DESC LIMIT 1) = c.usuario_id
                     THEN 'Respondido' ELSE c.estado END AS estado_cliente
                FROM consultas c WHERE c.usuario_id = :usuario_id ORDER BY c.id DESC");
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerPorUsuario: " . $e->getMessage());
            return [];
        }
    }

    // LEER (Obtener una sola consulta por ID)
    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM consultas WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    // CREAR
    public function crear($data) {
        try {
            $stmt = $this->db->prepare("INSERT INTO consultas (usuario_id, titulo, detalle) VALUES (:usuario_id, :titulo, :detalle)");
            $stmt->bindParam(':usuario_id', $data['usuario_id']);
            $stmt->bindParam(':titulo', $data['titulo']);
            $stmt->bindParam(':detalle', $data['detalle']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en crear consulta: " . $e->getMessage());
            return false;
        }
    }

    // ACTUALIZAR
    public function actualizar($data) {
        try {
            $stmt = $this->db->prepare("UPDATE consultas SET titulo = :titulo, detalle = :detalle WHERE id = :id AND usuario_id = :usuario_id");
            $stmt->bindParam(':id', $data['id']);
            $stmt->bindParam(':usuario_id', $data['usuario_id']);
            $stmt->bindParam(':titulo', $data['titulo']);
            $stmt->bindParam(':detalle', $data['detalle']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizar consulta: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR
    public function eliminar($id, $usuario_id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM consultas WHERE id = :id AND usuario_id = :usuario_id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':usuario_id', $usuario_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminar consulta: " . $e->getMessage());
            return false;
        }
    }

    // Determina el estado usando el rol real guardado en la base de datos, no solo la sesión.
    // rol_id = 1: contador; rol_id = 3: administrador; rol_id = 2: cliente.
    public function actualizarEstadoSegunRemitente($remitente_id, $destinatario_id, $consulta_id = null) {
        try {
            if (!$consulta_id || (int)$consulta_id < 1) return false;
            $stmtRol = $this->db->prepare("SELECT rol_id FROM usuarios WHERE id = :id LIMIT 1");
            $stmtRol->execute([':id' => (int)$remitente_id]);
            $rol = $stmtRol->fetchColumn();
            if ($rol === false) return false;

            // Solo cambia el estado de la consulta cuyo chat recibió el mensaje.
            if (in_array((int)$rol, [1, 3], true)) {
                $stmt = $this->db->prepare("UPDATE consultas SET estado = 'Respondido' WHERE id = :cid AND usuario_id = :cliente");
                return $stmt->execute([':cid' => (int)$consulta_id, ':cliente' => (int)$destinatario_id]);
            }
            $stmt = $this->db->prepare("UPDATE consultas SET estado = 'Pendiente' WHERE id = :cid AND usuario_id = :cliente");
            return $stmt->execute([':cid' => (int)$consulta_id, ':cliente' => (int)$remitente_id]);
        } catch (PDOException $e) {
            error_log('Error al actualizar estado de consulta: ' . $e->getMessage());
            return false;
        }
    }

    // Marcar como respondidas las consultas del cliente cuando el contador contesta por chat.
    public function marcarConsultasRespondidas($usuario_id) {
        try {
            $stmt = $this->db->prepare("UPDATE consultas SET estado = 'Respondido' WHERE usuario_id = :usuario_id AND estado <> 'Respondido'");
            return $stmt->execute([':usuario_id' => $usuario_id]);
        } catch (PDOException $e) {
            error_log("Error al actualizar estado de consulta: " . $e->getMessage());
            return false;
        }
    }

    // Un nuevo mensaje del cliente vuelve a dejar sus consultas pendientes.
    public function marcarConsultasPendientes($usuario_id) {
        try {
            $stmt = $this->db->prepare("UPDATE consultas SET estado = 'Pendiente' WHERE usuario_id = :usuario_id AND estado <> 'Pendiente'");
            return $stmt->execute([':usuario_id' => $usuario_id]);
        } catch (PDOException $e) {
            error_log("Error al marcar consultas pendientes: " . $e->getMessage());
            return false;
        }
    }

    // Obtener el estado más reciente de las consultas para actualizar la tabla sin recargar.
    public function obtenerEstadosConsultas() {
        try {
            $stmt = $this->db->query("SELECT c.id, CASE WHEN (SELECT m.remitente_id FROM mensajes m WHERE m.consulta_id=c.id ORDER BY m.fecha DESC, m.id DESC LIMIT 1) = c.usuario_id THEN 'Pendiente' ELSE c.estado END AS estado FROM consultas c ORDER BY c.id DESC");
            $estados = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
                $estados[(string)$fila['id']] = $fila['estado'];
            }
            return $estados;
        } catch (PDOException $e) {
            error_log("Error al obtener estados de consultas: " . $e->getMessage());
            return [];
        }
    }

    // ==========================================
    // MÉTODOS PARA EL CHAT Y MENSAJERÍA
    // ==========================================

    public function obtenerMensajesPorConsulta($usuarioId, $consultaId) {
        try {
            $q = $this->db->prepare("SELECT usuario_id FROM consultas WHERE id = :id");
            $q->execute([':id' => (int)$consultaId]);
            $clienteId = $q->fetchColumn();
            if (!$clienteId) return [];
            $rol = $this->db->prepare("SELECT rol_id FROM usuarios WHERE id = :id");
            $rol->execute([':id' => (int)$usuarioId]);
            $rolId = (int)$rol->fetchColumn();
            if ((int)$clienteId !== (int)$usuarioId && !in_array($rolId, [1,3], true)) return [];
            $stmt = $this->db->prepare("SELECT m.*, u.nombre, u.apellido FROM mensajes m LEFT JOIN usuarios u ON u.id=m.remitente_id WHERE m.consulta_id=:cid ORDER BY m.fecha ASC, m.id ASC");
            $stmt->execute([':cid' => (int)$consultaId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { error_log($e->getMessage()); return []; }
    }

    // Obtener el ID del primer contador disponible en el sistema (rol = 1)
    public function obtenerContadorId() {
        try {
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE rol_id = 1 LIMIT 1");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['id'] : 1;
        } catch (PDOException $e) {
            error_log("Error en obtenerContadorId: " . $e->getMessage());
            return 1;
        }
    }

    public function guardarMensajeDB($remitente_id, $destinatario_id, $mensaje, $consulta_id = null) {
        try {
            // Asegurar la columna en instalaciones que todavía tienen el esquema antiguo.
            $check = $this->db->query("SHOW COLUMNS FROM mensajes LIKE 'consulta_id'");
            if (!$check || !$check->fetch(PDO::FETCH_ASSOC)) {
                $this->db->exec("ALTER TABLE mensajes ADD COLUMN consulta_id INT NULL AFTER destinatario_id");
            }
            $stmt = $this->db->prepare("INSERT INTO mensajes (remitente_id, destinatario_id, consulta_id, mensaje, fecha, leido) VALUES (:remitente, :destinatario, :consulta_id, :mensaje, NOW(), 0)");
            $stmt->bindValue(':remitente', (int)$remitente_id, PDO::PARAM_INT);
            $stmt->bindValue(':destinatario', (int)$destinatario_id, PDO::PARAM_INT);
            $stmt->bindValue(':consulta_id', (int)$consulta_id, PDO::PARAM_INT);
            $stmt->bindValue(':mensaje', trim((string)$mensaje), PDO::PARAM_STR);
            return $stmt->execute();
        } catch (Throwable $e) {
            error_log("Error en guardarMensajeDB: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerMensajesDB($usuario_actual, $contacto_id) {
        try {
            // Marcar como leídos los mensajes dirigidos a este usuario
            $update = $this->db->prepare("UPDATE mensajes SET leido = 1 WHERE destinatario_id = :usuario AND remitente_id = :contacto");
            $update->execute([':usuario' => $usuario_actual, ':contacto' => $contacto_id]);

            // Obtener el historial de la conversación usando nombres de parámetros únicos y seguros
            $stmt = $this->db->prepare("SELECT * FROM mensajes 
                                        WHERE (remitente_id = :u1 AND destinatario_id = :c1) 
                                           OR (remitente_id = :c2 AND destinatario_id = :u2) 
                                        ORDER BY fecha ASC");
            
            $stmt->execute([
                ':u1' => $usuario_actual, 
                ':c1' => $contacto_id,
                ':c2' => $contacto_id, 
                ':u2' => $usuario_actual
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerMensajesDB: " . $e->getMessage());
            return [];
        }
    }

    public function contarMensajesNoLeidos($cliente_id) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM mensajes WHERE destinatario_id = :cliente_id AND leido = 0");
            $stmt->execute([':cliente_id' => $cliente_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['total'] : 0;
        } catch (PDOException $e) {
            error_log("Error en contarMensajesNoLeidos: " . $e->getMessage());
            return 0;
        }
    }
}