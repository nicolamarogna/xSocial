-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Creato il: Giu 23, 2016 alle 12:24
-- Versione del server: 10.1.13-MariaDB
-- Versione PHP: 5.5.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `social`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `social_albums`
--

CREATE TABLE `social_albums` (
  `id` int(10) UNSIGNED NOT NULL,
  `updated` datetime NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `social_albums`
--

INSERT INTO `social_albums` (`id`, `updated`, `id_user`, `title`) VALUES
(1, '2016-06-09 12:01:46', 1, 'test'),
(2, '2016-06-09 12:05:38', 2, 'test'),
(3, '2016-06-20 19:30:25', 1, 'asf');

-- --------------------------------------------------------

--
-- Struttura della tabella `social_albums_items`
--

CREATE TABLE `social_albums_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `updated` datetime NOT NULL,
  `id_album` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `img` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Struttura della tabella `social_calendar`
--

CREATE TABLE `social_calendar` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `updated` datetime NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `colums` varchar(3) NOT NULL,
  `x1` varchar(255) NOT NULL,
  `x2` varchar(255) NOT NULL,
  `x3` varchar(255) NOT NULL,
  `x4` varchar(255) NOT NULL,
  `x5` varchar(255) NOT NULL,
  `x6` varchar(255) NOT NULL,
  `x7` text NOT NULL,
  `x8` varchar(255) NOT NULL,
  `x9` varchar(255) NOT NULL,
  `x10` varchar(255) NOT NULL,
  `x11` varchar(255) NOT NULL,
  `x12` varchar(255) NOT NULL,
  `x13` varchar(255) NOT NULL,
  `x14` varchar(255) NOT NULL,
  `x15` varchar(255) NOT NULL,
  `x16` varchar(255) NOT NULL,
  `x17` varchar(255) NOT NULL,
  `x18` varchar(255) NOT NULL,
  `x19` varchar(255) NOT NULL,
  `x20` varchar(255) NOT NULL,
  `x21` varchar(255) NOT NULL,
  `x22` varchar(255) NOT NULL,
  `x23` varchar(255) NOT NULL,
  `x24` varchar(255) NOT NULL,
  `x25` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Struttura della tabella `social_calendar_items`
--

CREATE TABLE `social_calendar_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `updated` date NOT NULL,
  `id_calendar` int(10) UNSIGNED NOT NULL,
  `x1` varchar(255) NOT NULL,
  `x2` varchar(255) NOT NULL,
  `x3` varchar(255) NOT NULL,
  `x4` varchar(255) NOT NULL,
  `x5` varchar(255) NOT NULL,
  `x6` varchar(255) NOT NULL,
  `x7` varchar(255) NOT NULL,
  `x8` varchar(255) NOT NULL,
  `x9` varchar(255) NOT NULL,
  `x10` varchar(255) NOT NULL,
  `x11` varchar(255) NOT NULL,
  `x12` varchar(255) NOT NULL,
  `x13` varchar(255) NOT NULL,
  `x14` varchar(255) NOT NULL,
  `x15` varchar(255) NOT NULL,
  `x16` varchar(255) NOT NULL,
  `x17` varchar(255) NOT NULL,
  `x18` varchar(255) NOT NULL,
  `x19` varchar(255) NOT NULL,
  `x20` varchar(255) NOT NULL,
  `x21` varchar(255) NOT NULL,
  `x22` varchar(255) NOT NULL,
  `x23` varchar(255) NOT NULL,
  `x24` varchar(255) NOT NULL,
  `x25` varchar(255) NOT NULL,
  `xpos` tinyint(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Struttura della tabella `social_comments`
--

CREATE TABLE `social_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `updated` datetime NOT NULL,
  `id_comment` int(10) UNSIGNED NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `comment` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `social_confirms`
--

CREATE TABLE `social_confirms` (
  `id` int(10) UNSIGNED NOT NULL,
  `updated` datetime NOT NULL,
  `what` varchar(255) NOT NULL,
  `id_what` int(10) UNSIGNED NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `confirmed` tinyint(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Struttura della tabella `social_events`
--

CREATE TABLE `social_events` (
  `id` int(10) UNSIGNED NOT NULL,
  `updated` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` text NOT NULL,
  `img` varchar(150) NOT NULL,
  `date` date NOT NULL,
  `from_user` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Struttura della tabella `social_isfriend`
--

CREATE TABLE `social_isfriend` (
  `id` int(11) UNSIGNED NOT NULL,
  `updated` datetime NOT NULL,
  `id_user` int(11) UNSIGNED NOT NULL,
  `id_friend` int(11) UNSIGNED NOT NULL,
  `id_group` int(10) UNSIGNED NOT NULL,
  `confirmed` tinyint(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `social_isfriend`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `social_notify`
--

CREATE TABLE `social_notify` (
  `id` int(10) UNSIGNED NOT NULL,
  `updated` datetime NOT NULL,
  `from_user` int(10) UNSIGNED NOT NULL,
  `to_user` int(10) UNSIGNED NOT NULL,
  `id_what` int(10) UNSIGNED NOT NULL,
  `what` varchar(50) NOT NULL,
  `viewed` tinyint(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Struttura della tabella `social_ratings`
--

CREATE TABLE `social_ratings` (
  `id` int(10) UNSIGNED NOT NULL,
  `updated` datetime NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `id_status` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `social_status`
--

CREATE TABLE `social_status` (
  `id` int(10) UNSIGNED NOT NULL,
  `updated` datetime NOT NULL,
  `from_user` int(10) UNSIGNED NOT NULL,
  `to_user` int(10) UNSIGNED NOT NULL,
  `status` text NOT NULL,
  `img` varchar(150) NOT NULL,
  `youtube` varchar(255) NOT NULL,
  `share` tinyint(3) NOT NULL,
  `rating_average` decimal(2,1) NOT NULL DEFAULT '0.0',
  `rating_numbers` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `social_users`
--

CREATE TABLE `social_users` (
  `id` int(15) UNSIGNED NOT NULL,
  `updated` datetime NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `user` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `citta` varchar(255) NOT NULL,
  `livello` tinyint(3) NOT NULL,
  `img` varchar(255) NOT NULL,
  `group_admin` tinyint(3) NOT NULL,
  `birthday` date NOT NULL,
  `group` tinyint(3) NOT NULL,
  `xon` tinyint(3) NOT NULL,
  `email_old` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `social_users`
--

INSERT INTO `social_users` (`id`, `updated`, `nome`, `cognome`, `user`, `password`, `email`, `citta`, `livello`, `img`, `group_admin`, `birthday`, `group`, `xon`, `email_old`) VALUES
(1, '2016-06-15 17:46:48', 'Nicola', 'Marogna', 'demo', 'demo', 'nikuniku@tiscali.it', 'Cagliari', 1, 'corso_coding.jpg', 0, '0000-00-00', 0, 1, ''),
(2, '2016-06-09 10:45:45', 'Stefano', 'Mura', 'demo1', 'demo1', 'stemura@gmail.com', 'Cagliari', 1, '2010_10_19_10_37_11.665.jpg', 0, '0000-00-00', 0, 1, '');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `social_albums`
--
ALTER TABLE `social_albums`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `social_albums_items`
--
ALTER TABLE `social_albums_items`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `social_calendar`
--
ALTER TABLE `social_calendar`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `social_calendar_items`
--
ALTER TABLE `social_calendar_items`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `social_comments`
--
ALTER TABLE `social_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `social_confirms`
--
ALTER TABLE `social_confirms`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `social_events`
--
ALTER TABLE `social_events`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `social_isfriend`
--
ALTER TABLE `social_isfriend`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `social_notify`
--
ALTER TABLE `social_notify`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `social_ratings`
--
ALTER TABLE `social_ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `social_status`
--
ALTER TABLE `social_status`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `social_users`
--
ALTER TABLE `social_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `social_albums`
--
ALTER TABLE `social_albums`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT per la tabella `social_albums_items`
--
ALTER TABLE `social_albums_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT per la tabella `social_calendar`
--
ALTER TABLE `social_calendar`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT per la tabella `social_calendar_items`
--
ALTER TABLE `social_calendar_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT per la tabella `social_comments`
--
ALTER TABLE `social_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;
--
-- AUTO_INCREMENT per la tabella `social_confirms`
--
ALTER TABLE `social_confirms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT per la tabella `social_events`
--
ALTER TABLE `social_events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT per la tabella `social_isfriend`
--
ALTER TABLE `social_isfriend`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT per la tabella `social_notify`
--
ALTER TABLE `social_notify`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;
--
-- AUTO_INCREMENT per la tabella `social_ratings`
--
ALTER TABLE `social_ratings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=337;
--
-- AUTO_INCREMENT per la tabella `social_status`
--
ALTER TABLE `social_status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=557;
--
-- AUTO_INCREMENT per la tabella `social_users`
--
ALTER TABLE `social_users`
  MODIFY `id` int(15) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
