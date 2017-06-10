-- phpMyAdmin SQL Dump
-- version 3.5.8
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 24-06-2014 a las 19:45:17
-- Versión del servidor: 5.1.69
-- Versión de PHP: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `huerin`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cron_class`
--

CREATE TABLE IF NOT EXISTS `cron_class` (
  `idCron` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL,
  `bloque` int(11) NOT NULL,
  `ultimoBloque` int(11) NOT NULL,
  PRIMARY KEY (`idCron`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `cron_class`
--

INSERT INTO `cron_class` (`idCron`, `fecha`, `bloque`, `ultimoBloque`) VALUES
(1, '2014-06-24 17:49:59', 2000, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
