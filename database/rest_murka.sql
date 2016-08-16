-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Авг 16 2016 г., 18:58
-- Версия сервера: 5.5.25
-- Версия PHP: 5.6.21

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `rest_murka`
--

-- --------------------------------------------------------

--
-- Структура таблицы `mur_matches`
--

CREATE TABLE IF NOT EXISTS `mur_matches` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `time_start` int(10) unsigned NOT NULL,
  `time_end` int(10) unsigned NOT NULL,
  `players_id` varchar(255) NOT NULL,
  `winner_id` int(5) unsigned NOT NULL,
  `match_log` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `mur_matches`
--

INSERT INTO `mur_matches` (`id`, `time_start`, `time_end`, `players_id`, `winner_id`, `match_log`) VALUES
(1, 1471290288, 1471293288, '1,4', 4, '1.f2-f3 e7-e6\n\n2.g2-g4 Фd8-h4×'),
(2, 1471292288, 1471294288, '3,2', 3, '1.f2-f3 e7-e6\n\n2.g2-g4 Фd8-h4×'),
(3, 1471295288, 1471296288, '1,2', 0, '1.f2-f3 e7-e6\n\n2.g2-g4 Фd8-h4×'),
(4, 1471294288, 1471234288, '3,4,2', 0, '1.f2-f3 e7-e6\n\n2.g2-g4 Фd8-h4×');

-- --------------------------------------------------------

--
-- Структура таблицы `mur_players`
--

CREATE TABLE IF NOT EXISTS `mur_players` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `rating` int(4) unsigned NOT NULL,
  `default_rating` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Дамп данных таблицы `mur_players`
--

INSERT INTO `mur_players` (`id`, `name`, `rating`, `default_rating`) VALUES
(1, 'viktor', 1133, 1132),
(2, 'nika', 2343, 2341),
(3, 'gordon', 1768, 1765),
(4, 'vetal', 1968, 1965),
(5, 'grut', 1894, 1894),
(8, 'monika', 2261, 2261);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
