-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-11-2024 a las 15:15:44 (Actualizado)
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
-- Base de datos: `trabajo_especial_titulacion`
--
CREATE DATABASE IF NOT EXISTS `trabajo_especial_titulacion` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `trabajo_especial_titulacion`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carreras`
--

DROP TABLE IF EXISTS `carreras`;
CREATE TABLE `carreras` (
  `id` int(10) UNSIGNED NOT NULL,
  `carrera` varchar(100) NOT NULL,
  `facultad_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carreras`
--

INSERT INTO `carreras` (`id`, `carrera`, `facultad_id`) VALUES
(1, 'Licenciatura en Administración', 1),
(2, 'Licenciatura en Contaduría Pública', 1),
(3, 'Licenciatura en Gerencia de Recursos Humanos', 1),
(4, 'Licenciatura en Educación Integral', 2),
(5, 'Licenciatura en Educación Preescolar', 2),
(6, 'Ingeniería en Computación', 3),
(7, 'Ingeniería en Sistemas', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facultades`
--

DROP TABLE IF EXISTS `facultades`;
CREATE TABLE `facultades` (
  `id` int(10) UNSIGNED NOT NULL,
  `facultad` varchar(100) NOT NULL,
  `siglas` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `facultades`
--

INSERT INTO `facultades` (`id`, `facultad`, `siglas`) VALUES
(1, 'Facultad de Ciencias Económicas y Sociales', 'FACES'),
(2, 'Facultad de Humanidades, Arte y Educación', 'FHAE'),
(3, 'Facultad de Ingeniería', 'FING');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lineas_investigacion`
--

DROP TABLE IF EXISTS `lineas_investigacion`;
CREATE TABLE `lineas_investigacion` (
  `id` int(11) NOT NULL,
  `nombre_linea` varchar(255) NOT NULL,
  `eje_tematico` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `rol`) VALUES
(1, 'Decano FACES'),
(2, 'Decano FHAES'),
(3, 'Decano FING'),
(4, 'Administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tet`
--

DROP TABLE IF EXISTS `tet`;
CREATE TABLE `tet` (
  `id` int(11) NOT NULL,
  `annio` year(4) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `autores` varchar(255) NOT NULL,
  `tutores` varchar(255) NOT NULL,
  `palabras_claves` varchar(140) NOT NULL,
  `resumen` text DEFAULT NULL,
  `tesis` text DEFAULT NULL,
  `id_carrera` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tet`
--

INSERT INTO `tet` (`id`, `annio`, `titulo`, `autores`, `tutores`, `palabras_claves`, `resumen`, `tesis`, `id_carrera`) VALUES
(1, '2004', 'Liderazgo ', 'Dartayan', 'Aramis', 'sadasd', NULL, NULL, 1),
(8, '2015', 'dadadas', 'fdsfdsfsd', 'gyryry', 'hrnfbz', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 2),
(10, '2024', 'tryrty', 'fvbffd', 'rggs', 'fhfhfhg', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 1),
(11, '2015', 'hrhrh', 'ffghfhgf', 'fghfgh', 'fghfhfdh', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 4),
(12, '2016', 'dfdfgdf', 'plplldfbdf', 'dfbbfg', 'ergerge', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 7),
(13, '2024', 'efwvdvdv', 'dfdfbdfbd', 'wff', 'sdfsdf', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 1),
(14, '2005', 'dsbrs', 'gfgnfg', 'gbfgasd', 'ewefwc', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 4),
(15, '2024', 'ccxc ', 'sdsdfsd', 'dfsdfs', 'sdfsdfsdf', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 7),
(17, '2024', 'ascacacasdfsdfsfd', 'sadasdassdfsdfs', 'zCZXsdfsdfsfsfd', 'zxczxczsdfsfds', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 6),
(19, '2004', 'xczxczxc', 'zxczxczxc', 'zxczxczxc', 'zxczxczx', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 4),
(20, '2024', 'zxczxc', 'zxczXCzXC', 'zxczxc', 'zxczxczX', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 7),
(26, '2024', 'ZxZXZZX', 'zXxX', 'ZXZX', 'Zxx, Zxx', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 1),
(36, '2005', 'you', 'me', 'are', 'vscvxxxv', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 2),
(38, '2024', 'sdffsdfsdf', 'le', 'zczczczx', 'xzczczcz', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 1),
(39, '2023', 'Las Redes Domesticas', 'jesus', 'Marcos', 'dfsfsfsf, aaaaa', '../../assest/uploads/soporte magistv septi.pdf', '../../assest/uploads/soporte magistv septi.pdf', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tet_lineas` (Tabla Pivote)
--

DROP TABLE IF EXISTS `tet_lineas`;
CREATE TABLE `tet_lineas` (
  `tet_id` int(11) NOT NULL,
  `linea_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` varchar(13) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `contrasena` text NOT NULL,
  `rol_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `cedula`, `correo`, `usuario`, `contrasena`, `rol_id`) VALUES
(1, 'Mayluz', 'Balesta', '12345678', 'mayluz@gmai.com', 'mayluzb', '7c222fb2927d828af22f592134e8932480637c0d', 4);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `careers_power_id_foreign` (`facultad_id`);

--
-- Indices de la tabla `facultades`
--
ALTER TABLE `facultades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `lineas_investigacion`
--
ALTER TABLE `lineas_investigacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tet`
--
ALTER TABLE `tet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tet_carrera` (`id_carrera`);

--
-- Indices de la tabla `tet_lineas`
--
ALTER TABLE `tet_lineas`
  ADD PRIMARY KEY (`tet_id`,`linea_id`),
  ADD KEY `fk_tetlineas_linea` (`linea_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carreras`
--
ALTER TABLE `carreras`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `facultades`
--
ALTER TABLE `facultades`
  MODIFY`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `lineas_investigacion`
--
ALTER TABLE `lineas_investigacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tet`
--
ALTER TABLE `tet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tet_lineas`
--
ALTER TABLE `tet_lineas`
  ADD CONSTRAINT `fk_tetlineas_linea` FOREIGN KEY (`linea_id`) REFERENCES `lineas_investigacion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tetlineas_tet` FOREIGN KEY (`tet_id`) REFERENCES `tet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;