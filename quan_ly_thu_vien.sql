-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2025 at 09:36 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quan_ly_thu_vien`
--

-- --------------------------------------------------------

--
-- Table structure for table `baocao`
--

CREATE TABLE `baocao` (
  `ma_bao_cao` int(11) NOT NULL,
  `loai_bao_cao` varchar(100) NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `mo_ta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `baocao`
--

INSERT INTO `baocao` (`ma_bao_cao`, `loai_bao_cao`, `ngay_tao`, `mo_ta`) VALUES
(1, 'Sách đang được mượn', '2025-03-06 19:49:33', 'Danh sách các sách hiện đang được mượn trong thư viện'),
(2, 'Số lượng bạn đọc', '2025-03-06 19:49:33', 'Thống kê tổng số bạn đọc đang đăng ký sử dụng thư viện');

-- --------------------------------------------------------

--
-- Table structure for table `muontra`
--

CREATE TABLE `muontra` (
  `ma_giao_dich` int(11) NOT NULL,
  `ma_nguoi_dung` int(11) NOT NULL,
  `ma_sach` int(11) NOT NULL,
  `ngay_muon` date NOT NULL,
  `han_tra` date NOT NULL,
  `ngay_tra` date DEFAULT NULL,
  `trang_thai` enum('Đang mượn','Đã trả','Quá hạn') DEFAULT 'Đang mượn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `muontra`
--

INSERT INTO `muontra` (`ma_giao_dich`, `ma_nguoi_dung`, `ma_sach`, `ngay_muon`, `han_tra`, `ngay_tra`, `trang_thai`) VALUES
(1, 1, 1, '2024-03-01', '2024-03-10', NULL, 'Đang mượn'),
(2, 2, 2, '2024-03-02', '2024-03-11', NULL, 'Đang mượn');

-- --------------------------------------------------------

--
-- Table structure for table `nguoidung`
--

CREATE TABLE `nguoidung` (
  `ma_nguoi_dung` int(11) NOT NULL,
  `ho_ten` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `vai_tro` enum('Thủ thư','Bạn đọc') NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nguoidung`
--

INSERT INTO `nguoidung` (`ma_nguoi_dung`, `ho_ten`, `email`, `mat_khau`, `vai_tro`, `ngay_tao`) VALUES
(1, 'Nguyễn Văn A', 'nguyenvana@email.com', '123456', 'Bạn đọc', '2025-03-06 19:49:33'),
(2, 'Trần Thị B', 'tranthib@email.com', '123456', 'Bạn đọc', '2025-03-06 19:49:33'),
(3, 'Admin Thư viện', 'admin@email.com', 'admin123', 'Thủ thư', '2025-03-06 19:49:33');

-- --------------------------------------------------------

--
-- Table structure for table `sach`
--

CREATE TABLE `sach` (
  `ma_sach` int(11) NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `tac_gia` varchar(255) NOT NULL,
  `nha_xuat_ban` varchar(255) DEFAULT NULL,
  `nam_xuat_ban` year(4) DEFAULT NULL,
  `the_loai` varchar(100) DEFAULT NULL,
  `trang_thai` enum('Còn','Đã mượn','Mất','Hỏng') DEFAULT 'Còn',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sach`
--

INSERT INTO `sach` (`ma_sach`, `tieu_de`, `tac_gia`, `nha_xuat_ban`, `nam_xuat_ban`, `the_loai`, `trang_thai`, `ngay_tao`) VALUES
(1, 'Lập trình C cơ bản', 'Nguyễn Văn C', 'NXB Giáo dục', '2020', 'Công nghệ thông tin', 'Còn', '2025-03-06 19:49:33'),
(2, 'SQL cho người mới bắt đầu', 'Trần Văn D', 'NXB Đại học', '2019', 'Công nghệ thông tin', 'Còn', '2025-03-06 19:49:33'),
(3, 'Học Python trong 24 giờ', 'Lê Thị E', 'NXB Khoa học', '2021', 'Lập trình', 'Còn', '2025-03-06 19:49:33');

-- --------------------------------------------------------

--
-- Table structure for table `vipham`
--

CREATE TABLE `vipham` (
  `ma_vi_pham` int(11) NOT NULL,
  `ma_giao_dich` int(11) NOT NULL,
  `loai_vi_pham` enum('Mất sách','Hư hỏng','Trễ hạn') NOT NULL,
  `muc_phat` decimal(10,2) NOT NULL,
  `trang_thai` enum('Chưa thanh toán','Đã thanh toán') DEFAULT 'Chưa thanh toán',
  `ngay_xu_ly` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vipham`
--

INSERT INTO `vipham` (`ma_vi_pham`, `ma_giao_dich`, `loai_vi_pham`, `muc_phat`, `trang_thai`, `ngay_xu_ly`) VALUES
(1, 1, 'Trễ hạn', 5000.00, 'Chưa thanh toán', '2025-03-06 19:49:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `baocao`
--
ALTER TABLE `baocao`
  ADD PRIMARY KEY (`ma_bao_cao`);

--
-- Indexes for table `muontra`
--
ALTER TABLE `muontra`
  ADD PRIMARY KEY (`ma_giao_dich`),
  ADD KEY `ma_nguoi_dung` (`ma_nguoi_dung`),
  ADD KEY `ma_sach` (`ma_sach`);

--
-- Indexes for table `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`ma_nguoi_dung`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `sach`
--
ALTER TABLE `sach`
  ADD PRIMARY KEY (`ma_sach`);

--
-- Indexes for table `vipham`
--
ALTER TABLE `vipham`
  ADD PRIMARY KEY (`ma_vi_pham`),
  ADD KEY `ma_giao_dich` (`ma_giao_dich`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `baocao`
--
ALTER TABLE `baocao`
  MODIFY `ma_bao_cao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `muontra`
--
ALTER TABLE `muontra`
  MODIFY `ma_giao_dich` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `ma_nguoi_dung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sach`
--
ALTER TABLE `sach`
  MODIFY `ma_sach` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vipham`
--
ALTER TABLE `vipham`
  MODIFY `ma_vi_pham` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `muontra`
--
ALTER TABLE `muontra`
  ADD CONSTRAINT `muontra_ibfk_1` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `nguoidung` (`ma_nguoi_dung`) ON DELETE CASCADE,
  ADD CONSTRAINT `muontra_ibfk_2` FOREIGN KEY (`ma_sach`) REFERENCES `sach` (`ma_sach`) ON DELETE CASCADE;

--
-- Constraints for table `vipham`
--
ALTER TABLE `vipham`
  ADD CONSTRAINT `vipham_ibfk_1` FOREIGN KEY (`ma_giao_dich`) REFERENCES `muontra` (`ma_giao_dich`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
