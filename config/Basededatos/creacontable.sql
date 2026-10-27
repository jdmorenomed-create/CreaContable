-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-10-2026 a las 21:08:20
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

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
-- Estructura de tabla para la tabla `contador_perfiles`
--

CREATE TABLE `contador_perfiles` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `matricula_profesional` varchar(120) DEFAULT NULL,
  `experiencia_anos` smallint(5) UNSIGNED DEFAULT NULL,
  `especialidades` text DEFAULT NULL,
  `universidad` varchar(180) DEFAULT NULL,
  `documento_verificacion` varchar(255) DEFAULT NULL,
  `biografia` text DEFAULT NULL,
  `estado_verificacion` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `observaciones_admin` text DEFAULT NULL,
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `contador_perfiles`
--

INSERT INTO `contador_perfiles` (`id`, `usuario_id`, `matricula_profesional`, `experiencia_anos`, `especialidades`, `universidad`, `documento_verificacion`, `biografia`, `estado_verificacion`, `observaciones_admin`, `actualizado_en`, `creado_en`) VALUES
(1, 22, '123', 12, 'asd', 'nacional', '123', 'asd', 'aprobado', NULL, '2026-10-27 19:41:44', '2026-10-27 19:41:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contrataciones`
--

CREATE TABLE `contrataciones` (
  `id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `contador_id` int(11) NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `moneda` char(3) NOT NULL DEFAULT 'COP',
  `estado_pago` enum('pendiente','procesando','pagado','fallido','reembolsado') NOT NULL DEFAULT 'pendiente',
  `proveedor_pago` varchar(80) DEFAULT NULL,
  `referencia_pago` varchar(180) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `metodo_pago_cliente` varchar(80) DEFAULT NULL,
  `referencia_pago_cliente` varchar(180) DEFAULT NULL,
  `fecha_pago_cliente` date DEFAULT NULL,
  `banco_origen` varchar(120) DEFAULT NULL,
  `comprobante_url` varchar(255) DEFAULT NULL,
  `notas_pago` text DEFAULT NULL,
  `pago_enviado_en` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratacion_mensajes`
--

CREATE TABLE `contratacion_mensajes` (
  `id` int(11) NOT NULL,
  `contratacion_id` int(11) NOT NULL,
  `remitente_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratacion_notificaciones`
--

CREATE TABLE `contratacion_notificaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `contratacion_id` int(11) NOT NULL,
  `titulo` varchar(180) NOT NULL,
  `detalle` varchar(500) NOT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
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
-- Estructura de tabla para la tabla `servicios_contables`
--

CREATE TABLE `servicios_contables` (
  `id` int(11) NOT NULL,
  `contador_id` int(11) NOT NULL,
  `titulo` varchar(160) NOT NULL,
  `descripcion` text NOT NULL,
  `especialidad` varchar(120) NOT NULL,
  `precio` decimal(12,2) NOT NULL,
  `moneda` char(3) NOT NULL DEFAULT 'COP',
  `estado` enum('borrador','pendiente_revision','publicado','rechazado') NOT NULL DEFAULT 'pendiente_revision',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `servicios_contables`
--

INSERT INTO `servicios_contables` (`id`, `contador_id`, `titulo`, `descripcion`, `especialidad`, `precio`, `moneda`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 22, 'hola', 'hola', 'Impuestos y DIAN', 100000.00, 'COP', 'pendiente_revision', '2026-10-27 19:41:44', '2026-10-27 19:41:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_metodos_pago`
--

CREATE TABLE `servicios_metodos_pago` (
  `id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  `metodo` varchar(80) NOT NULL,
  `datos_cuenta` varchar(255) NOT NULL,
  `titular` varchar(180) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `servicios_metodos_pago`
--

INSERT INTO `servicios_metodos_pago` (`id`, `servicio_id`, `metodo`, `datos_cuenta`, `titular`, `activo`, `creado_en`) VALUES
(1, 1, 'Nequi', '123', '', 1, '2026-10-27 19:41:44'),
(2, 1, 'Daviplata', '123', '', 1, '2026-10-27 19:41:44'),
(3, 1, 'Bancolombia', '123', '', 1, '2026-10-27 19:41:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio_notificaciones`
--

CREATE TABLE `servicio_notificaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  `titulo` varchar(180) NOT NULL,
  `detalle` varchar(500) NOT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_explicacion_rechazo`
--

CREATE TABLE `solicitudes_explicacion_rechazo` (
  `id` int(11) NOT NULL,
  `contador_id` int(11) NOT NULL,
  `asunto` varchar(180) NOT NULL,
  `mensaje` text NOT NULL,
  `respuesta_admin` text DEFAULT NULL,
  `estado` enum('pendiente','respondida') NOT NULL DEFAULT 'pendiente',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `respondido_en` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(23, 2, 'juan', '2345', 'juan@gmail.com', '$2y$10$E86MK1TfgcEV2yeX9HQ88.3PzT4ZLp7rL0Lh03G9EYDOSv9i9qSEy', 'activo', '2422342', 'cliente', '2026-10-23 22:04:21'),
(24, 2, 'pepe2', '12', 'Laura@gmail.com', '$2y$10$8a7Me4QO/cQ/pgWgdEv4XeI4bdT2uzOZc3oS/L0ncM1kKXNKJGve.', 'activo', '2314', 'admin', '2026-10-27 19:42:59');

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
-- Indices de la tabla `contador_perfiles`
--
ALTER TABLE `contador_perfiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `contrataciones`
--
ALTER TABLE `contrataciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_contratacion_estado` (`estado_pago`),
  ADD KEY `fk_contratacion_servicio` (`servicio_id`),
  ADD KEY `fk_contratacion_cliente` (`cliente_id`),
  ADD KEY `fk_contratacion_contador` (`contador_id`);

--
-- Indices de la tabla `contratacion_mensajes`
--
ALTER TABLE `contratacion_mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_chat_contratacion` (`contratacion_id`),
  ADD KEY `fk_cm_remitente` (`remitente_id`);

--
-- Indices de la tabla `contratacion_notificaciones`
--
ALTER TABLE `contratacion_notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_usuario` (`usuario_id`,`leida`),
  ADD KEY `fk_cn_contratacion` (`contratacion_id`);

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
-- Indices de la tabla `servicios_contables`
--
ALTER TABLE `servicios_contables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_servicio_estado` (`estado`),
  ADD KEY `fk_servicio_contador` (`contador_id`);

--
-- Indices de la tabla `servicios_metodos_pago`
--
ALTER TABLE `servicios_metodos_pago`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_smp_servicio` (`servicio_id`);

--
-- Indices de la tabla `servicio_notificaciones`
--
ALTER TABLE `servicio_notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sn_usuario` (`usuario_id`,`leida`),
  ADD KEY `fk_sn_servicio` (`servicio_id`);

--
-- Indices de la tabla `solicitudes_explicacion_rechazo`
--
ALTER TABLE `solicitudes_explicacion_rechazo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_solicitud_estado` (`estado`),
  ADD KEY `fk_ser_contador` (`contador_id`);

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
-- AUTO_INCREMENT de la tabla `contador_perfiles`
--
ALTER TABLE `contador_perfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `contrataciones`
--
ALTER TABLE `contrataciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contratacion_mensajes`
--
ALTER TABLE `contratacion_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contratacion_notificaciones`
--
ALTER TABLE `contratacion_notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT de la tabla `servicios_contables`
--
ALTER TABLE `servicios_contables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `servicios_metodos_pago`
--
ALTER TABLE `servicios_metodos_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicio_notificaciones`
--
ALTER TABLE `servicio_notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudes_explicacion_rechazo`
--
ALTER TABLE `solicitudes_explicacion_rechazo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
-- Filtros para la tabla `contador_perfiles`
--
ALTER TABLE `contador_perfiles`
  ADD CONSTRAINT `fk_perfil_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `contrataciones`
--
ALTER TABLE `contrataciones`
  ADD CONSTRAINT `fk_contratacion_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_contratacion_contador` FOREIGN KEY (`contador_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_contratacion_servicio` FOREIGN KEY (`servicio_id`) REFERENCES `servicios_contables` (`id`);

--
-- Filtros para la tabla `contratacion_mensajes`
--
ALTER TABLE `contratacion_mensajes`
  ADD CONSTRAINT `fk_cm_contratacion` FOREIGN KEY (`contratacion_id`) REFERENCES `contrataciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cm_remitente` FOREIGN KEY (`remitente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `contratacion_notificaciones`
--
ALTER TABLE `contratacion_notificaciones`
  ADD CONSTRAINT `fk_cn_contratacion` FOREIGN KEY (`contratacion_id`) REFERENCES `contrataciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cn_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

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
-- Filtros para la tabla `servicios_contables`
--
ALTER TABLE `servicios_contables`
  ADD CONSTRAINT `fk_servicio_contador` FOREIGN KEY (`contador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `servicios_metodos_pago`
--
ALTER TABLE `servicios_metodos_pago`
  ADD CONSTRAINT `fk_smp_servicio` FOREIGN KEY (`servicio_id`) REFERENCES `servicios_contables` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `servicio_notificaciones`
--
ALTER TABLE `servicio_notificaciones`
  ADD CONSTRAINT `fk_sn_servicio` FOREIGN KEY (`servicio_id`) REFERENCES `servicios_contables` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sn_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes_explicacion_rechazo`
--
ALTER TABLE `solicitudes_explicacion_rechazo`
  ADD CONSTRAINT `fk_ser_contador` FOREIGN KEY (`contador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
