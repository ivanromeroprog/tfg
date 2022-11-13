-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.8.3-MariaDB-log - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Volcando estructura para tabla tfg.actividad
CREATE TABLE IF NOT EXISTS `actividad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8DF2BD06DB38439E` (`usuario_id`),
  CONSTRAINT `FK_8DF2BD06DB38439E` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.actividad: ~2 rows (aproximadamente)
DELETE FROM `actividad`;
INSERT INTO `actividad` (`id`, `titulo`, `descripcion`, `tipo`, `usuario_id`) VALUES
	(2, 'Introductorio de informática', NULL, 'Cuestionario', 3),
	(3, 'CPU', NULL, 'Cuestionario', 3);

-- Volcando estructura para tabla tfg.alumno
CREATE TABLE IF NOT EXISTS `alumno` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cua` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.alumno: ~12 rows (aproximadamente)
DELETE FROM `alumno`;
INSERT INTO `alumno` (`id`, `nombre`, `apellido`, `cua`) VALUES
	(1, 'Juan', 'Ayala', 'mt0001'),
	(2, 'Pedro', 'Barrios', 'mt0002'),
	(3, 'Luciana', 'Cabral', 'mt0003'),
	(4, 'Milagros', 'Garrido', 'mt0004'),
	(5, 'Mariela', 'Barboza', 'mt0005'),
	(6, 'Fabio', 'Mendez', 'mt0006'),
	(7, 'Miguel', 'Zorrilla', 'mt0008'),
	(8, 'Luís', 'Yalo', 'mt0007'),
	(9, 'Miguel', 'Cardozo', 'mt0009'),
	(10, 'Debora', 'Lucero', 'mt0001'),
	(11, 'Valeria', 'Cuadro', 'mt0002'),
	(12, 'Mara', 'Gonzales', 'mt0003');

-- Volcando estructura para tabla tfg.alumno_curso
CREATE TABLE IF NOT EXISTS `alumno_curso` (
  `alumno_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  PRIMARY KEY (`alumno_id`,`curso_id`),
  KEY `IDX_66FE498EFC28E5EE` (`alumno_id`),
  KEY `IDX_66FE498E87CB4A1F` (`curso_id`),
  CONSTRAINT `FK_66FE498E87CB4A1F` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_66FE498EFC28E5EE` FOREIGN KEY (`alumno_id`) REFERENCES `alumno` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.alumno_curso: ~12 rows (aproximadamente)
DELETE FROM `alumno_curso`;
INSERT INTO `alumno_curso` (`alumno_id`, `curso_id`) VALUES
	(1, 1),
	(2, 1),
	(3, 1),
	(4, 1),
	(5, 1),
	(6, 1),
	(7, 1),
	(8, 1),
	(9, 1),
	(10, 3),
	(11, 3),
	(12, 3);

-- Volcando estructura para tabla tfg.asistencia
CREATE TABLE IF NOT EXISTS `asistencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `toma_de_asistencia_id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `presente` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D8264A8D4515ECEF` (`toma_de_asistencia_id`),
  KEY `IDX_D8264A8DFC28E5EE` (`alumno_id`),
  CONSTRAINT `FK_D8264A8D4515ECEF` FOREIGN KEY (`toma_de_asistencia_id`) REFERENCES `toma_de_asistencia` (`id`),
  CONSTRAINT `FK_D8264A8DFC28E5EE` FOREIGN KEY (`alumno_id`) REFERENCES `alumno` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.asistencia: ~8 rows (aproximadamente)
DELETE FROM `asistencia`;
INSERT INTO `asistencia` (`id`, `toma_de_asistencia_id`, `alumno_id`, `presente`) VALUES
	(1, 1, 1, 1),
	(2, 1, 2, 0),
	(3, 1, 3, 1),
	(4, 1, 7, 1),
	(5, 1, 5, 0),
	(6, 1, 4, 1),
	(7, 1, 6, 1),
	(8, 1, 9, 1);

-- Volcando estructura para tabla tfg.curso
CREATE TABLE IF NOT EXISTS `curso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `grado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `division` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `materia` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anio` int(11) NOT NULL,
  `organizacion_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CA3B40ECDB38439E` (`usuario_id`),
  KEY `IDX_CA3B40EC90B1019E` (`organizacion_id`),
  CONSTRAINT `FK_CA3B40EC90B1019E` FOREIGN KEY (`organizacion_id`) REFERENCES `organizacion` (`id`),
  CONSTRAINT `FK_CA3B40ECDB38439E` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.curso: ~2 rows (aproximadamente)
DELETE FROM `curso`;
INSERT INTO `curso` (`id`, `usuario_id`, `grado`, `division`, `materia`, `anio`, `organizacion_id`) VALUES
	(1, 3, '1', 'U', 'Informática', 2022, 1),
	(3, 8, '1', 'U', 'Ciencias Naturales', 2022, 2);

-- Volcando estructura para tabla tfg.detalle_actividad
CREATE TABLE IF NOT EXISTS `detalle_actividad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dato` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `relacion` int(11) DEFAULT NULL,
  `correcto` tinyint(1) DEFAULT NULL,
  `actividad_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AC9E0C466014FACA` (`actividad_id`),
  CONSTRAINT `FK_AC9E0C466014FACA` FOREIGN KEY (`actividad_id`) REFERENCES `actividad` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.detalle_actividad: ~39 rows (aproximadamente)
DELETE FROM `detalle_actividad`;
INSERT INTO `detalle_actividad` (`id`, `dato`, `tipo`, `relacion`, `correcto`, `actividad_id`) VALUES
	(6, '¿Qué es Hardware ?', 'Pregunta', 6, NULL, 2),
	(7, 'Es la parte lógica de la computadora.', 'Respuesta', 6, 0, 2),
	(8, 'Es un tipo de virus.', 'Respuesta', 6, 0, 2),
	(9, 'Es el nombre de un programa.', 'Respuesta', 6, 0, 2),
	(10, 'Es la parte física de la computadora.', 'Respuesta', 6, 1, 2),
	(11, '¿Qué son los periféricos de salida?', 'Pregunta', 11, NULL, 2),
	(12, 'Son componentes wireless que permiten la salida de datos de la computadora.', 'Respuesta', 11, 0, 2),
	(13, ' Son componentes que permiten la entrada de datos a la computadora.', 'Respuesta', 11, 0, 2),
	(14, ' Son componentes que permiten la salida de datos de la computadora.', 'Respuesta', 11, 1, 2),
	(15, 'Es una placa conectada a un slot de la placa-madre.', 'Respuesta', 11, 0, 2),
	(16, '¿Cuáles de estos son ejemplos específicos de periféricos de entrada ?', 'Pregunta', 16, NULL, 2),
	(17, 'Placa de vídeo, fax y teléfono.', 'Respuesta', 16, 0, 2),
	(18, 'Monitor, parlantes e impresora.', 'Respuesta', 16, 0, 2),
	(19, 'Mouse y webcam.', 'Respuesta', 16, 1, 2),
	(20, 'Escáner.', 'Respuesta', 16, 1, 2),
	(21, 'CD, DVD y disquete.', 'Respuesta', 16, 0, 2),
	(22, '¿Cuáles de estos son tipos de virus ?', 'Pregunta', 22, NULL, 2),
	(23, 'Spywares, Trojans y Malwares.', 'Respuesta', 22, 1, 2),
	(24, 'Firewalls, P2P y Blocks.', 'Respuesta', 22, 0, 2),
	(25, 'AVGs, Nortons y Aviras.', 'Respuesta', 22, 0, 2),
	(26, '¿Cuáles de estas son unidades de medidas en informática ?', 'Pregunta', 26, NULL, 2),
	(27, ' Poligonos, Antigonos, Pentagonos, Perimetros y Milímetros.', 'Respuesta', 26, 0, 2),
	(28, ' Bit, Byte, Kilobyte, Megabyte y Gigabyte.', 'Respuesta', 26, 1, 2),
	(29, ' Metros, Centímetros, Milímetros, Pixels y Pulgadas.', 'Respuesta', 26, 0, 2),
	(30, '¿Que significa CPU?', 'Pregunta', 30, NULL, 3),
	(31, 'Unidad Central De Almacenamiento', 'Respuesta', 30, 0, 3),
	(32, 'Unidad Central De Procesamiento', 'Respuesta', 30, 1, 3),
	(33, 'Unidad Central De Algoritmia', 'Respuesta', 30, 0, 3),
	(34, 'Unidad de Control', 'Respuesta', 30, 0, 3),
	(35, 'Partes de la CPU', 'Pregunta', 35, NULL, 3),
	(36, 'Registros', 'Respuesta', 35, 1, 3),
	(37, 'ALU', 'Respuesta', 35, 1, 3),
	(38, 'RAM', 'Respuesta', 35, 0, 3),
	(39, 'SSD', 'Respuesta', 35, 0, 3),
	(40, 'CU', 'Respuesta', 35, 1, 3),
	(41, '¿Qué es la ALU?', 'Pregunta', 41, NULL, 3),
	(42, 'La Unidad Aritmético-Lógica ', 'Respuesta', 41, 1, 3),
	(43, 'La Unidad Lógica Única', 'Respuesta', 41, 0, 3),
	(44, 'Unidad Central De Algoritmia', 'Respuesta', 41, 0, 3);

-- Volcando estructura para tabla tfg.detalle_presentacion_actividad
CREATE TABLE IF NOT EXISTS `detalle_presentacion_actividad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `presentacion_actividad_id` int(11) NOT NULL,
  `dato` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `relacion` int(11) DEFAULT NULL,
  `correcto` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3BBFF9656B98C825` (`presentacion_actividad_id`),
  CONSTRAINT `FK_3BBFF9656B98C825` FOREIGN KEY (`presentacion_actividad_id`) REFERENCES `presentacion_actividad` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.detalle_presentacion_actividad: ~24 rows (aproximadamente)
DELETE FROM `detalle_presentacion_actividad`;
INSERT INTO `detalle_presentacion_actividad` (`id`, `presentacion_actividad_id`, `dato`, `tipo`, `relacion`, `correcto`) VALUES
	(1, 1, '¿Qué es Hardware ?', 'Pregunta', 6, NULL),
	(2, 1, 'Es la parte lógica de la computadora.', 'Respuesta', 6, 0),
	(3, 1, 'Es un tipo de virus.', 'Respuesta', 6, 0),
	(4, 1, 'Es el nombre de un programa.', 'Respuesta', 6, 0),
	(5, 1, 'Es la parte física de la computadora.', 'Respuesta', 6, 1),
	(6, 1, '¿Qué son los periféricos de salida?', 'Pregunta', 11, NULL),
	(7, 1, 'Son componentes wireless que permiten la salida de datos de la computadora.', 'Respuesta', 11, 0),
	(8, 1, ' Son componentes que permiten la entrada de datos a la computadora.', 'Respuesta', 11, 0),
	(9, 1, ' Son componentes que permiten la salida de datos de la computadora.', 'Respuesta', 11, 1),
	(10, 1, 'Es una placa conectada a un slot de la placa-madre.', 'Respuesta', 11, 0),
	(11, 1, '¿Cuáles de estos son ejemplos específicos de periféricos de entrada ?', 'Pregunta', 16, NULL),
	(12, 1, 'Placa de vídeo, fax y teléfono.', 'Respuesta', 16, 0),
	(13, 1, 'Monitor, parlantes e impresora.', 'Respuesta', 16, 0),
	(14, 1, 'Mouse y webcam.', 'Respuesta', 16, 1),
	(15, 1, 'Escáner.', 'Respuesta', 16, 1),
	(16, 1, 'CD, DVD y disquete.', 'Respuesta', 16, 0),
	(17, 1, '¿Cuáles de estos son tipos de virus ?', 'Pregunta', 22, NULL),
	(18, 1, 'Spywares, Trojans y Malwares.', 'Respuesta', 22, 1),
	(19, 1, 'Firewalls, P2P y Blocks.', 'Respuesta', 22, 0),
	(20, 1, 'AVGs, Nortons y Aviras.', 'Respuesta', 22, 0),
	(21, 1, '¿Cuáles de estas son unidades de medidas en informática ?', 'Pregunta', 26, NULL),
	(22, 1, ' Poligonos, Antigonos, Pentagonos, Perimetros y Milímetros.', 'Respuesta', 26, 0),
	(23, 1, ' Bit, Byte, Kilobyte, Megabyte y Gigabyte.', 'Respuesta', 26, 1),
	(24, 1, ' Metros, Centímetros, Milímetros, Pixels y Pulgadas.', 'Respuesta', 26, 0);

-- Volcando estructura para tabla tfg.doctrine_migration_versions
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Volcando datos para la tabla tfg.doctrine_migration_versions: ~20 rows (aproximadamente)
DELETE FROM `doctrine_migration_versions`;
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20220920013152', '2022-11-12 18:24:16', 322),
	('DoctrineMigrations\\Version20220920131539', '2022-11-12 18:24:16', 17),
	('DoctrineMigrations\\Version20220920132258', '2022-11-12 18:24:16', 177),
	('DoctrineMigrations\\Version20220920135603', '2022-11-12 18:24:16', 109),
	('DoctrineMigrations\\Version20220924224452', '2022-11-12 18:24:16', 167),
	('DoctrineMigrations\\Version20220924230455', '2022-11-12 18:24:17', 70),
	('DoctrineMigrations\\Version20220924232640', '2022-11-12 18:24:17', 108),
	('DoctrineMigrations\\Version20220924235138', '2022-11-12 18:24:17', 35),
	('DoctrineMigrations\\Version20220924235423', '2022-11-12 18:24:17', 86),
	('DoctrineMigrations\\Version20220924235758', '2022-11-12 18:24:17', 89),
	('DoctrineMigrations\\Version20220925000554', '2022-11-12 18:24:17', 109),
	('DoctrineMigrations\\Version20220925003539', '2022-11-12 18:24:17', 136),
	('DoctrineMigrations\\Version20220925004343', '2022-11-12 18:24:17', 61),
	('DoctrineMigrations\\Version20220925005521', '2022-11-12 18:24:17', 113),
	('DoctrineMigrations\\Version20220925010333', '2022-11-12 18:24:17', 21),
	('DoctrineMigrations\\Version20221004221847', '2022-11-12 18:24:18', 22),
	('DoctrineMigrations\\Version20221019153605', '2022-11-12 18:24:18', 25),
	('DoctrineMigrations\\Version20221024045836', '2022-11-12 18:24:18', 25),
	('DoctrineMigrations\\Version20221024051333', '2022-11-12 18:24:18', 23),
	('DoctrineMigrations\\Version20221031011213', '2022-11-12 18:24:18', 43);

-- Volcando estructura para tabla tfg.interaccion
CREATE TABLE IF NOT EXISTS `interaccion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumno_id` int(11) NOT NULL,
  `detalle_presentacion_actividad_id` int(11) NOT NULL,
  `correcto` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FA439281FC28E5EE` (`alumno_id`),
  KEY `IDX_FA439281B6FD3829` (`detalle_presentacion_actividad_id`),
  CONSTRAINT `FK_FA439281B6FD3829` FOREIGN KEY (`detalle_presentacion_actividad_id`) REFERENCES `detalle_presentacion_actividad` (`id`),
  CONSTRAINT `FK_FA439281FC28E5EE` FOREIGN KEY (`alumno_id`) REFERENCES `alumno` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.interaccion: ~49 rows (aproximadamente)
DELETE FROM `interaccion`;
INSERT INTO `interaccion` (`id`, `alumno_id`, `detalle_presentacion_actividad_id`, `correcto`) VALUES
	(1, 1, 1, 0),
	(2, 5, 1, 0),
	(3, 2, 1, NULL),
	(4, 3, 1, NULL),
	(5, 4, 1, NULL),
	(6, 6, 1, 0),
	(7, 7, 1, NULL),
	(8, 6, 3, 0),
	(9, 6, 6, 1),
	(10, 6, 9, 1),
	(11, 6, 11, 1),
	(12, 6, 14, 1),
	(13, 6, 15, 1),
	(14, 6, 17, 0),
	(15, 6, 19, 0),
	(16, 6, 21, 1),
	(17, 6, 23, 1),
	(18, 1, 3, 0),
	(19, 1, 6, 1),
	(20, 1, 9, 1),
	(21, 1, 11, 1),
	(22, 1, 14, 1),
	(23, 1, 15, 1),
	(24, 1, 17, 0),
	(25, 1, 20, 0),
	(26, 1, 21, 0),
	(27, 1, 22, 0),
	(28, 9, 1, 1),
	(29, 9, 3, NULL),
	(30, 9, 5, 1),
	(31, 9, 6, 0),
	(32, 9, 8, 0),
	(33, 9, 11, 0),
	(34, 9, 16, 0),
	(35, 9, 17, 1),
	(36, 9, 18, 1),
	(37, 9, 21, 0),
	(38, 9, 24, 0),
	(39, 5, 2, 0),
	(40, 5, 6, 1),
	(41, 5, 9, 1),
	(42, 5, 11, 1),
	(43, 5, 13, NULL),
	(44, 5, 15, 1),
	(45, 5, 14, 1),
	(46, 5, 17, 1),
	(47, 5, 18, 1),
	(48, 5, 21, 1),
	(49, 5, 23, 1);

-- Volcando estructura para tabla tfg.invitacion
CREATE TABLE IF NOT EXISTS `invitacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organizacion_id` int(11) NOT NULL,
  `usuario_origen_id` int(11) NOT NULL,
  `usuario_destino_id` int(11) NOT NULL,
  `rol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aceptada` tinyint(1) NOT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3CD30E8490B1019E` (`organizacion_id`),
  KEY `IDX_3CD30E841A6974DF` (`usuario_origen_id`),
  KEY `IDX_3CD30E8417064CB7` (`usuario_destino_id`),
  CONSTRAINT `FK_3CD30E8417064CB7` FOREIGN KEY (`usuario_destino_id`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_3CD30E841A6974DF` FOREIGN KEY (`usuario_origen_id`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_3CD30E8490B1019E` FOREIGN KEY (`organizacion_id`) REFERENCES `organizacion` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.invitacion: ~0 rows (aproximadamente)
DELETE FROM `invitacion`;

-- Volcando estructura para tabla tfg.messenger_messages
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.messenger_messages: ~0 rows (aproximadamente)
DELETE FROM `messenger_messages`;

-- Volcando estructura para tabla tfg.organizacion
CREATE TABLE IF NOT EXISTS `organizacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `localidad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provincia` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pais` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creador_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C200C5A62F40C3D` (`creador_id`),
  CONSTRAINT `FK_C200C5A62F40C3D` FOREIGN KEY (`creador_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.organizacion: ~2 rows (aproximadamente)
DELETE FROM `organizacion`;
INSERT INTO `organizacion` (`id`, `titulo`, `direccion`, `localidad`, `provincia`, `pais`, `telefono`, `email`, `creador_id`) VALUES
	(1, 'ISFD "Dr. Ramón J. Cárcano"', 'Colón 880', 'Monte Caseros', 'Corrientes', 'Argentina', NULL, NULL, 2),
	(2, 'Colegio Secundario Juan Pablo II', '20 de Junio 1060', 'Monte Caseros', 'Corrientes', 'Argentina', NULL, NULL, 2);

-- Volcando estructura para tabla tfg.organizacion_usuario
CREATE TABLE IF NOT EXISTS `organizacion_usuario` (
  `organizacion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY (`organizacion_id`,`usuario_id`),
  KEY `IDX_97373C6D90B1019E` (`organizacion_id`),
  KEY `IDX_97373C6DDB38439E` (`usuario_id`),
  CONSTRAINT `FK_97373C6D90B1019E` FOREIGN KEY (`organizacion_id`) REFERENCES `organizacion` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_97373C6DDB38439E` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.organizacion_usuario: ~2 rows (aproximadamente)
DELETE FROM `organizacion_usuario`;
INSERT INTO `organizacion_usuario` (`organizacion_id`, `usuario_id`) VALUES
	(1, 3),
	(2, 8);

-- Volcando estructura para tabla tfg.presentacion_actividad
CREATE TABLE IF NOT EXISTS `presentacion_actividad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9328115487CB4A1F` (`curso_id`),
  KEY `IDX_93281154DB38439E` (`usuario_id`),
  CONSTRAINT `FK_9328115487CB4A1F` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`),
  CONSTRAINT `FK_93281154DB38439E` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.presentacion_actividad: ~1 rows (aproximadamente)
DELETE FROM `presentacion_actividad`;
INSERT INTO `presentacion_actividad` (`id`, `usuario_id`, `curso_id`, `estado`, `fecha`, `titulo`, `descripcion`, `tipo`) VALUES
	(1, 3, 1, 'Finalizado', '2022-11-12 23:54:53', 'Introductorio de informática', NULL, 'Cuestionario');

-- Volcando estructura para tabla tfg.toma_de_asistencia
CREATE TABLE IF NOT EXISTS `toma_de_asistencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `curso_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4BC66AF87CB4A1F` (`curso_id`),
  CONSTRAINT `FK_4BC66AF87CB4A1F` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.toma_de_asistencia: ~1 rows (aproximadamente)
DELETE FROM `toma_de_asistencia`;
INSERT INTO `toma_de_asistencia` (`id`, `curso_id`, `fecha`, `estado`) VALUES
	(1, 1, '2022-11-12 18:41:12', 'Iniciado');

-- Volcando estructura para tabla tfg.usuario
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_2265B05DF85E0677` (`username`),
  UNIQUE KEY `UNIQ_2265B05DE7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.usuario: ~3 rows (aproximadamente)
DELETE FROM `usuario`;
INSERT INTO `usuario` (`id`, `username`, `roles`, `password`, `email`, `nombre`, `apellido`, `telefono`, `direccion`) VALUES
	(2, 'responsable', '["ROLE_USER","ROLE_RESPONSABLE"]', '$2y$13$n5jsFek.mSqvlSkBu5D4GeEvc6mi/vwdcif0/mYd5Dyfsi3O/Crfy', 'responsable@test.com', 'responsable', 'responsable', NULL, NULL),
	(3, 'roberto', '["ROLE_USER","ROLE_DOCENTE"]', '$2y$13$FkVMhmWUSIVvTkxXO/fiPuqriPvn/.Cpj501w2m5R7/L9Uh.h/WUm', 'roberto@rober.com', 'Roberto', 'Rober', '3775-15456789', 'CalleRober 456'),
	(8, 'griselda', '["ROLE_USER","ROLE_DOCENTE"]', '$2y$13$E7Y/bYxUiwsjIdlkHnfEyehm2cNa785HGJtCFlp6H89p4cv0xKxUy', 'griselda_miller@hotmail.com', 'Griselda', 'Miller', '03775-457889', 'José Pascual Arce 448');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
