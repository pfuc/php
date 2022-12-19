SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `libreria_db`
--
CREATE DATABASE IF NOT EXISTS `libreria_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `libreria_db`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alquileres`
--

DROP TABLE IF EXISTS `alquileres`;
CREATE TABLE IF NOT EXISTS `alquileres` (
  `cliente_dni` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `libro_referencia` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `dia_alquilado` datetime NOT NULL,
  `dia_devuelto` datetime DEFAULT NULL,
  UNIQUE KEY `cliente_dni` (`cliente_dni`,`libro_referencia`),
  KEY `libro_referencia` (`libro_referencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `alquileres`
--

INSERT INTO `alquileres` (`cliente_dni`, `libro_referencia`, `dia_alquilado`, `dia_devuelto`) VALUES
('12345678A', '000X', '2022-12-19 16:48:21', '2022-12-19 15:37:40'),
('12345678A', '001X', '2022-12-18 22:12:46', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `tipo` varchar(32) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `tipo`) VALUES
('AV', 'Aventuras'),
('CO', 'Comedia'),
('DR', 'Drama'),
('PO', 'Policíaca');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `dni` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `nombre` varchar(32) COLLATE utf8_spanish_ci NOT NULL,
  `apellidos` varchar(64) COLLATE utf8_spanish_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `telefono` varchar(13) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`dni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`dni`, `nombre`, `apellidos`, `email`, `telefono`) VALUES
('12345678A', 'Pablo', 'Urones', 'correo1@correo.es', '985123456');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

DROP TABLE IF EXISTS `libros`;
CREATE TABLE IF NOT EXISTS `libros` (
  `referencia` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `titulo` varchar(32) COLLATE utf8_spanish_ci NOT NULL,
  `categoria_id` varchar(64) COLLATE utf8_spanish_ci NOT NULL,
  `escritor` varchar(64) COLLATE utf8_spanish_ci NOT NULL,
  `personaje_principal` varchar(64) COLLATE utf8_spanish_ci NOT NULL,
  `portada` varchar(64) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ha_ganado_planeta` int(11) NOT NULL,
  PRIMARY KEY (`referencia`),
  KEY `categoria_id` (`categoria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`referencia`, `titulo`, `categoria_id`, `escritor`, `personaje_principal`, `portada`, `ha_ganado_planeta`) VALUES
('000X', 'Nada', 'DR', 'Carmen Laforet', 'Andrea', 'portadas/nada.jpg', 0),
('001X', 'La familia de Pascual Duarte', 'DR', 'Camilo Jóse Cela', 'Pascual', 'portadas/pascual.jpg', 0),
('002X', 'El fuego invisible', 'DR', 'Javier Sierra', 'David', 'portadas/fuego.jpg', 1),
('003X', 'Contra el viento', 'AV', 'Ángeles Caso', 'São', 'portadas/viento.jpg', 1);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alquileres`
--
ALTER TABLE `alquileres`
  ADD CONSTRAINT `alquileres_ibfk_1` FOREIGN KEY (`cliente_dni`) REFERENCES `clientes` (`dni`),
  ADD CONSTRAINT `alquileres_ibfk_2` FOREIGN KEY (`libro_referencia`) REFERENCES `libros` (`referencia`);

--
-- Filtros para la tabla `libros`
--
ALTER TABLE `libros`
  ADD CONSTRAINT `libros_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;