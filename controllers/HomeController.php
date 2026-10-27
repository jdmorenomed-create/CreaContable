<?php
class HomeController extends Controller {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Actualizar el rol desde la BD en cada entrada a la página principal.
        // Así los cambios hechos en phpMyAdmin no quedan atrapados en la sesión anterior.
        if (!empty($_SESSION['user_id'])) {
            try {
                $usuarioModel = $this->model('Usuario');
                $usuario = $usuarioModel->obtenerPorId($_SESSION['user_id']);
                if ($usuario) {
                    $rolNombre = strtolower(trim((string)($usuario['rol_nombre'] ?? $usuario['rol'] ?? '')));
                    if ($rolNombre === 'contador') {
                        $_SESSION['user_rol'] = 1;
                    } elseif (in_array($rolNombre, ['admin', 'administrador'], true)) {
                        $_SESSION['user_rol'] = 3;
                    } elseif ($rolNombre === 'cliente') {
                        $_SESSION['user_rol'] = 2;
                    } else {
                        $_SESSION['user_rol'] = (int)($usuario['rol_id'] ?? 2);
                    }
                    $_SESSION['user_nombre'] = $usuario['nombre'] ?? ($_SESSION['user_nombre'] ?? '');
                    $_SESSION['user_email'] = $usuario['email'] ?? ($_SESSION['user_email'] ?? '');
                }
            } catch (Throwable $e) {
                error_log('No se pudo actualizar el rol de sesión: ' . $e->getMessage());
            }
        }

        $data = ['titulo' => 'Bienvenido a la Plataforma de Asesoría Contable'];
        $this->view('home/index', $data);
    }

    public function quienesSomos() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $data = ['titulo' => 'Quiénes somos - CreaContable'];
        $this->view('home/quienes-somos', $data);
    }

}
