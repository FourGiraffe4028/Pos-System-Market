-- ============================================================
-- POS System Market — Database Backup
-- Generated  : 2026-09-26 18:49:49
-- Database   : posystem_baru
-- PHP Version: 8.3.33
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Table: `barang`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `barang`;

CREATE TABLE `barang` (
  `id_barang` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `barcode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_barang` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_kategori` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_satuan` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `harga_beli_terakhir` decimal(18,2) NOT NULL DEFAULT '0.00',
  `harga_jual` decimal(18,2) NOT NULL DEFAULT '0.00',
  `stok` int NOT NULL DEFAULT '0',
  `stok_minimum` int NOT NULL DEFAULT '0',
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_barang`),
  UNIQUE KEY `uq_barcode` (`barcode`),
  KEY `fk_barang_kategori` (`id_kategori`),
  KEY `fk_barang_satuan` (`id_satuan`),
  KEY `fk_barang_users` (`created_by`),
  KEY `idx_nama_barang` (`nama_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `barang` (`id_barang`, `barcode`, `nama_barang`, `id_kategori`, `id_satuan`, `harga_beli_terakhir`, `harga_jual`, `stok`, `stok_minimum`, `aktif`, `created_by`) VALUES
('B0001', 8991234567890, 'Buku Tulis Sinar Dunia 38 Lbr', 'K0001', 'ST001', 0.00, 5000.00, 47, 10, 1, 'inv_indomarco'),
('B0002', 8991234567891, 'Air Mineral 600ml', 'K0002', 'ST005', 0.00, 3500.00, 95, 24, 1, 'inv_alfaria'),
('B0003', 8991234567892, 'Chitato Sapi Panggang 120ml', 'K0002', 'ST003', 0.00, 11500.00, 0, 10, 1, 'admin01');

-- --------------------------------------------------------
-- Table: `customer`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `customer`;

CREATE TABLE `customer` (
  `id_customer` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kode_member` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_customer` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_telepon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_daftar` date NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_customer`),
  UNIQUE KEY `uq_kode_member` (`kode_member`),
  UNIQUE KEY `uq_telepon_customer` (`no_telepon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: `detail_pembelian`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `detail_pembelian`;

CREATE TABLE `detail_pembelian` (
  `id_detail_beli` int NOT NULL AUTO_INCREMENT,
  `id_pembelian` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_barang` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `harga_beli` decimal(18,2) NOT NULL,
  `jumlah` int NOT NULL,
  `subtotal` decimal(18,2) GENERATED ALWAYS AS ((`harga_beli` * `jumlah`)) STORED,
  `tgl_kadaluarsa` date DEFAULT NULL,
  PRIMARY KEY (`id_detail_beli`),
  KEY `fk_dbeli_header` (`id_pembelian`),
  KEY `fk_dbeli_barang` (`id_barang`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: `detail_penjualan`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `detail_penjualan`;

CREATE TABLE `detail_penjualan` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `id_penjualan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_barang` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `harga_satuan` decimal(18,2) NOT NULL,
  `jumlah` int NOT NULL,
  `diskon_item` decimal(18,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(18,2) GENERATED ALWAYS AS (((`harga_satuan` * `jumlah`) - `diskon_item`)) STORED,
  PRIMARY KEY (`id_detail`),
  KEY `fk_detail_header` (`id_penjualan`),
  KEY `fk_detail_barang` (`id_barang`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `detail_penjualan` (`id_detail`, `id_penjualan`, `id_barang`, `harga_satuan`, `jumlah`, `diskon_item`) VALUES
(1, 'PJ202609180001', 'B0002', 3500.00, 2, 0.00),
(2, 'PJ202609180002', 'B0001', 5000.00, 3, 0.00),
(3, 'PJ202609180003', 'B0002', 3500.00, 3, 0.00);

-- --------------------------------------------------------
-- Table: `detail_retur`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `detail_retur`;

CREATE TABLE `detail_retur` (
  `id_detail_retur` int NOT NULL AUTO_INCREMENT,
  `id_retur` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_barang` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `harga_satuan` decimal(18,2) NOT NULL,
  `jumlah_retur` int NOT NULL,
  `subtotal_refund` decimal(18,2) GENERATED ALWAYS AS ((`harga_satuan` * `jumlah_retur`)) STORED,
  `kondisi_barang` enum('reject_rusak','kembali_stok') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'reject_rusak',
  PRIMARY KEY (`id_detail_retur`),
  KEY `fk_dretur_header` (`id_retur`),
  KEY `fk_dretur_barang` (`id_barang`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `detail_retur` (`id_detail_retur`, `id_retur`, `id_barang`, `harga_satuan`, `jumlah_retur`, `kondisi_barang`) VALUES
(2, 'RT202609180001', 'B0001', 5000.00, 1, 'reject_rusak'),
(3, 'RT202609180002', 'B0002', 3500.00, 1, 'reject_rusak');

-- --------------------------------------------------------
-- Table: `detail_retur_supplier`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `detail_retur_supplier`;

CREATE TABLE `detail_retur_supplier` (
  `id_detail_rsup` int NOT NULL AUTO_INCREMENT,
  `id_retur_sup` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_barang` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `harga_beli` decimal(18,2) NOT NULL,
  `jumlah` int NOT NULL,
  `subtotal` decimal(18,2) GENERATED ALWAYS AS ((`harga_beli` * `jumlah`)) STORED,
  PRIMARY KEY (`id_detail_rsup`),
  KEY `fk_drsup_header` (`id_retur_sup`),
  KEY `fk_drsup_barang` (`id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: `kategori`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `kategori`;

CREATE TABLE `kategori` (
  `id_kategori` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_kategori` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_kategori`),
  UNIQUE KEY `uq_nama_kategori` (`nama_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
('K0001', 'Alat Tulis'),
('K0003', 'Kebutuhan Rumah'),
('K0002', 'Makanan & Minuman');

-- --------------------------------------------------------
-- Table: `pembelian`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `pembelian`;

CREATE TABLE `pembelian` (
  `id_pembelian` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_supplier` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_faktur` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_beli` datetime NOT NULL,
  `total_beli` decimal(18,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','diterima','batal') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'diterima',
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pembelian`),
  UNIQUE KEY `uq_faktur_supplier` (`id_supplier`,`no_faktur`),
  KEY `fk_pembelian_users` (`user_id`),
  KEY `idx_tanggal_beli` (`tanggal_beli`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `pembelian` (`id_pembelian`, `id_supplier`, `user_id`, `no_faktur`, `tanggal_beli`, `total_beli`, `status`, `keterangan`) VALUES
('PB202609170001', 'S0001', 'inv_indomarco', 'FKT-001', '2026-09-17 08:00:00', 175000.00, 'diterima', NULL),
('PB202609170002', 'S0002', 'inv_alfaria', 'FKT-002', '2026-09-17 08:30:00', 264000.00, 'diterima', NULL);

-- --------------------------------------------------------
-- Table: `penjualan`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `penjualan`;

CREATE TABLE `penjualan` (
  `id_penjualan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_customer` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_jual` datetime NOT NULL,
  `subtotal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `diskon` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_belanja` decimal(18,2) NOT NULL DEFAULT '0.00',
  `metode_bayar` enum('tunai','debit','kredit','qris','ewallet') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'tunai',
  `bayar` decimal(18,2) NOT NULL DEFAULT '0.00',
  `kembali` decimal(18,2) NOT NULL DEFAULT '0.00',
  `status` enum('selesai','void') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'selesai',
  PRIMARY KEY (`id_penjualan`),
  KEY `fk_penjualan_users` (`user_id`),
  KEY `fk_penjualan_customer` (`id_customer`),
  KEY `idx_tanggal_jual` (`tanggal_jual`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `penjualan` (`id_penjualan`, `user_id`, `id_customer`, `tanggal_jual`, `subtotal`, `diskon`, `total_belanja`, `metode_bayar`, `bayar`, `kembali`, `status`) VALUES
('PJ202609180001', 'admin01', NULL, '2026-09-18 08:04:41', 7000.00, 0.00, 7000.00, 'tunai', 7000.00, 0.00, 'selesai'),
('PJ202609180002', 'kasir01', NULL, '2026-09-18 08:05:42', 15000.00, 0.00, 15000.00, 'tunai', 20000.00, 5000.00, 'selesai'),
('PJ202609180003', 'admin01', NULL, '2026-09-18 08:26:56', 10500.00, 0.00, 10500.00, 'tunai', 15000.00, 4500.00, 'selesai');

-- --------------------------------------------------------
-- Table: `recovery_keys`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `recovery_keys`;

CREATE TABLE `recovery_keys` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `label` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `recovery_keys` (`id`, `key_hash`, `label`, `expires_at`, `used_at`, `is_used`) VALUES
(1, '$2y$10$j2DPrgRANYCLk.Kvman3euJN/dvS9bitXoGLA0xhrpXPmh5oUPXWi', 'Key-20260926180709', '2026-10-26 18:07:09', NULL, 0),
(2, '$2y$10$OVgoJPGIb9f5X36sJkbAqOcSGlTnoMUaU/kEUrWccNyw6tiUmSMPe', 'Key-20260926181757', '2026-10-26 18:17:57', NULL, 0),
(3, '$2y$10$wz.fpAkcW6upggtHVZRnf.P1vim2sWb.bwKvZqP9atXon5R4ZC4Eu', 'Key-20260926182400', '2026-10-26 18:24:00', NULL, 0),
(4, '$2y$10$tJveXo0B8WhsLPCkmdNwnuS0xpkJLywLLIGnhClqjhWeUdTouwArG', 'Key-20260926182429', '2026-10-26 18:24:29', '2026-09-26 18:30:22', 1),
(5, '$2y$10$FFkm0PZburEtRcuyCCCIAeM4p7DX5s7Ri0OC.QaEyj.eUGvA/DvHu', 'Key-20260926183643', '2026-10-26 18:36:43', '2026-09-26 18:36:51', 1);

-- --------------------------------------------------------
-- Table: `retur`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `retur`;

CREATE TABLE `retur` (
  `id_retur` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_penjualan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id_spv` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id_kasir` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_retur` datetime NOT NULL,
  `total_refund` decimal(18,2) NOT NULL DEFAULT '0.00',
  `alasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'approved',
  PRIMARY KEY (`id_retur`),
  KEY `fk_retur_penjualan` (`id_penjualan`),
  KEY `fk_retur_spv` (`user_id_spv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `retur` (`id_retur`, `id_penjualan`, `user_id_spv`, `user_id_kasir`, `tanggal_retur`, `total_refund`, `alasan`, `status`) VALUES
('RT202609180001', 'PJ202609180002', 'spv01', 'admin01', '2026-09-18 08:17:20', 5000.00, 'Salah beli', 'approved'),
('RT202609180002', 'PJ202609180003', 'spv01', 'admin01', '2026-09-18 08:28:28', 3500.00, 'Salah beli', 'approved');

-- --------------------------------------------------------
-- Table: `retur_supplier`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `retur_supplier`;

CREATE TABLE `retur_supplier` (
  `id_retur_sup` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_supplier` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_pembelian` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_retur` datetime NOT NULL,
  `total_retur` decimal(18,2) NOT NULL DEFAULT '0.00',
  `alasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_retur_sup`),
  KEY `fk_rsup_supplier` (`id_supplier`),
  KEY `fk_rsup_pembelian` (`id_pembelian`),
  KEY `fk_rsup_users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: `satuan`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `satuan`;

CREATE TABLE `satuan` (
  `id_satuan` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_satuan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_satuan`),
  UNIQUE KEY `uq_nama_satuan` (`nama_satuan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `satuan` (`id_satuan`, `nama_satuan`) VALUES
('ST005', 'Botol'),
('ST002', 'Dus'),
('ST003', 'Pack'),
('ST001', 'Pcs'),
('ST004', 'Renteng');

-- --------------------------------------------------------
-- Table: `supplier`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `supplier`;

CREATE TABLE `supplier` (
  `id_supplier` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_supplier` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kota` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_telepon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `supplier` (`id_supplier`, `nama_supplier`, `alamat`, `kota`, `no_telepon`, `aktif`) VALUES
('S0001', 'PT. Indomarco Prismatama', 'Kawasan Industri Ancol', 'Jakarta Utara', '0211234567', 1),
('S0002', 'PT. Sumber Alfaria Trijaya', 'Cikokol', 'Tangerang', '0217654321', 1);

-- --------------------------------------------------------
-- Table: `users`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_user` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','kasir','supervisor','owner','inventory') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'kasir',
  `id_supplier` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_username` (`username`),
  KEY `fk_users_supplier` (`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`user_id`, `username`, `nama_user`, `password`, `role`, `id_supplier`, `aktif`) VALUES
('admin01', 'admin', 'Administrator', '$2y$10$SvOsFOt4jsIsf/c6QlIn1OSecBZlNE9QmqLP/PkorraLGVXa0wiXm', 'admin', NULL, 1),
('inv_alfaria', 'budi', 'Budi Inventory', '$2y$10$SvOsFOt4jsIsf/c6QlIn1OSecBZlNE9QmqLP/PkorraLGVXa0wiXm', 'inventory', 'S0002', 1),
('inv_indomarco', 'gina', 'Gina Inventory', '$2y$10$SvOsFOt4jsIsf/c6QlIn1OSecBZlNE9QmqLP/PkorraLGVXa0wiXm', 'inventory', 'S0001', 1),
('kasir01', 'jiddan', 'Jiddan Kasir', '$2y$10$SvOsFOt4jsIsf/c6QlIn1OSecBZlNE9QmqLP/PkorraLGVXa0wiXm', 'kasir', NULL, 1),
('owner01', 'reihan', 'Pak Reihan', '$2y$10$SvOsFOt4jsIsf/c6QlIn1OSecBZlNE9QmqLP/PkorraLGVXa0wiXm', 'owner', NULL, 1),
('spv01', 'rian', 'Rian Supervisor', '$2y$10$SvOsFOt4jsIsf/c6QlIn1OSecBZlNE9QmqLP/PkorraLGVXa0wiXm', 'supervisor', NULL, 1);

SET FOREIGN_KEY_CHECKS = 1;
