-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-11-2024 a las 21:05:57
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
-- Estructura de tabla para la tabla `formato_3_5`
--

CREATE TABLE `formato_3_5` (
  `formato_id` int(11) NOT NULL,
  `no` int(11) NOT NULL,
  `numero` varchar(255) NOT NULL,
  `fecha` varchar(255) NOT NULL,
  `asunto` varchar(255) NOT NULL,
  `fojas` int(11) NOT NULL,
  `firma_si` char(1) DEFAULT NULL,
  `firma_no` char(1) DEFAULT NULL,
  `sello_si` char(1) DEFAULT NULL,
  `sello_no` char(1) DEFAULT NULL,
  `informacion_al` text DEFAULT NULL,
  `responsable` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `formato_3_5`
--
ALTER TABLE `formato_3_5`
  ADD PRIMARY KEY (`formato_id`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `formato_3_5`
--
ALTER TABLE `formato_3_5`
  ADD CONSTRAINT `formato_3_5_ibfk_1` FOREIGN KEY (`formato_id`) REFERENCES `formatos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
