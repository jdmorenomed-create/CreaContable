<?php
class AuthController extends Controller {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = $this->model('Usuario');
    }

    // Iniciar Sesión
    public function login() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $usuario = $this->usuarioModel->obtenerPorEmail($email);

            if ($usuario && password_verify($password, $usuario['password'])) {
                $_SESSION['user_id']     = $usuario['id'];
                $_SESSION['user_nombre'] = $usuario['nombre'];
                $_SESSION['user_email']  = $usuario['email'];
                $_SESSION['user_rol']    = $this->resolverRolId($usuario);

                // Los administradores entran directamente a su panel de gestión.
                if ((int)$_SESSION['user_rol'] === 3) {
                    header('Location: ' . URLROOT . '/marketplace/admin');
                } else {
                    header('Location: ' . URLROOT . '/');
                }
                exit();
            } else {
                $data = [
                    'error' => 'El correo o la contraseña son incorrectos.',
                    'email' => $email
                ];
                $this->view('auth/login', $data);
            }
        } else {
            $this->view('auth/login');
        }
    }

    // Registro de Usuarios
    public function register() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'rol_id'   => (isset($_POST['rol_id']) && (int)$_POST['rol_id'] === 1) ? 1 : 2,
                'nombre'   => trim($_POST['nombre'] ?? ''),
                'apellido' => trim($_POST['apellido'] ?? ''),
                'email'    => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'telefono' => trim($_POST['telefono'] ?? ''),
                'error'    => ''
            ];

            // La validación también se hace en el servidor para que no dependa solo del navegador.
            $patronPassword = '/^(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9\s]).{8,}$/';

            if (!preg_match($patronPassword, $data['password'])) {
                $data['error'] = 'La contraseña debe tener al menos 8 caracteres e incluir una mayúscula, una minúscula, un número y un símbolo.';
                $this->view('auth/register', $data);
                return;
            }

            if ($data['telefono'] !== '' && !preg_match('/^[0-9]+$/', $data['telefono'])) {
                $data['error'] = 'El teléfono solo puede contener números.';
                $this->view('auth/register', $data);
                return;
            }

            if ($this->usuarioModel->existeEmail($data['email'])) {
                $data['error'] = 'El correo electrónico ya se encuentra registrado.';
                $this->view('auth/register', $data);
                return;
            }

            $nuevoUsuarioId = $this->usuarioModel->registrar($data);

            if ($nuevoUsuarioId) {
                $_SESSION['user_id']     = $nuevoUsuarioId;
                $_SESSION['user_nombre'] = $data['nombre'];
                $_SESSION['user_email']  = $data['email'];
                $_SESSION['user_rol']    = (int)$data['rol_id'];

                // Redirige estrictamente a la pantalla de Inicio
                header('Location: ' . URLROOT . '/');
                exit();
            } else {
                $data['error'] = 'Ocurrió un error al registrar el usuario.';
                $this->view('auth/register', $data);
            }
        } else {
            $this->view('auth/register');
        }
    }

    // Convierte siempre el rol de la BD a los IDs oficiales: 1 contador, 2 cliente, 3 admin.
    private function resolverRolId($usuario) {
        $rolNombre = strtolower(trim((string)($usuario['rol_nombre'] ?? $usuario['rol'] ?? '')));
        if ($rolNombre === 'contador') return 1;
        if ($rolNombre === 'admin' || $rolNombre === 'administrador') return 3;
        if (isset($usuario['rol_id']) && in_array((int)$usuario['rol_id'], [1, 2, 3], true)) {
            return (int)$usuario['rol_id'];
        }
        return 2;
    }

    // Cerrar Sesión
    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_nombre']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_rol']);

        session_destroy();

        header('Location: ' . URLROOT . '/auth/login');
        exit();
    }
}