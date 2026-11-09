-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2026 at 06:45 AM
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
-- Database: `etalase_stella`
--

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_project` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `link_project` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `user_id`, `nama_project`, `deskripsi`, `gambar`, `link_project`, `created_at`) VALUES
(3, 1, 'Project IOT Mesin Pemotong Rumput Otomatis', 'Kategori: Internet of Things\r\nSistem pemotong rumput berbasis mikrokontroler yang dirancang untuk membantu proses pemeliharaan rumput secara otomatis.\r\nTeknologi: Arduino, sensor, motor DC', '1792469569-1c919785.jpg', 'https://youtube.com', '2026-10-20 04:12:49'),
(4, 2, 'Manajemen Eskul', 'Manajemen Eskul merupakan aplikasi berbasis web yang dirancang untuk mempermudah pengelolaan kegiatan ekstrakurikuler di sekolah. Sistem ini membantu mengelola data ekstrakurikuler, anggota, jadwal kegiatan, serta informasi terkait dalam satu platform yang terorganisir.\r\n\r\nMelalui aplikasi ini, pengelola dapat memantau data anggota dan mengelola informasi kegiatan dengan lebih praktis, sehingga proses administrasi ekstrakurikuler menjadi lebih efektif, terstruktur, dan mudah diakses.', '1792470545-20535c3d.png', 'https://github.com/filza-hash/UH-BE-MANAJEMEN-EKSKUL.git', '2026-10-20 04:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','siswa') NOT NULL DEFAULT 'siswa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`) VALUES
(1, 'Super Admin Stella', 'superadmin@stella.sch.id', '$2y$10$x1/52MH0htaczSr5ESoNNOLco1TFNfJDk0NBtJxVHCU2hvBdqmMOq', 'super_admin'),
(2, 'Admin Stella', 'admin@stella.sch.id', '$2y$10$fs/lWmFXUE1EILDGdc61g./6luRVXfzdzYXlRXCufAWJyVN8KcloC', 'admin'),
(3, 'Pilja kok cantik', 'siswa@stella.sch.id', '$2y$10$gwG9K0d0SyJxx9PUXxcqSukgFyDBSb8I3jX4MylqAGtUn4PWOebt2', 'siswa'),
(4, 'syaida', 'syaida@stella.sch.id', '$2y$10$wcv8EedNwLMsjmZUWQCNyuiJzDuAxMPEgWAPwtilXB4sg8biSTuau', 'siswa'),
(5, 'Keniza Adelia', 'keniza@stella.sch.id', '$2y$10$1.wM3kSkSQfZP8l3KGrw6.CMw8gZZdx7aL2yy8JM8cqV94ZsuXxHC', 'siswa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
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
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
