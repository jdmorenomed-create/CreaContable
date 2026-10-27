<?php
class ConsultasController extends Controller {
    private $consultaModel;

    public function __construct() {
        // Asegurar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validar si es una petición dirigida a los endpoints del chat o AJAX
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $esChatRequest = (
            strpos($uri, 'guardarMensaje') !== false || 
            strpos($uri, 'obtenerMensajes') !== false || 
            strpos($uri, 'verificarRespuestas') !== false ||
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        );

        if (!isset($_SESSION['user_id'])) {
            if ($esChatRequest) {
                if (ob_get_level()) { ob_clean(); }
                ini_set('display_errors', 0);
                error_reporting(0);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['status' => 'error', 'message' => 'No autorizado o sesión expirada']);
                exit();
            }
            header('Location: ' . URLROOT . '/auth/login');
            exit();
        }

        $this->consultaModel = $this->model('Consulta');
    }

    public function index() {
        header('Location: ' . URLROOT . '/dashboard');
        exit();
    }

    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'usuario_id' => $_SESSION['user_id'],
                'titulo'     => trim($_POST['titulo'] ?? ''),
                'detalle'    => trim($_POST['detalle'] ?? '')
            ];

            if (!empty($data['titulo']) && !empty($data['detalle'])) {
                $this->consultaModel->crear($data);
                header('Location: ' . URLROOT . '/dashboard#consultas');
                exit();
            }
        }
        header('Location: ' . URLROOT . '/dashboard');
        exit();
    }

    public function editar($id = null) {
        if (!$id) {
            header('Location: ' . URLROOT . '/dashboard');
            exit();
        }

        $consulta = $this->consultaModel->obtenerPorId($id);
        $esContador = in_array((int)($_SESSION['user_rol'] ?? 2), [1, 3], true);

        if (!$consulta || ($consulta['usuario_id'] != $_SESSION['user_id'] && !$esContador)) {
            header('Location: ' . URLROOT . '/dashboard');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id'         => $id,
                'usuario_id' => $consulta['usuario_id'],
                'titulo'     => trim($_POST['titulo'] ?? ''),
                'detalle'    => trim($_POST['detalle'] ?? '')
            ];

            if ($this->consultaModel->actualizar($data)) {
                header('Location: ' . URLROOT . '/dashboard#consultas');
                exit();
            }
        }

        $this->view('consultas/editar', ['consulta' => $consulta]);
    }

    public function eliminar($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $usuario_id = $_SESSION['user_id'];
            
            if (in_array((int)($_SESSION['user_rol'] ?? 2), [1, 3], true)) {
                $consulta = $this->consultaModel->obtenerPorId($id);
                if ($consulta) {
                    $usuario_id = $consulta['usuario_id'];
                }
            }

            $this->consultaModel->eliminar($id, $usuario_id);
        }
        header('Location: ' . URLROOT . '/dashboard#consultas');
        exit();
    }

    public function responder($id = null) {
        $id = (int)$id;
        $consulta = $id ? $this->consultaModel->obtenerPorId($id) : false;
        if (!$consulta) { header('Location: ' . URLROOT . '/dashboard#consultas'); exit(); }
        $uid = (int)($_SESSION['user_id'] ?? 0);
        $rol = (int)($_SESSION['user_rol'] ?? 2);
        if ((int)$consulta['usuario_id'] !== $uid && !in_array($rol, [1,3], true)) { http_response_code(403); exit('No autorizado'); }
        $consulta['usuario_nombre'] = '';
        try {
            $db = Database::connect();
            $q = $db->prepare("SELECT nombre, apellido FROM usuarios WHERE id = ?");
            $q->execute([(int)$consulta['usuario_id']]);
            $u = $q->fetch(PDO::FETCH_ASSOC);
            if ($u) $consulta['usuario_nombre'] = trim($u['nombre'].' '.($u['apellido'] ?? ''));
        } catch (Exception $e) {}
        $listaConsultas = in_array($rol, [1,3], true) ? $this->consultaModel->obtenerTodasConUsuario() : [];
        $this->view('consultas/responder', ['consulta'=>$consulta, 'consultas'=>$listaConsultas, 'consulta_id'=>$id, 'cliente_id'=>(int)$consulta['usuario_id'], 'contador_id'=>(int)$this->consultaModel->obtenerContadorId()]);
    }

    // ==========================================
    // MÉTODOS PARA EL CHAT Y NOTIFICACIONES
    // ==========================================

    public function guardarMensaje() {
        if (ob_get_level()) { @ob_clean(); }
        ini_set('display_errors', 0); error_reporting(0);
        header('Content-Type: application/json; charset=utf-8');
        $remitente_id = (int)($_SESSION['user_id'] ?? 0);
        $mensaje = trim((string)($_POST['mensaje'] ?? ''));
        $consulta_id = (int)($_POST['consulta_id'] ?? 0);
        if (!$remitente_id || !$consulta_id || $mensaje === '') {
            echo json_encode(['status'=>'error','message'=>'Faltan datos. Escribe un mensaje y vuelve a intentarlo.']); exit();
        }
        try {
            $consulta = $this->consultaModel->obtenerPorId($consulta_id);
            if (!$consulta) { echo json_encode(['status'=>'error','message'=>'La consulta ya no existe.']); exit(); }
            $db = Database::connect();
            $q = $db->prepare('SELECT rol_id FROM usuarios WHERE id = ?');
            $q->execute([$remitente_id]);
            $rol = (int)$q->fetchColumn();
            $cliente_id = (int)$consulta['usuario_id'];
            if ($rol === 2) {
                if ($cliente_id !== $remitente_id) { echo json_encode(['status'=>'error','message'=>'No tienes permiso para enviar mensajes en esta consulta.']); exit(); }
                // El mensaje queda en el hilo de la consulta y visible para todos los contadores.
                $destinatario_id = (int)$this->consultaModel->obtenerContadorId();
            } elseif (in_array($rol, [1,3], true)) {
                $destinatario_id = $cliente_id;
            } else {
                echo json_encode(['status'=>'error','message'=>'Tu rol no tiene permiso para enviar mensajes.']); exit();
            }
            $guardado = $this->consultaModel->guardarMensajeDB($remitente_id, $destinatario_id, $mensaje, $consulta_id);
            if (!$guardado) {
                echo json_encode(['status'=>'error','message'=>'No se pudo guardar el mensaje. Verifica que la base de datos tenga la tabla mensajes y la columna consulta_id.']); exit();
            }
            $estadoActualizado = $this->consultaModel->actualizarEstadoSegunRemitente($remitente_id, $destinatario_id, $consulta_id);
            echo json_encode(['status'=>'success','estado_actualizado'=>(bool)$estadoActualizado]);
        } catch (Throwable $e) {
            error_log('Error en guardarMensaje: '.$e->getMessage());
            echo json_encode(['status'=>'error','message'=>'Error interno al guardar el mensaje. Revisa la conexión con la base de datos.']);
        }
        exit();
    }

    public function obtenerMensajes() {
        if (ob_get_level()) { ob_clean(); }
        ini_set('display_errors', 0); error_reporting(0);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $uid = (int)($_SESSION['user_id'] ?? 0);
        $cid = (int)($_GET['consulta_id'] ?? 0);
        if (!$uid || !$cid) { echo json_encode([]); exit(); }
        echo json_encode($this->consultaModel->obtenerMensajesPorConsulta($uid, $cid) ?: []);
        exit();
    }

    public function obtenerEstadosConsultas() {
        if (ob_get_level()) { ob_clean(); }
        ini_set('display_errors', 0);
        error_reporting(0);
        header('Content-Type: application/json; charset=utf-8');

        if (!in_array((int)($_SESSION['user_rol'] ?? 2), [1, 3], true)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
            exit();
        }
        try {
            echo json_encode($this->consultaModel->obtenerEstadosConsultas());
        } catch (Exception $e) {
            echo json_encode([]);
        }
        exit();
    }

    public function verificarRespuestas() {
        if (ob_get_level()) { ob_clean(); }
        ini_set('display_errors', 0);
        error_reporting(0);

        header('Content-Type: application/json; charset=utf-8');

        $cliente_id = $_SESSION['user_id'] ?? null;

        if (!$cliente_id) {
            echo json_encode(['mensajes_no_leidos' => 0]);
            exit();
        }

        try {
            $total = $this->consultaModel->contarMensajesNoLeidos($cliente_id);
            echo json_encode(['mensajes_no_leidos' => $total]);
        } catch (Exception $e) {
            echo json_encode(['mensajes_no_leidos' => 0]);
        }
        exit();
    }
}