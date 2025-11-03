-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2025 at 05:58 AM
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
-- Database: `eventify`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `ticket_count` int(11) DEFAULT 1,
  `status` enum('confirmed','pending','cancelled') DEFAULT 'pending',
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `event_id`, `ticket_count`, `status`, `booked_at`, `last_updated`, `notes`) VALUES
(5, 107, 7, 3, 'confirmed', '2025-06-30 05:17:19', '2025-07-06 15:33:59', NULL),
(6, 107, 7, 4, 'confirmed', '2025-06-30 05:44:59', '2025-07-06 15:34:00', NULL),
(7, 107, 7, 1, 'confirmed', '2025-06-30 07:39:28', '2025-07-06 15:34:00', NULL),
(8, 107, 7, 6, 'confirmed', '2025-06-30 07:40:30', '2025-07-06 15:34:00', NULL),
(9, 107, 7, 5, 'confirmed', '2025-07-01 04:05:13', '2025-07-06 15:34:00', NULL),
(10, 107, 7, 4, 'confirmed', '2025-07-01 04:08:13', '2025-07-06 15:34:00', NULL),
(11, 108, 9, 4, 'confirmed', '2025-07-02 14:21:53', '2025-07-06 15:34:00', NULL),
(12, 109, 7, 3, 'confirmed', '2025-07-02 15:00:00', '2025-07-06 15:34:00', NULL),
(13, 109, 11, 1, 'confirmed', '2025-07-05 14:49:16', '2025-07-06 15:34:00', NULL),
(14, 110, 11, 2, 'confirmed', '2025-07-06 11:29:33', '2025-07-06 15:34:00', NULL),
(15, 110, 9, 1, 'cancelled', '2025-07-06 12:41:35', '2025-07-06 15:29:30', NULL),
(16, 112, 11, 14, 'confirmed', '2025-07-08 07:29:20', '2025-07-08 07:29:21', NULL),
(17, 112, 10, 12, 'confirmed', '2025-07-08 07:40:59', '2025-07-08 07:41:00', NULL),
(18, 118, 11, 2, 'cancelled', '2025-07-08 07:50:53', '2025-07-08 08:07:15', NULL),
(21, 120, 10, 3, 'cancelled', '2025-07-10 09:12:37', '2025-07-10 09:13:55', NULL),
(22, 120, 13, 2, 'confirmed', '2025-07-10 09:13:28', '2025-07-10 09:13:28', NULL),
(23, 120, 10, 5, 'cancelled', '2025-07-10 09:14:40', '2025-07-10 09:15:02', NULL),
(24, 115, 13, 3, 'cancelled', '2025-07-19 07:10:16', '2025-07-27 11:44:54', NULL),
(25, 115, 9, 1, 'cancelled', '2025-07-19 07:28:29', '2025-07-19 07:30:10', NULL),
(26, 115, 12, 2, 'cancelled', '2025-07-19 07:29:50', '2025-07-19 13:00:22', NULL),
(27, 115, 9, 2, 'cancelled', '2025-07-19 12:46:56', '2025-07-19 12:47:24', NULL),
(28, 115, 17, 4, 'confirmed', '2025-07-27 11:47:15', '2025-07-27 11:47:16', NULL),
(29, 115, 13, 3, 'confirmed', '2025-07-27 11:48:38', '2025-07-27 11:48:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `location_description` text DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `tickets_available` int(11) DEFAULT 0,
  `tickets_sold` int(11) DEFAULT 0,
  `price` decimal(10,2) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category` varchar(100) NOT NULL DEFAULT 'General'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `user_id`, `name`, `description`, `location`, `location_description`, `event_date`, `tickets_available`, `tickets_sold`, `price`, `video_path`, `created_at`, `last_modified`, `category`) VALUES
(7, 106, 'Sunset View', 'Enjoy quiet evening sunset on one of Kottayam\'s hallmark balconies.', 'Kottayam', 'Kanjikuzhy, Kottayam', '2025-07-15', 40, 26, 100.00, 'uploads/videos/68621d7c5dd99_PXL_20240915_175331295.mp4', '2025-06-30 05:15:40', '2025-07-02 20:30:00', 'General'),
(8, 108, 'Coldplay', 'Coldplay is a British rock/pop-rock band formed in London in 1996 (officially 1997) by Chris Martin (vocals, piano) and Jonny Buckland (guitar) at University College London. They soon added Guy Berryman (bass), Will Champion (drums), and manager Phil Harvey as their fifth member', 'Ernakulam', 'Kaloor Stadium', '2025-07-03', 50, 0, 150.00, '', '2025-07-01 04:39:55', '2025-07-01 10:09:55', 'General'),
(9, 108, 'Food fest', 'In Kozhikode (Calicut), the city’s vibrant food culture is expressed through various festive events, communal initiatives, and traditional gatherings—not always labeled “FoodFest,” but often equally flavorful.', 'Kozhikode', 'The Great Indian Food Art was a vibrant, multi-genre food festival held at the Kozhikode Trade Centre—bringing together authentic flavours from 22 Indian cities, including the famed Kozhikode biryani, Lucknow’s kebabs, Kashmiri rogan josh, Rajasthani litti chokha, and more .', '2025-08-10', 250, 4, 150.00, '', '2025-07-01 04:55:26', '2025-07-19 18:17:24', 'General'),
(10, 108, 'Blood Donation Camp', 'Donating blood is a safe and structured process that can be done multiple times a year. Besides saving lives, it often benefits the donor\'s physical and mental health. Just follow pre- and post-care guidelines—stay hydrated, eat well, get rest—and adhere to eligibility protocols.', 'Alappuzha', 'Vandanam General Hospital Alappuzha', '2025-07-10', 200, 12, 0.00, '', '2025-07-01 05:03:11', '2025-07-10 14:45:02', 'General'),
(11, 108, 'Trekking', 'Munnar offers a fantastic blend of scenic beauty, diverse trails suitable for all fitness levels, and lush biodiversity. Whether you\'re after a sunrise vista from Kolukkumalai or a challenge in the Nilgiri hills, there\'s a trek here with your name on it.', 'Idukki', 'Meesapulimala', '2025-07-15', 200, 17, 150.00, '', '2025-07-01 05:12:59', '2025-07-08 13:37:15', 'General'),
(12, 108, 'Summer Camp', 'Family summer camps offer a unique opportunity for families to bond, explore nature, and engage in enriching activities together. While Kozhikode may not have dedicated family summer camps, several options in Kerala and neighboring regions provide immersive experiences suitable for families.', 'Wayanad', 'Wayanad', '2025-08-25', 150, 0, 150.00, '', '2025-07-01 05:20:36', '2025-07-19 18:30:22', 'General'),
(13, 112, 'Coldplay', 'Coldplay is a British rock band known for their atmospheric sound, emotional lyrics, and stunning live performances. Formed in 1996, the band rose to global fame with hits like \"Yellow\", \"Clocks\", \"Fix You\", and \"Viva La Vida\". Blending alternative rock with elements of pop and electronic music, Coldplay is celebrated for their evolving musical style and uplifting themes of love, hope, and humanity. Led by frontman Chris Martin, they are also known for their vibrant concerts filled with lights, colors, and fan interaction, making them one of the most beloved bands in the world.', 'Ernakulam', 'kaloor Stadium', '2025-08-14', 200, 5, 1000.00, '', '2025-07-08 07:39:44', '2025-07-27 17:18:39', 'General'),
(17, 121, 'Star-Con', 'Join thousands of Star Wars fans at this galactic celebration—a convergence of cosplay, collectibles, and community. The convention features:\r\n\r\nImmersive cosplay showcases: From Jedi and Stormtroopers to custom creations judged in lively competitions—think “Shogun Vader” levels of craftsmanship \r\n\r\n.\r\n\r\nPanels & premieres: Watch exclusive trailers, attend Q&A sessions with cast and crew from upcoming films and series, and explore behind‑the‑scenes reveals \r\n.\r\n\r\nMarketplace & exhibits: Shop official merch, rare collectibles, and artist creations. Enjoy special displays like life‑sized X‑wings or Mandalorian props .\r\n.\r\n\r\nInteractive fun: Take part in activities like tattoo pavilions themed around the galaxy, droid hunts or charity games led by the 501st Legion ', 'Thiruvananthapuram', 'Lulu International Shopping Mall', '2026-05-04', 500, 4, 3000.00, '', '2025-07-27 11:43:31', '2025-07-27 17:17:15', 'Art');

-- --------------------------------------------------------

--
-- Table structure for table `event_images`
--

CREATE TABLE `event_images` (
  `id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_images`
--

INSERT INTO `event_images` (`id`, `event_id`, `image_path`, `uploaded_at`) VALUES
(12, 7, 'uploads/images/68621d7cb0f7a_IMG_20240928_183224639_HDR.jpg', '2025-06-30 05:15:40'),
(13, 8, 'uploads/images/6863669c04601_coldplay.jpeg', '2025-07-01 04:39:56'),
(14, 8, 'uploads/images/6863669c4410f_coldpla1.jpeg', '2025-07-01 04:39:56'),
(15, 8, 'uploads/images/6863669c582e2_coldplay2.jpeg', '2025-07-01 04:39:56'),
(16, 9, 'uploads/images/68636a3e6faf4_food-fest.jpeg', '2025-07-01 04:55:26'),
(17, 9, 'uploads/images/68636a3ec6ba4_foodfest.jpg', '2025-07-01 04:55:26'),
(18, 9, 'uploads/images/68636a3f3b23d_Foodie.jpg', '2025-07-01 04:55:27'),
(19, 10, 'uploads/images/68636c0f34885_blood-donation-4165394_1280.jpg', '2025-07-01 05:03:11'),
(20, 10, 'uploads/images/68636c0f61a4b_Blood-Donation-1.jpg', '2025-07-01 05:03:11'),
(21, 10, 'uploads/images/68636c0fa2799_blood-donation-6.jpg', '2025-07-01 05:03:11'),
(22, 11, 'uploads/images/68636e5b57a32_trekking-phantom-hill-in-munnar-india-1699261027.jpg', '2025-07-01 05:12:59'),
(23, 11, 'uploads/images/68636e5baa97a_meesapulimala.jpg', '2025-07-01 05:12:59'),
(24, 11, 'uploads/images/68636e5bc0b9f_munnar-offroading-1024x576.jpg', '2025-07-01 05:12:59'),
(25, 12, 'uploads/images/686370246777c_cropped-wayanad-cottages-raindrops-resorts.jpg', '2025-07-01 05:20:36'),
(26, 12, 'uploads/images/68637024762ae_wayanad-041684733400.webp', '2025-07-01 05:20:36'),
(27, 12, 'uploads/images/6863702494965_OIP.jpeg', '2025-07-01 05:20:36'),
(28, 13, 'uploads/images/686ccb410c717_coldpla1.jpeg', '2025-07-08 07:39:45'),
(29, 13, 'uploads/images/686ccb4139b40_coldplay.jpeg', '2025-07-08 07:39:45'),
(30, 13, 'uploads/images/686ccb415299f_coldplay2.jpeg', '2025-07-08 07:39:45'),
(34, 17, 'uploads/images/688610e3b915d_Star-Wars-Comic-Con-2018-Lucasfilm-poster-cropped.avif', '2025-07-27 11:43:31');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('event_organizer','ticket_booker') NOT NULL,
  `comment` text NOT NULL,
  `rating` int(11) DEFAULT 5,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `user_type`, `comment`, `rating`, `created_at`) VALUES
(1, 106, 'event_organizer', 'Organizing events on Eventify is seamless and efficient!', 5, '2025-07-01 23:12:44'),
(2, 107, 'ticket_booker', 'Loved how easy it was to book tickets!', 4, '2025-07-01 23:12:44'),
(3, 108, 'event_organizer', 'Absolutely loved the event! Smooth booking process.', 5, '2025-07-06 21:31:34'),
(4, 109, 'event_organizer', 'It was okay. Some delays, but still good.', 3, '2025-07-06 21:31:34'),
(5, 110, 'event_organizer', 'Great experience from start to finish!', 4, '2025-07-06 21:31:34'),
(6, 111, 'event_organizer', 'The process was very friendly. Will attend again!', 5, '2025-07-06 21:31:34'),
(7, 112, 'event_organizer', 'Room for more features, but overall fine.', 3, '2025-07-06 21:31:34'),
(8, 113, 'event_organizer', 'Awesome vibes and crowd!', 4, '2025-07-06 21:31:34'),
(9, 114, 'event_organizer', 'Super easy to organize events.', 5, '2025-07-06 21:31:34'),
(10, 115, 'event_organizer', 'Great for discovering local events.', 4, '2025-07-06 21:31:34'),
(11, 116, 'event_organizer', 'Booking was seamless and fast.', 5, '2025-07-06 21:31:34'),
(12, 117, 'event_organizer', 'Appreciated the notifications and customer support.', 4, '2025-07-06 21:31:34');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT 'mock_gateway',
  `payment_reference` varchar(100) DEFAULT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `user_id`, `amount`, `status`, `payment_method`, `payment_reference`, `paid_at`) VALUES
(5, 5, 107, 300.00, 'completed', 'mock_gateway', 'MOCK-68621DDFC1E58', '2025-06-30 05:17:19'),
(6, 6, 107, 400.00, 'completed', 'mock_gateway', 'MOCK-6862245B882ED', '2025-06-30 05:44:59'),
(7, 7, 107, 100.00, 'completed', 'mock_gateway', 'MOCK-68623F30B5F98', '2025-06-30 07:39:28'),
(8, 8, 107, 600.00, 'completed', 'mock_gateway', 'MOCK-68623F6E3D128', '2025-06-30 07:40:30'),
(9, 9, 107, 500.00, 'completed', 'mock_gateway', 'MOCK-68635E7B2FBD4', '2025-07-01 04:05:15'),
(10, 10, 107, 400.00, 'completed', 'mock_gateway', 'MOCK-68635F2E4BF0D', '2025-07-01 04:08:14'),
(11, 11, 108, 600.00, 'completed', 'mock_gateway', 'MOCK-6865408291BAE', '2025-07-02 14:21:54'),
(12, 12, 109, 300.00, 'completed', 'mock_gateway', 'MOCK-686549705C04D', '2025-07-02 15:00:00'),
(13, 13, 109, 150.00, 'completed', 'mock_gateway', 'MOCK-68693B6D1E861', '2025-07-05 14:49:17'),
(14, 14, 110, 300.00, 'completed', 'mock_gateway', 'MOCK-686A5E1E263D5', '2025-07-06 11:29:34'),
(15, 15, 110, 150.00, 'completed', 'mock_gateway', 'MOCK-686A6F0018472', '2025-07-06 12:41:36'),
(16, 16, 112, 2100.00, 'completed', 'mock_gateway', 'MOCK-686CC8D0E4E1F', '2025-07-08 07:29:20'),
(17, 17, 112, 0.00, 'completed', 'mock_gateway', 'MOCK-686CCB8BE6F07', '2025-07-08 07:40:59'),
(18, 18, 118, 300.00, 'completed', 'mock_gateway', 'MOCK-686CCDDE2CCE5', '2025-07-08 07:50:54'),
(21, 21, 120, 0.00, 'completed', 'mock_gateway', 'MOCK-686F8405D3138', '2025-07-10 09:12:37'),
(22, 22, 120, 2000.00, 'completed', 'mock_gateway', 'MOCK-686F8438A125A', '2025-07-10 09:13:28'),
(23, 23, 120, 0.00, 'completed', 'mock_gateway', 'MOCK-686F84812F7B8', '2025-07-10 09:14:41'),
(24, 24, 115, 3000.00, 'completed', 'mock_gateway', 'MOCK-687B44D9081BD', '2025-07-19 07:10:17'),
(25, 25, 115, 150.00, 'completed', 'mock_gateway', 'MOCK-687B491E2F745', '2025-07-19 07:28:30'),
(26, 26, 115, 300.00, 'completed', 'mock_gateway', 'MOCK-687B496E950B9', '2025-07-19 07:29:50'),
(27, 27, 115, 300.00, 'completed', 'mock_gateway', 'MOCK-687B93C0C1B54', '2025-07-19 12:46:56'),
(28, 28, 115, 12000.00, 'completed', 'mock_gateway', 'MOCK-688611C38C84E', '2025-07-27 11:47:15'),
(29, 29, 115, 3000.00, 'completed', 'mock_gateway', 'MOCK-68861217B2968', '2025-07-27 11:48:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('ticket_booker','event_organizer','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `created_at`) VALUES
(105, 'JB', 'adminjb@email.com', '$2y$10$t92k1weA5lgU9wmCkkieFuD6zcsFR8TtIvkc8IAcyxlzyeEACw8fy', 'admin', '2025-06-30 04:33:22'),
(106, 'Amna Thaju', 'amna123@gmail.com', '$2y$10$.Api.pga16G36/yfbdpks.WWbxQnTseucdR/HcDTxR3sgIcpg1AsG', 'event_organizer', '2025-06-30 05:12:58'),
(107, 'Ashik Rojan', 'ashikrojanvaikom@gmail.com', '$2y$10$71BjuT7z2gwD.G1qLMC.4eXfPoJpP3QlEXTVgSZJG23rIWnTeaE5K', 'ticket_booker', '2025-06-30 05:16:38'),
(108, 'Aftab S', 'aftab123@gmail.com', '$2y$10$AIiW4QlkWd/GdqTwrLrI3uL2/hUbsuMtucAv/QF8Z3CJH5/2LDwTu', 'event_organizer', '2025-07-01 04:27:28'),
(109, 'Kaladin Stormblessed', 'kaladin@gmail.com', '$2y$10$YPk7tSEGfdXNDkye0W6Nu.tIZXMOX2.5rRDpW5dpEn4Bn/0pFKePO', 'event_organizer', '2025-07-02 14:59:13'),
(110, 'Justin Xavier', 'justinx123@gmail.com', '$2y$10$vEEdHXvBr3VjjBkii8PqAOYs.wakYY9yxwKa05wB6SSh2o6aTACwm', 'ticket_booker', '2025-07-06 11:23:38'),
(111, 'Sam Temple', 'samtemple@email.com', '$2y$10$orYZLZJVrZsStc1uu6aVseN2i20oMyQOtnwQjamz8GbOi5PAdniaC', 'ticket_booker', '2025-07-06 15:41:15'),
(112, 'Anakin Skywalker', 'anakinskywalker@email.com', '$2y$10$p3G9voGFtc7UpxuI9.mmI.hzN.2rgC7yyLLqQf2Wg2QHKMO0WFAB.', 'event_organizer', '2025-07-06 15:42:56'),
(113, 'Po', 'dragonwarrior@email.com', '$2y$10$6K/ceQLbycdRGOQS0WIhuuMR4DzDL33CyZ5v2Z8qexRnsySKQk13O', 'ticket_booker', '2025-07-06 15:49:44'),
(114, 'Sasikok', 'sasikok@email.com', '$2y$10$CcEV5S.Fnt.pBBmC2BRfGONIJDgZSSo/58Fabfb4xauMT/QbAFX1u', 'event_organizer', '2025-07-06 15:50:35'),
(115, 'Kaz Brekker', 'kazbrekker@email.com', '$2y$10$rYj49OyN/os/vBfdqptre.SvDxj2jbBGX0ZnzPqH/GCM2oIokfXN.', 'ticket_booker', '2025-07-06 15:51:24'),
(116, 'Gerard Way', 'mcr5@email.com', '$2y$10$XZdBup.pdyiLzosmgTyDFeA0W8eUNlPrx8eaXWLctVSHDDOXFiryS', 'ticket_booker', '2025-07-06 15:53:39'),
(117, 'Gumball Waterson', 'gumball@email.com', '$2y$10$NpOnsrPVNMtnR6Fp6UejP.XKq707n168cp7TPnyWLx4UmyE.t9LOq', 'ticket_booker', '2025-07-06 15:54:50'),
(118, 'Adwaith Ajith', 'unni123@gmail.com', '$2y$10$FMpZw5Cg8FbOLbN3.o1Taee2mIe7o5wjQ4dsM6mvfU9OdgU5YlNBS', 'ticket_booker', '2025-07-08 07:49:55'),
(120, 'sampuser', 'samuser@sam.sam', '$2y$10$4W9VU9cJCvQZDEigBLdu/u0vDxFgwG3jOFp6XWSPf6DJDKT6qIMg.', 'ticket_booker', '2025-07-10 09:11:31'),
(121, 'Obi-Wan Kenobi', 'obione@gmail.com', '$2y$10$VOaek7XiBR7Xw4ZIBzZRuOeSgAvyomSEk32Um0/t0S0slN13X4Qk6', 'event_organizer', '2025-07-27 11:30:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_ibfk_1` (`user_id`),
  ADD KEY `bookings_ibfk_2` (`event_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `event_images`
--
ALTER TABLE `event_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `payments_ibfk_1` (`booking_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `event_images`
--
ALTER TABLE `event_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_images`
--
ALTER TABLE `event_images`
  ADD CONSTRAINT `event_images_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
