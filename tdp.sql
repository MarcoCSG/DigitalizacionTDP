-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-08-2024 a las 01:10:29
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tdp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contraloria`
--

CREATE TABLE `contraloria` (
  `id` int(11) NOT NULL,
  `subclasificacion` varchar(255) NOT NULL,
  `clasificacion` varchar(255) NOT NULL,
  `periodo` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `cantidad_folios` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `obraspublicas`
--

CREATE TABLE `obraspublicas` (
  `id` int(11) NOT NULL,
  `subclasificacion` varchar(255) NOT NULL,
  `clasificacion` varchar(255) NOT NULL,
  `periodo` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `cantidad_folios` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presidencia`
--

CREATE TABLE `presidencia` (
  `id` int(11) NOT NULL,
  `subclasificacion` varchar(255) NOT NULL,
  `clasificacion` varchar(255) NOT NULL,
  `periodo` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `cantidad_folios` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secretaria`
--

CREATE TABLE `secretaria` (
  `id` int(11) NOT NULL,
  `subclasificacion` varchar(255) NOT NULL,
  `clasificacion` varchar(255) NOT NULL,
  `periodo` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `cantidad_folios` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sindicatura`
--

CREATE TABLE `sindicatura` (
  `id` int(11) NOT NULL,
  `subclasificacion` varchar(255) NOT NULL,
  `clasificacion` varchar(255) NOT NULL,
  `periodo` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `cantidad_folios` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tesoreria`
--

CREATE TABLE `tesoreria` (
  `id` int(11) NOT NULL,
  `subclasificacion` varchar(255) NOT NULL,
  `clasificacion` varchar(255) NOT NULL,
  `periodo` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `cantidad_folios` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `contraloria`
--
ALTER TABLE `contraloria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `obraspublicas`
--
ALTER TABLE `obraspublicas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `presidencia`
--
ALTER TABLE `presidencia`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `secretaria`
--
ALTER TABLE `secretaria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sindicatura`
--
ALTER TABLE `sindicatura`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tesoreria`
--
ALTER TABLE `tesoreria`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `contraloria`
--
ALTER TABLE `contraloria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `obraspublicas`
--
ALTER TABLE `obraspublicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presidencia`
--
ALTER TABLE `presidencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `secretaria`
--
ALTER TABLE `secretaria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sindicatura`
--
ALTER TABLE `sindicatura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tesoreria`
--
ALTER TABLE `tesoreria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
