DROP TABLE IF EXISTS `indicators`;
CREATE TABLE IF NOT EXISTS `indicators` (
  `id` int NOT NULL AUTO_INCREMENT,
  `crypto` varchar(50) NOT NULL,
  `price` decimal(10,5) NOT NULL,
  `date` datetime NOT NULL,
  `id_u` int NOT NULL,
  `qte` decimal(10,5) NOT NULL,
  `type` varchar(10) NOT NULL, 
  PRIMARY KEY (`id`),
  KEY `id_u` (`id_u`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `indicators`
--

INSERT INTO `indicators` (`id`, `crypto`, `price`, `date`, `id_u`, `qte`,`type`) VALUES

(21, 'bitcoin', 87762.00, '2025-03-25 16:55:31', 0, 0.00,'Achat'),
(23, 'bitcoin', 87769.00, '2025-03-25 17:00:16', 0, 0.00,'Achat'),
(29, 'bitcoin', 88113.00, '2025-03-26 08:12:34', 1, 0.00,'Achat'),


-- --------------------------------------------------------
-- UPDATE indicators
-- SET type = 'Achat';
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
(1, 'admin@allobobo.fr', '$2y$10$mopWeoInEXKMGDovQNCJoudoNXhisi5NIBousA/jhKhkFlZ0CdgPK'),
(2, 'dada@group.com', '$2y$10$dYtB1uqp/PPZ8SFuYSJVqOOt0DvNuZXcEDEwuaeilVZl4zVRx6cEO'),
(3, 'baba@group.com', '$2y$10$yNiKG0Mh/4954VqMg4P1fuON56lMApoffSbSj7lDqjWtQR9/Q4Ml2'),
(4, 'nana@group.com', '$2y$10$mx.C.RxnCHbIIf32IIBV1uSlToA5ssA66NaPZExZzt0VtnGylsFGi'),
(5, 'fofo@group.com', '$2y$10$o3PN2TcwQs77lYj50SfwbObiLNMLWvRqxZABregECmHS5VEFf5JX.'),
(6, 'lolo@group.com', '$2y$10$p9P.3aY/gEnlhurWxTrzH.y0w5Rr0k1Zkx1.HAhGkINbeNHOyi8oa'),
(7, 'papa@group.com', '$2y$10$ZPZ79kbiDoUQwgvlZah6seH5pky.s34EV.uTwixGrTLpl78k5y1XG'),
(8, 'qaqa@group.com', '$2y$10$c7u/fCpUq3EWXIdCylendeIbXIRUFDVWlkJfvhV9sHeI8NwDUFF/C'),
(9, 'pipi@group.com', '$2y$10$2n.AmNSfTZrgQv7T5gI32.Q104ew0F60.5EL9KszIs4LDeATBQGmi');
COMMIT;

CREATE TABLE alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_indicator INT NOT NULL,
    target_price DECIMAL(10, 2) NOT NULL,
    type ENUM('Achat', 'Vente') NOT NULL,
    content VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    percentage_ VARCHAR(2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_indicator) REFERENCES indicators(id) ON DELETE CASCADE
);
