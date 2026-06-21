-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2026 at 01:38 AM
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
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kos_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `dokumen_kepemilikan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kos`
--

INSERT INTO `kos` (`id`, `user_id`, `nama_kos`, `alamat`, `kampus_terdekat`, `harga`, `gender`, `status`, `fasilitas`, `jam_malam`, `rating`, `terverifikasi`, `dokumen_kepemilikan`, `created_at`, `lat`, `lng`) VALUES
(4, 10, 'Kos NCT', 'Jl. Pelor Mas Raya No.II, Kekalik Jaya, Kec. Sekarbela, Kota Mataram', 'Universitas Teknologi Mataram', 700000, 'putra', 'tersedia', 'WiFi, dapur pribadi, kamar mandi pribadi', '', 0, 1, 'dokumen_1780733036_5127.jpg', '2026-06-06 08:03:56', -8.59262170, 116.09257830);

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

--
-- Dumping data for table `kos_foto`
--

INSERT INTO `kos_foto` (`id`, `kos_id`, `nama_file`, `is_primary`, `created_at`) VALUES
(1, 2, 'kos_2_1779603025_0.jpg', 1, '2026-05-24 14:10:25'),
(2, 2, 'kos_2_1779603025_1.jpg', 0, '2026-05-24 14:10:25'),
(3, 2, 'kos_2_1779603025_2.jpg', 0, '2026-05-24 14:10:25'),
(4, 2, 'kos_2_1779603025_3.jpg', 0, '2026-05-24 14:10:25'),
(5, 2, 'kos_2_1779603025_4.jpg', 0, '2026-05-24 14:10:25'),
(6, 3, 'kos_3_1780732292_0.jpg', 1, '2026-06-06 15:51:32'),
(7, 4, 'kos_4_1780733036_0.jpg', 1, '2026-06-06 16:03:56'),
(8, 5, 'kos_5_1780733389_0.jpg', 1, '2026-06-06 16:09:49'),
(9, 6, 'kos_6_1781762320_0.jpg', 1, '2026-06-18 13:58:40'),
(10, 7, 'kos_7_1781830778_0.jpg', 1, '2026-06-19 08:59:38');

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
(1, 5, 1, 3, 'Laporan untuk kos \"Kos Ayu\" mendapat tanggapan admin: terima kasih atas laporan anda, akan segera kami tindak lanjuti dan perbaiki segala kekurangan ', 'info', 1, '2026-06-01 11:05:35'),
(2, 8, 1, 3, 'Laporan untuk kos \"Kos Ayu\" mendapat tanggapan admin: terima kasih atas laporan anda, akan segera kami tindak lanjuti dan perbaiki segala kekurangan ', 'info', 1, '2026-06-01 11:05:35'),
(3, 8, 1, 1, 'Laporan untuk kos \"Kos Ayu\" telah ditutup. Terima kasih atas laporan Anda.', 'selesai', 1, '2026-06-01 11:09:15'),
(4, 5, 1, 4, 'Laporan untuk kos \"Kos Ayu\" mendapat tanggapan admin: hehe', 'info', 1, '2026-06-01 11:44:30'),
(5, 8, 1, 4, 'Laporan untuk kos \"Kos Ayu\" mendapat tanggapan admin: hehe', 'info', 1, '2026-06-01 11:44:30'),
(6, 5, 2, 5, 'Ada laporan baru pada kos \"KosTo\" yang perlu ditinjau.', 'info', 1, '2026-06-01 12:03:52'),
(7, 5, 2, 5, 'Laporan untuk kos \"KosTo\" mendapat tanggapan admin: hehe', 'info', 1, '2026-06-01 12:05:14'),
(8, 9, 2, 5, 'Laporan untuk kos \"KosTo\" mendapat tanggapan admin: hehe', 'info', 1, '2026-06-01 12:05:14'),
(9, 5, 1, 6, 'Ada laporan baru pada kos \"Kos Ayu\" yang perlu ditinjau.', 'info', 1, '2026-06-01 12:35:14'),
(10, 9, 1, 6, 'Pemilik kos telah memberikan tanggapan terhadap laporan Anda.', 'info', 1, '2026-06-01 12:37:07'),
(11, 5, 1, 6, 'Laporan untuk kos \"Kos Ayu\" mendapat tanggapan admin: terima kasih atas lapoaran yang diberikan, akan segera ditindak lanjut', 'info', 1, '2026-06-01 12:38:04'),
(12, 9, 1, 6, 'Laporan untuk kos \"Kos Ayu\" mendapat tanggapan admin: terima kasih atas lapoaran yang diberikan, akan segera ditindak lanjut', 'info', 1, '2026-06-01 12:38:04'),
(13, 10, 4, 7, 'Ada laporan baru pada kos \"Kos NCT\" yang perlu ditinjau.', 'info', 0, '2026-06-18 14:00:42'),
(14, 10, 4, 8, 'Ada laporan baru pada kos \"Kos NCT\" yang perlu ditinjau.', 'info', 0, '2026-06-19 08:56:45');

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
  `tanggapan_pemilik` text DEFAULT NULL,
  `foto_tanggapan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `reporter_id`, `kos_id`, `alasan`, `keterangan`, `status`, `created_at`, `level_peringatan`, `tanggapan_admin`, `tanggapan_pemilik`, `foto_tanggapan`) VALUES
(1, 8, 1, 'Informasi tidak sesuai kenyataan', NULL, 'selesai', '2026-05-21 11:02:30', 0, NULL, NULL, NULL),
(2, 8, 1, 'Informasi tidak sesuai kenyataan', NULL, 'pending', '2026-06-01 10:47:50', 0, NULL, NULL, NULL),
(3, 8, 1, 'Informasi tidak sesuai kenyataan', NULL, 'ditinjau', '2026-06-01 11:01:11', 0, 'terima kasih atas laporan anda, akan segera kami tindak lanjuti dan perbaiki segala kekurangan ', 'fasilitasnya ada, tapi kemarin lagi rusak, dan akan segera diperbaiki, terima kasih atas laporannya', NULL),
(4, 8, 1, 'Informasi tidak sesuai kenyataan', NULL, 'ditinjau', '2026-06-01 11:42:15', 0, 'hehe', 'hehe', NULL),
(5, 9, 2, 'Foto tidak asli / menyesatkan', 'foto pinterest', 'ditinjau', '2026-06-01 12:03:52', 0, 'hehe', 'hehe', NULL),
(6, 9, 1, 'Informasi tidak sesuai kenyataan', 'keterangan fasilitas terdapat AC namun aslinya tidak ada', 'ditinjau', '2026-06-01 12:35:14', 0, 'terima kasih atas lapoaran yang diberikan, akan segera ditindak lanjut', 'fasilitas memang ada tapi lagi rusak jadi masih dalam masa perbaikan, akan kami segerakan untuk perbaikan', NULL),
(7, 8, 4, 'Informasi tidak sesuai kenyataan', 'ga sesuai', 'pending', '2026-06-18 14:00:42', 0, NULL, NULL, NULL),
(8, 8, 4, 'Informasi tidak sesuai kenyataan', 'Informasi titik kos belum sesuai', 'pending', '2026-06-19 08:56:45', 0, NULL, NULL, NULL);

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
(1, 1, 8, 5, 'bagus banget mantap mendunia slebew', '2026-05-21 11:01:54', 'ulasan_8_1779332514.jpg'),
(2, 1, 9, 4, 'nyaman tapi cat nya belum diperbarui', '2026-05-31 12:20:39', 'ulasan_9_1780201239.jpg');

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
(6, 'Juli', 'juli@admin.com', '4c37dbfae76a9a48544d7248127d2d29', 'admin', NULL, '2026-05-16 02:54:53'),
(8, 'dita ayu julita', 'ditaaayujulita@gmail.com', '42cbbe8a1f471041ae07b725de5fe1a0', 'penyewa', NULL, '2026-05-21 02:51:59'),
(9, 'mark', 'mark@gmail.com', '07ca4741cb7e21cf0eefd4b9019f0884', 'penyewa', NULL, '2026-05-31 04:18:53'),
(10, 'Haechan Lee', 'haechan@gmail.com', '23468c3ed6575892ccde73d11d2a2e1a', 'pemilik', '082345725374', '2026-06-06 07:59:51'),
(11, 'ayu', 'juli@gmail.com', 'e21bb074fd150247fac5c5402e139097', 'pemilik', '098736352436', '2026-06-18 05:55:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_fav` (`user_id`,`kos_id`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_review` (`kos_id`,`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kos`
--
ALTER TABLE `kos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kos_foto`
--
ALTER TABLE `kos_foto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
