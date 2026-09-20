-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2026 at 08:29 AM
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
-- Database: `posystem_baru`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` varchar(15) NOT NULL,
  `barcode` varchar(20) DEFAULT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `id_kategori` varchar(5) NOT NULL,
  `id_satuan` varchar(5) NOT NULL,
  `harga_beli_terakhir` decimal(18,2) NOT NULL DEFAULT 0.00,
  `harga_jual` decimal(18,2) NOT NULL DEFAULT 0.00,
  `stok` int(11) NOT NULL DEFAULT 0,
  `stok_minimum` int(11) NOT NULL DEFAULT 0,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `barcode`, `nama_barang`, `id_kategori`, `id_satuan`, `harga_beli_terakhir`, `harga_jual`, `stok`, `stok_minimum`, `aktif`, `created_by`, `created_at`, `updated_at`) VALUES
('B0001', '8991234567890', 'Buku Tulis Sinar Dunia 38 Lbr', 'K0001', 'ST001', 1500.00, 5000.00, 96, 10, 1, 'inv_indomarco', '2026-09-17 05:24:26', '2026-09-20 05:22:55'),
('B0002', '8991234567891', 'Air Mineral 600ml', 'K0002', 'ST005', 2000.00, 3500.00, 91, 24, 1, 'inv_alfaria', '2026-09-17 05:24:26', '2026-09-20 05:22:55');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id_customer` varchar(10) NOT NULL,
  `kode_member` varchar(20) DEFAULT NULL,
  `nama_customer` varchar(50) NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `tgl_daftar` date NOT NULL DEFAULT current_timestamp(),
  `aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_pembelian`
--

CREATE TABLE `detail_pembelian` (
  `id_detail_beli` int(11) NOT NULL,
  `id_pembelian` varchar(20) NOT NULL,
  `id_barang` varchar(15) NOT NULL,
  `harga_beli` decimal(18,2) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` decimal(18,2) GENERATED ALWAYS AS (`harga_beli` * `jumlah`) STORED,
  `tgl_kadaluarsa` date DEFAULT NULL
) ;

--
-- Dumping data for table `detail_pembelian`
--

INSERT INTO `detail_pembelian` (`id_detail_beli`, `id_pembelian`, `id_barang`, `harga_beli`, `jumlah`, `tgl_kadaluarsa`) VALUES
(1, 'PB202609170001', 'B0001', 3500.00, 50, NULL),
(2, 'PB202609170002', 'B0002', 2200.00, 120, NULL),
(3, 'PB202609200001', 'B0002', 2000.00, 100, NULL),
(4, 'PB202609200002', 'B0001', 1500.00, 100, NULL);

--
-- Triggers `detail_pembelian`
--
DELIMITER $$
CREATE TRIGGER `trg_beli_masuk` AFTER INSERT ON `detail_pembelian` FOR EACH ROW BEGIN UPDATE `barang` SET `stok` = `stok` + NEW.`jumlah`, `harga_beli_terakhir` = NEW.`harga_beli` WHERE `id_barang` = NEW.`id_barang`; END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_penjualan`
--

CREATE TABLE `detail_penjualan` (
  `id_detail` int(11) NOT NULL,
  `id_penjualan` varchar(20) NOT NULL,
  `id_barang` varchar(15) NOT NULL,
  `harga_satuan` decimal(18,2) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `diskon_item` decimal(18,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(18,2) GENERATED ALWAYS AS (`harga_satuan` * `jumlah` - `diskon_item`) STORED
) ;

--
-- Dumping data for table `detail_penjualan`
--

INSERT INTO `detail_penjualan` (`id_detail`, `id_penjualan`, `id_barang`, `harga_satuan`, `jumlah`, `diskon_item`) VALUES
(1, 'PJ202609200001', 'B0002', 3500.00, 3, 0.00),
(2, 'PJ202609200002', 'B0001', 5000.00, 3, 0.00),
(3, 'PJ202609200003', 'B0001', 5000.00, 1, 0.00),
(4, 'PJ202609200003', 'B0002', 3500.00, 1, 0.00),
(5, 'PJ202609200004', 'B0002', 3500.00, 1, 0.00),
(6, 'PJ202609200005', 'B0001', 5000.00, 1, 0.00),
(7, 'PJ202609200006', 'B0001', 5000.00, 1, 0.00),
(8, 'PJ202609200007', 'B0001', 5000.00, 1, 0.00),
(9, 'PJ202609200008', 'B0001', 5000.00, 4, 0.00),
(10, 'PJ202609200008', 'B0002', 3500.00, 4, 0.00);

--
-- Triggers `detail_penjualan`
--
DELIMITER $$
CREATE TRIGGER `trg_jual_keluar` AFTER INSERT ON `detail_penjualan` FOR EACH ROW BEGIN DECLARE v_stok INT; SELECT `stok` INTO v_stok FROM `barang` WHERE `id_barang` = NEW.`id_barang` FOR UPDATE; IF v_stok < NEW.`jumlah` THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok barang tidak mencukupi'; END IF; UPDATE `barang` SET `stok` = `stok` - NEW.`jumlah` WHERE `id_barang` = NEW.`id_barang`; END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_retur`
--

CREATE TABLE `detail_retur` (
  `id_detail_retur` int(11) NOT NULL,
  `id_retur` varchar(20) NOT NULL,
  `id_barang` varchar(15) NOT NULL,
  `harga_satuan` decimal(18,2) NOT NULL,
  `jumlah_retur` int(11) NOT NULL,
  `subtotal_refund` decimal(18,2) GENERATED ALWAYS AS (`harga_satuan` * `jumlah_retur`) STORED,
  `kondisi_barang` enum('reject_rusak','kembali_stok') NOT NULL DEFAULT 'reject_rusak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_retur`
--

INSERT INTO `detail_retur` (`id_detail_retur`, `id_retur`, `id_barang`, `harga_satuan`, `jumlah_retur`, `kondisi_barang`) VALUES
(1, 'RT202609200001', 'B0002', 3500.00, 2, 'reject_rusak'),
(2, 'RT202609200002', 'B0001', 5000.00, 3, 'kembali_stok'),
(3, 'RT202609200003', 'B0001', 5000.00, 1, 'kembali_stok');

-- --------------------------------------------------------

--
-- Table structure for table `detail_retur_supplier`
--

CREATE TABLE `detail_retur_supplier` (
  `id_detail_rsup` int(11) NOT NULL,
  `id_retur_sup` varchar(20) NOT NULL,
  `id_barang` varchar(15) NOT NULL,
  `harga_beli` decimal(18,2) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` decimal(18,2) GENERATED ALWAYS AS (`harga_beli` * `jumlah`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `detail_retur_supplier`
--
DELIMITER $$
CREATE TRIGGER `trg_retur_supplier_keluar` AFTER INSERT ON `detail_retur_supplier` FOR EACH ROW BEGIN UPDATE `barang` SET `stok` = `stok` - NEW.`jumlah` WHERE `id_barang` = NEW.`id_barang`; END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` varchar(5) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
('K0001', 'Alat Tulis'),
('K0003', 'Kebutuhan Rumah'),
('K0002', 'Makanan & Minuman');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-09-20-034913', 'App\\Database\\Migrations\\AddStatusAndKasirToReturTable', 'default', 'App', 1789876175, 1),
(2, '2026-09-20-041059', 'App\\Database\\Migrations\\DropTrgReturMasukTrigger', 'default', 'App', 1789877474, 2),
(3, '2026-09-20-044414', 'App\\Database\\Migrations\\AddKepuasanToPenjualanTable', 'default', 'App', 1789879476, 3);

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `id_pembelian` varchar(20) NOT NULL,
  `id_supplier` varchar(5) NOT NULL,
  `user_id` varchar(15) NOT NULL,
  `no_faktur` varchar(30) DEFAULT NULL,
  `tanggal_beli` datetime NOT NULL,
  `total_beli` decimal(18,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','diterima','batal') NOT NULL DEFAULT 'diterima',
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`id_pembelian`, `id_supplier`, `user_id`, `no_faktur`, `tanggal_beli`, `total_beli`, `status`, `keterangan`, `created_at`) VALUES
('PB202609170001', 'S0001', 'inv_indomarco', 'FKT-001', '2026-09-17 08:00:00', 175000.00, 'diterima', NULL, '2026-09-17 05:24:26'),
('PB202609170002', 'S0002', 'inv_alfaria', 'FKT-002', '2026-09-17 08:30:00', 264000.00, 'diterima', NULL, '2026-09-17 05:24:26'),
('PB202609200001', 'S0001', 'admin01', 'FKT-112177721', '2026-09-20 03:28:00', 200000.00, 'diterima', NULL, '2026-09-20 03:30:11'),
('PB202609200002', 'S0002', 'inv_alfaria', 'FKT-366372832', '2026-09-20 04:03:00', 150000.00, 'diterima', NULL, '2026-09-20 04:04:19');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id_penjualan` varchar(20) NOT NULL,
  `user_id` varchar(15) NOT NULL,
  `id_customer` varchar(10) DEFAULT NULL,
  `tanggal_jual` datetime NOT NULL,
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `diskon` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_belanja` decimal(18,2) NOT NULL DEFAULT 0.00,
  `metode_bayar` enum('tunai','debit','kredit','qris','ewallet') NOT NULL DEFAULT 'tunai',
  `bayar` decimal(18,2) NOT NULL DEFAULT 0.00,
  `kembali` decimal(18,2) NOT NULL DEFAULT 0.00,
  `status` enum('selesai','void') NOT NULL DEFAULT 'selesai',
  `kepuasan` enum('puas','tidak_puas') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id_penjualan`, `user_id`, `id_customer`, `tanggal_jual`, `subtotal`, `diskon`, `total_belanja`, `metode_bayar`, `bayar`, `kembali`, `status`, `kepuasan`) VALUES
('PJ202609200001', 'admin01', NULL, '2026-09-20 03:32:13', 10500.00, 0.00, 10500.00, 'tunai', 20000.00, 9500.00, 'selesai', NULL),
('PJ202609200002', 'kasir01', NULL, '2026-09-20 04:06:00', 15000.00, 0.00, 15000.00, 'tunai', 20000.00, 5000.00, 'selesai', NULL),
('PJ202609200003', 'kasir01', NULL, '2026-09-20 04:17:11', 8500.00, 0.00, 8500.00, 'tunai', 10000.00, 1500.00, 'selesai', NULL),
('PJ202609200004', 'kasir01', NULL, '2026-09-20 04:48:45', 3500.00, 0.00, 3500.00, 'tunai', 5000.00, 1500.00, 'selesai', NULL),
('PJ202609200005', 'kasir01', NULL, '2026-09-20 04:53:26', 5000.00, 0.00, 5000.00, 'tunai', 50000.00, 45000.00, 'selesai', NULL),
('PJ202609200006', 'kasir01', NULL, '2026-09-20 05:20:59', 5000.00, 0.00, 5000.00, 'tunai', 22000.00, 17000.00, 'selesai', NULL),
('PJ202609200007', 'kasir01', NULL, '2026-09-20 05:21:48', 5000.00, 0.00, 5000.00, 'tunai', 5000.00, 0.00, 'selesai', NULL),
('PJ202609200008', 'kasir01', NULL, '2026-09-20 05:22:55', 34000.00, 0.00, 34000.00, 'tunai', 100000.00, 66000.00, 'selesai', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `retur`
--

CREATE TABLE `retur` (
  `id_retur` varchar(20) NOT NULL,
  `id_penjualan` varchar(20) NOT NULL,
  `user_id_kasir` varchar(15) DEFAULT NULL,
  `user_id_spv` varchar(15) DEFAULT NULL,
  `tanggal_retur` datetime NOT NULL,
  `total_refund` decimal(18,2) NOT NULL DEFAULT 0.00,
  `alasan` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retur`
--

INSERT INTO `retur` (`id_retur`, `id_penjualan`, `user_id_kasir`, `user_id_spv`, `tanggal_retur`, `total_refund`, `alasan`, `status`) VALUES
('RT202609200001', 'PJ202609200001', 'kasir01', 'spv01', '2026-09-20 04:00:43', 7000.00, 'cacat pabrik', 'approved'),
('RT202609200002', 'PJ202609200002', 'kasir01', 'spv01', '2026-09-20 04:07:00', 15000.00, 'Salah beli barang', 'approved'),
('RT202609200003', 'PJ202609200003', 'kasir01', 'spv01', '2026-09-20 04:18:48', 5000.00, 'salah beli', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `retur_supplier`
--

CREATE TABLE `retur_supplier` (
  `id_retur_sup` varchar(20) NOT NULL,
  `id_supplier` varchar(5) NOT NULL,
  `id_pembelian` varchar(20) DEFAULT NULL,
  `user_id` varchar(15) NOT NULL,
  `tanggal_retur` datetime NOT NULL,
  `total_retur` decimal(18,2) NOT NULL DEFAULT 0.00,
  `alasan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE `satuan` (
  `id_satuan` varchar(5) NOT NULL,
  `nama_satuan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `satuan`
--

INSERT INTO `satuan` (`id_satuan`, `nama_satuan`) VALUES
('ST005', 'Botol'),
('ST002', 'Dus'),
('ST003', 'Pack'),
('ST001', 'Pcs'),
('ST004', 'Renteng');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id_supplier` varchar(5) NOT NULL,
  `nama_supplier` varchar(50) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `kota` varchar(25) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id_supplier`, `nama_supplier`, `alamat`, `kota`, `no_telepon`, `aktif`) VALUES
('S0001', 'PT. Indomarco Prismatama', 'Kawasan Industri Ancol', 'Jakarta Utara', '0211234567', 1),
('S0002', 'PT. Sumber Alfaria Trijaya', 'Cikokol', 'Tangerang', '0217654321', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(15) NOT NULL,
  `username` varchar(30) NOT NULL,
  `nama_user` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kasir','supervisor','owner','inventory') NOT NULL DEFAULT 'kasir',
  `id_supplier` varchar(5) DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `nama_user`, `password`, `role`, `id_supplier`, `aktif`, `created_at`) VALUES
('admin01', 'admin', 'Administrator', '$2y$10$WL9xJuu2HRcK9jU5wN1xIuBvaYBpiF56bq0lnlWv6oGz2KbR8.s5O', 'admin', NULL, 1, '2026-09-17 05:24:26'),
('inv_alfaria', 'budi', 'Budi Inventory', '$2y$10$kN2bfSGXJyM1AaYWxOxP0e/CwTYvGGSYctQn.UDaXek.GDmjOpY4q', 'inventory', 'S0002', 1, '2026-09-17 05:24:26'),
('inv_indomarco', 'gina', 'Gina Inventory', '$2y$10$SvOsFOt4jsIsf/c6QlIn1OSecBZlNE9QmqLP/PkorraLGVXa0wiXm', 'inventory', 'S0001', 1, '2026-09-17 05:24:26'),
('kasir01', 'jiddan', 'Jiddan Kasir', '$2y$10$G.C9bdu7Dn4wK.sNNuwFX.m.U.f707pTfpxomB9k0hGcSMiTFf7BK', 'kasir', NULL, 1, '2026-09-17 05:24:26'),
('owner01', 'reihan', 'Pak Reihan', '$2y$10$SvOsFOt4jsIsf/c6QlIn1OSecBZlNE9QmqLP/PkorraLGVXa0wiXm', 'owner', NULL, 1, '2026-09-17 05:24:26'),
('spv01', 'rian', 'Rian Supervisor', '$2y$10$Mz30/VCvaI6Bj5rZvPTF2ubrMjGfHkTaHY5oMpCILCNwKfMJXQley', 'supervisor', NULL, 1, '2026-09-17 05:24:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD UNIQUE KEY `uq_barcode` (`barcode`),
  ADD KEY `fk_barang_kategori` (`id_kategori`),
  ADD KEY `fk_barang_satuan` (`id_satuan`),
  ADD KEY `fk_barang_users` (`created_by`),
  ADD KEY `idx_nama_barang` (`nama_barang`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id_customer`),
  ADD UNIQUE KEY `uq_kode_member` (`kode_member`),
  ADD UNIQUE KEY `uq_telepon_customer` (`no_telepon`);

--
-- Indexes for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD PRIMARY KEY (`id_detail_beli`),
  ADD KEY `fk_dbeli_header` (`id_pembelian`),
  ADD KEY `fk_dbeli_barang` (`id_barang`);

--
-- Indexes for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_detail_header` (`id_penjualan`),
  ADD KEY `fk_detail_barang` (`id_barang`);

--
-- Indexes for table `detail_retur`
--
ALTER TABLE `detail_retur`
  ADD PRIMARY KEY (`id_detail_retur`),
  ADD KEY `fk_dretur_header` (`id_retur`),
  ADD KEY `fk_dretur_barang` (`id_barang`);

--
-- Indexes for table `detail_retur_supplier`
--
ALTER TABLE `detail_retur_supplier`
  ADD PRIMARY KEY (`id_detail_rsup`),
  ADD KEY `fk_drsup_header` (`id_retur_sup`),
  ADD KEY `fk_drsup_barang` (`id_barang`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `uq_nama_kategori` (`nama_kategori`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id_pembelian`),
  ADD UNIQUE KEY `uq_faktur_supplier` (`id_supplier`,`no_faktur`),
  ADD KEY `fk_pembelian_users` (`user_id`),
  ADD KEY `idx_tanggal_beli` (`tanggal_beli`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id_penjualan`),
  ADD KEY `fk_penjualan_users` (`user_id`),
  ADD KEY `fk_penjualan_customer` (`id_customer`),
  ADD KEY `idx_tanggal_jual` (`tanggal_jual`);

--
-- Indexes for table `retur`
--
ALTER TABLE `retur`
  ADD PRIMARY KEY (`id_retur`),
  ADD KEY `fk_retur_penjualan` (`id_penjualan`),
  ADD KEY `fk_retur_spv` (`user_id_spv`);

--
-- Indexes for table `retur_supplier`
--
ALTER TABLE `retur_supplier`
  ADD PRIMARY KEY (`id_retur_sup`),
  ADD KEY `fk_rsup_supplier` (`id_supplier`),
  ADD KEY `fk_rsup_pembelian` (`id_pembelian`),
  ADD KEY `fk_rsup_users` (`user_id`);

--
-- Indexes for table `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`id_satuan`),
  ADD UNIQUE KEY `uq_nama_satuan` (`nama_satuan`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uq_username` (`username`),
  ADD KEY `fk_users_supplier` (`id_supplier`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  MODIFY `id_detail_beli` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_retur`
--
ALTER TABLE `detail_retur`
  MODIFY `id_detail_retur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `detail_retur_supplier`
--
ALTER TABLE `detail_retur_supplier`
  MODIFY `id_detail_rsup` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `fk_barang_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barang_satuan` FOREIGN KEY (`id_satuan`) REFERENCES `satuan` (`id_satuan`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barang_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD CONSTRAINT `fk_dbeli_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dbeli_header` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id_pembelian`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD CONSTRAINT `fk_detail_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_header` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id_penjualan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_retur`
--
ALTER TABLE `detail_retur`
  ADD CONSTRAINT `fk_dretur_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dretur_header` FOREIGN KEY (`id_retur`) REFERENCES `retur` (`id_retur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_retur_supplier`
--
ALTER TABLE `detail_retur_supplier`
  ADD CONSTRAINT `fk_drsup_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_drsup_header` FOREIGN KEY (`id_retur_sup`) REFERENCES `retur_supplier` (`id_retur_sup`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `fk_pembelian_supplier` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pembelian_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `fk_penjualan_customer` FOREIGN KEY (`id_customer`) REFERENCES `customer` (`id_customer`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_penjualan_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `retur`
--
ALTER TABLE `retur`
  ADD CONSTRAINT `fk_retur_penjualan` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id_penjualan`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_retur_spv` FOREIGN KEY (`user_id_spv`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `retur_supplier`
--
ALTER TABLE `retur_supplier`
  ADD CONSTRAINT `fk_rsup_pembelian` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id_pembelian`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rsup_supplier` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rsup_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_supplier` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
