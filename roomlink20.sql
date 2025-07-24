-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 23, 2025 at 03:29 PM
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
-- Database: `roomlink`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `student_id` varchar(100) NOT NULL,
  `room_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `receipt_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('pending','paid') DEFAULT 'pending',
  `payment_proof` varchar(255) DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `student_id`, `room_id`, `status`, `payment_method`, `receipt_file`, `created_at`, `payment_status`, `payment_proof`, `check_in`, `check_out`) VALUES
(6, 'BSCSF/M/2222/09/24', 5, 'accepted', 'Mpesa', '1753259099_Screenshot_2025-06-11_10_20_58.png', '2025-07-23 08:24:59', 'paid', NULL, '2025-07-08', '2025-08-28'),
(7, 'BSCSF/M/2287/09/24', 8, 'accepted', 'Bank', '1753260593_Screenshot_2025-06-11_10_20_58.png', '2025-07-23 08:49:53', 'paid', NULL, '2025-07-17', '2025-11-13');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `apartment_name` varchar(100) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `capacity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `availability` enum('available','booked') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `apartment_name`, `room_number`, `gender`, `capacity`, `price`, `image`, `created_at`, `availability`) VALUES
(1, 'Lenamoi', '1', 'male', 4, 10000.00, '1753276747_4sharing.jpeg', '2025-07-20 13:31:57', 'booked'),
(2, 'Lenamoi', '2', 'male', 2, 20000.00, '1753276761_2sharing.jpeg', '2025-07-20 13:48:03', 'booked'),
(3, 'Lenamoi', '3', 'male', 3, 15000.00, '1753276789_3sharing.jpeg', '2025-07-20 13:48:37', 'available'),
(5, 'Baringo', '1', 'male', 4, 10000.00, '1753276587_4sharing.jpeg', '2025-07-20 15:27:29', 'booked'),
(6, 'Eldoret', '1', 'male', 2, 20000.00, '1753276613_2sharing.jpeg', '2025-07-20 15:28:33', 'available'),
(7, 'Eldoret', '2', 'male', 3, 20000.00, '1753276736_3sharing.jpeg', '2025-07-20 16:23:34', 'available'),
(8, 'Sote-A', '1', 'male', 1, 15000.00, '1753276810_single.jpeg', '2025-07-20 16:24:29', 'booked');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `role` enum('student','admin') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `gender`, `role`, `created_at`) VALUES
('BSCSF/M/2222/09/24', 'bradley kemboi', 'BRAD2121@gmail.com', '$2y$10$eDtLnsN6v8nkSyXYUrp2A.bwAc7MCTPsUV/BiOcF38Tqh8s2GvM3m', 'male', 'student', '2025-07-20 10:41:23'),
('BSCSF/M/2287/09/24', 'mannase kiptoo', 'mma21@gnail.com', '$2y$10$A7bODn/ve57b9Cv9Xw0fw.E7AK.pIV1tvAQLBZSO7U8iVfO2ksebG', 'male', 'student', '2025-07-20 14:46:27'),
('stf/002', 'kiptoo', 'mana2121@gmail.com', '$2y$10$idp5uv7mqweiWKkamanAPOaMMk/0onc1wy2m.36yzOji/9RlcyQIe', 'male', 'admin', '2025-07-20 11:52:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `bookings_ibfk_1` (`student_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
