<?php

class DashboardController extends Controller {

    private $usuarioModel;
    private $consultaModel;
    private $citaModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Proteger la ruta: si no hay sesión iniciada, redirige al login
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        $this->usuarioModel  = $this->model('Usuario');
        $this->consultaModel = $this->model('Consulta');
        $this->citaModel     = $this->model('Cita');
    }

    public function index() {
        // Recargar el usuario desde la base de datos para reflejar cambios inmediatos de rol
        $usuarioActual = $this->usuarioModel->obtenerPorId($_SESSION['user_id']);

        if ($usuarioActual) {
            $rolNombre = strtolower(trim((string)($usuarioActual['rol_nombre'] ?? $usuarioActual['rol'] ?? '')));
            if ($rolNombre === 'contador') $_SESSION['user_rol'] = 1;
            elseif ($rolNombre === 'admin' || $rolNombre === 'administrador') $_SESSION['user_rol'] = 3;
            elseif ($rolNombre === 'cliente') $_SESSION['user_rol'] = 2;
            else $_SESSION['user_rol'] = (int)($usuarioActual['rol_id'] ?? 2);
        }

        $rol = (int)($_SESSION['user_rol'] ?? 2);
        $db = Database::connect();
        $servicios = []; $contrataciones = []; $misServicios = [];
        try { $servicios = $db->query("SELECT s.*,u.nombre AS contador_nombre,u.apellido AS contador_apellido,p.matricula_profesional,p.experiencia_anos,p.especialidades,p.estado_verificacion FROM servicios_contables s JOIN usuarios u ON u.id=s.contador_id LEFT JOIN contador_perfiles p ON p.usuario_id=u.id WHERE s.estado='publicado' AND (p.estado_verificacion='aprobado') ORDER BY s.creado_en DESC")->fetchAll(PDO::FETCH_ASSOC); } catch (PDOException $e) { $servicios=[]; }
        try { if ($rol===2) { $st=$db->prepare("SELECT c.*,s.titulo,u.nombre AS contador_nombre FROM contrataciones c JOIN servicios_contables s ON s.id=c.servicio_id JOIN usuarios u ON u.id=c.contador_id WHERE c.cliente_id=? ORDER BY c.creado_en DESC"); $st->execute([$_SESSION['user_id']]); $contrataciones=$st->fetchAll(PDO::FETCH_ASSOC); } elseif ($rol===1) { $st=$db->prepare("SELECT c.*,s.titulo,u.nombre AS cliente_nombre FROM contrataciones c JOIN servicios_contables s ON s.id=c.servicio_id JOIN usuarios u ON u.id=c.cliente_id WHERE c.contador_id=? ORDER BY c.creado_en DESC"); $st->execute([$_SESSION['user_id']]); $contrataciones=$st->fetchAll(PDO::FETCH_ASSOC); } } catch (PDOException $e) { $contrataciones=[]; }
        if ($rol===1) { try { $st=$db->prepare("SELECT id,titulo,especialidad,descripcion,precio,estado,creado_en FROM servicios_contables WHERE contador_id=? ORDER BY creado_en DESC"); $st->execute([$_SESSION['user_id']]); $misServicios=$st->fetchAll(PDO::FETCH_ASSOC); } catch (PDOException $e) { $misServicios=[]; } }

        if (in_array($rol, [1, 3], true)) {
            // === VISTA CONTADOR / ADMIN ===
            $consultas = method_exists($this->consultaModel, 'obtenerTodasConUsuario') 
                ? $this->consultaModel->obtenerTodasConUsuario() 
                : (method_exists($this->consultaModel, 'obtenerTodas') ? $this->consultaModel->obtenerTodas() : []);

            $citas = method_exists($this->citaModel, 'obtenerTodasConUsuario') 
                ? $this->citaModel->obtenerTodasConUsuario() 
                : (method_exists($this->citaModel, 'obtenerTodas') ? $this->citaModel->obtenerTodas() : []);

            $this->view('dashboard/contador', [
                'consultas' => $consultas ?: [],
                'citas'     => $citas ?: [],
                'servicios' => $servicios, 'contrataciones' => $contrataciones, 'misServicios' => $misServicios
            ]);
        } else {
            // === VISTA CLIENTE ===
            $consultas = $this->consultaModel->obtenerPorUsuario($_SESSION['user_id']);
            $citas     = $this->citaModel->obtenerPorUsuario($_SESSION['user_id']);
            
            // Obtener dinámicamente el ID del contador desde la base de datos
            $contadorId = method_exists($this->consultaModel, 'obtenerContadorId') 
                ? $this->consultaModel->obtenerContadorId() 
                : 1;

            $this->view('dashboard/index', [
                'consultas'  => $consultas ?: [],
                'citas'      => $citas ?: [],
                'contadorId' => $contadorId,
                'servicios' => $servicios, 'contrataciones' => $contrataciones, 'misServicios' => $misServicios
            ]);
        }
    }
}