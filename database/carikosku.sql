-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2026 at 08:45 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kos_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `kos_id`, `created_at`) VALUES
(1, 7, 1, '2026-05-24 11:10:45'),
(5, 7, 2, '2026-06-06 14:37:55');

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
  `Foto_KTP` varchar(255) DEFAULT NULL,
  `Dokumen_kepemilikan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kos`
--

INSERT INTO `kos` (`id`, `user_id`, `nama_kos`, `alamat`, `kampus_terdekat`, `harga`, `gender`, `status`, `fasilitas`, `jam_malam`, `rating`, `terverifikasi`, `Foto_KTP`, `Dokumen_kepemilikan`, `created_at`, `lat`, `lng`) VALUES
(1, 5, 'Kos Ayu', '111 Victoria St, Toronto, ON M5C 2M6, Canada', 'Universitas Mataram (UNRAM)', 1000000, 'putri', 'tersedia', 'WiFi, AC, Kulkas, Mesin cuci, dapur pribadi, kamar mandi pribadi', '', 5, 1, NULL, NULL, '2026-05-16 02:49:19', NULL, NULL),
(2, 8, 'kos neo', 'jl.majapahit', 'Universitas Mataram (UNRAM)', 650000, 'campur', 'tersedia', 'WiFi, Kipas angin, kasur, meja belajar, lemari, kamar mandi dalam', '', 0, 1, NULL, 'dokumen_1780727557_1382.jpeg', '2026-06-06 06:32:37', -8.59144520, 116.07864190);

-- --------------------------------------------------------

--
-- Table structure for table `kos_foto`
--

CREATE TABLE `kos_foto` (
  `id` int(11) NOT NULL,
  `kos_id` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kos_id` int(11) DEFAULT NULL,
  `report_id` int(11) DEFAULT NULL,
  `pesan` text NOT NULL,
  `tipe` enum('peringatan','info','selesai') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id`, `user_id`, `kos_id`, `report_id`, `pesan`, `tipe`, `is_read`, `created_at`) VALUES
(1, 5, 1, 1, 'Ada laporan baru pada kos \"Kos Ayu\" yang perlu ditinjau.', 'info', 1, '2026-06-06 14:12:40'),
(2, 7, 1, 1, 'Pemilik kos telah memberikan tanggapan terhadap laporan Anda.', 'info', 0, '2026-06-06 14:25:59');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `kos_id` int(11) NOT NULL,
  `alasan` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('pending','ditinjau','selesai') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `level_peringatan` int(11) DEFAULT 0,
  `tanggapan_admin` text DEFAULT NULL,
  `pembelaan_pemilik` text DEFAULT NULL,
  `foto_pembelaan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `reporter_id`, `kos_id`, `alasan`, `keterangan`, `status`, `created_at`, `level_peringatan`, `tanggapan_admin`, `pembelaan_pemilik`, `foto_pembelaan`) VALUES
(1, 7, 1, 'Informasi tidak sesuai kenyataan', 'kosnya kotor\r\n', 'pending', '2026-06-06 14:12:40', 0, NULL, 'hoax', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `kos_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `komentar` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `kos_id`, `user_id`, `rating`, `komentar`, `created_at`, `foto`) VALUES
(1, 1, 7, 5, 'KOSNYA BGS BGSBGS BGTBGTBGT', '2026-06-06 14:13:15', NULL);

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
(6, 'Juli', 'juli@admin.com', '4c37dbfae76a9a48544d7248127d2d29', 'admin', NULL, '2026-05-16 02:54:53'),
(7, 'jinu', 'jinu@gmail.com', '9443563aa84a4291e2a1a095c2d066b7', 'penyewa', NULL, '2026-05-19 14:02:00'),
(8, 'jeamin', 'jaemin@gmail.com', '549992bc976878e0449bc0bcf4326fd4', 'pemilik', NULL, '2026-06-06 06:26:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_fav` (`user_id`,`kos_id`),
  ADD KEY `fk_favorites_kos` (`kos_id`);

--
-- Indexes for table `kos`
--
ALTER TABLE `kos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kos_foto`
--
ALTER TABLE `kos_foto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_kos_foto_kos` (`kos_id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifikasi_user` (`user_id`),
  ADD KEY `fk_notifikasi_kos` (`kos_id`),
  ADD KEY `fk_notifikasi_report` (`report_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reports_user` (`reporter_id`),
  ADD KEY `fk_reports_kos` (`kos_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_review` (`kos_id`,`user_id`),
  ADD KEY `fk_reviews_user` (`user_id`);

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
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kos`
--
ALTER TABLE `kos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kos_foto`
--
ALTER TABLE `kos_foto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_kos` FOREIGN KEY (`kos_id`) REFERENCES `kos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kos`
--
ALTER TABLE `kos`
  ADD CONSTRAINT `kos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kos_foto`
--
ALTER TABLE `kos_foto`
  ADD CONSTRAINT `fk_kos_foto_kos` FOREIGN KEY (`kos_id`) REFERENCES `kos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `fk_notifikasi_kos` FOREIGN KEY (`kos_id`) REFERENCES `kos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notifikasi_report` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notifikasi_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_reports_kos` FOREIGN KEY (`kos_id`) REFERENCES `kos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reports_user` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_kos` FOREIGN KEY (`kos_id`) REFERENCES `kos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
