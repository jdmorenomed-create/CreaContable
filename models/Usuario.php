<?php
class Usuario {
    private $db;

    public function __construct() {
        // Se instancian correctamente los métodos o clase PDO de la base de datos
        $this->db = Database::connect();
    }

    // Registrar un nuevo usuario
    public function registrar($data) {
        $sql = "INSERT INTO usuarios (rol_id, rol, nombre, apellido, email, password, telefono) 
                VALUES (:rol_id, :rol, :nombre, :apellido, :email, :password, :telefono)";
        
        $stmt = $this->db->prepare($sql);

        // Encriptar la contraseña por seguridad
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

        $rolId = (int)($data['rol_id'] ?? 2);
        $rolNombre = ($rolId === 1) ? 'contador' : 'cliente';
        $stmt->bindParam(':rol_id', $rolId, PDO::PARAM_INT);
        $stmt->bindParam(':rol', $rolNombre, PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $data['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $data['apellido'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(':password', $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $data['telefono'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Buscar usuario por Email para el Login
    public function obtenerPorEmail($email) {
        // Se utiliza LEFT JOIN para evitar fallos en caso de inconsistencia con la tabla roles
        $sql = "SELECT u.*, COALESCE(NULLIF(u.rol, ''), r.nombre) AS rol_nombre 
                FROM usuarios u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                WHERE u.email = :email";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar si el correo ya existe
    public function existeEmail($email) {
        $sql = "SELECT id FROM usuarios WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Buscar usuario por ID (Útil para consultar datos del contador o cliente)
    public function obtenerPorId($id) {
        $sql = "SELECT u.*, COALESCE(NULLIF(u.rol, ''), r.nombre) AS rol_nombre 
                FROM usuarios u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                WHERE u.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}