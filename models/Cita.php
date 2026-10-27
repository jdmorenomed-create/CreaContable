<?php
class Cita {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // LEER PARA CONTADOR / ADMIN (Obtener todas las citas unidas con los datos del usuario)
    public function obtenerTodasConUsuario() {
        try {
            $sql = "SELECT citas.*, 
                           usuarios.nombre, 
                           usuarios.apellido, 
                           usuarios.email, 
                           usuarios.telefono 
                    FROM citas 
                    LEFT JOIN usuarios ON citas.usuario_id = usuarios.id 
                    ORDER BY citas.fecha ASC, citas.hora ASC";
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

    // LEER (Obtener todas las citas de un usuario cliente)
    public function obtenerPorUsuario($usuario_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM citas WHERE usuario_id = :usuario_id ORDER BY fecha ASC, hora ASC");
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerPorUsuario: " . $e->getMessage());
            return [];
        }
    }

    // LEER (Obtener una cita por ID)
    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM citas WHERE id = :id");
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
            $stmt = $this->db->prepare("INSERT INTO citas (usuario_id, fecha, hora, motivo) VALUES (:usuario_id, :fecha, :hora, :motivo)");
            $stmt->bindParam(':usuario_id', $data['usuario_id']);
            $stmt->bindParam(':fecha', $data['fecha']);
            $stmt->bindParam(':hora', $data['hora']);
            $stmt->bindParam(':motivo', $data['motivo']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en crear cita: " . $e->getMessage());
            return false;
        }
    }

    // ACTUALIZAR
    public function actualizar($data) {
        try {
            $stmt = $this->db->prepare("UPDATE citas SET fecha = :fecha, hora = :hora, motivo = :motivo WHERE id = :id AND usuario_id = :usuario_id");
            $stmt->bindParam(':id', $data['id']);
            $stmt->bindParam(':usuario_id', $data['usuario_id']);
            $stmt->bindParam(':fecha', $data['fecha']);
            $stmt->bindParam(':hora', $data['hora']);
            $stmt->bindParam(':motivo', $data['motivo']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizar cita: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR / CANCELAR
    public function eliminar($id, $usuario_id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM citas WHERE id = :id AND usuario_id = :usuario_id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':usuario_id', $usuario_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminar cita: " . $e->getMessage());
            return false;
        }
    }
}