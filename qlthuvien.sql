-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2025 at 03:04 AM
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
-- Database: `qlthuvien`
--

-- --------------------------------------------------------

--
-- Table structure for table `ban_doc`
--

CREATE TABLE `ban_doc` (
  `ma_ban_doc` int(11) NOT NULL,
  `ten_dang_nhap` varchar(50) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `ho_ten` varchar(100) DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `so_dien_thoai` varchar(20) DEFAULT NULL,
  `dia_chi` text DEFAULT NULL,
  `vai_tro` varchar(20) DEFAULT 'bandoc',
  `trang_thai_the` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1: Hoạt động, 0: Bị Khóa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ban_doc`
--

INSERT INTO `ban_doc` (`ma_ban_doc`, `ten_dang_nhap`, `mat_khau`, `ho_ten`, `ngay_sinh`, `email`, `so_dien_thoai`, `dia_chi`, `vai_tro`, `trang_thai_the`) VALUES
(4, 'quynh23', '$2y$10$ccauLe.VhLlRRmiV3m2z8.uqQAN6Sw06PIT7csuOdBZkoQu7MC/wu', 'Nguyễn Thị Quỳnh', '2004-05-23', 'quynh23@gmail.com', '0962030970', 'Hà Nam', 'admin', 1),
(5, 'quan17', '$2y$10$XNex3/74ZWH0NrsVRiNdM.0QfLBzOhs9mVTbj2T4n1J4dI8ls0Pci', 'Phạm Mạnh Quân', '2004-12-17', 'quan1712@gmail.com', '0372473780', 'Hải Phòng', 'admin', 1),
(7, 'an2000', '$2y$10$Btyo.vcv0tCiPgW0ipXX3.IMdM8Yrhnp4mKwOMREF6ot0V3zaDolG', 'Phạm Minh An', '1999-04-19', 'anne@gmail.com', '0964685125', 'Lào Cai', 'bandoc', 1),
(12, 'Phạm Công Thành', '$2y$10$qyY3pQALTqiQVHmHXUFFZOfBm8W/TvWRGnJQGzphcyYW8h4yw4D7O', 'Phạm Công Thành', '2004-09-12', 'pt175436@gmail.com', '0352738483', 'số nhà 30 Nam Dư Lĩnh Nam', 'bandoc', 1),
(13, 'phuong19', '$2y$10$H5eascCPMT.vmuYGtcsJ9uHs2HG1gcAUDiGqGYpRCC8m1UdWGCktu', 'Nguyễn Thị Phượng', '1997-01-19', 'phuong190197@gmail.com', '0979255978', 'Lào Cai', 'bandoc', 1),
(15, 'anh256', '$2y$10$aAVrfk9HRmUv.FTai7xS/uCuCCcBHoKzcduXVlzsGzZYL8g5t8J7G', 'Bùi Thanh Anh', NULL, 'anhth9@gmail.com', '0365204236', '', 'bandoc', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_dat_phong`
--

CREATE TABLE `chi_tiet_dat_phong` (
  `ma_phieu` int(11) NOT NULL,
  `ma_phong` int(11) NOT NULL,
  `thoi_gian_dat` datetime DEFAULT NULL,
  `thoi_gian_tra` datetime DEFAULT NULL,
  `trang_thai` varchar(20) DEFAULT 'dang_dat',
  `ma_ban_doc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chi_tiet_dat_phong`
--

INSERT INTO `chi_tiet_dat_phong` (`ma_phieu`, `ma_phong`, `thoi_gian_dat`, `thoi_gian_tra`, `trang_thai`, `ma_ban_doc`) VALUES
(46, 1, '2025-05-22 20:49:30', '2025-05-22 22:49:30', 'da_muon', 12),
(50, 5, '2025-05-23 01:24:17', '2025-05-23 03:24:17', 'da_muon', 13),
(51, 17, '2025-05-23 01:24:30', '2025-05-23 03:24:30', 'da_muon', 13),
(52, 1, '2025-05-23 01:24:39', '2025-05-23 03:24:39', 'da_muon', 13);

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_muon`
--

CREATE TABLE `chi_tiet_muon` (
  `ma_phieu` int(11) NOT NULL,
  `ma_sach` int(11) NOT NULL,
  `so_luong` int(11) DEFAULT NULL,
  `ten_sach` varchar(255) DEFAULT NULL,
  `ngay_muon` date DEFAULT NULL,
  `ngay_tra` date DEFAULT NULL,
  `trang_thai` varchar(20) DEFAULT NULL,
  `ma_ban_doc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chi_tiet_muon`
--

INSERT INTO `chi_tiet_muon` (`ma_phieu`, `ma_sach`, `so_luong`, `ten_sach`, `ngay_muon`, `ngay_tra`, `trang_thai`, `ma_ban_doc`) VALUES
(47, 8, 1, 'An Introduction to Language and Linguistics', '2025-05-23', '2025-05-30', 'dang_muon', 13),
(48, 5, 1, 'Cấu trúc dữ liệu và thuật toán', '2025-05-23', '2025-05-30', 'dang_muon', 13),
(49, 22, 1, 'Tuyển tập các bản án – Hình sự', '2025-05-23', '2025-05-30', 'dang_muon', 13);

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_nhap`
--

CREATE TABLE `chi_tiet_nhap` (
  `ma_phieu_nhap` int(11) NOT NULL,
  `ma_sach` int(11) NOT NULL,
  `so_luong` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kho`
--

CREATE TABLE `kho` (
  `ma_kho` int(11) NOT NULL,
  `ten_kho` varchar(100) NOT NULL,
  `suc_chua` int(11) DEFAULT NULL,
  `vi_tri` varchar(255) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `trang_thai` varchar(20) DEFAULT 'Hoạt động',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kho`
--

INSERT INTO `kho` (`ma_kho`, `ten_kho`, `suc_chua`, `vi_tri`, `mo_ta`, `trang_thai`, `ngay_tao`) VALUES
(1, 'Kho Sách Khoa Học Tự Nhiên', 1600, 'Tầng 2 khu C', 'Lưu trữ sách về Toán, Lý, Hóa, Sinh học', 'hoat_dong', '2025-05-22 18:01:27'),
(2, 'Kho Sách Khoa Học Xã Hội', 1200, 'Tầng 2 Khu B', 'Lưu trữ sách về Kinh tế, Luật, Xã hội học', 'Hoạt động', '2025-05-22 18:01:27'),
(3, 'Kho Sách Công Nghệ Thông Tin', 800, 'Tầng 2 khu A\r\n', 'Lưu trữ sách về IT, Lập trình, Mạng', 'Hoạt động', '2025-05-22 18:01:27'),
(4, 'Kho Sách Ngoại Ngữ', 600, 'Tầng 1 khu C\r\n', 'Lưu trữ sách tiếng Anh, tiếng Nhật, tiếng Hàn', 'Hoạt động', '2025-05-22 18:01:27'),
(5, 'Kho Sách Văn Học', 1000, 'Tầng 1 khu B\r\n', 'Lưu trữ tiểu thuyết, thơ ca, truyện ngắn', 'Hoạt động', '2025-05-22 18:01:27'),
(6, 'Kho Lưu Trữ', 2000, 'tầng 1 khu A\r\n', 'Lưu trữ sách cũ và tài liệu lưu trữ', 'hoat_dong', '2025-05-22 18:01:27'),
(7, 'Kho sách Âm Nhạc', 100, 'Tầng 2 khu C', 'kho sách âm nhạc ', 'hoat_dong', '2025-05-22 20:17:38');

-- --------------------------------------------------------

--
-- Table structure for table `nha_cung_cap`
--

CREATE TABLE `nha_cung_cap` (
  `ma_ncc` int(11) NOT NULL,
  `ten_ncc` varchar(100) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `so_dien_thoai` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nguoi_lien_he` varchar(100) DEFAULT NULL,
  `trang_thai` enum('hoat_dong','ngung_hop_tac') DEFAULT 'hoat_dong',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nha_cung_cap`
--

INSERT INTO `nha_cung_cap` (`ma_ncc`, `ten_ncc`, `dia_chi`, `so_dien_thoai`, `email`, `nguoi_lien_he`, `trang_thai`, `ngay_tao`) VALUES
(1, 'Nhà Xuất Bản Giáo Dục Việt Nam', '81 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội', '024-3822-3434', 'info@nxbgd.vn', 'Nguyễn Văn A', 'hoat_dong', '2025-05-22 18:05:44'),
(2, 'Nhà Xuất Bản Trẻ', '161B Lý Chính Thắng, Q.3, TP.HCM', '028-3930-5859', 'info@nxbtre.com.vn', 'Trần Thị B', 'hoat_dong', '2025-05-22 18:05:44'),
(3, 'Nhà Xuất Bản Thế Giới', '7 Nguyễn Thị Minh Khai, Q.1, TP.HCM', '028-3822-2340', 'thegioi@nxbthegioi.vn', 'Lê Văn C', 'hoat_dong', '2025-05-22 18:05:44'),
(4, 'Công Ty TNHH Sách Alpha', '123 Điện Biên Phủ, Ba Đình, Hà Nội', '024-3736-2612', 'contact@alphabooks.vn', 'Phạm Minh D', 'hoat_dong', '2025-05-22 18:05:44'),
(5, 'Nhà Phát Hành Fahasa', '60-62 Lê Lợi, Q.1, TP.HCM', '028-3822-4477', 'fahasa@fahasa.com', 'Hoàng Thị E', 'hoat_dong', '2025-05-22 18:05:44'),
(6, 'Công Ty CP Đầu Tư và Phát Triển Giáo Dục Phương Nam', 'số 110 Phố Ngũ Nhạc Hoàng Mai Hà Nội\r\n', '028-3848-4499', 'vuvanphi@pnbook.com', 'Vũ Văn Phi', 'hoat_dong', '2025-05-22 18:05:44');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_muon`
--

CREATE TABLE `phieu_muon` (
  `ma_phieu` int(11) NOT NULL,
  `ma_ban_doc` int(11) DEFAULT NULL,
  `ngay_muon` date DEFAULT NULL,
  `ngay_tra` date DEFAULT NULL,
  `trang_thai` enum('dang_muon','da_tra') DEFAULT 'dang_muon'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phieu_muon`
--

INSERT INTO `phieu_muon` (`ma_phieu`, `ma_ban_doc`, `ngay_muon`, `ngay_tra`, `trang_thai`) VALUES
(46, 12, '2025-05-22', '2025-05-29', 'da_tra'),
(47, 13, '2025-05-23', '2025-05-30', 'dang_muon'),
(48, 13, '2025-05-23', '2025-05-30', 'dang_muon'),
(49, 13, '2025-05-23', '2025-05-30', 'dang_muon'),
(50, 13, '2025-05-23', '2025-05-30', 'dang_muon'),
(51, 13, '2025-05-23', '2025-05-30', 'dang_muon'),
(52, 13, '2025-05-23', '2025-05-30', 'da_tra');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_nhap`
--

CREATE TABLE `phieu_nhap` (
  `ma_phieu_nhap` int(11) NOT NULL,
  `ngay_nhap` date DEFAULT NULL,
  `ma_kho` int(11) DEFAULT NULL,
  `ma_ncc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phong`
--

CREATE TABLE `phong` (
  `id` int(11) NOT NULL,
  `ten_phong` varchar(50) DEFAULT NULL,
  `suc_chua` int(11) DEFAULT NULL,
  `loai_nhom` varchar(50) DEFAULT NULL,
  `trang_thai` varchar(20) DEFAULT 'trống',
  `mo_ta` text DEFAULT NULL,
  `anh` varchar(100) DEFAULT NULL,
  `ma_phieu` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phong`
--

INSERT INTO `phong` (`id`, `ten_phong`, `suc_chua`, `loai_nhom`, `trang_thai`, `mo_ta`, `anh`, `ma_phieu`) VALUES
(1, 'Phòng Nhỏ 1', 6, 'nhỏ', 'da_muon', 'Phòng học nhóm nhỏ, phù hợp nhóm 5–7 người.', 'nho1.jpg', 52),
(5, 'Phòng Nhỏ 5', 6, 'nhỏ', 'trong', 'Phòng học nhóm nhỏ, phù hợp nhóm 5–7 người.', 'nho5.jpg', NULL),
(6, 'Phòng Nhỏ 6', 6, 'nhỏ', 'trong', 'Phòng học nhóm nhỏ, phù hợp nhóm 5–7 người.', 'nho6.jpg', NULL),
(10, 'Phòng Vừa 4', 20, 'vừa', 'trong', 'Phòng học nhóm vừa, sức chứa lớn hơn, phù hợp từ 7–20 người.', 'vua4.jpg', NULL),
(11, 'Phòng Vừa 5', 14, 'vừa', 'trong', 'Phòng học nhóm vừa, sức chứa lớn hơn, phù hợp từ 7–20 người.', 'vua5.jpg', NULL),
(13, 'Phòng Vừa 7', 17, 'vừa', 'trong', 'Phòng học nhóm vừa, sức chứa lớn hơn, phù hợp từ 7–20 người.', 'vua7.jpg', NULL),
(15, 'Phòng Lớn 2', 40, 'lớn', 'trong', 'Phòng học nhóm lớn, tổ chức được các buổi thảo luận, hội thảo quy mô lớn.', 'lon2.jpg', NULL),
(16, 'Phòng Lớn 3', 45, 'lớn', 'trong', 'Phòng học nhóm lớn, tổ chức được các buổi thảo luận, hội thảo quy mô lớn.', 'lon3.jpg', NULL),
(17, 'Phòng Lớn 4', 50, 'lớn', 'da_muon', 'Phòng học nhóm lớn, tổ chức được các buổi thảo luận, hội thảo quy mô lớn.', 'lon4.jpg', 51);

-- --------------------------------------------------------

--
-- Table structure for table `sach`
--

CREATE TABLE `sach` (
  `ma_sach` int(11) NOT NULL,
  `ten_sach` varchar(255) NOT NULL,
  `tac_gia` varchar(255) DEFAULT NULL,
  `nha_xuat_ban` varchar(100) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `nam_xuat_ban` int(11) DEFAULT NULL,
  `so_luong` int(11) DEFAULT 1,
  `anh` varchar(255) DEFAULT NULL,
  `the_loai` varchar(100) DEFAULT NULL,
  `ma_ncc` int(11) DEFAULT NULL,
  `gia_nhap` decimal(10,2) DEFAULT 0.00,
  `ngay_nhap` timestamp NOT NULL DEFAULT current_timestamp(),
  `ma_kho` int(11) DEFAULT NULL,
  `trang_thai_sach` enum('trong_kho','co_the_muon','tam_ngung') DEFAULT 'trong_kho'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sach`
--

INSERT INTO `sach` (`ma_sach`, `ten_sach`, `tac_gia`, `nha_xuat_ban`, `mo_ta`, `nam_xuat_ban`, `so_luong`, `anh`, `the_loai`, `ma_ncc`, `gia_nhap`, `ngay_nhap`, `ma_kho`, `trang_thai_sach`) VALUES
(1, 'Khéo ăn nói sẽ có được thiên hạ', 'Trác Nhã', 'NXB Lao Động', 'Cẩm nang giao tiếp giúp bạn tự tin hơn trong cuộc sống và công việc.', 2017, 21, 'sach1.jpg', 'Kỹ năng giao tiếp', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(2, '500 bài thuốc hay chữa bệnh theo kinh nghiệm dân gian', 'Nhiều tác giả', 'NXB Y học', 'Tổng hợp các bài thuốc cổ truyền trị bệnh hiệu quả từ kinh nghiệm dân gian.', 2015, 6, 'sach2.jpg', 'Y học', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(3, 'Lén nhặt chuyện đời', 'Mộc Trầm', 'NXB Văn Học', 'Nhật ký cảm xúc giúp chữa lành và truyền động lực sống.', 2012, 7, 'sach3.jpg', 'Tiểu thuyết ngôn tình', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(4, 'Điện toán đám mây', 'Nhiều tác giả', 'NXB Bách Khoa Hà Nội', 'Hướng dẫn sử dụng máy tính, phần mềm cơ bản dành cho người mới bắt đầu.', 2018, 10, 'sach4.jpg', 'Công nghệ thông tin', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(5, 'Cấu trúc dữ liệu và thuật toán', 'Nguyễn Đức Nghĩa', 'NXB Bách Khoa Hà Nội', 'Giáo trình cấu trúc dữ liệu và thuật toán chuyên sâu, phù hợp sinh viên CNTT.', 2016, 3, 'sach5.jpg', 'Tin học ', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(6, 'Nhập môn Linux & phần mềm mã nguồn mở', 'Hà Quốc Trung', 'NXB Bách Khoa Hà Nội', 'Nhập môn hệ điều hành Linux và các phần mềm mã nguồn mở thông dụng.', 2014, 8, 'sach6.jpg', 'Tin học ', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(7, 'Contemporary Linguistics', 'William O\'Grady', 'Bedford/St. Martin\'s', 'Giáo trình ngôn ngữ học hiện đại với các ví dụ và ứng dụng thực tế.', 2010, 6, 'sach7.jpg', 'Ngôn ngữ học', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(8, 'An Introduction to Language and Linguistics', 'Ralph Fasold & Jeff Connor-Linton', 'Cambridge University Press', 'Giới thiệu tổng quan ngôn ngữ học và ứng dụng trong giao tiếp.', 2006, 23, 'sach8.jpg', 'Ngôn ngữ học', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(9, 'Kế toán quản trị', 'PGS.TS. Nguyễn Ngọc Quang', 'NXB Đại học Kinh tế quốc dân', 'Sách cung cấp kiến thức quản trị kế toán và kỹ năng phân tích tài chính.', 2013, 13, 'sach9.jpg', 'Kế toán', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(10, 'Luật kế toán – Chế độ kế toán dành cho doanh nghiệp', 'Kim Phượng', 'NXB Tài chính', 'Phân tích chế độ kế toán theo luật doanh nghiệp mới nhất.', 2015, 22, 'sach10.jpg', 'Kế toán', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(11, 'Kế toán thương mại dịch vụ', 'TS. Trần Phước', 'NXB Tài Chính', 'Tổng quan về kế toán trong lĩnh vực thương mại dịch vụ hiện đại.', 2014, 4, 'sach11.jpg', 'Kế toán', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(12, 'Chí Phèo', 'Nam Cao', 'NXB Văn Học', 'Tác phẩm văn học hiện thực phê phán tiêu biểu của Nam Cao.', 1941, 30, 'sach12.jpg', 'Văn học', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(13, 'Số đỏ', 'Vũ Trọng Phụng', 'NXB Văn Học', 'Truyện ngắn đặc sắc phản ánh xã hội phong kiến và thân phận con người.', 1936, 11, 'sach13.jpg', 'Văn học ', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(14, 'Truyện Kiều', 'Nguyễn Du', 'NXB Văn Học', 'Tác phẩm thơ truyện nổi tiếng của Nguyễn Du, giá trị văn học cao.', 1820, 9, 'sach14.jpg', 'Văn học', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(15, 'Cánh đồng bất tận', 'Nguyễn Ngọc Tư', 'NXB Trẻ', 'Tuyển tập truyện ngắn hiện đại, sâu sắc về cuộc sống nông thôn.', 2005, 2, 'sach15.jpg', 'Văn học', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(16, 'Tâm lý học ứng dụng', 'Patrick King', 'NXB Giáo dục Việt Nam', 'Kỹ năng lắng nghe và thấu hiểu trong giao tiếp tiếng Anh.', 2012, 1, 'sach16.jpg', 'Tâm lý học', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(17, 'Tâm lý học tội phạm', 'Stantion E. Samenow', 'NXB Công an nhân dân', 'Phân tích tâm lý tội phạm dưới góc nhìn khoa học và thực tế.', 2013, 11, 'sach17.jpg', 'Tâm lý học ', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(18, 'Tâm lý phát triển của học sinh', 'Nguyễn Sinh-Lan Phương', 'NXB Lao Động', 'Nghiên cứu sự phát triển tâm lý học sinh qua từng giai đoạn.', 2011, 15, 'sach18.jpg', 'Tâm lý học', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(19, 'Luật bảo vệ môi trường', 'Quốc hội Việt Nam', 'NXB Chính trị quốc gia', 'Tổng hợp các luật bảo vệ môi trường áp dụng tại Việt Nam.', 2014, 25, 'sach19.jpg', 'Pháp luật', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(20, 'Luật trật tự, an toàn giao thông đường bộ', 'Bộ GTVT', 'NXB Giao thông vận tải', 'Luật giao thông đường bộ và các quy định xử phạt hiện hành.', 2015, 17, 'sach20.jpg', 'Pháp luật', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(21, 'Công tác phòng chống dịch bệnh trong nhà trường', 'Bộ Y tế', 'NXB Giáo dục Việt Nam', 'Sách hướng dẫn công tác y tế trong phòng dịch học đường.', 2016, 6, 'sach21.jpg', 'Y học', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(22, 'Tuyển tập các bản án – Hình sự', 'TAND Tối cao', 'NXB Tư pháp', 'Tuyển tập các bản án hình sự tiêu biểu được xét xử bởi TAND Tối cao.', 2017, 7, 'sach22.jpg', 'Pháp luật ', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(23, 'Tuyển tập các bản án – Dân sự', 'TAND Tối cao', 'NXB Tư pháp', 'Các bản án dân sự đã có hiệu lực pháp luật tại Việt Nam.', 2017, 18, 'sach23.jpg', 'Pháp luật', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(24, 'Các thông tư liên tịch', 'Nhiều tác giả', 'NXB Tư pháp', 'Tổng hợp các thông tư liên tịch liên quan ngành tư pháp.', 2018, 13, 'sach24.jpg', 'Pháp luật', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(25, 'Tuyển tập 63 án lệ', 'TAND Tối cao', 'NXB Tư pháp', 'Danh sách 63 án lệ nổi bật được TAND tối cao ban hành.', 2016, 19, 'sach25.jpg', 'Pháp luật', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(26, 'Một số vấn đề về tín ngưỡng tôn giáo ở Việt Nam', 'Ban Tôn giáo Chính phủ', 'NXB Tôn giáo', 'Các nghiên cứu về tín ngưỡng và tôn giáo truyền thống Việt Nam.', 2015, 4, 'sach26.jpg', 'Tôn giáo', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(27, 'Phật tích', 'Thích Minh Châu', 'NXB Tôn giáo', 'Phật tích và những giá trị tâm linh trong văn hóa dân tộc.', 2010, 8, 'sach27.jpg', 'Tôn giáo', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(28, 'Thời đại Hùng Vương', 'Nhiều tác giả', 'NXB Khoa học xã hội', 'Khái quát lịch sử thời đại Hùng Vương qua các truyền thuyết và khảo cổ.', 2013, 13, 'sach28.jpg', 'Văn hóa', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(29, 'Sách thánh hiền nhân', 'Nhiều tác giả', 'NXB Văn Hóa', 'Tuyển tập lời dạy, truyện ngắn đạo đức từ các vị thánh hiền.', 2012, 10, 'sach29.jpg', 'Văn hóa', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon'),
(30, 'Sử ta so với sử tàu', 'Nhiều tác giả', 'NXB Giáo dục Việt Nam', 'So sánh sử liệu Việt Nam với Trung Hoa trong các thời kỳ lịch sử.', 2014, 8, 'sach30.jpg', 'Văn học', NULL, 0.00, '2025-05-22 17:51:47', NULL, 'co_the_muon');

-- --------------------------------------------------------

--
-- Table structure for table `vi_pham`
--

CREATE TABLE `vi_pham` (
  `ma_vi_pham` int(11) NOT NULL,
  `ma_phieu` int(11) DEFAULT NULL,
  `noi_dung` text DEFAULT NULL,
  `muc_phat` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ban_doc`
--
ALTER TABLE `ban_doc`
  ADD PRIMARY KEY (`ma_ban_doc`),
  ADD UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`);

--
-- Indexes for table `chi_tiet_dat_phong`
--
ALTER TABLE `chi_tiet_dat_phong`
  ADD PRIMARY KEY (`ma_phieu`,`ma_phong`),
  ADD KEY `ma_phong` (`ma_phong`);

--
-- Indexes for table `chi_tiet_muon`
--
ALTER TABLE `chi_tiet_muon`
  ADD PRIMARY KEY (`ma_phieu`,`ma_sach`),
  ADD KEY `ma_sach` (`ma_sach`);

--
-- Indexes for table `chi_tiet_nhap`
--
ALTER TABLE `chi_tiet_nhap`
  ADD PRIMARY KEY (`ma_phieu_nhap`,`ma_sach`),
  ADD KEY `ma_sach` (`ma_sach`);

--
-- Indexes for table `kho`
--
ALTER TABLE `kho`
  ADD PRIMARY KEY (`ma_kho`);

--
-- Indexes for table `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  ADD PRIMARY KEY (`ma_ncc`);

--
-- Indexes for table `phieu_muon`
--
ALTER TABLE `phieu_muon`
  ADD PRIMARY KEY (`ma_phieu`),
  ADD KEY `ma_ban_doc` (`ma_ban_doc`);

--
-- Indexes for table `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  ADD PRIMARY KEY (`ma_phieu_nhap`),
  ADD KEY `ma_ncc` (`ma_ncc`),
  ADD KEY `fk_phieu_nhap_kho` (`ma_kho`);

--
-- Indexes for table `phong`
--
ALTER TABLE `phong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_phong_muon_2` (`ma_phieu`);

--
-- Indexes for table `sach`
--
ALTER TABLE `sach`
  ADD PRIMARY KEY (`ma_sach`),
  ADD KEY `fk_sach_kho` (`ma_kho`),
  ADD KEY `fk_sach_ncc` (`ma_ncc`);

--
-- Indexes for table `vi_pham`
--
ALTER TABLE `vi_pham`
  ADD PRIMARY KEY (`ma_vi_pham`),
  ADD KEY `ma_phieu` (`ma_phieu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ban_doc`
--
ALTER TABLE `ban_doc`
  MODIFY `ma_ban_doc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `kho`
--
ALTER TABLE `kho`
  MODIFY `ma_kho` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  MODIFY `ma_ncc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `phieu_muon`
--
ALTER TABLE `phieu_muon`
  MODIFY `ma_phieu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  MODIFY `ma_phieu_nhap` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phong`
--
ALTER TABLE `phong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `sach`
--
ALTER TABLE `sach`
  MODIFY `ma_sach` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `vi_pham`
--
ALTER TABLE `vi_pham`
  MODIFY `ma_vi_pham` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chi_tiet_dat_phong`
--
ALTER TABLE `chi_tiet_dat_phong`
  ADD CONSTRAINT `chi_tiet_dat_phong_ibfk_1` FOREIGN KEY (`ma_phieu`) REFERENCES `phieu_muon` (`ma_phieu`),
  ADD CONSTRAINT `chi_tiet_dat_phong_ibfk_2` FOREIGN KEY (`ma_phong`) REFERENCES `phong` (`id`);

--
-- Constraints for table `chi_tiet_muon`
--
ALTER TABLE `chi_tiet_muon`
  ADD CONSTRAINT `chi_tiet_muon_ibfk_1` FOREIGN KEY (`ma_phieu`) REFERENCES `phieu_muon` (`ma_phieu`),
  ADD CONSTRAINT `chi_tiet_muon_ibfk_2` FOREIGN KEY (`ma_sach`) REFERENCES `sach` (`ma_sach`);

--
-- Constraints for table `chi_tiet_nhap`
--
ALTER TABLE `chi_tiet_nhap`
  ADD CONSTRAINT `chi_tiet_nhap_ibfk_1` FOREIGN KEY (`ma_phieu_nhap`) REFERENCES `phieu_nhap` (`ma_phieu_nhap`),
  ADD CONSTRAINT `chi_tiet_nhap_ibfk_2` FOREIGN KEY (`ma_sach`) REFERENCES `sach` (`ma_sach`);

--
-- Constraints for table `phieu_muon`
--
ALTER TABLE `phieu_muon`
  ADD CONSTRAINT `phieu_muon_ibfk_1` FOREIGN KEY (`ma_ban_doc`) REFERENCES `ban_doc` (`ma_ban_doc`);

--
-- Constraints for table `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  ADD CONSTRAINT `fk_phieu_nhap_kho` FOREIGN KEY (`ma_kho`) REFERENCES `kho` (`ma_kho`),
  ADD CONSTRAINT `phieu_nhap_ibfk_1` FOREIGN KEY (`ma_ncc`) REFERENCES `nha_cung_cap` (`ma_ncc`);

--
-- Constraints for table `phong`
--
ALTER TABLE `phong`
  ADD CONSTRAINT `fk_phong_muon` FOREIGN KEY (`ma_phieu`) REFERENCES `phieu_muon` (`ma_phieu`);

--
-- Constraints for table `vi_pham`
--
ALTER TABLE `vi_pham`
  ADD CONSTRAINT `vi_pham_ibfk_1` FOREIGN KEY (`ma_phieu`) REFERENCES `phieu_muon` (`ma_phieu`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
