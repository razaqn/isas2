-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Nov 2024 pada 14.03
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web-resto`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `action_items`
--

CREATE TABLE `action_items` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `priority` enum('high','medium','low') DEFAULT 'medium',
  `status` varchar(50) DEFAULT 'Not Started',
  `timeline_months` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `action_items`
--

INSERT INTO `action_items` (`id`, `title`, `priority`, `status`, `timeline_months`, `created_at`) VALUES
(1, 'Table Reservation System', 'high', 'Planning', 1, '2024-11-15 08:57:16'),
(2, 'Digital Payment Integration', 'high', 'Planning', 2, '2024-11-15 08:57:16'),
(3, 'Loyalty Program', 'medium', 'Not Started', 4, '2024-11-15 08:57:16'),
(4, 'Mobile App Development', 'low', 'Not Started', 8, '2024-11-15 08:57:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `contact`
--

INSERT INTO `contact` (`id`, `name`, `contact_number`, `message`, `status`, `created_at`) VALUES
(1, 'Admin', '08145656565', 'saran min tolong dipercepat layanannya', 'read', '2024-11-15 07:23:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `development_timeline`
--

CREATE TABLE `development_timeline` (
  `id` int(11) NOT NULL,
  `period` enum('short','medium','long') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `development_timeline`
--

INSERT INTO `development_timeline` (`id`, `period`, `title`, `description`, `sort_order`, `created_at`) VALUES
(1, 'short', 'Implement table reservation system', 'Develop and deploy online table booking functionality', 1, '2024-11-15 10:18:10'),
(2, 'short', 'Add digital payment options', 'Integrate multiple payment gateways', 2, '2024-11-15 10:18:10'),
(3, 'short', 'Enhance security measures', 'Implement additional security protocols', 3, '2024-11-15 10:18:10'),
(4, 'medium', 'Launch loyalty program', 'Design and implement customer rewards system', 1, '2024-11-15 10:18:10'),
(5, 'medium', 'Develop advanced analytics', 'Create comprehensive reporting dashboard', 2, '2024-11-15 10:18:10'),
(6, 'medium', 'Delivery integration', 'Partner with delivery services', 3, '2024-11-15 10:18:10'),
(7, 'long', 'Mobile app development', 'Create native mobile applications', 1, '2024-11-15 10:18:10'),
(8, 'long', 'AI recommendations', 'Implement AI-based menu suggestions', 2, '2024-11-15 10:18:10'),
(9, 'long', 'Advanced analytics', 'Develop predictive analytics capabilities', 3, '2024-11-15 10:18:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `category` enum('Appetizer','Pasta','Main Course','Dessert','Beverage') NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `jumlah_beli` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `menu`
--

INSERT INTO `menu` (`id`, `nama`, `category`, `description`, `price`, `image`, `jumlah_beli`, `created_at`, `updated_at`) VALUES
(1, 'Seafood Risotto', 'Main Course', 'Creamy arborio rice with fresh seafood and herbs', 24.00, 'seafood-risotto.jpg', 0, '2024-11-15 02:44:35', '2024-11-15 02:44:35'),
(2, 'Linguine alle Vongole', 'Pasta', 'Fresh clams with garlic, white wine, and parsley', 22.00, 'linguine-vongole.jpg', 0, '2024-11-15 02:44:35', '2024-11-15 02:44:35'),
(3, 'Branzino al Forno', 'Main Course', 'Whole roasted sea bass with herbs and lemon', 28.00, 'branzino.jpg', 0, '2024-11-15 02:44:35', '2024-11-15 02:44:35'),
(4, 'Carpaccio di Tonno', 'Appetizer', 'Thinly sliced fresh tuna with olive oil and capers', 18.00, 'tuna-carpaccio.jpg', 2, '2024-11-15 02:44:35', '2024-11-15 07:55:25'),
(5, 'Insalata di Mare', 'Appetizer', 'Mixed seafood salad with fresh vegetables', 16.00, 'seafood-salad.jpg', 2, '2024-11-15 02:44:35', '2024-11-15 07:55:25'),
(6, 'Spaghetti alle Cozze', 'Pasta', 'Spaghetti with fresh mussels in white wine sauce', 20.00, 'spaghetti-mussels.jpg', 0, '2024-11-15 02:44:35', '2024-11-15 02:44:35'),
(7, 'Calamari Fritti', 'Appetizer', 'Crispy fried calamari with marinara sauce', 15.00, '6736fa50ccdf2.jpg', 1, '2024-11-15 02:44:35', '2024-11-15 07:53:31'),
(8, 'Grigliata Mista', 'Main Course', 'Mixed grilled seafood platter', 32.00, 'mixed-grill.jpg', 0, '2024-11-15 02:44:35', '2024-11-15 02:44:35'),
(9, 'Zuppa di Pesce', 'Main Course', 'Traditional Italian fish soup', 26.00, 'fish-soup.jpg', 0, '2024-11-15 02:44:35', '2024-11-15 02:44:35'),
(10, 'Ravioli di Aragosta', 'Pasta', 'Homemade lobster ravioli in cream sauce', 28.00, 'lobster-ravioli.jpg', 0, '2024-11-15 02:44:35', '2024-11-15 02:44:35'),
(11, 'Tiramisu', 'Dessert', 'Classic Italian coffee-flavored dessert', 10.00, 'tiramisu.jpg', 0, '2024-11-15 02:44:35', '2024-11-15 02:44:35'),
(12, 'Panna Cotta', 'Dessert', 'Vanilla cream with berry sauce', 9.00, 'panna-cotta.jpg', 0, '2024-11-15 02:44:35', '2024-11-15 02:44:35'),
(13, 'Cannoli Siciliani', 'Dessert', 'Sicilian pastry filled with sweet ricotta', 8.00, 'cannoli.jpg', 0, '2024-11-15 02:44:35', '2024-11-15 02:44:35'),
(14, 'Gelato Misto', 'Dessert', 'Assorted Italian ice cream', 8.00, 'gelato.jpg', 0, '2024-11-15 02:44:35', '2024-11-15 02:44:35'),
(15, 'Affogato', 'Dessert', 'Vanilla ice cream with espresso', 7.00, 'affogato.jpg', 0, '2024-11-15 02:44:35', '2024-11-15 02:44:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesan`
--

CREATE TABLE `pesan` (
  `id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_contact` varchar(50) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesan`
--

INSERT INTO `pesan` (`id`, `order_date`, `total_amount`, `status`, `customer_name`, `customer_contact`, `customer_email`, `notes`) VALUES
(1, '2024-11-15 07:53:31', 49.00, 'completed', 'razaq', '08213213', 'razaq@mail.com', 'lezatnyooo'),
(2, '2024-11-15 07:55:25', 34.00, 'completed', 'razaq', '08213213', 'razaq@mail.com', 'dasadsasd');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesan_detail`
--

CREATE TABLE `pesan_detail` (
  `id` int(11) NOT NULL,
  `pesan_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesan_detail`
--

INSERT INTO `pesan_detail` (`id`, `pesan_id`, `menu_id`, `quantity`, `price`) VALUES
(1, 1, 7, 1, 15.00),
(2, 1, 4, 1, 18.00),
(3, 1, 5, 1, 16.00),
(4, 2, 4, 1, 18.00),
(5, 2, 5, 1, 16.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `review`
--

CREATE TABLE `review` (
  `id` int(11) NOT NULL,
  `pesan_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('approved','pending','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `review`
--

INSERT INTO `review` (`id`, `pesan_id`, `customer_name`, `rating`, `review_text`, `review_date`, `status`) VALUES
(1, 2, 'razaq', 4, 'mantap', '2024-11-15 07:56:45', 'approved');

-- --------------------------------------------------------

--
-- Struktur dari tabel `swot_categories`
--

CREATE TABLE `swot_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `swot_categories`
--

INSERT INTO `swot_categories` (`id`, `name`, `description`, `icon`, `color`) VALUES
(1, 'Strengths', 'Internal positive aspects', 'fas fa-check-circle', '#28a745'),
(2, 'Weaknesses', 'Internal negative aspects', 'fas fa-exclamation-circle', '#dc3545'),
(3, 'Opportunities', 'External positive aspects', 'fas fa-lightbulb', '#17a2b8'),
(4, 'Threats', 'External negative aspects', 'fas fa-shield-alt', '#ffc107');

-- --------------------------------------------------------

--
-- Struktur dari tabel `swot_items`
--

CREATE TABLE `swot_items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `priority` enum('high','medium','low') DEFAULT 'medium',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `swot_items`
--

INSERT INTO `swot_items` (`id`, `category_id`, `description`, `priority`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Comprehensive admin system', 'high', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(2, 1, 'User-friendly interface', 'medium', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(3, 1, 'Integrated review system', 'medium', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(4, 1, 'Real-time order tracking', 'high', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(5, 1, 'Efficient menu management', 'medium', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(6, 2, 'No table reservation system', 'high', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(7, 2, 'Limited payment options', 'high', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(8, 2, 'No loyalty program', 'medium', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(9, 2, 'Basic analytics', 'medium', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(10, 2, 'No mobile app', 'low', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(11, 3, 'Digital payment integration', 'high', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(12, 3, 'Mobile app development', 'medium', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(13, 3, 'Loyalty program implementation', 'medium', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(14, 3, 'Delivery service partnership', 'high', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(15, 3, 'AI-powered recommendations', 'low', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(16, 4, 'Cybersecurity risks', 'high', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(17, 4, 'Competitor platforms', 'medium', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(18, 4, 'System downtime risks', 'high', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(19, 4, 'Data privacy concerns', 'high', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16'),
(20, 4, 'Market saturation', 'medium', 'active', '2024-11-15 08:57:16', '2024-11-15 08:57:16');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `action_items`
--
ALTER TABLE `action_items`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `development_timeline`
--
ALTER TABLE `development_timeline`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pesan`
--
ALTER TABLE `pesan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pesan_detail`
--
ALTER TABLE `pesan_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesan_id` (`pesan_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indeks untuk tabel `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesan_id` (`pesan_id`);

--
-- Indeks untuk tabel `swot_categories`
--
ALTER TABLE `swot_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `swot_items`
--
ALTER TABLE `swot_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `action_items`
--
ALTER TABLE `action_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `development_timeline`
--
ALTER TABLE `development_timeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `pesan`
--
ALTER TABLE `pesan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pesan_detail`
--
ALTER TABLE `pesan_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `review`
--
ALTER TABLE `review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `swot_categories`
--
ALTER TABLE `swot_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `swot_items`
--
ALTER TABLE `swot_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pesan_detail`
--
ALTER TABLE `pesan_detail`
  ADD CONSTRAINT `pesan_detail_ibfk_1` FOREIGN KEY (`pesan_id`) REFERENCES `pesan` (`id`),
  ADD CONSTRAINT `pesan_detail_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`);

--
-- Ketidakleluasaan untuk tabel `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`pesan_id`) REFERENCES `pesan` (`id`);

--
-- Ketidakleluasaan untuk tabel `swot_items`
--
ALTER TABLE `swot_items`
  ADD CONSTRAINT `swot_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `swot_categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

