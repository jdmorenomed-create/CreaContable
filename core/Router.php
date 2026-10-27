<?php

class Router {
    protected $currentController = 'HomeController';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();

        // 1. Determinar el controlador
        if (isset($url[0]) && !empty($url[0])) {
            $controllerName = ucwords($url[0]) . 'Controller';
            
            if (file_exists('controllers/' . $controllerName . '.php')) {
                $this->currentController = $controllerName;
                unset($url[0]);
            } elseif (file_exists('../app/controllers/' . $controllerName . '.php')) {
                $this->currentController = $controllerName;
                unset($url[0]);
            }
        }

        // Cargar el archivo del controlador
        if (file_exists('controllers/' . $this->currentController . '.php')) {
            require_once 'controllers/' . $this->currentController . '.php';
        } elseif (file_exists('../app/controllers/' . $this->currentController . '.php')) {
            require_once '../app/controllers/' . $this->currentController . '.php';
        }

        // Instanciar el controlador
        $this->currentController = new $this->currentController;

        // 2. Determinar el método
        if (isset($url[1]) && !empty($url[1])) {
            $methodCandidate = $url[1];
            
            if (method_exists($this->currentController, $methodCandidate)) {
                $this->currentMethod = $methodCandidate;
                unset($url[1]);
            } elseif ($methodCandidate === 'registro' && method_exists($this->currentController, 'register')) {
                $this->currentMethod = 'register';
                unset($url[1]);
            }
        }

        // Validar que el método a llamar exista en el controlador actual
        if (!method_exists($this->currentController, $this->currentMethod)) {
            $this->currentMethod = 'index';
        }

        // 3. Obtener parámetros
        $this->params = $url ? array_values($url) : [];

        // 4. Ejecutar el método
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}