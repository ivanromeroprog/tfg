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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.actividad: ~4 rows (aproximadamente)
DELETE FROM `actividad`;
INSERT INTO `actividad` (`id`, `titulo`, `descripcion`, `tipo`, `usuario_id`) VALUES
	(2, 'Introductorio de informática', NULL, 'Cuestionario', 3),
	(3, 'CPU', NULL, 'Cuestionario', 3),
	(4, 'Programación', NULL, 'Cuestionario', 3),
	(5, 'Redes', NULL, 'Cuestionario', 3);

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

-- Volcando datos para la tabla tfg.alumno_curso: ~15 rows (aproximadamente)
DELETE FROM `alumno_curso`;
INSERT INTO `alumno_curso` (`alumno_id`, `curso_id`) VALUES
	(1, 1),
	(2, 1),
	(3, 1),
	(4, 1),
	(5, 1),
	(5, 4),
	(6, 1),
	(7, 1),
	(7, 4),
	(8, 1),
	(8, 4),
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.asistencia: ~11 rows (aproximadamente)
DELETE FROM `asistencia`;
INSERT INTO `asistencia` (`id`, `toma_de_asistencia_id`, `alumno_id`, `presente`) VALUES
	(1, 1, 1, 1),
	(2, 1, 2, 0),
	(3, 1, 3, 1),
	(4, 1, 7, 1),
	(5, 1, 5, 0),
	(6, 1, 4, 1),
	(7, 1, 6, 1),
	(8, 1, 9, 1),
	(9, 2, 5, 1),
	(10, 2, 8, 1),
	(11, 2, 7, 0);

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.curso: ~3 rows (aproximadamente)
DELETE FROM `curso`;
INSERT INTO `curso` (`id`, `usuario_id`, `grado`, `division`, `materia`, `anio`, `organizacion_id`) VALUES
	(1, 3, '1', 'U', 'Informática', 2022, 1),
	(3, 8, '1', 'U', 'Ciencias Naturales', 2022, 2),
	(4, 3, '1', 'U', 'Curso Extra-clase de Informática', 2022, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.detalle_actividad: ~75 rows (aproximadamente)
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
	(44, 'Unidad Central De Algoritmia', 'Respuesta', 41, 0, 3),
	(45, '¿Que es la computación?', 'Pregunta', 45, NULL, 4),
	(46, 'Es el estudio de los fundamentos teóricos de la información que procesan las computadoras, y las distintas implementaciones en forma de sistemas comp.', 'Respuesta', 45, 1, 4),
	(47, 'Es un vocablo inspirado en el francés “informatique” que se encarga de procesar la Información.', 'Respuesta', 45, 0, 4),
	(48, 'Es el estudio de las Computadoras creadas por Bill Gates en los 80\'s', 'Respuesta', 45, 0, 4),
	(49, 'Un algoritmo es', 'Pregunta', 49, NULL, 4),
	(50, 'Son las distintas herramientas que el programador utiliza para crear la aplicación en un lenguaje de programación determinado', 'Respuesta', 49, 0, 4),
	(51, 'Una serie de pasos que se siguen para solucionar un determinado problema. Debe de poseer una serie de características', 'Respuesta', 49, 1, 4),
	(52, 'Siempre líneas rectas para guardar la mayor simetría posible', 'Respuesta', 49, 0, 4),
	(53, '¿Con que extensión se guardan los programas realizados en c++?', 'Pregunta', 53, NULL, 4),
	(54, '.html', 'Respuesta', 53, 0, 4),
	(55, '.cpp', 'Respuesta', 53, 1, 4),
	(56, '.cp', 'Respuesta', 53, 0, 4),
	(57, '.js', 'Respuesta', 53, 0, 4),
	(58, '¿Cuál es la función que debe de incluir cada programa en C++?', 'Pregunta', 58, NULL, 4),
	(59, 'Función ()', 'Respuesta', 58, 0, 4),
	(60, 'nain()', 'Respuesta', 58, 0, 4),
	(61, 'cpp()', 'Respuesta', 58, 0, 4),
	(62, 'main()', 'Respuesta', 58, 1, 4),
	(63, 'Un host es cualquier dispositivo que', 'Pregunta', 63, NULL, 5),
	(64, 'solo reciben información de la red', 'Respuesta', 63, 0, 5),
	(65, 'sirven como periféricos de la re', 'Respuesta', 63, 0, 5),
	(66, 'envía y recibe información en la red', 'Respuesta', 63, 1, 5),
	(67, 'Seleccione dos beneficios del networking', 'Pregunta', 67, NULL, 5),
	(68, 'Se necesitan más periféricos', 'Respuesta', 67, 0, 5),
	(69, 'Mayores capacidades de comunicación', 'Respuesta', 67, 1, 5),
	(70, 'Administración descentralizada', 'Respuesta', 67, 0, 5),
	(71, 'Menor costo en la adquisición de licencias', 'Respuesta', 67, 1, 5),
	(72, 'Una red de área local (LAN) se caracteriza porque', 'Pregunta', 72, NULL, 5),
	(73, 'Sus dispositivos se interconectan bajo el mismo control administrativo', 'Respuesta', 72, 1, 5),
	(74, 'Sus dispositivos se pueden conectar con ondas de radio', 'Respuesta', 72, 0, 5),
	(75, 'Todas las anteriores', 'Respuesta', 72, 0, 5),
	(76, 'Tipos de Redes Informaticas', 'Pregunta', 76, NULL, 5),
	(77, 'WAN', 'Respuesta', 76, 1, 5),
	(78, 'TAM', 'Respuesta', 76, 0, 5),
	(79, 'LAN', 'Respuesta', 76, 1, 5),
	(80, 'ZAM', 'Respuesta', 76, 0, 5);

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
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.detalle_presentacion_actividad: ~99 rows (aproximadamente)
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
	(24, 1, ' Metros, Centímetros, Milímetros, Pixels y Pulgadas.', 'Respuesta', 26, 0),
	(25, 2, '¿Que significa CPU?', 'Pregunta', 30, NULL),
	(26, 2, 'Unidad Central De Almacenamiento', 'Respuesta', 30, 0),
	(27, 2, 'Unidad Central De Procesamiento', 'Respuesta', 30, 1),
	(28, 2, 'Unidad Central De Algoritmia', 'Respuesta', 30, 0),
	(29, 2, 'Unidad de Control', 'Respuesta', 30, 0),
	(30, 2, 'Partes de la CPU', 'Pregunta', 35, NULL),
	(31, 2, 'Registros', 'Respuesta', 35, 1),
	(32, 2, 'ALU', 'Respuesta', 35, 1),
	(33, 2, 'RAM', 'Respuesta', 35, 0),
	(34, 2, 'SSD', 'Respuesta', 35, 0),
	(35, 2, 'CU', 'Respuesta', 35, 1),
	(36, 2, '¿Qué es la ALU?', 'Pregunta', 41, NULL),
	(37, 2, 'La Unidad Aritmético-Lógica ', 'Respuesta', 41, 1),
	(38, 2, 'La Unidad Lógica Única', 'Respuesta', 41, 0),
	(39, 2, 'Unidad Central De Algoritmia', 'Respuesta', 41, 0),
	(40, 3, '¿Que es la computación?', 'Pregunta', 45, NULL),
	(41, 3, 'Es el estudio de los fundamentos teóricos de la información que procesan las computadoras, y las distintas implementaciones en forma de sistemas comp.', 'Respuesta', 45, 1),
	(42, 3, 'Es un vocablo inspirado en el francés “informatique” que se encarga de procesar la Información.', 'Respuesta', 45, 0),
	(43, 3, 'Es el estudio de las Computadoras creadas por Bill Gates en los 80\'s', 'Respuesta', 45, 0),
	(44, 3, 'Un algoritmo es', 'Pregunta', 49, NULL),
	(45, 3, 'Son las distintas herramientas que el programador utiliza para crear la aplicación en un lenguaje de programación determinado', 'Respuesta', 49, 0),
	(46, 3, 'Una serie de pasos que se siguen para solucionar un determinado problema. Debe de poseer una serie de características', 'Respuesta', 49, 1),
	(47, 3, 'Siempre líneas rectas para guardar la mayor simetría posible', 'Respuesta', 49, 0),
	(48, 3, '¿Con que extensión se guardan los programas realizados en c++?', 'Pregunta', 53, NULL),
	(49, 3, '.html', 'Respuesta', 53, 0),
	(50, 3, '.cpp', 'Respuesta', 53, 1),
	(51, 3, '.cp', 'Respuesta', 53, 0),
	(52, 3, '.js', 'Respuesta', 53, 0),
	(53, 3, '¿Cuál es la función que debe de incluir cada programa en C++?', 'Pregunta', 58, NULL),
	(54, 3, 'Función ()', 'Respuesta', 58, 0),
	(55, 3, 'nain()', 'Respuesta', 58, 0),
	(56, 3, 'cpp()', 'Respuesta', 58, 0),
	(57, 3, 'main()', 'Respuesta', 58, 1),
	(58, 4, 'Un host es cualquier dispositivo que', 'Pregunta', 63, NULL),
	(59, 4, 'solo reciben información de la red', 'Respuesta', 63, 0),
	(60, 4, 'sirven como periféricos de la re', 'Respuesta', 63, 0),
	(61, 4, 'envía y recibe información en la red', 'Respuesta', 63, 1),
	(62, 4, 'Seleccione dos beneficios del networking', 'Pregunta', 67, NULL),
	(63, 4, 'Se necesitan más periféricos', 'Respuesta', 67, 0),
	(64, 4, 'Mayores capacidades de comunicación', 'Respuesta', 67, 1),
	(65, 4, 'Administración descentralizada', 'Respuesta', 67, 0),
	(66, 4, 'Menor costo en la adquisición de licencias', 'Respuesta', 67, 1),
	(67, 4, 'Una red de área local (LAN) se caracteriza porque', 'Pregunta', 72, NULL),
	(68, 4, 'Sus dispositivos se interconectan bajo el mismo control administrativo', 'Respuesta', 72, 1),
	(69, 4, 'Sus dispositivos se pueden conectar con ondas de radio', 'Respuesta', 72, 0),
	(70, 4, 'Todas las anteriores', 'Respuesta', 72, 0),
	(71, 4, 'Tipos de Redes Informaticas', 'Pregunta', 76, NULL),
	(72, 4, 'WAN', 'Respuesta', 76, 1),
	(73, 4, 'TAM', 'Respuesta', 76, 0),
	(74, 4, 'LAN', 'Respuesta', 76, 1),
	(75, 4, 'ZAM', 'Respuesta', 76, 0),
	(76, 5, '¿Qué es Hardware ?', 'Pregunta', 6, NULL),
	(77, 5, 'Es la parte lógica de la computadora.', 'Respuesta', 6, 0),
	(78, 5, 'Es un tipo de virus.', 'Respuesta', 6, 0),
	(79, 5, 'Es el nombre de un programa.', 'Respuesta', 6, 0),
	(80, 5, 'Es la parte física de la computadora.', 'Respuesta', 6, 1),
	(81, 5, '¿Qué son los periféricos de salida?', 'Pregunta', 11, NULL),
	(82, 5, 'Son componentes wireless que permiten la salida de datos de la computadora.', 'Respuesta', 11, 0),
	(83, 5, ' Son componentes que permiten la entrada de datos a la computadora.', 'Respuesta', 11, 0),
	(84, 5, ' Son componentes que permiten la salida de datos de la computadora.', 'Respuesta', 11, 1),
	(85, 5, 'Es una placa conectada a un slot de la placa-madre.', 'Respuesta', 11, 0),
	(86, 5, '¿Cuáles de estos son ejemplos específicos de periféricos de entrada ?', 'Pregunta', 16, NULL),
	(87, 5, 'Placa de vídeo, fax y teléfono.', 'Respuesta', 16, 0),
	(88, 5, 'Monitor, parlantes e impresora.', 'Respuesta', 16, 0),
	(89, 5, 'Mouse y webcam.', 'Respuesta', 16, 1),
	(90, 5, 'Escáner.', 'Respuesta', 16, 1),
	(91, 5, 'CD, DVD y disquete.', 'Respuesta', 16, 0),
	(92, 5, '¿Cuáles de estos son tipos de virus ?', 'Pregunta', 22, NULL),
	(93, 5, 'Spywares, Trojans y Malwares.', 'Respuesta', 22, 1),
	(94, 5, 'Firewalls, P2P y Blocks.', 'Respuesta', 22, 0),
	(95, 5, 'AVGs, Nortons y Aviras.', 'Respuesta', 22, 0),
	(96, 5, '¿Cuáles de estas son unidades de medidas en informática ?', 'Pregunta', 26, NULL),
	(97, 5, ' Poligonos, Antigonos, Pentagonos, Perimetros y Milímetros.', 'Respuesta', 26, 0),
	(98, 5, ' Bit, Byte, Kilobyte, Megabyte y Gigabyte.', 'Respuesta', 26, 1),
	(99, 5, ' Metros, Centímetros, Milímetros, Pixels y Pulgadas.', 'Respuesta', 26, 0);

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
) ENGINE=InnoDB AUTO_INCREMENT=329 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.interaccion: ~327 rows (aproximadamente)
DELETE FROM `interaccion`;
INSERT INTO `interaccion` (`id`, `alumno_id`, `detalle_presentacion_actividad_id`, `correcto`) VALUES
	(1, 1, 1, 0),
	(2, 5, 1, 0),
	(3, 2, 1, NULL),
	(4, 3, 1, NULL),
	(5, 4, 1, 0),
	(6, 6, 1, 0),
	(7, 7, 1, 0),
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
	(39, 5, 2, NULL),
	(40, 5, 6, 1),
	(41, 5, 9, NULL),
	(42, 5, 11, 1),
	(43, 5, 13, NULL),
	(44, 5, 15, NULL),
	(45, 5, 14, NULL),
	(46, 5, 17, 1),
	(47, 5, 18, NULL),
	(48, 5, 21, 1),
	(49, 5, 23, NULL),
	(50, 1, 25, 1),
	(51, 5, 25, 0),
	(52, 2, 25, 1),
	(53, 3, 25, 1),
	(54, 9, 25, 0),
	(55, 4, 25, 1),
	(56, 6, 25, 1),
	(57, 8, 25, 0),
	(58, 7, 25, NULL),
	(59, 1, 27, 1),
	(60, 1, 30, 1),
	(61, 1, 31, 1),
	(62, 1, 32, 1),
	(63, 1, 35, 1),
	(64, 1, 36, 1),
	(65, 1, 37, 1),
	(66, 5, 28, 0),
	(67, 5, 30, 0),
	(68, 5, 31, 1),
	(69, 5, 32, 1),
	(70, 5, 34, 0),
	(71, 5, 36, 1),
	(72, 5, 37, 1),
	(73, 2, 27, 1),
	(74, 2, 30, 1),
	(75, 2, 31, 1),
	(76, 2, 32, 1),
	(77, 2, 35, 1),
	(78, 2, 36, 1),
	(79, 2, 37, 1),
	(81, 3, 27, 1),
	(82, 3, 30, 1),
	(83, 3, 31, 1),
	(84, 3, 32, 1),
	(85, 3, 35, 1),
	(86, 3, 36, 0),
	(87, 3, 39, 0),
	(88, 9, 29, 0),
	(89, 9, 30, 1),
	(90, 9, 31, 1),
	(91, 9, 32, 1),
	(92, 9, 35, 1),
	(93, 9, 36, 0),
	(94, 9, 39, 0),
	(95, 4, 27, 1),
	(96, 4, 30, 1),
	(97, 4, 31, 1),
	(98, 4, 32, 1),
	(99, 4, 35, 1),
	(100, 4, 36, 1),
	(101, 4, 37, 1),
	(102, 6, 27, 1),
	(103, 6, 30, 1),
	(104, 6, 31, 1),
	(105, 6, 32, 1),
	(106, 6, 35, 1),
	(107, 6, 36, 0),
	(108, 6, 39, 0),
	(109, 8, 26, 0),
	(110, 8, 30, 1),
	(111, 8, 31, 1),
	(112, 8, 32, 1),
	(113, 8, 35, 1),
	(114, 8, 36, 0),
	(115, 8, 39, 0),
	(116, 7, 2, NULL),
	(117, 7, 6, 0),
	(118, 7, 8, NULL),
	(119, 7, 11, 1),
	(120, 7, 14, NULL),
	(121, 7, 15, NULL),
	(122, 7, 17, 1),
	(123, 7, 18, NULL),
	(124, 7, 21, 1),
	(125, 7, 23, NULL),
	(126, 1, 40, 1),
	(127, 5, 40, 1),
	(128, 2, 40, 1),
	(129, 3, 40, 1),
	(130, 9, 40, 0),
	(131, 4, 40, NULL),
	(132, 6, 40, 1),
	(133, 8, 40, 1),
	(134, 7, 40, 1),
	(135, 1, 41, 1),
	(136, 1, 44, 1),
	(137, 1, 46, 1),
	(138, 1, 48, 1),
	(139, 1, 50, 1),
	(140, 1, 53, 1),
	(141, 1, 57, 1),
	(142, 2, 41, 1),
	(143, 2, 44, 1),
	(144, 2, 46, 1),
	(145, 2, 48, 0),
	(146, 2, 51, 0),
	(147, 2, 53, 1),
	(148, 2, 57, 1),
	(149, 1, 58, 1),
	(150, 5, 58, 1),
	(151, 2, 58, 0),
	(152, 3, 58, 1),
	(153, 9, 58, 1),
	(154, 4, 58, 1),
	(155, 6, 58, 0),
	(156, 8, 58, NULL),
	(157, 7, 58, 0),
	(158, 2, 60, 0),
	(159, 2, 62, 1),
	(160, 2, 64, 1),
	(161, 2, 66, 1),
	(162, 2, 67, 1),
	(163, 2, 68, 1),
	(164, 2, 71, 1),
	(165, 2, 72, 1),
	(166, 2, 74, 1),
	(167, 4, 61, 1),
	(168, 4, 62, 1),
	(169, 4, 64, 1),
	(170, 4, 66, 1),
	(171, 4, 67, 1),
	(172, 4, 68, 1),
	(173, 4, 71, 1),
	(174, 4, 72, 1),
	(175, 4, 74, 1),
	(176, 4, 3, 0),
	(177, 4, 6, 0),
	(178, 4, 7, 0),
	(179, 4, 11, 0),
	(180, 4, 13, 0),
	(181, 4, 15, 1),
	(182, 4, 17, 1),
	(183, 4, 18, 1),
	(184, 4, 21, 1),
	(185, 4, 23, 1),
	(186, 8, 1, 1),
	(187, 8, 5, NULL),
	(188, 8, 6, 1),
	(189, 8, 9, NULL),
	(190, 8, 11, 1),
	(191, 8, 14, NULL),
	(192, 8, 15, NULL),
	(193, 8, 17, 0),
	(194, 8, 19, NULL),
	(195, 8, 21, 1),
	(196, 8, 23, NULL),
	(197, 1, 61, 1),
	(198, 1, 62, 1),
	(199, 1, 64, 1),
	(200, 1, 66, 1),
	(201, 1, 67, 1),
	(202, 1, 68, 1),
	(203, 1, 71, 1),
	(204, 1, 72, 1),
	(205, 1, 74, 1),
	(206, 5, 61, 1),
	(207, 5, 62, 1),
	(208, 5, 64, 1),
	(209, 5, 66, 1),
	(210, 5, 67, 0),
	(211, 5, 69, 0),
	(212, 5, 71, 0),
	(213, 5, 72, 1),
	(214, 5, 73, 0),
	(215, 3, 61, 1),
	(216, 3, 62, 0),
	(217, 3, 63, 0),
	(218, 3, 65, 0),
	(219, 3, 67, 0),
	(220, 3, 69, 0),
	(221, 3, 71, 1),
	(222, 3, 72, 1),
	(223, 3, 74, 1),
	(224, 9, 61, 1),
	(225, 9, 62, 1),
	(226, 9, 64, 1),
	(227, 9, 66, 1),
	(228, 9, 67, 1),
	(229, 9, 68, 1),
	(230, 9, 71, 0),
	(231, 9, 73, 0),
	(232, 9, 74, 1),
	(233, 9, 75, 0),
	(234, 6, 59, 0),
	(235, 6, 62, 0),
	(236, 6, 64, 1),
	(237, 6, 65, 0),
	(238, 6, 67, 0),
	(239, 6, 70, 0),
	(240, 6, 71, 1),
	(241, 6, 72, 1),
	(242, 6, 74, 1),
	(243, 5, 41, 1),
	(244, 5, 44, 1),
	(245, 5, 46, 1),
	(246, 5, 48, 1),
	(247, 5, 50, 1),
	(248, 5, 53, 0),
	(249, 5, 56, 0),
	(250, 3, 41, 1),
	(251, 3, 44, 1),
	(252, 3, 46, 1),
	(253, 3, 48, 1),
	(254, 3, 50, 1),
	(255, 3, 53, 1),
	(256, 3, 57, 1),
	(257, 9, 42, 0),
	(258, 9, 44, 0),
	(259, 9, 45, 0),
	(260, 9, 48, 1),
	(261, 9, 50, 1),
	(262, 9, 53, 0),
	(263, 9, 55, 0),
	(264, 7, 76, 1),
	(265, 7, 77, NULL),
	(266, 7, 81, 0),
	(267, 7, 83, 0),
	(268, 7, 59, 0),
	(269, 7, 62, 1),
	(270, 7, 64, 1),
	(271, 7, 66, 1),
	(272, 7, 67, 1),
	(273, 7, 68, 1),
	(274, 7, 71, 1),
	(275, 7, 72, 1),
	(276, 7, 74, 1),
	(277, 7, 80, 1),
	(278, 7, 86, 1),
	(279, 7, 89, 1),
	(280, 7, 90, 1),
	(281, 7, 92, 0),
	(282, 7, 95, 0),
	(283, 7, 96, 1),
	(284, 7, 98, 1),
	(285, 5, 76, 0),
	(286, 5, 77, 0),
	(287, 5, 81, 0),
	(288, 5, 83, 0),
	(289, 5, 86, 0),
	(290, 5, 88, 0),
	(291, 5, 90, 1),
	(292, 5, 92, 1),
	(293, 5, 93, 1),
	(294, 5, 96, 1),
	(295, 5, 98, 1),
	(296, 8, 76, 1),
	(297, 8, 80, 1),
	(298, 8, 81, 0),
	(299, 8, 82, 0),
	(300, 8, 86, 1),
	(301, 8, 89, 1),
	(302, 8, 90, 1),
	(303, 8, 92, 1),
	(304, 8, 93, 1),
	(305, 8, 96, 0),
	(306, 8, 97, 0),
	(307, 8, 41, 1),
	(308, 8, 44, 0),
	(309, 8, 45, 0),
	(310, 8, 48, 1),
	(311, 8, 50, 1),
	(312, 8, 53, 0),
	(313, 8, 56, 0),
	(314, 7, 41, 1),
	(315, 7, 44, 0),
	(316, 7, 45, 0),
	(317, 7, 48, 1),
	(318, 7, 50, 1),
	(319, 7, 53, 1),
	(320, 7, 57, 1),
	(321, 6, 41, 1),
	(322, 6, 44, 1),
	(323, 6, 45, NULL),
	(324, 6, 46, 1),
	(325, 6, 48, 1),
	(326, 6, 50, 1),
	(327, 6, 53, 1),
	(328, 6, 57, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.presentacion_actividad: ~4 rows (aproximadamente)
DELETE FROM `presentacion_actividad`;
INSERT INTO `presentacion_actividad` (`id`, `usuario_id`, `curso_id`, `estado`, `fecha`, `titulo`, `descripcion`, `tipo`) VALUES
	(1, 3, 1, 'Finalizado', '2022-11-12 23:54:53', 'Introductorio de informática', NULL, 'Cuestionario'),
	(2, 3, 1, 'Finalizado', '2022-11-29 15:54:29', 'CPU', NULL, 'Cuestionario'),
	(3, 3, 1, 'Finalizado', '2022-11-29 20:07:31', 'Programación', NULL, 'Cuestionario'),
	(4, 3, 1, 'Finalizado', '2022-11-29 20:18:13', 'Redes', NULL, 'Cuestionario'),
	(5, 3, 4, 'Finalizado', '2022-11-30 19:59:11', 'Introductorio de informática', NULL, 'Cuestionario');

-- Volcando estructura para tabla tfg.toma_de_asistencia
CREATE TABLE IF NOT EXISTS `toma_de_asistencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `curso_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4BC66AF87CB4A1F` (`curso_id`),
  CONSTRAINT `FK_4BC66AF87CB4A1F` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tfg.toma_de_asistencia: ~1 rows (aproximadamente)
DELETE FROM `toma_de_asistencia`;
INSERT INTO `toma_de_asistencia` (`id`, `curso_id`, `fecha`, `estado`) VALUES
	(1, 1, '2022-11-12 18:41:12', 'Finalizado'),
	(2, 4, '2022-11-30 21:53:58', 'Iniciado');

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
