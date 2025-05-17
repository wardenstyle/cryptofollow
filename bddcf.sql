-- Nettoyage préalable (sécurisé, si tu repars à zéro)
DROP TABLE IF EXISTS alerts;
DROP TABLE IF EXISTS indicators;
DROP TABLE IF EXISTS users;

-- Création table `users`
CREATE TABLE users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  email VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création table `indicators`
CREATE TABLE indicators (
  id INT(11) NOT NULL AUTO_INCREMENT,
  crypto VARCHAR(50) NOT NULL,
  price DECIMAL(10,5) NOT NULL,
  date DATETIME NOT NULL,
  id_u INT(11) NOT NULL,
  qte DECIMAL(10,5) NOT NULL,
  type VARCHAR(10) NOT NULL,
  PRIMARY KEY (id),
  KEY id_u (id_u)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création table `alerts`
CREATE TABLE alerts (
  id INT(11) NOT NULL AUTO_INCREMENT,
  id_indicator INT(11) NOT NULL,
  target_price DECIMAL(10,2) NOT NULL,
  type ENUM('Achat','Vente') NOT NULL,
  content VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  percentage_ VARCHAR(4) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  sent_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY id_indicator (id_indicator),
  CONSTRAINT fk_alerts_indicators FOREIGN KEY (id_indicator)
    REFERENCES indicators(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création table `crypto`
CREATE TABLE crypto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_api VARCHAR(50) NOT NULL
);

-- Données pour `users`
INSERT INTO users (id, email, password) VALUES
(1, 'admin@allobobo.fr', '$2y$10$5.tEprp1vQxlzT0xydj3ieVBGEWavYFbHIdpT0N8jiomXyAq1jrGS'),
(2, 'suivi@fortil.group', '$2y$10$GqHioxBVMXAs/08/BwQAtezDHcg9s/oeT14hOq7eugh3xbfbCVgrG'),
(3, 'test@fortil.group', '$2y$10$ZQtQx1wDgHTiVwnZcIRbtOB/SBuj9x5/yyRzOysEYwBm0ls44hkVy');

-- Données pour `indicators`
INSERT INTO indicators (id, crypto, price, date, id_u, qte, type) VALUES
(1, 'bitcoin', 86944.00000, '2025-03-26 20:11:42', 1, 0.00300, 'Achat'),
(3, 'theta-token', 1.01000, '2025-02-27 00:03:27', 1, 25.00000, 'Achat'),
(4, 'injective-protocol', 10.48000, '2025-03-27 00:20:47', 1, 2.00000, 'Achat'),
(5, 'quant-network', 72.41000, '2025-03-28 10:43:06', 2, 1.00000, 'Achat'),
(7, 'bitcoin', 83731.00000, '2025-03-28 20:59:22', 1, 0.00000, 'Achat'),
(8, 'injective-protocol', 8.93000, '2025-03-29 10:49:27', 2, 1.00000, 'Achat'),
(9, 'quant-network', 68.68000, '2025-03-29 10:51:36', 2, 1.00000, 'Achat'),
(10, 'theta-token', 0.80000, '2025-03-11 12:01:51', 2, 25.00000, 'Achat'),
(11, 'theta-token', 0.89000, '2025-03-10 12:01:51', 2, 10.00000, 'Vente'),
(12, 'theta-token', 0.98000, '2025-03-09 12:07:39', 2, 50.00000, 'Achat'),
(13, 'theta-token', 1.04000, '2025-03-04 12:07:39', 2, 150.00000, 'Achat'),
(15, 'theta-token', 1.09000, '2025-02-26 12:07:39', 2, 125.00000, 'Achat'),
(16, 'quant-network', 100.00000, '2025-03-03 12:25:14', 2, 1.00000, 'Vente'),
(17, 'quant-network', 90.00000, '2025-03-04 12:25:14', 2, 1.00000, 'Achat'),
(19, 'quant-network', 81.00000, '2025-03-07 12:29:53', 2, 1.00000, 'Achat'),
(20, 'quant-network', 76.00000, '2025-03-10 12:29:53', 2, 3.00000, 'Achat'),
(21, 'injective-protocol', 17.94000, '2025-01-28 12:30:54', 2, 1.00000, 'Achat'),
(22, 'injective-protocol', 15.40000, '2025-02-03 12:30:54', 2, 5.00000, 'Achat'),
(23, 'injective-protocol', 13.42000, '2025-02-08 12:46:11', 2, 1.00000, 'Vente'),
(24, 'injective-protocol', 13.11000, '2025-02-25 12:48:04', 2, 2.00000, 'Achat'),
(25, 'theta-token', 0.69981, '2025-04-06 21:55:37', 2, 40.00000, 'Achat'),
(26, 'quant-network', 65.05000, '2025-04-06 22:04:05', 2, 1.00000, 'Achat'),
(27, 'injective-protocol', 6.50000, '2025-04-07 07:36:33', 2, 1.00000, 'Achat'),
(28, 'quant-network', 60.59000, '2025-04-07 07:39:26', 2, 1.00000, 'Achat'),
(29, 'quant-network', 59.99000, '2025-04-08 23:10:48', 2, 1.00000, 'Achat'),
(31, 'bitcoin', 83250.00000, '2025-04-13 21:08:40', 3, 0.00500, 'Achat');

-- Données pour `alerts`
INSERT INTO alerts (id, id_indicator, target_price, type, content, email, percentage_, created_at, sent_at) VALUES
(1, 25, 0.84, 'Vente', 'Il est temps de vendre', 'suivi@fortil.group', '20', '2025-04-15 18:21:44', NULL);

-- Données pour `crypto`
INSERT INTO crypto (id_api) VALUES 
('bitcoin'),
('theta-token'),
('injective-protocol'),
('quant-network');

-- Auto-incrément
ALTER TABLE alerts AUTO_INCREMENT = 2;
ALTER TABLE indicators AUTO_INCREMENT = 32;
ALTER TABLE users AUTO_INCREMENT = 4;
