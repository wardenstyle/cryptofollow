-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 25 mars 2025 à 11:17
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `crypto_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `indicators`
--

DROP TABLE IF EXISTS `indicators`;
CREATE TABLE IF NOT EXISTS `indicators` (
  `id` int NOT NULL AUTO_INCREMENT,
  `crypto` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `date` datetime NOT NULL,
  `id_u` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_u` (`id_u`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `indicators`
--

INSERT INTO `indicators` (`id`, `crypto`, `price`, `date`, `id_u`) VALUES
(1, 'bitcoin', 84170.00, '2025-03-22 13:09:56', 1),
(2, 'bitcoin', 84173.00, '2025-03-22 13:25:37', 1),
(3, 'bitcoin', 84125.00, '2025-03-22 14:13:16', 1),
(4, 'bitcoin', 84131.00, '2025-03-22 14:15:11', 1),
(5, 'bitcoin', 84973.00, '2025-03-23 14:38:41', 1),
(6, 'theta-token', 0.88, '2025-03-16 16:52:25', 1),
(7, 'injective-protocol', 9.96, '2025-03-16 16:54:31', 1),
(8, 'quant-network', 76.30, '2025-03-23 16:56:02', 1),
(9, 'quant-network', 88.30, '2025-02-15 16:56:02', 1),
(10, 'injective-protocol', 12.75, '2025-01-12 17:00:05', 1),
(11, 'injective-protocol', 18.73, '2024-12-01 17:00:05', 1),
(12, 'quant-network', 100.00, '2025-03-01 17:03:43', 1),
(13, 'quant-network', 108.00, '2025-02-23 17:03:43', 1),
(14, 'theta-token', 0.97, '2025-03-25 11:26:34', 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`) VALUES
(1, 'fortil@group.com', '$2y$10$mopWeoInEXKMGDovQNCJoudoNXhisi5NIBousA/jhKhkFlZ0CdgPK');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
