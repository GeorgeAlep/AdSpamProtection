-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 11. Okt 2023 um 17:13
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
-- Datenbank: `ad_protection_db_new`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `myprefix_admins`
--

CREATE TABLE `myprefix_admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `hashed_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `myprefix_admins`
--

INSERT INTO `myprefix_admins` (`id`, `username`, `hashed_password`) VALUES
(1, 'admin', '$2y$10$DKkHfQaCGFXUrF//vL8dEO5Jl8sGKLxcYBe8jZ465TgTZbcd3utv2');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `myprefix_blocked_ips_table`
--

CREATE TABLE `myprefix_blocked_ips_table` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `block_until` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ad_unit_id` varchar(255) DEFAULT NULL,
  `fingerprint` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `myprefix_clicks_table`
--

CREATE TABLE `myprefix_clicks_table` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `ad_unit_id` varchar(255) NOT NULL,
  `fingerprint` varchar(255) DEFAULT NULL,
  `clicked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ad_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `myprefix_permanent_blocks`
--

CREATE TABLE `myprefix_permanent_blocks` (
  `id` int(11) NOT NULL,
  `ip_range` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `myprefix_settings`
--

CREATE TABLE `myprefix_settings` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `myprefix_settings`
--

INSERT INTO `myprefix_settings` (`id`, `name`, `value`) VALUES
(6, 'ad_block_mode', 'single'),
(7, 'adProtection_clickLimit', '3'),
(8, 'adProtection_timeFrame', '20 SECOND'),
(9, 'adProtection_blockDuration', '5 SECOND'),
(10, 'adProtection_fingerprintjsEnabled', '1');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `myprefix_admins`
--
ALTER TABLE `myprefix_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indizes für die Tabelle `myprefix_blocked_ips_table`
--
ALTER TABLE `myprefix_blocked_ips_table`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`,`ad_unit_id`);

--
-- Indizes für die Tabelle `myprefix_clicks_table`
--
ALTER TABLE `myprefix_clicks_table`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `myprefix_permanent_blocks`
--
ALTER TABLE `myprefix_permanent_blocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_range` (`ip_range`);

--
-- Indizes für die Tabelle `myprefix_settings`
--
ALTER TABLE `myprefix_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `myprefix_admins`
--
ALTER TABLE `myprefix_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `myprefix_blocked_ips_table`
--
ALTER TABLE `myprefix_blocked_ips_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT für Tabelle `myprefix_clicks_table`
--
ALTER TABLE `myprefix_clicks_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=438;

--
-- AUTO_INCREMENT für Tabelle `myprefix_permanent_blocks`
--
ALTER TABLE `myprefix_permanent_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT für Tabelle `myprefix_settings`
--
ALTER TABLE `myprefix_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
