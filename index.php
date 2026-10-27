<?php
session_start();

require_once 'config/Database.php';
require_once 'core/Controller.php';
require_once 'core/Router.php';

// Inicializar la aplicación
$app = new Router();