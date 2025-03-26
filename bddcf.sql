DROP TABLE IF EXISTS `indicators`;
CREATE TABLE IF NOT EXISTS `indicators` (
  `id` int NOT NULL AUTO_INCREMENT,
  `crypto` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `date` datetime NOT NULL,
  `id_u` int NOT NULL,
  `qte` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_u` (`id_u`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `indicators`
--

INSERT INTO `indicators` (`id`, `crypto`, `price`, `date`, `id_u`, `qte`) VALUES
(7, 'injective-protocol', 9.96, '2025-03-16 16:54:31', 1, 0.00),
(8, 'quant-network', 76.30, '2025-03-23 16:56:02', 1, 0.00),
(9, 'quant-network', 88.30, '2025-02-15 16:56:02', 1, 0.00),
(34, 'theta-token', 1.00, '2025-03-26 14:20:10', 1, 10.00),
(18, 'bitcoin', 87906.00, '2025-03-25 16:42:28', 0, 0.00),
(19, 'theta-token', 0.98, '2025-03-25 16:50:09', 0, 0.00),
(20, 'injective-protocol', 10.63, '2025-03-25 16:52:59', 0, 0.00),
(21, 'bitcoin', 87762.00, '2025-03-25 16:55:31', 0, 0.00),
(22, 'theta-token', 0.97, '2025-03-25 16:58:00', 0, 0.00),
(23, 'bitcoin', 87769.00, '2025-03-25 17:00:16', 0, 0.00),
(24, 'quant-network', 78.25, '2025-03-25 17:13:02', 0, 0.00),
(25, 'bitcoin', 88025.00, '2025-03-25 17:13:18', 0, 0.00),
(26, 'theta-token', 0.98, '2025-03-25 17:13:57', 1, 0.00),
(27, 'injective-protocol', 10.65, '2025-03-25 17:15:21', 1, 0.00),
(28, 'theta-token', 1.02, '2025-03-26 08:00:12', 1, 0.00),
(29, 'bitcoin', 88113.00, '2025-03-26 08:12:34', 1, 0.00),
(30, 'theta-token', 1.02, '2025-03-26 08:25:19', 1, 0.00),
(31, 'theta-token', 1.02, '2025-03-26 08:30:37', 1, 0.00),
(32, 'theta-token', 1.02, '2025-03-26 10:52:22', 1, 0.00),
(33, 'theta-token', 0.00, '2025-03-26 09:55:03', 1, 0.00);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`) VALUES
(1, 'fortil@group.com', '$2y$10$mopWeoInEXKMGDovQNCJoudoNXhisi5NIBousA/jhKhkFlZ0CdgPK'),
(2, 'dada@group.com', '$2y$10$dYtB1uqp/PPZ8SFuYSJVqOOt0DvNuZXcEDEwuaeilVZl4zVRx6cEO'),
(3, 'baba@group.com', '$2y$10$yNiKG0Mh/4954VqMg4P1fuON56lMApoffSbSj7lDqjWtQR9/Q4Ml2'),
(4, 'nana@group.com', '$2y$10$mx.C.RxnCHbIIf32IIBV1uSlToA5ssA66NaPZExZzt0VtnGylsFGi'),
(5, 'fofo@group.com', '$2y$10$o3PN2TcwQs77lYj50SfwbObiLNMLWvRqxZABregECmHS5VEFf5JX.'),
(6, 'lolo@group.com', '$2y$10$p9P.3aY/gEnlhurWxTrzH.y0w5Rr0k1Zkx1.HAhGkINbeNHOyi8oa'),
(7, 'papa@group.com', '$2y$10$ZPZ79kbiDoUQwgvlZah6seH5pky.s34EV.uTwixGrTLpl78k5y1XG'),
(8, 'qaqa@group.com', '$2y$10$c7u/fCpUq3EWXIdCylendeIbXIRUFDVWlkJfvhV9sHeI8NwDUFF/C'),
(9, 'pipi@group.com', '$2y$10$2n.AmNSfTZrgQv7T5gI32.Q104ew0F60.5EL9KszIs4LDeATBQGmi');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
