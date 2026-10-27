<?php
class ForoController extends Controller {
    private $foroModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        // Sincroniza la sesión con el registro real de usuarios. Esto evita
        // errores de clave foránea cuando la sesión conserva un ID antiguo.
        try {
            $db = Database::connect();
            $stmtUsuario = $db->prepare('SELECT id, rol_id, rol FROM usuarios WHERE id = :id LIMIT 1');
            $stmtUsuario->execute([':id' => (int)$_SESSION['user_id']]);
            $usuarioActual = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

            if (!$usuarioActual && !empty($_SESSION['user_email'])) {
                $stmtUsuario = $db->prepare('SELECT id, rol_id, rol FROM usuarios WHERE email = :email LIMIT 1');
                $stmtUsuario->execute([':email' => (string)$_SESSION['user_email']]);
                $usuarioActual = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
            }

            if (!$usuarioActual) {
                unset($_SESSION['user_id']);
                header('Location: ' . URLROOT . '/auth/login');
                exit;
            }

            $_SESSION['user_id'] = (int)$usuarioActual['id'];
            $rolNombreActual = strtolower(trim((string)($usuarioActual['rol'] ?? '')));
            $_SESSION['user_rol'] = $rolNombreActual === 'contador' ? 1 :
                (($rolNombreActual === 'admin' || $rolNombreActual === 'administrador') ? 3 :
                (int)($usuarioActual['rol_id'] ?? ($_SESSION['user_rol'] ?? 2)));
        } catch (PDOException $e) {
            error_log('Error verificando usuario del foro: ' . $e->getMessage());
            http_response_code(500);
            exit('No se pudo verificar tu cuenta. Revisa la conexión a la base de datos e inténtalo de nuevo.');
        }

        // El foro profesional está reservado a contadores y administradores.
        $rol = (int)($_SESSION['user_rol'] ?? 0);
        if (!in_array($rol, [1, 3], true)) {
            http_response_code(403);
            echo '<!doctype html><html lang="es"><meta charset="utf-8"><title>Acceso restringido</title><body style="font-family:Arial,sans-serif;padding:40px"><h1>Acceso restringido</h1><p>El foro profesional está disponible para contadores.</p><a href="' . htmlspecialchars(URLROOT, ENT_QUOTES, 'UTF-8') . '/">Volver al inicio</a></body></html>';
            exit;
        }

        if (empty($_SESSION['foro_csrf'])) {
            $_SESSION['foro_csrf'] = bin2hex(random_bytes(32));
        }
        $this->foroModel = $this->model('Foro');
    }

    public function index() {
        $busqueda = trim((string)($_GET['q'] ?? ''));
        if (function_exists('mb_substr')) { $busqueda = mb_substr($busqueda, 0, 120, 'UTF-8'); } else { $busqueda = substr($busqueda, 0, 120); }
        $categoriasValidas = ['Cambios normativos', 'Impuestos y DIAN', 'Facturación', 'Contabilidad', 'General'];
        $categoria = trim((string)($_GET['categoria'] ?? ''));
        if (!in_array($categoria, $categoriasValidas, true)) $categoria = '';
        $publicaciones = $this->foroModel->obtenerPublicaciones($busqueda, $categoria);
        $comentarios = [];
        foreach ($publicaciones as $publicacion) {
            $comentarios[(int)$publicacion['id']] = $this->foroModel->obtenerComentarios((int)$publicacion['id']);
        }

        $this->view('foro/index', [
            'publicaciones' => $publicaciones,
            'busqueda' => $busqueda,
            'categoriaSeleccionada' => $categoria,
            'comentarios' => $comentarios,
            'csrf' => $_SESSION['foro_csrf'],
            'mensaje' => $_SESSION['foro_mensaje'] ?? '',
            'error' => $_SESSION['foro_error'] ?? ''
        ]);
        unset($_SESSION['foro_mensaje'], $_SESSION['foro_error']);
    }

    public function detalle($publicacionId = null) {
        $id = (int)$publicacionId;
        $tema = $id > 0 ? $this->foroModel->obtenerPublicacion($id) : false;
        if (!$tema) {
            http_response_code(404);
            $_SESSION['foro_error'] = 'No encontramos ese tema del foro.';
            header('Location: ' . URLROOT . '/foro');
            exit;
        }
        $this->view('foro/detalle', [
            'tema' => $tema,
            'comentarios' => $this->foroModel->obtenerComentarios($id),
            'csrf' => $_SESSION['foro_csrf'],
            'mensaje' => $_SESSION['foro_mensaje'] ?? '',
            'error' => $_SESSION['foro_error'] ?? ''
        ]);
        unset($_SESSION['foro_mensaje'], $_SESSION['foro_error']);
    }

    public function publicar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/foro');
            exit;
        }
        $this->validarCsrf();

        $titulo = trim($_POST['titulo'] ?? '');
        $categoria = trim($_POST['categoria'] ?? 'General');
        $contenido = trim($_POST['contenido'] ?? '');
        $categorias = ['Cambios normativos', 'Impuestos y DIAN', 'Facturación', 'Contabilidad', 'General'];

        if ($titulo === '' || $contenido === '') {
            $_SESSION['foro_error'] = 'Escribe un título y el contenido de la publicación.';
        } elseif ((function_exists('mb_strlen') ? mb_strlen($titulo, 'UTF-8') : strlen($titulo)) > 160 || (function_exists('mb_strlen') ? mb_strlen($contenido, 'UTF-8') : strlen($contenido)) > 10000) {
            $_SESSION['foro_error'] = 'El título debe tener máximo 160 caracteres y el contenido máximo 10.000.';
        } elseif (!in_array($categoria, $categorias, true)) {
            $_SESSION['foro_error'] = 'Selecciona una categoría válida.';
        } else {
            try {
                $this->foroModel->crearPublicacion((int)$_SESSION['user_id'], $titulo, $categoria, $contenido);
                $_SESSION['foro_mensaje'] = 'Tu tema se publicó correctamente.';
            } catch (PDOException $e) {
                error_log('Error publicando en foro: ' . $e->getMessage());
                $_SESSION['foro_error'] = 'No se pudo publicar el tema. Verifica tu sesión y la estructura de la base de datos.';
            }
        }
        header('Location: ' . URLROOT . '/foro');
        exit;
    }

    public function comentar($publicacionId = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/foro');
            exit;
        }
        $this->validarCsrf();
        $id = (int)$publicacionId;
        $contenido = trim($_POST['contenido'] ?? '');

        if ($id < 1 || !$this->foroModel->existePublicacion($id)) {
            $_SESSION['foro_error'] = 'No encontramos ese tema del foro.';
        } elseif ($contenido === '' || (function_exists('mb_strlen') ? mb_strlen($contenido, 'UTF-8') : strlen($contenido)) > 5000) {
            $_SESSION['foro_error'] = 'La respuesta no puede estar vacía y debe tener máximo 5.000 caracteres.';
        } else {
            try {
                $this->foroModel->agregarComentario($id, (int)$_SESSION['user_id'], $contenido);
                $_SESSION['foro_mensaje'] = 'Tu respuesta se agregó al tema.';
            } catch (PDOException $e) {
                error_log('Error respondiendo en foro: ' . $e->getMessage());
                $_SESSION['foro_error'] = 'No se pudo guardar la respuesta. Actualiza la página e inténtalo de nuevo.';
            }
        }
        header('Location: ' . URLROOT . '/foro/detalle/' . max(0, $id));
        exit;
    }

    private function validarCsrf() {
        $token = $_POST['csrf_token'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['foro_csrf'] ?? '', $token)) {
            http_response_code(419);
            exit('La sesión del formulario venció. Regresa al foro y vuelve a intentarlo.');
        }
    }
}
