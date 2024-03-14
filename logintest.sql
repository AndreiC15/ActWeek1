-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 14, 2024 at 01:13 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `logintest`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_acct`
--

CREATE TABLE `user_acct` (
  `ID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `MiddleName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `Country` varchar(50) NOT NULL,
  `Province` varchar(50) NOT NULL,
  `CityCity` varchar(50) NOT NULL,
  `District` varchar(50) NOT NULL,
  `HouseNoStreet` varchar(50) NOT NULL,
  `ZipCode` varchar(255) NOT NULL,
  `ProfilePic` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_acct`
--

INSERT INTO `user_acct` (`ID`, `FirstName`, `MiddleName`, `LastName`, `Email`, `Password`, `PhoneNumber`, `Country`, `Province`, `CityCity`, `District`, `HouseNoStreet`, `ZipCode`, `ProfilePic`) VALUES
(89, 'Christian Javen', 'Yuquimpo', 'Samson', 'tkhized@gmail.com', 'tkhized123', '09223333331', 'Canada', 'Manitoba', 'Winnipeg', 'The Exchange District', '102 Good Street', 'R0E-1J3', 'profilePic/Headbang.gif'),
(90, 'Matthew', 'Tiotangco', 'Caldeorn', 'matthew18@gmail.com', 'matthew18', '99999999999', 'A', 'A', 'A', 'A', '0701 Talong Purok', '0213', ''),
(91, 'Calderon', 'Calderon', 'Calderon', 'andreicalderon15@gmail.com', 'andreic15151', '22222222222', 'Aaa', 'Aaa', 'Aaa', 'Aaa', 'Aaa', '123', 'profilePic/cropped-1920-1080-1297017.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `wallpaper`
--

CREATE TABLE `wallpaper` (
  `WallpaperID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `WallpaperLocation` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallpaper`
--

INSERT INTO `wallpaper` (`WallpaperID`, `Title`, `WallpaperLocation`) VALUES
(47, 'Spider-Man', 'upload/789178.jpg'),
(48, 'Pacific Rim Uprising', 'upload/cropped-1360-768-910645.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user_acct`
--
ALTER TABLE `user_acct`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `wallpaper`
--
ALTER TABLE `wallpaper`
  ADD PRIMARY KEY (`WallpaperID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_acct`
--
ALTER TABLE `user_acct`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `wallpaper`
--
ALTER TABLE `wallpaper`
  MODIFY `WallpaperID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
