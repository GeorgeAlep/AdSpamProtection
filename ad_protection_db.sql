-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 10. Okt 2023 um 18:28
-- Server-Version: 10.4.28-MariaDB
-- PHP-Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `ad_protection_db`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `hashed_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `admins`
--

INSERT INTO `admins` (`id`, `username`, `hashed_password`) VALUES
(1, 'admin', '$2y$10$.fppyR6HfIXLk6hPQHx/AOnSwFwAW5N/Su04/Rllg5m9mV.0q7Qp2');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blocked_ips_table`
--

CREATE TABLE `blocked_ips_table` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `block_until` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ad_unit_id` varchar(255) DEFAULT NULL,
  `fingerprint` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `blocked_ips_table`
--

INSERT INTO `blocked_ips_table` (`id`, `ip_address`, `block_until`, `ad_unit_id`, `fingerprint`) VALUES
(131, '::1', '2023-10-10 16:28:04', 'adUnit1', '0bfbaa795e52ceee684c031b8deecfc9');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `clicks_table`
--

CREATE TABLE `clicks_table` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `ad_unit_id` varchar(255) NOT NULL,
  `fingerprint` varchar(255) DEFAULT NULL,
  `clicked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ad_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `permanent_blocks`
--

CREATE TABLE `permanent_blocks` (
  `id` int(11) NOT NULL,
  `ip_range` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `permanent_blocks`
--

INSERT INTO `permanent_blocks` (`id`, `ip_range`) VALUES
(23, 'a');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(6, 'ad_block_mode', 'all'),
(7, 'adProtection_clickLimit', '3'),
(8, 'adProtection_timeFrame', '20 SECOND'),
(9, 'adProtection_blockDuration', '10 SECOND'),
(10, 'adProtection_fingerprintjsEnabled', '1');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indizes für die Tabelle `blocked_ips_table`
--
ALTER TABLE `blocked_ips_table`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`,`ad_unit_id`);

--
-- Indizes für die Tabelle `clicks_table`
--
ALTER TABLE `clicks_table`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `permanent_blocks`
--
ALTER TABLE `permanent_blocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_range` (`ip_range`);

--
-- Indizes für die Tabelle `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `blocked_ips_table`
--
ALTER TABLE `blocked_ips_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT für Tabelle `clicks_table`
--
ALTER TABLE `clicks_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;

--
-- AUTO_INCREMENT für Tabelle `permanent_blocks`
--
ALTER TABLE `permanent_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT für Tabelle `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
