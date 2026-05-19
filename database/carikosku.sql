-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2026 at 04:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `carikosku`
--

-- --------------------------------------------------------

--
-- Table structure for table `kos`
--

CREATE TABLE `kos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_kos` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `kampus_terdekat` varchar(100) DEFAULT NULL,
  `harga` int(11) NOT NULL,
  `gender` enum('putra','putri','campur') NOT NULL,
  `status` enum('tersedia','hampir_penuh','penuh') DEFAULT 'tersedia',
  `fasilitas` text DEFAULT NULL,
  `jam_malam` varchar(10) DEFAULT NULL,
  `rating` float DEFAULT 0,
  `terverifikasi` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kos`
--

INSERT INTO `kos` (`id`, `user_id`, `nama_kos`, `alamat`, `kampus_terdekat`, `harga`, `gender`, `status`, `fasilitas`, `jam_malam`, `rating`, `terverifikasi`, `created_at`) VALUES
(1, 5, 'Kos Ayu', '111 Victoria St, Toronto, ON M5C 2M6, Canada', 'Universitas Mataram (UNRAM)', 1, 'putri', 'tersedia', 'WiFi, AC, Kulkas, Mesin cuci, dapur pribadi, kamar mandi pribadi', '', 0, 1, '2026-05-16 02:49:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('penyewa','pemilik','admin') NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `no_hp`, `created_at`) VALUES
(3, 'Admin Juli', 'admin@carikosku.com', '4c37dbfae76a9a48544d7248127d2d29', 'admin', NULL, '2026-05-12 04:50:59'),
(4, 'dita ayu julita', 'dita@gmail.com', '29c65f781a1068a41f735e1b092546de', 'penyewa', NULL, '2026-05-16 02:43:47'),
(5, 'ayu', 'juli@gmail.com', '4c37dbfae76a9a48544d7248127d2d29', 'pemilik', NULL, '2026-05-16 02:45:53'),
(6, 'Juli', 'juli@admin.com', '4c37dbfae76a9a48544d7248127d2d29', 'admin', NULL, '2026-05-16 02:54:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kos`
--
ALTER TABLE `kos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kos`
--
ALTER TABLE `kos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kos`
--
ALTER TABLE `kos`
  ADD CONSTRAINT `kos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
