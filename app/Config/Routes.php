<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Public / Guest Routes
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::processLogin');
$routes->get('logout', 'Auth::logout');

// Authenticated Routes (Requires AuthFilter)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard General
    $routes->get('dashboard', 'Dashboard::index');

    // 1. Kategori (Role: admin)
    $routes->group('kategori', ['filter' => 'role:admin'], static function ($routes) {
        $routes->get('/', 'Kategori::index');
        $routes->post('store', 'Kategori::store');
        $routes->post('update/(:segment)', 'Kategori::update/$1');
        $routes->get('delete/(:segment)', 'Kategori::delete/$1');
    });

    // 2. Satuan (Role: admin)
    $routes->group('satuan', ['filter' => 'role:admin'], static function ($routes) {
        $routes->get('/', 'Satuan::index');
        $routes->post('store', 'Satuan::store');
        $routes->post('update/(:segment)', 'Satuan::update/$1');
        $routes->get('delete/(:segment)', 'Satuan::delete/$1');
    });

    // 3. Supplier (Role: admin, inventory)
    $routes->group('supplier', ['filter' => 'role:admin,inventory'], static function ($routes) {
        $routes->get('/', 'Supplier::index');
        $routes->post('store', 'Supplier::store');
        $routes->post('update/(:segment)', 'Supplier::update/$1');
        $routes->get('delete/(:segment)', 'Supplier::delete/$1');
    });

    // 4. Master Barang (Role: admin, inventory)
    $routes->group('barang', ['filter' => 'role:admin,inventory'], static function ($routes) {
        $routes->get('/', 'Barang::index');
        $routes->get('get-json/(:segment)', 'Barang::getJson/$1');
        $routes->get('create', 'Barang::create');
        $routes->post('store', 'Barang::store');
        $routes->get('edit/(:segment)', 'Barang::edit/$1');
        $routes->post('update/(:segment)', 'Barang::update/$1');
        $routes->get('delete/(:segment)', 'Barang::delete/$1');
    });

    // 5. Kasir Penjualan (Role: kasir, admin)
    $routes->group('penjualan', ['filter' => 'role:kasir,admin'], static function ($routes) {
        $routes->get('/',                'Penjualan::index');
        $routes->get('customer-display', 'Penjualan::customerDisplay');
        $routes->post('survey',          'Penjualan::saveSurvey');
        $routes->get('get-barang',       'Penjualan::getBarang');
        $routes->post('store',           'Penjualan::store');
        $routes->get('struk/(:segment)', 'Penjualan::struk/$1');
        $routes->get('riwayat',          'Penjualan::riwayat');
        $routes->get('void/(:segment)',  'Penjualan::void/$1');
    });

    // 6. Pembelian / Stok (Role: inventory, admin)
    $routes->group('pembelian', ['filter' => 'role:inventory,admin'], static function ($routes) {
        $routes->get('/',                   'Pembelian::index');
        $routes->get('get-barang',          'Pembelian::getBarang');
        $routes->post('store',              'Pembelian::store');
        $routes->get('riwayat',             'Pembelian::riwayat');
        $routes->get('faktur/(:segment)',   'Pembelian::faktur/$1');
    });

    // 7. Approval Retur (Role: kasir, supervisor, admin)
    $routes->group('retur-customer', ['filter' => 'role:kasir,supervisor,admin'], static function ($routes) {
        $routes->get('/',                   'ReturCustomer::index');
        $routes->get('approval',            'ReturCustomer::approvalQueue');
        $routes->get('get-penjualan',       'ReturCustomer::getPenjualan');
        $routes->post('store',              'ReturCustomer::store');
        $routes->get('approve/(:segment)',  'ReturCustomer::approve/$1');
        $routes->get('reject/(:segment)',   'ReturCustomer::reject/$1');
        $routes->get('riwayat',             'ReturCustomer::riwayat');
        $routes->get('detail/(:segment)',   'ReturCustomer::detail/$1');
    });

    // 8. Laporan Keuangan (Role: owner, admin)
    $routes->group('laporan', ['filter' => 'role:owner,admin'], static function ($routes) {
        $routes->get('/',      'Laporan::index');
        $routes->get('cetak',  'Laporan::cetak');
    });

    // 9. Master Users (Role: admin)
    $routes->group('master-users', ['filter' => 'role:admin'], static function ($routes) {
        $routes->get('/',                  'MasterUsers::index');
        $routes->get('get-json/(:segment)','MasterUsers::getJson/$1');
        $routes->post('store',             'MasterUsers::store');
        $routes->post('update/(:segment)', 'MasterUsers::update/$1');
        $routes->get('delete/(:segment)',  'MasterUsers::delete/$1');
    });
});
