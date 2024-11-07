-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2024 at 02:59 AM
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
-- Database: `espino`
--

-- --------------------------------------------------------

--
-- Table structure for table `sam`
--

CREATE TABLE `sam` (
  `iID` int(11) NOT NULL,
  `iName` varchar(50) NOT NULL,
  `iEmail` varchar(50) NOT NULL,
  `iPass` varchar(256) NOT NULL,
  `iRegDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sam`
--

INSERT INTO `sam` (`iID`, `iName`, `iEmail`, `iPass`, `iRegDate`) VALUES
(1, 'Sam Espino', 'sam@gmail.com', 'secret1', '2024-01-28 11:52:57'),
(2, 'Shae Smith', 'shae@gmail.com', 'secret2', '2024-01-28 11:52:57'),
(3, 'Third Lee', 'third@gmail.com', 'secret3', '2024-01-28 11:54:05'),
(4, 'Lorn Tyler', 'lorn@gmail.com', 'secret4', '2024-01-28 11:54:05'),
(5, 'Manny Pogi', 'manny@gmail.com', '$2y$10$Als3VHs4WzjkN7Kgz3gIj.Ywe8EbAkNx4nniNOTJXFBvtcUnY4uRO', '2024-02-13 06:20:50'),
(6, 'erick campano', 'erick@gmail.com', '$2y$10$HzlJzPFJpxLGakyy8P6hauBC96tM1BqZAE0v5UJm5YLL72.hnpdUK', '2024-02-13 06:36:00'),
(7, 'asd espino', 'asd@gmail.com', '$2y$10$CnOORA3Kv5pcf80YUUhZLetR64b2W9o2E9owtcLq3yREzZI4AIaZy', '2024-02-13 08:54:02'),
(8, 'New Account', 'new1@gmail.com', '$2y$10$KWJy3lQmR3V3uz8jByyPnuZ/IZwyClNiXlS/NTpLfO1zfG29jP8yS', '2024-03-19 05:17:21'),
(9, 'Sample Client', 'sample@yahoo.com', '$2y$10$o5YvmEwCztcQrYBf3a5.8.y9xI0SZGEIRttytrmOAY.VeSPLERIxu', '2024-03-19 06:06:12'),
(10, 'AGUINALDO PIERRE ISAIAH IGNACIO', 'aguinaldopi@students.nu-baliwag.edu.ph', '$2y$10$VMlB7tfgMV01GXzq2.HMFuj0r.QbJUHiHqDKXZwN6KlZbd2UZMvP2', '2024-04-28 00:38:23'),
(11, 'ANGAD BRIXTER GABRIEL ORDINARIO', 'angadbg@students.nu-baliwag.edu.ph', '$2y$10$SC7HyacXN9D40e7T/JHNbuxMSUrCChNai8YmSLsGmD4M9S6kxhtF2', '2024-04-28 00:39:06'),
(12, 'BAGTAS DHENNIS SEWEL CORPUZ', 'bagtasdc@students.nu-baliwag.edu.ph', '$2y$10$Hr5zYYLBqnYZZdNZ3lscqOnHqg662amoZEpA0FB0SWFn9DKnkHNFO', '2024-04-28 00:39:48'),
(13, 'CARMONA MARC JOSEPH PASCO', 'carmonamjp@students.nu-baliwag.edu.ph', '$2y$10$FZOoc3N8d2h61WskW4W8mOQXl/FZn8vy3zpeOikp/4znSiNWnIuIu', '2024-04-28 00:40:48'),
(14, 'CATUBAG JOAQUIN LORENZO CRUZ', 'catubagjc@students.nu-baliwag.edu.ph', '$2y$10$txMo4p.Z59atpqkmDJlHNuUvsUCcorFzrgb6r6lARNiPqiueDIy76', '2024-04-28 00:41:22'),
(15, 'CLAVIO LUIGI MIGUEL AVENDANO', 'claviolma@students.nu-baliwag.edu.ph', '$2y$10$1PMhVwFfnbbzN0mexwxQ0.iqkAU39AUc8fw7y7Uthb81jSU1EqRlu', '2024-04-28 00:42:15'),
(16, 'CORREA CHARLES GARCIA', 'correacg@students.nu-baliwag.edu.ph', '$2y$10$96MQS69J2XxG3pxBBIAnsuxheo8OFdr9yWHRSKzxIhwvbkZpCcyS2', '2024-04-28 00:42:51'),
(17, 'CRUZ KIAN GABRIEL ROMAN', 'cruzkgr@students.nu-baliwag.edu.ph', '$2y$10$emJly2AQZaI1izuc90akL.Ydb0VlBZvCiKB0RMtAhz/a0lma8P5Fm', '2024-04-28 00:43:30'),
(18, 'CRUZ MARK ADRIANE CALDERON', 'cruzmac@students.nu-baliwag.edu.ph', '$2y$10$I6TIT9xjPWaXmiNms3rApeF/OWrMEfwzCEVj6BpTl4ic1sD8TVPHq', '2024-04-28 00:44:10'),
(19, 'DAEPO ANDREW BERNARDO', 'daepoab@students.nu-baliwag.edu.ph', '$2y$10$hU/IbT/oHheIWrnAR8QacOjGwa3SW0kCHMie0ITM4V8p6eR9LwsPa', '2024-04-28 00:44:46'),
(20, 'DAVID RICHARDSON SAN DIEGO', 'davidrs@students.nu-baliwag.edu.ph', '$2y$10$isaeoWvHuZequ4IJF3heZOWSqP2EXhnHUU04twdPV5a08xqeJd5va', '2024-04-28 00:45:28'),
(21, 'DE CASTRO HANS LOREN MAMARADLO', 'decastromlm@students.nu-baliwag.edu.ph', '$2y$10$tw0Lvk/NcXvTLpj6VGOWWOsthOeFrTMSaXd13MaKUw66Le41Sq6Su', '2024-04-28 00:46:52'),
(22, 'DE JESUS JANSER JERICHO CORDERO', 'dejesusjjc@students.nu-baliwag.edu.ph', '$2y$10$1J5hIkn4whjYuMEG70ixG.nXU9fHCmsrENR0H17zPYq/Kfnce/iqy', '2024-04-28 00:47:25'),
(23, 'DE LEON DENISE ALPHA CIPRIANO', 'deleondc@students.nu-baliwag.edu.ph', '$2y$10$zPORvdyBRNO2yrWLe4g4VOuofU5zkN8MwU6W4w2Ni3WRlxT0DuEeq', '2024-04-28 00:48:07'),
(24, 'DINO JUSTIN SAN JUAN', 'dinojjs@students.nu-baliwag.edu.ph', '$2y$10$UaG.EI6MeP4YXrX3/8eJ/.W/.2.kL1dZP9wH2kx1rFwwILnap3P2q', '2024-04-28 00:48:46'),
(25, 'ELLADO CYRUS ZEDRICK GASPAR', 'elladoczg@students.nu-baliwag.edu.ph', '$2y$10$JtTzQImy08g2/wDablOO9eLS0S8nJ1NBBTAZkkSDRG2K2OJdw9sf.', '2024-04-28 00:49:21'),
(26, 'EVANGELISTA MADELLEINE HIPOLITO', 'evangelistaah@students.nu-baliwag.edu.ph', '$2y$10$OrosyIQezUfFvT4GE7oksuw8r764WRm0gYJpKYz5WrCFNuyjf11oi', '2024-04-28 00:50:00'),
(27, 'FABIAN JOSEPH JR DE GUZMAN', 'fabianjg@students.nu-baliwag.edu.ph', '$2y$10$gWCdtcE2Ws/6XHV2c2bxjeSaPMoyHccgQirZUwLAZ3qtOOFOgXg7i', '2024-04-28 00:50:35'),
(28, 'FRANCISCO RICHMOND GUMABON', 'franciscorg@students.nu-baliwag.edu.ph', '$2y$10$qP3/LyPXOlI/7UUAhgedTu0znKZe4xMhhgIuWtM9vXsfrSMMzCDaG', '2024-04-28 00:51:16'),
(29, 'GALANG KIEL NOEL AVENDANO', 'galangkna@students.nu-baliwag.edu.ph', '$2y$10$6.g0bEq2wOMUVMEvdmydx.5Aa5IbCfL0bt1b44T98gll4mA3YSbqa', '2024-04-28 00:51:54'),
(30, 'GARCIA EDWARD JOMARI', 'garciae@students.nu-baliwag.edu.ph', '$2y$10$OmU73h9CGeWK2Ho.T6S/HuTYftDLqOxzSO3FCeLW52XkaMJqnt83m', '2024-04-28 00:52:35'),
(31, 'GARCIA NATHANIEL CUNANAN', 'garcianc@students.nu-baliwag.edu.ph', '$2y$10$WZttvt.SqxUckAGbCoZ5pujQFY8YDlh08xqk4p4HclbBMIM77D0je', '2024-04-28 00:53:55'),
(32, 'ISLETA CHRISTIAN MILLES CONCEPCION', 'isletacc@students.nu-baliwag.edu.ph', '$2y$10$Ql1q9qGHlTIoXmgEc1CeKuGqoMiGj5eq6Kx1MM5t01AHcsb0rX6pS', '2024-04-28 00:54:26'),
(33, 'LADIA LEONARDO SANTIAGO', 'ladials@students.nu-baliwag.edu.ph', '$2y$10$1HzlTMUuEoMpaU3fAmpg.uI12hF4iRjaYfgno3C.ZqWv0dO9n46ii', '2024-04-28 00:55:01'),
(34, 'LEGASPI FRANCES ELEANOR DAYSA', 'legaspifed@students.nu-baliwag.edu.ph', '$2y$10$Nw02iGidI4dCCfYE6fwEke65eRdQN.QjleE5xTLUp6ptZwDOgLj0C', '2024-04-28 00:55:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sam`
--
ALTER TABLE `sam`
  ADD PRIMARY KEY (`iID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sam`
--
ALTER TABLE `sam`
  MODIFY `iID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
