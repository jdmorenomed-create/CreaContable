-- CreaContable: base de datos completa en un solo archivo para phpMyAdmin/XAMPP
-- Importar este archivo una sola vez en una instalación nueva.
-- Incluye estructura principal, métodos de pago y datos de comprobantes.
CREATE DATABASE IF NOT EXISTS `creacontable` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `creacontable`;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-10-2026 a las 00:08:29
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `creacontable`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada') DEFAULT 'Pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultas`
--

CREATE TABLE `consultas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `detalle` text NOT NULL,
  `estado` enum('Pendiente','Respondido') NOT NULL DEFAULT 'Pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `foro_comentarios`
--

CREATE TABLE `foro_comentarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `publicacion_id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `contenido` text NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `foro_publicaciones`
--

CREATE TABLE `foro_publicaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(160) NOT NULL,
  `categoria` enum('Cambios normativos','Impuestos y DIAN','Facturación','Contabilidad','General') NOT NULL DEFAULT 'General',
  `contenido` text NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `remitente_id` int(11) NOT NULL,
  `destinatario_id` int(11) NOT NULL,
  `consulta_id` int(11) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) DEFAULT 0,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'contador'),
(2, 'cliente'),
(3, 'admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) DEFAULT 2,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `telefono` varchar(20) DEFAULT NULL,
  `rol` enum('cliente','contador','admin') DEFAULT 'cliente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `rol_id`, `nombre`, `apellido`, `email`, `password`, `estado`, `telefono`, `rol`, `created_at`) VALUES
(22, 1, 'dacleram2', 'Jonatan Moreno', 'jdmorenomed@gmail.com', '$2y$10$HswfAbt0RlAbLvEmODeI6O3//Yeqqwl7f9QJsEopchx9SuuGimq3S', 'activo', '2422342', 'contador', '2026-10-23 22:00:56'),
(23, 2, 'juan', '2345', 'juan@gmail.com', '$2y$10$E86MK1TfgcEV2yeX9HQ88.3PzT4ZLp7rL0Lh03G9EYDOSv9i9qSEy', 'activo', '2422342', 'cliente', '2026-10-23 22:04:21');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_citas_usuario_id` (`usuario_id`);

--
-- Indices de la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_consultas_usuario_id` (`usuario_id`);

--
-- Indices de la tabla `foro_comentarios`
--
ALTER TABLE `foro_comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_foro_comentarios_publicacion_fecha` (`publicacion_id`,`fecha_creacion`),
  ADD KEY `idx_foro_comentarios_usuario` (`usuario_id`);

--
-- Indices de la tabla `foro_publicaciones`
--
ALTER TABLE `foro_publicaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_foro_publicaciones_fecha` (`fecha_creacion`),
  ADD KEY `idx_foro_publicaciones_usuario` (`usuario_id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mensajes_remitente_id` (`remitente_id`),
  ADD KEY `idx_mensajes_destinatario_id` (`destinatario_id`),
  ADD KEY `idx_mensajes_consulta_id` (`consulta_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_usuarios_rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `consultas`
--
ALTER TABLE `consultas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `foro_comentarios`
--
ALTER TABLE `foro_comentarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `foro_publicaciones`
--
ALTER TABLE `foro_publicaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `fk_citas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD CONSTRAINT `fk_consultas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `foro_comentarios`
--
ALTER TABLE `foro_comentarios`
  ADD CONSTRAINT `fk_foro_comentarios_publicacion` FOREIGN KEY (`publicacion_id`) REFERENCES `foro_publicaciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_foro_comentarios_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `foro_publicaciones`
--
ALTER TABLE `foro_publicaciones`
  ADD CONSTRAINT `fk_foro_publicaciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `fk_mensajes_destinatario` FOREIGN KEY (`destinatario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mensajes_remitente` FOREIGN KEY (`remitente_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- ============================================================
-- MARKETPLACE PROFESIONAL, CONTRATACIONES Y TOKENS DE PAGO
-- ============================================================
-- CreaContable: módulos de marketplace, perfiles profesionales y pagos.
-- Ejecutar una sola vez en phpMyAdmin sobre la BD creacontable.
CREATE TABLE IF NOT EXISTS contador_perfiles (
 id INT AUTO_INCREMENT PRIMARY KEY,
 usuario_id INT NOT NULL UNIQUE,
 matricula_profesional VARCHAR(120) NULL,
 experiencia_anos SMALLINT UNSIGNED NULL,
 especialidades TEXT NULL,
 universidad VARCHAR(180) NULL,
 documento_verificacion VARCHAR(255) NULL,
 biografia TEXT NULL,
 estado_verificacion ENUM('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
 observaciones_admin TEXT NULL,
 actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_perfil_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS servicios_contables (
 id INT AUTO_INCREMENT PRIMARY KEY,
 contador_id INT NOT NULL,
 titulo VARCHAR(160) NOT NULL,
 descripcion TEXT NOT NULL,
 especialidad VARCHAR(120) NOT NULL,
 precio DECIMAL(12,2) NOT NULL,
 moneda CHAR(3) NOT NULL DEFAULT 'COP',
 estado ENUM('borrador','pendiente_revision','publicado','rechazado') NOT NULL DEFAULT 'pendiente_revision',
 creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_servicio_estado (estado),
 CONSTRAINT fk_servicio_contador FOREIGN KEY (contador_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contrataciones (
 id INT AUTO_INCREMENT PRIMARY KEY,
 servicio_id INT NOT NULL,
 cliente_id INT NOT NULL,
 contador_id INT NOT NULL,
 monto DECIMAL(12,2) NOT NULL,
 moneda CHAR(3) NOT NULL DEFAULT 'COP',
 estado_pago ENUM('pendiente','procesando','pagado','fallido','reembolsado') NOT NULL DEFAULT 'pendiente',
 proveedor_pago VARCHAR(80) NULL,
 referencia_pago VARCHAR(180) NULL,
 creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_contratacion_estado (estado_pago),
 CONSTRAINT fk_contratacion_servicio FOREIGN KEY (servicio_id) REFERENCES servicios_contables(id),
 CONSTRAINT fk_contratacion_cliente FOREIGN KEY (cliente_id) REFERENCES usuarios(id),
 CONSTRAINT fk_contratacion_contador FOREIGN KEY (contador_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- No almacenar números de tarjeta, CVV ni claves. Los métodos guardados deben ser tokens
-- emitidos por un proveedor de pagos compatible con PCI DSS.
-- Chat privado por contratación y notificaciones para cliente/contador.
-- El controlador también crea estas tablas automáticamente si la base ya existe.
CREATE TABLE IF NOT EXISTS contratacion_mensajes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  contratacion_id INT NOT NULL,
  remitente_id INT NOT NULL,
  mensaje TEXT NOT NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_chat_contratacion (contratacion_id),
  CONSTRAINT fk_cm_contratacion FOREIGN KEY (contratacion_id) REFERENCES contrataciones(id) ON DELETE CASCADE,
  CONSTRAINT fk_cm_remitente FOREIGN KEY (remitente_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contratacion_notificaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  contratacion_id INT NOT NULL,
  titulo VARCHAR(180) NOT NULL,
  detalle VARCHAR(500) NOT NULL,
  leida TINYINT(1) NOT NULL DEFAULT 0,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notif_usuario (usuario_id, leida),
  CONSTRAINT fk_cn_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_cn_contratacion FOREIGN KEY (contratacion_id) REFERENCES contrataciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Solicitudes de explicación de contadores rechazados (módulo administrativo).
CREATE TABLE IF NOT EXISTS solicitudes_explicacion_rechazo (
 id INT AUTO_INCREMENT PRIMARY KEY,
 contador_id INT NOT NULL,
 asunto VARCHAR(180) NOT NULL,
 mensaje TEXT NOT NULL,
 respuesta_admin TEXT NULL,
 estado ENUM('pendiente','respondida') NOT NULL DEFAULT 'pendiente',
 creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 respondido_en TIMESTAMP NULL,
 INDEX idx_solicitud_estado (estado),
 CONSTRAINT fk_ser_contador FOREIGN KEY (contador_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- Métodos de pago por servicio y campos para registrar comprobantes
-- ============================================================
-- CreaContable: métodos de pago por servicio y comprobantes manuales de contratación
-- Ejecutar una sola vez en la base de datos creacontable.
CREATE TABLE IF NOT EXISTS servicios_metodos_pago (
 id INT AUTO_INCREMENT PRIMARY KEY, servicio_id INT NOT NULL, metodo VARCHAR(80) NOT NULL, datos_cuenta VARCHAR(255) NOT NULL, titular VARCHAR(180) NULL, activo TINYINT(1) NOT NULL DEFAULT 1, creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_smp_servicio (servicio_id), CONSTRAINT fk_smp_servicio FOREIGN KEY (servicio_id) REFERENCES servicios_contables(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE PROCEDURE cc_add_payment_columns() BEGIN
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='metodo_pago_cliente') THEN ALTER TABLE contrataciones ADD COLUMN metodo_pago_cliente VARCHAR(80) NULL; END IF;
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='referencia_pago_cliente') THEN ALTER TABLE contrataciones ADD COLUMN referencia_pago_cliente VARCHAR(180) NULL; END IF;
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='fecha_pago_cliente') THEN ALTER TABLE contrataciones ADD COLUMN fecha_pago_cliente DATE NULL; END IF;
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='banco_origen') THEN ALTER TABLE contrataciones ADD COLUMN banco_origen VARCHAR(120) NULL; END IF;
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='comprobante_url') THEN ALTER TABLE contrataciones ADD COLUMN comprobante_url VARCHAR(255) NULL; END IF;
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='notas_pago') THEN ALTER TABLE contrataciones ADD COLUMN notas_pago TEXT NULL; END IF;
 IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contrataciones' AND COLUMN_NAME='pago_enviado_en') THEN ALTER TABLE contrataciones ADD COLUMN pago_enviado_en DATETIME NULL; END IF;
END$$
CALL cc_add_payment_columns()$$
DROP PROCEDURE cc_add_payment_columns$$
DELIMITER ;


-- Notificaciones para el contador cuando administración aprueba o rechaza un servicio.
CREATE TABLE IF NOT EXISTS `servicio_notificaciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `servicio_id` INT NOT NULL,
  `titulo` VARCHAR(180) NOT NULL,
  `detalle` VARCHAR(500) NOT NULL,
  `leida` TINYINT(1) NOT NULL DEFAULT 0,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_sn_usuario` (`usuario_id`, `leida`),
  CONSTRAINT `fk_sn_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sn_servicio` FOREIGN KEY (`servicio_id`) REFERENCES `servicios_contables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
