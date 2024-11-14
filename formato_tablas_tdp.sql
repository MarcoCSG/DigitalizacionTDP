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
-- Estructura de tabla para la tabla `formato_4_38`
--

CREATE TABLE `formato_4_38` (
  `formato_id` int(11) NOT NULL,
  `concepto`  text NOT NULL,
  `serie` varchar(50) NOT NULL,
  `del_folio` int(11) NOT NULL,
  `al_folio` int(11) NOT NULL,
  `area` varchar(100) NOT NULL,
  `observaciones` text NOT NULL,
  `informacion_al` text NOT NULL,
  `responsable` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `formato_4_38`
--
ALTER TABLE `formato_4_38`
  ADD PRIMARY KEY (`formato_id`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `formato_4_38`
--
ALTER TABLE `formato_4_38`
  ADD CONSTRAINT `formato_4_38_ibfk_1` FOREIGN KEY (`formato_id`) REFERENCES `formatos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



CREATE TABLE `formato_5_1` (
  `formato_id` int(11) NOT NULL,
  `no` int(11) NOT NULL,
  `articulo`  text NOT NULL,
  `unidad_medida` varchar(255) NOT NULL,
  `existencia` varchar(255) NOT NULL,
  `costo_unitario` varchar(255) NOT NULL,
  `importe` varchar(255) NOT NULL,
  `informacion_al` text NOT NULL,
  `responsable` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `formato_5_1`
--
ALTER TABLE `formato_5_1`
  ADD PRIMARY KEY (`formato_id`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `formato_5_1`
--
ALTER TABLE `formato_5_1`
  ADD CONSTRAINT `formato_5_1_ibfk_1` FOREIGN KEY (`formato_id`) REFERENCES `formatos` (`id`) ON DELETE CASCADE;
COMMIT;