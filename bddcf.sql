CREATE TABLE indicators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    crypto VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    date DATETIME NOT NULL
);

INSERT INTO `indicators` (`id`, `crypto`, `price`, `date`) VALUES
(1, 'bitcoin', 84170.00, '2025-03-22 13:09:56'),
(2, 'bitcoin', 84173.00, '2025-03-22 13:25:37'),
(3, 'bitcoin', 84125.00, '2025-03-22 14:13:16'),
(4, 'bitcoin', 84131.00, '2025-03-22 14:15:11'),
(5, 'bitcoin', 84973.00, '2025-03-23 14:38:41'),
(6, 'theta-token', 0.88, '2025-03-16 16:52:25'),
(7, 'injective-protocol', 9.96, '2025-03-16 16:54:31'),
(8, 'quant-network', 76.30, '2025-03-23 16:56:02'),
(9, 'quant-network', 88.30, '2025-02-15 16:56:02'),
(10, 'injective-protocol', 12.75, '2025-01-12 17:00:05'),
(11, 'injective-protocol', 18.73, '2024-12-01 17:00:05'),
(12, 'quant-network', 100.00, '2025-03-01 17:03:43'),
(13, 'quant-network', 108.00, '2025-02-23 17:03:43');
COMMIT;
