-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 11-05-2023 a las 12:12:49
-- Versión del servidor: 8.0.31
-- Versión de PHP: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `apitest`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `test`
--

DROP TABLE IF EXISTS `test`;
CREATE TABLE IF NOT EXISTS `test` (
  `resulid` int NOT NULL AUTO_INCREMENT,
  `testid` int NOT NULL,
  `testname` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `partnumber` int NOT NULL,
  `serialno` varchar(75) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `datetime` datetime NOT NULL,
  `duration` int NOT NULL,
  `defaults` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `results` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  PRIMARY KEY (`resulid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `test`
--

INSERT INTO `test` (`resulid`, `testid`, `testname`, `partnumber`, `serialno`, `datetime`, `duration`, `defaults`, `results`) VALUES
(1, 1, 'prueba primera', 25, 'asd5fsd2', '2023-05-08 00:00:00', 45, '{\'defaults\': {\r\n        {\'defname\': \'def1\', \'min\': \'1.10\', \'max\': \'1.20\'},\r\n        {\'defname\': \'def2\', \'min\': \'1.10\', \'max\': \'1.20\'},\r\n        {\'defname\': \'def3\', \'min\': \'1.10\', \'max\': \'1.20\'},\r\n        {\'defname\': \'def4\', \'min\': \'1.10\', \'max\': \'1.20\'}\r\n    }}', '456123'),
(2, 1, 'prueba primera', 25, 'asd5fsd2', '2023-05-08 00:00:00', 45, '45674477', '456123'),
(3, 1, 'prueba primera', 25, 'asd5fsd2', '2023-05-08 00:00:00', 45, 'sdgsdg', '456123'),
(4, 1, 'prueba primera', 25, 'asd5fsd2', '2023-05-08 00:00:00', 45, '{defaults:{}}', '456123'),
(5, 1, 'prueba primera', 25, 'asd5fsd2', '2023-05-08 00:00:00', 45, '{\"defname\":\"def1\",\"min\":\"1.10\",\"max\":\"1.20\"},{\"defname\":\"def2\",\"min\":\"1.10\",\"max\":\"1.20\"},{\"defname\":\"def3\",\"min\":\"1.10\",\"max\":\"1.20\"},{\"defname\":\"def4\",\"min\":\"1.10\",\"max\":\"1.20\"}', '456123'),
(6, 1, 'prueba primera', 25, 'asd5fsd2', '2023-05-08 00:00:00', 45, '{\"defname\":\"def1\",\"min\":\"1.10\",\"max\":\"1.20\"},{\"defname\":\"def2\",\"min\":\"1.10\",\"max\":\"1.20\"},{\"defname\":\"def3\",\"min\":\"1.10\",\"max\":\"1.20\"},{\"defname\":\"def4\",\"min\":\"1.10\",\"max\":\"1.20\"}', '456123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `usu_id` int NOT NULL AUTO_INCREMENT,
  `usu_codigo` varchar(150) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `usu_clave` varchar(500) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `usu_estado` varchar(11) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `usu_nombre_1` varchar(150) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `usu_apellido_1` varchar(150) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `usu_conteo_fallo_login` int NOT NULL,
  `usu_auth_key` varchar(500) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `usu_auth_key_time` varchar(500) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `usu_fecha_cambio_clave` varchar(20) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `usu_fecha_ultimo_ingreso` varchar(20) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `usu_fecha_creacion` varchar(20) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `usu_fecha_actualizacion` varchar(20) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`usu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usu_id`, `usu_codigo`, `usu_clave`, `usu_estado`, `usu_nombre_1`, `usu_apellido_1`, `usu_conteo_fallo_login`, `usu_auth_key`, `usu_auth_key_time`, `usu_fecha_cambio_clave`, `usu_fecha_ultimo_ingreso`, `usu_fecha_creacion`, `usu_fecha_actualizacion`) VALUES
(1, '123456', '$2y$13$dJkC9GGnPS.JRFuAhXTNtOm/3bvgXKaQMqplByhezt7Tf4F4K8c/u', '1', 'emilio', 'castro', 0, 'HQpGeXEAxIB1fxZqRSSLREOw97zpawol', '20230508171302', '20230506165423', '20230508171108', '20230506165423', '20230506165423');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
