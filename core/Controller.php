<?php
class Controller {
    // Cargar un modelo
    public function model($model) {
        require_once __DIR__ . "/../models/" . $model . ".php";
        return new $model();
    }

    // Cargar una vista
    public function view($view, $data = []) {
        if (file_exists(__DIR__ . "/../views/" . $view . ".php")) {
            require_once __DIR__ . "/../views/" . $view . ".php";
        } else {
            die("La vista '" . $view . "' no existe.");
        }
    }
}