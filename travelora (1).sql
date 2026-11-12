-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-11-2026 a las 06:00:51
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
-- Base de datos: `travelora`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cruceros`
--

CREATE TABLE `cruceros` (
  `id` int(11) NOT NULL,
  `destino` varchar(100) NOT NULL,
  `pais` varchar(100) NOT NULL,
  `fecha_salida` date NOT NULL,
  `fecha_regreso` date NOT NULL,
  `duracion` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cupos` int(11) NOT NULL DEFAULT 50
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Volcado de datos para la tabla `cruceros`
--

INSERT INTO `cruceros` (`id`, `destino`, `pais`, `fecha_salida`, `fecha_regreso`, `duracion`, `precio`, `descripcion`, `cupos`) VALUES
(1, 'Maldivas', 'Asia', '2027-02-10', '2027-02-20', 10, 1850000.00, 'Un viaje entre playas paradisíacas y aguas cristalinas.', 48),
(2, 'Brasil', 'América del Sur', '2027-01-15', '2027-01-22', 7, 850000.00, 'Un recorrido por las costas brasileñas lleno de música y diversión.', 60),
(3, 'Bali', 'Asia', '2027-03-05', '2027-03-17', 12, 2100000.00, 'Una experiencia tropical entre naturaleza y cultura.', 40),
(4, 'París', 'Europa', '2027-04-10', '2027-04-20', 10, 1950000.00, 'Un viaje para descubrir el encanto y la historia europea.', 45),
(5, 'Nueva York', 'América del Norte', '2027-05-01', '2027-05-10', 9, 1750000.00, 'Una aventura para conocer una de las ciudades más famosas del mundo.', 49),
(6, 'Italia', 'Europa', '2027-06-12', '2027-06-24', 12, 2050000.00, 'Un recorrido por paisajes, ciudades históricas y gastronomía italiana.', 40);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `crucero_id` int(11) NOT NULL,
  `pasajeros` int(11) NOT NULL,
  `fecha_reserva` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','confirmada','cancelada') NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `usuario_id`, `crucero_id`, `pasajeros`, `fecha_reserva`, `estado`) VALUES
(1, 4, 5, 1, '2026-11-12 03:35:55', 'pendiente'),
(2, 4, 1, 2, '2026-11-12 03:38:41', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('usuario','admin') DEFAULT 'usuario',
  `fecha_archivado` datetime DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `dni`, `correo`, `contraseña`, `rol`, `fecha_archivado`, `fecha_creacion`) VALUES
(1, 'juana', 'lopez', '50123456', 'juanalo@gmail.com', '$2y$10$iXoe0jwp.dYFQkPOUyPYD.EpLJpmavGLrdwteMKMS2wsk5/6jV6ri', 'admin', NULL, '2026-11-12 03:53:43'),
(2, 'luna', 'hernandez', '50997456', 'lunahe@gmail.com', '$2y$10$da9er0rKyL8LEZUhNDa5xuwyVeNtmGts3ilrYPNu/sSxhErYtjSNG', 'usuario', NULL, '2026-11-12 03:53:43'),
(3, 'juana', 'lopez', '6412456', 'juanalopex@gmail.com', '$2y$10$D3fFe12IHN3eECxnEKYQ7u6.yJeCuKfJprlW2Vk12FeEyU0X8bSZ6', 'usuario', '2026-11-12 01:41:41', '2026-11-12 03:53:43'),
(4, 'sol', 'rodriguez', '80765432', 'solro@gmail.com', '$2y$10$ohgzZHrV9MwTWuIiqEbGBOtCHFyISCxzCt1MozPKCBQApFCGBjV0K', 'admin', NULL, '2026-11-12 03:53:43'),
(5, 'sofia', 'mendez', '12678908', 'sofimendez@gmail.com', '$2y$10$hc9X1.yV5DomfbA0fVbbluIMuW//6M9eyG8QOmBMId0SrS6KlvzIG', 'usuario', NULL, '2026-11-12 03:53:43'),
(6, 'lara', 'fernandez', '90456456', 'laraf@gmail.com', '$2y$10$tB05pogUAm/V07.rj4S9ReM.P3fcVAgmEP8Ty182EtRfUDYU8MrM.', 'usuario', NULL, '2026-11-12 03:53:43'),
(7, 'celeste', 'rojas', '34567567', 'celesro@gmail.com', '$2y$10$S6YJRynnSMcTkj97c8OW8ehJZSCMRlEPGr9Cl5WsxzLwnHmrNDkCS', 'usuario', NULL, '2026-11-12 03:58:48'),
(8, 'lucia', 'herrera', '43321123', 'juanah@gmail.com', '$2y$10$cVjTRaQe1xjFd1LTydT8H.JtLF94uZTEOWZ7O6Vdw0jTSAMYzCS7W', 'usuario', NULL, '2026-11-12 04:09:10');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cruceros`
--
ALTER TABLE `cruceros`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `crucero_id` (`crucero_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cruceros`
--
ALTER TABLE `cruceros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`crucero_id`) REFERENCES `cruceros` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
