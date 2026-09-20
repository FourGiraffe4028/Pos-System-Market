<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard - POS System') ?></title>
    <!-- Bootstrap 5, FontAwesome, Chart.js -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    
    <style>
        /* Admin Panel Custom Styling */

        :root {
            --admin-primary: #a8732d;
            --admin-primary-dark: #8e6226;
            --admin-bg: #f3f5f9;
            --sidebar-width: 260px;
            --header-height: 70px;
            --sidebar-bg: #1e1e2d;
            --sidebar-color: #a2a3b7;
            --sidebar-active-bg: #2b2b40;
            --sidebar-active-color: #ffffff;
            --card-border: rgba(0, 0, 0, 0.04);
        }

        body {
            background: var(--admin-bg);
            overflow-x: hidden;
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* --- SIDEBAR --- */
        .pc-sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1010;
            transition: all 0.3s ease;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .navbar-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .m-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .m-header .logo-text {
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            text-decoration: none;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .m-header .logo-text span {
            color: var(--admin-primary);
        }

        .navbar-content {
            flex-grow: 1;
            padding: 20px 0;
            overflow-y: auto;
        }

        .pc-navbar {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pc-item {
            margin: 4px 15px;
        }

        .pc-link {
            display: flex;
            align-items: center;
            padding: 12px 18px;
            color: var(--sidebar-color);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .pc-link:hover {
            background: rgba(255, 255, 255, 0.04);
            color: #ffffff;
        }

        .pc-item.active > .pc-link {
            background: var(--admin-primary);
            color: var(--sidebar-active-color);
        }

        .pc-micon {
            font-size: 18px;
            margin-right: 12px;
            display: inline-flex;
        }

        /* Submenu Dropdown Styles */
        .pc-hasmenu .arrow-icon {
            margin-left: auto;
            font-size: 11px;
            transition: transform 0.2s ease;
        }
        .pc-hasmenu.open .arrow-icon {
            transform: rotate(180deg);
        }
        .pc-submenu {
            list-style: none;
            padding-left: 15px;
            margin: 4px 0 6px 0;
            display: none;
        }
        .pc-submenu.show {
            display: block;
        }
        .pc-submenu .pc-item {
            margin: 2px 0;
        }
        .pc-submenu .pc-link {
            padding: 9px 14px;
            font-size: 13px;
        }

        /* --- HEADER --- */
        .pc-header {
            height: var(--header-height);
            background: #ffffff;
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 0 30px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .header-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .head-link-secondary {
            color: #555;
            font-size: 20px;
            text-decoration: none;
            padding: 8px;
            border-radius: 50%;
            background: #f3f5f9;
            display: inline-flex;
            transition: all 0.2s;
        }
        .head-link-secondary:hover {
            background: #e2e5ec;
            color: var(--admin-primary);
        }

        .header-user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .header-user-profile img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
        }
        .header-user-profile .user-name {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        /* --- CONTAINER --- */
        .pc-container {
            margin-top: var(--header-height);
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: calc(100vh - var(--header-height));
            transition: all 0.3s ease;
        }

        .pc-footer {
            margin-left: var(--sidebar-width);
            padding: 20px 30px;
            border-top: 1px solid rgba(0,0,0,0.05);
            background: #ffffff;
            transition: all 0.3s ease;
            font-size: 13px;
            color: #777;
        }

        /* --- DASHBOARD STAT CARDS --- */
        .card-stat {
            position: relative;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        }
        .stat-icon {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 38px;
            opacity: 0.25;
        }
        .card-gradient-primary { background: linear-gradient(135deg, #a8732d 0%, #d49f57 100%); }
        .card-gradient-success { background: linear-gradient(135deg, #1cc88a 0%, #36e4a2 100%); }
        .card-gradient-warning { background: linear-gradient(135deg, #f6c23e 0%, #fcd570 100%); }
        .card-gradient-danger { background: linear-gradient(135deg, #e74a3b 0%, #f08277 100%); }

        /* --- TABLES & CARDS --- */
        .card {
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 20px;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }
        .card-header h3, .card-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }
        .card-body {
            padding: 20px;
        }

        .table th {
            background: #f8f9fc;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            color: #555;
            border-bottom: 2px solid #eaedf4;
        }
        .table td {
            font-size: 14px;
            vertical-align: middle;
            color: #444;
        }

        /* Modals inputs */
        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #444;
        }
        .form-control, .form-select {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 14px;
            border: 1px solid #dcdcdc;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 3px rgba(168, 115, 45, 0.15);
        }

        .btn-primary {
            background: var(--admin-primary);
            border-color: var(--admin-primary);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: var(--admin-primary-dark);
            border-color: var(--admin-primary-dark);
        }

        /* Sidebar Hide State for responsiveness */
        body.sidebar-hidden .pc-sidebar {
            left: -260px;
        }
        body.sidebar-hidden .pc-header,
        body.sidebar-hidden .pc-container,
        body.sidebar-hidden .pc-footer {
            left: 0;
            margin-left: 0;
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .pc-sidebar {
                left: -260px;
            }
            .pc-header,
            .pc-container,
            .pc-footer {
                left: 0 !important;
                margin-left: 0 !important;
            }
            body.sidebar-active .pc-sidebar {
                left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Section -->
    <nav class="pc-sidebar" id="adminSidebar">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="<?= site_url('dashboard') ?>" class="logo-text">
                    </i> POS <span>System</span>
                </a>
            </div>
            <div class="navbar-content">
                <ul class="pc-navbar">
                    <?php $currentUri = uri_string(); ?>

                    <li class="pc-item <?= $currentUri == 'dashboard' || $currentUri == '' ? 'active' : '' ?>">
                        <a href="<?= site_url('dashboard') ?>" class="pc-link">
                            <span class="pc-micon"><i class="fa-solid fa-gauge-high"></i></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>

                    <?php $role = session()->get('role'); ?>

                    <!-- Dropdown Data Master (Barang, Supplier, Kategori, Satuan, Users) -->
                    <?php if (in_array($role, ['admin', 'inventory'], true)) : ?>
                        <?php
                            $isMasterActive = in_array($currentUri, ['barang', 'supplier', 'kategori', 'satuan', 'master-users'], true)
                                || strpos($currentUri, 'barang') === 0
                                || strpos($currentUri, 'supplier') === 0
                                || strpos($currentUri, 'kategori') === 0
                                || strpos($currentUri, 'satuan') === 0
                                || strpos($currentUri, 'master-users') === 0;
                        ?>
                        <li class="pc-item pc-hasmenu <?= $isMasterActive ? 'active open' : '' ?>">
                            <a href="#" class="pc-link" onclick="toggleSubmenu(event, this)">
                                <span class="pc-micon"><i class="fa-solid fa-database"></i></span>
                                <span class="pc-mtext">Data Master</span>
                                <i class="fa-solid fa-chevron-down arrow-icon"></i>
                            </a>
                            <ul class="pc-submenu <?= $isMasterActive ? 'show' : '' ?>">
                                <?php if ($role === 'inventory' || $role === 'admin') : ?>
                                    <li class="pc-item">
                                        <a href="<?= site_url('barang') ?>" class="pc-link <?= strpos($currentUri, 'barang') === 0 ? 'text-warning fw-bold' : '' ?>">
                                            <span class="pc-micon"><i class="fa-solid fa-box-archive"></i></span>
                                            <span class="pc-mtext">Barang</span>
                                        </a>
                                    </li>
                                    <li class="pc-item">
                                        <a href="<?= site_url('supplier') ?>" class="pc-link <?= strpos($currentUri, 'supplier') === 0 ? 'text-warning fw-bold' : '' ?>">
                                            <span class="pc-micon"><i class="fa-solid fa-truck-field"></i></span>
                                            <span class="pc-mtext">Supplier</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if ($role === 'admin') : ?>
                                    <li class="pc-item">
                                        <a href="<?= site_url('kategori') ?>" class="pc-link <?= strpos($currentUri, 'kategori') === 0 ? 'text-warning fw-bold' : '' ?>">
                                            <span class="pc-micon"><i class="fa-solid fa-tags"></i></span>
                                            <span class="pc-mtext">Kategori</span>
                                        </a>
                                    </li>
                                    <li class="pc-item">
                                        <a href="<?= site_url('satuan') ?>" class="pc-link <?= strpos($currentUri, 'satuan') === 0 ? 'text-warning fw-bold' : '' ?>">
                                            <span class="pc-micon"><i class="fa-solid fa-scale-balanced"></i></span>
                                            <span class="pc-mtext">Satuan</span>
                                        </a>
                                    </li>
                                    <li class="pc-item">
                                        <a href="<?= site_url('master-users') ?>" class="pc-link <?= strpos($currentUri, 'master-users') === 0 ? 'text-warning fw-bold' : '' ?>">
                                            <span class="pc-micon"><i class="fa-solid fa-users-gear"></i></span>
                                            <span class="pc-mtext">Users</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- Modul Transaksi & Laporan -->
                    <?php if ($role === 'kasir' || $role === 'admin') : ?>
                        <?php
                            $isPenjualanActive = strpos($currentUri, 'penjualan') === 0;
                        ?>
                        <li class="pc-item pc-hasmenu <?= $isPenjualanActive ? 'active open' : '' ?>">
                            <a href="#" class="pc-link" onclick="toggleSubmenu(event, this)">
                                <span class="pc-micon"><i class="fa-solid fa-cart-shopping"></i></span>
                                <span class="pc-mtext">Kasir Penjualan</span>
                                <i class="fa-solid fa-chevron-down arrow-icon"></i>
                            </a>
                            <ul class="pc-submenu <?= $isPenjualanActive ? 'show' : '' ?>">
                                <li class="pc-item">
                                    <a href="<?= site_url('penjualan') ?>" class="pc-link <?= $currentUri === 'penjualan' ? 'text-warning fw-bold' : '' ?>">
                                        <span class="pc-micon"><i class="fa-solid fa-cash-register"></i></span>
                                        <span class="pc-mtext">Transaksi POS</span>
                                    </a>
                                </li>
                                <li class="pc-item">
                                    <a href="<?= site_url('penjualan/riwayat') ?>" class="pc-link <?= strpos($currentUri, 'penjualan/riwayat') === 0 ? 'text-warning fw-bold' : '' ?>">
                                        <span class="pc-micon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                                        <span class="pc-mtext">Riwayat & Struk</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if ($role === 'inventory' || $role === 'admin') : ?>
                        <li class="pc-item <?= strpos($currentUri, 'pembelian') === 0 ? 'active' : '' ?>">
                            <a href="<?= site_url('pembelian') ?>" class="pc-link">
                                <span class="pc-micon"><i class="fa-solid fa-boxes-packing"></i></span>
                                <span class="pc-mtext">Pembelian / Stok</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($role, ['kasir', 'supervisor', 'admin'], true)) : ?>
                        <?php
                            $isReturActive = strpos($currentUri, 'retur-customer') === 0;
                        ?>
                        <li class="pc-item pc-hasmenu <?= $isReturActive ? 'active open' : '' ?>">
                            <a href="#" class="pc-link" onclick="toggleSubmenu(event, this)">
                                <span class="pc-micon"><i class="fa-solid fa-rotate-left"></i></span>
                                <span class="pc-mtext"><?= $role === 'supervisor' ? 'Approval Retur' : 'Retur Customer' ?></span>
                                <i class="fa-solid fa-chevron-down arrow-icon"></i>
                            </a>
                            <ul class="pc-submenu <?= $isReturActive ? 'show' : '' ?>">
                                <?php if ($role === 'supervisor' || $role === 'admin') : ?>
                                    <li class="pc-item">
                                        <a href="<?= site_url('retur-customer/approval') ?>" class="pc-link <?= strpos($currentUri, 'retur-customer/approval') === 0 ? 'text-warning fw-bold' : '' ?>">
                                            <span class="pc-micon"><i class="fa-solid fa-user-check"></i></span>
                                            <span class="pc-mtext">Antrean Approval</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if ($role === 'kasir' || $role === 'admin') : ?>
                                    <li class="pc-item">
                                        <a href="<?= site_url('retur-customer') ?>" class="pc-link <?= $currentUri === 'retur-customer' ? 'text-warning fw-bold' : '' ?>">
                                            <span class="pc-micon"><i class="fa-solid fa-file-signature"></i></span>
                                            <span class="pc-mtext">Pengajuan Retur</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <li class="pc-item">
                                    <a href="<?= site_url('retur-customer/riwayat') ?>" class="pc-link <?= strpos($currentUri, 'retur-customer/riwayat') === 0 ? 'text-warning fw-bold' : '' ?>">
                                        <span class="pc-micon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                                        <span class="pc-mtext">Riwayat Retur</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if ($role === 'owner' || $role === 'admin') : ?>
                        <li class="pc-item <?= strpos($currentUri, 'laporan') === 0 ? 'active' : '' ?>">
                            <a href="<?= site_url('laporan') ?>" class="pc-link">
                                <span class="pc-micon"><i class="fa-solid fa-chart-line"></i></span>
                                <span class="pc-mtext">Laporan Keuangan</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="pc-item mt-4">
                        <a href="<?= site_url('logout') ?>" class="pc-link text-danger">
                            <span class="pc-micon"><i class="fa-solid fa-right-from-bracket"></i></span>
                            <span class="pc-mtext">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header Section (Clean Streamlined Navbar Without Title Heading) -->
    <header class="pc-header" id="adminHeader">
        <div class="header-wrapper">
            <div class="d-flex align-items-center gap-3">
                <a href="#" class="head-link-secondary" onclick="document.body.classList.toggle('sidebar-hidden'); return false;">
                    <i class="fa-solid fa-bars"></i>
                </a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="header-user-profile">
                    <div class="rounded-circle bg-warning text-dark font-weight-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <?= esc(strtoupper(substr(session()->get('nama_user') ?? 'U', 0, 1))) ?>
                    </div>
                    <div class="d-none d-md-block">
                        <span class="user-name d-block"><?= esc(session()->get('nama_user')) ?></span>
                        <span class="badge bg-warning text-dark font-weight-bold" style="font-size: 11px;"><?= esc(strtoupper(session()->get('role'))) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="pc-container">
        <div class="pc-content">

            <!-- Flash Success Alert -->
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Flash Error Alert -->
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>

        </div>
    </div>

    <!-- Admin Footer -->
    <footer class="pc-footer">
        <div class="footer-wrapper container-fluid">
            <div class="row">
                <div class="col-sm-6 my-1">
                    <p class="m-0">
                        POS System &copy; <?= date('Y') ?> Enterprise Edition
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Required Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleSubmenu(e, element) {
            e.preventDefault();
            const parentLi = element.closest('.pc-hasmenu');
            const submenu = parentLi.querySelector('.pc-submenu');

            if (submenu.classList.contains('show')) {
                submenu.classList.remove('show');
                parentLi.classList.remove('open');
            } else {
                submenu.classList.add('show');
                parentLi.classList.add('open');
            }
        }
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
