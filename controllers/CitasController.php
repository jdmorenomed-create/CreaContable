<?php

class CitasController extends Controller {
    private $citaModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit();
        }
        $this->citaModel = $this->model('Cita');
    }

    // CREAR / AGENDAR CITA (POST)
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'usuario_id' => $_SESSION['user_id'],
                'fecha'      => trim($_POST['fecha'] ?? ''),
                'hora'       => trim($_POST['hora'] ?? ''),
                'motivo'     => trim($_POST['motivo'] ?? '')
            ];

            if (!empty($data['fecha']) && !empty($data['hora']) && !empty($data['motivo'])) {
                if ($this->citaModel->crear($data)) {
                    header('Location: ' . URLROOT . '/dashboard#citas');
                    exit();
                }
            }
        }
        header('Location: ' . URLROOT . '/dashboard');
        exit();
    }

    // EDITAR CITA
    public function editar($id = null) {
        if (!$id) {
            header('Location: ' . URLROOT . '/dashboard');
            exit();
        }

        $cita = $this->citaModel->obtenerPorId($id);
        $esContador = in_array((int)($_SESSION['user_rol'] ?? 2), [1, 3], true);

        if (!$cita || ($cita['usuario_id'] != $_SESSION['user_id'] && !$esContador)) {
            header('Location: ' . URLROOT . '/dashboard');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id'         => $id,
                'usuario_id' => $cita['usuario_id'],
                'fecha'      => trim($_POST['fecha'] ?? ''),
                'hora'       => trim($_POST['hora'] ?? ''),
                'motivo'     => trim($_POST['motivo'] ?? '')
            ];

            if ($this->citaModel->actualizar($data)) {
                header('Location: ' . URLROOT . '/dashboard#citas');
                exit();
            }
        }

        $this->view('citas/editar', ['cita' => $cita]);
    }

    // ELIMINAR CITA (POST)
    public function eliminar($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $usuario_id = $_SESSION['user_id'];
            
            if (in_array((int)($_SESSION['user_rol'] ?? 2), [1, 3], true)) {
                $cita = $this->citaModel->obtenerPorId($id);
                if ($cita) {
                    $usuario_id = $cita['usuario_id'];
                }
            }

            $this->citaModel->eliminar($id, $usuario_id);
        }
        header('Location: ' . URLROOT . '/dashboard#citas');
        exit();
    }
}