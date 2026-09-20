<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Customer Display - POS System') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --brand-primary: #a8732d;
            --brand-dark: #1e1e2d;
            --bg-canvas: #f4f6fb;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        body {
            background-color: var(--bg-canvas);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            overflow: hidden;
            height: 100vh;
        }

        .header-display {
            background: linear-gradient(135deg, #1e1e2d 0%, #2b2b40 100%);
            color: #fff;
            padding: 16px 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .store-logo {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #fff;
        }
        .store-logo span {
            color: #f6c23e;
        }

        .main-content {
            height: calc(100vh - 75px);
            padding: 25px;
        }

        .card-display {
            background: #ffffff;
            border-radius: 16px;
            border: none;
            box-shadow: var(--card-shadow);
            height: 100%;
        }

        /* Cart Table */
        .cart-table-wrapper {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
        }
        .table-custom th {
            background-color: #f8f9fc;
            color: #555;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            padding: 14px;
            border-bottom: 2px solid #eaedf4;
        }
        .table-custom td {
            padding: 16px 14px;
            vertical-align: middle;
            font-size: 15px;
            border-bottom: 1px solid #f0f2f7;
        }

        /* Big Total Banner */
        .total-box {
            background: linear-gradient(135deg, #a8732d 0%, #d49f57 100%);
            color: #ffffff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(168, 115, 45, 0.3);
        }
        .total-label {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
        }
        .total-amount {
            font-size: 42px;
            font-weight: 900;
            line-height: 1.1;
        }

        /* Idle Welcome Screen */
        .welcome-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            padding: 40px;
        }
        .welcome-icon {
            font-size: 80px;
            color: #a8732d;
            margin-bottom: 20px;
            animation: pulseIcon 2s infinite ease-in-out;
        }
        @keyframes pulseIcon {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }

        /* Modal Custom Styling */
        .modal-survey .modal-content {
            border-radius: 24px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            background: #ffffff;
        }
        .survey-card {
            border: 3px solid #e9ecef;
            border-radius: 20px;
            padding: 30px 20px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            background: #fff;
            user-select: none;
        }
        .survey-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
        }
        .survey-card.card-puas:hover {
            border-color: #1cc88a;
            background: #f0fff9;
        }
        .survey-card.card-tidak-puas:hover {
            border-color: #e74a3b;
            background: #fff5f5;
        }

        .mascot-img {
            width: 130px;
            height: 130px;
            object-fit: contain;
            transition: transform 0.3s;
        }
        .survey-card:hover .mascot-img {
            transform: scale(1.1) rotate(4deg);
        }

        .survey-title {
            font-size: 24px;
            font-weight: 800;
            color: #2b2b40;
        }
        .survey-subtitle {
            font-size: 16px;
            color: #6c757d;
        }
    </style>
</head>
<body>

    <!-- Header Display -->
    <div class="header-display d-flex justify-content-between align-items-center">
        <div class="store-logo">
            <i class="fa-solid me-2"></i>POS <span>System</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill fw-bold">
                <i class="fa-solid fa-star me-1"></i>TERIMA KASIH TELAH BERBELANJA
            </span>
            <span id="liveClock" class="fw-bold fs-6">00:00:00</span>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="row h-100 g-4">

            <!-- PANEL KIRI: Cart Table -->
            <div class="col-lg-7 h-100">
                <div class="card-display p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <h5 class="fw-bold m-0 text-dark">
                                Daftar Belanja Anda
                            </h5>
                            <span id="badgeCount" class="badge bg-secondary fs-6">0 item</span>
                        </div>

                        <!-- Idle Screen State -->
                        <div id="welcomeState" class="welcome-box">
                            <i class="fa-solid fa-basket-shopping welcome-icon"></i>
                            <h3 class="fw-bold text-dark mb-2">Selamat Datang!</h3>
                            <p class="text-muted fs-5">Kami siap melayani belanjaan Anda.</p>
                        </div>

                        <!-- Active Cart State -->
                        <div id="cartState" class="cart-table-wrapper" style="display: none;">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50%;">Nama Barang</th>
                                        <th class="text-center" style="width: 20%;">Qty</th>
                                        <th class="text-end" style="width: 30%;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="cartItemsBody">
                                    <!-- Dynamic Items -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footnote -->
                    <div class="border-top pt-3 text-muted small d-flex justify-content-between">
                        <span><i class="fa-solid fa-shield-halved me-1 text-success"></i>Layar Transaksi Pelanggan</span>
                        <span>Harap periksa kembali belanjaan Anda</span>
                    </div>
                </div>
            </div>

            <!-- PANEL KANAN: Big Summary -->
            <div class="col-lg-5 h-100">
                <div class="card-display p-4 d-flex flex-column justify-content-between">

                    <!-- Total Box -->
                    <div class="total-box">
                        <div class="total-label mb-1"><i class="fa-solid fa-wallet me-2"></i>Total Belanja</div>
                        <div class="total-amount" id="displayTotal">Rp 0</div>
                    </div>

                    <!-- Payment Details (Appears when checkout) -->
                    <div id="checkoutInfo" class="mt-4 p-3 bg-light rounded-3 border" style="display: none;">
                        <div class="d-flex justify-content-between mb-2 fs-6">
                            <span class="text-muted">Metode Bayar:</span>
                            <strong id="displayMetode" class="text-uppercase text-dark">-</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 fs-6">
                            <span class="text-muted">Nominal Bayar:</span>
                            <strong id="displayBayar" class="text-dark">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between fs-5 pt-2 border-top">
                            <span class="fw-bold text-dark">Kembalian:</span>
                            <strong id="displayKembali" class="text-success fw-bold">Rp 0</strong>
                        </div>
                    </div>

                    <!-- Customer Promotion Banner -->
                    <div class="mt-auto text-center p-4 rounded-4" style="background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);">
                        <div class="mb-2">
                            <i class="fa-solid fa-gift fa-3x text-warning"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Promo Hemat Hari Ini!</h6>
                        <p class="text-muted small mb-0">Dapatkan berbagai diskon menarik dan poin reward untuk setiap transaksi.</p>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. MODAL SUKSES PEMBAYARAN (Tampil 10 Detik Pertama) -->
    <!-- ========================================================================= -->
    <div class="modal fade modal-survey" id="paymentSuccessModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-4 text-center">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <i class="fa-solid fa-circle-check text-success fa-5x"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-1">Pembayaran Berhasil!</h2>
                    <p class="text-muted fs-6 mb-4">Terima kasih telah berbelanja di toko kami.</p>

                    <!-- Detail Ringkasan Pembayaran -->
                    <div class="p-3 bg-light rounded-4 border text-start mb-4 mx-auto" style="max-width: 500px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">No. Transaksi:</span>
                            <strong id="modalSuccessId" class="text-dark">-</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Belanja:</span>
                            <strong id="modalSuccessTotal" class="text-dark">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Metode Bayar:</span>
                            <strong id="modalSuccessMetode" class="text-uppercase text-dark">-</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Nominal Bayar:</span>
                            <strong id="modalSuccessBayar" class="text-dark">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between fs-5 pt-2 border-top">
                            <span class="fw-bold text-dark">Kembalian:</span>
                            <strong id="modalSuccessKembali" class="text-success fw-bold">Rp 0</strong>
                        </div>
                    </div>

                    <div class="small text-muted">
                        <i class="fa-solid fa-spinner fa-spin me-1 text-warning"></i>
                        Survei kepuasan pelanggan akan tampil dalam <span id="paymentCountdown" class="fw-bold text-dark">10</span> detik...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. MODAL SURVEI KEPUASAN PELANGGAN (Tampil 10 Detik Setelah Modal Sukses) -->
    <!-- ========================================================================= -->
    <div class="modal fade modal-survey" id="surveyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-4">
                <div class="modal-body text-center py-3">

                    <!-- Initial Survey Question State -->
                    <div id="surveyQuestionState">
                        <h3 class="survey-title mb-2">Anda puas dengan pelayanan kami?</h3>
                        <p class="survey-subtitle mb-4">Pilih gambar di bawah ini.</p>

                        <div class="row g-4 justify-content-center">
                            <!-- PUAS -->
                            <div class="col-md-5">
                                <div class="survey-card card-puas" onclick="submitSurvey('puas')">
                                    <div class="mb-3">
                                        <!-- Mascot Puas (Animated SVG Emoji) -->
                                        <svg class="mascot-img" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="50" cy="50" r="45" fill="#1CC88A" opacity="0.15"/>
                                            <circle cx="50" cy="50" r="40" fill="#F6C23E"/>
                                            <circle cx="35" cy="40" r="5" fill="#2E2E2E"/>
                                            <circle cx="65" cy="40" r="5" fill="#2E2E2E"/>
                                            <path d="M 30 60 Q 50 80 70 60" stroke="#2E2E2E" stroke-width="5" stroke-linecap="round" fill="none"/>
                                            <circle cx="25" cy="48" r="6" fill="#E74A3B" opacity="0.4"/>
                                            <circle cx="75" cy="48" r="6" fill="#E74A3B" opacity="0.4"/>
                                        </svg>
                                    </div>
                                    <h4 class="fw-bold text-success mb-0"><i class="fa-solid fa-face-smile me-2"></i>Puas</h4>
                                </div>
                            </div>

                            <!-- TIDAK PUAS -->
                            <div class="col-md-5">
                                <div class="survey-card card-tidak-puas" onclick="submitSurvey('tidak_puas')">
                                    <div class="mb-3">
                                        <!-- Mascot Tidak Puas (Animated SVG Emoji) -->
                                        <svg class="mascot-img" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="50" cy="50" r="45" fill="#E74A3B" opacity="0.15"/>
                                            <circle cx="50" cy="50" r="40" fill="#F6C23E"/>
                                            <circle cx="35" cy="42" r="5" fill="#2E2E2E"/>
                                            <circle cx="65" cy="42" r="5" fill="#2E2E2E"/>
                                            <path d="M 30 68 Q 50 50 70 68" stroke="#2E2E2E" stroke-width="5" stroke-linecap="round" fill="none"/>
                                        </svg>
                                    </div>
                                    <h4 class="fw-bold text-danger mb-0"><i class="fa-solid fa-face-frown me-2"></i>Tidak Puas</h4>
                                </div>
                            </div>
                        </div>

                        <!-- 10 Detik Auto Close Timer Indicator -->
                        <div class="small text-muted mt-4" id="surveyTimerText">
                            <i class="fa-solid fa-clock me-1 text-warning"></i>Halaman akan tertutup otomatis dalam <span id="surveyCountdown" class="fw-bold text-dark">10</span> detik...
                        </div>
                    </div>

                    <!-- Thank You State after voting -->
                    <div id="surveyThanksState" style="display: none;" class="py-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-circle-check text-success fa-5x"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-2">Terima Kasih!</h2>
                        <p class="fs-5 text-muted mb-0">Masukan Anda sangat berharga untuk meningkatkan kualitas pelayanan kami.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Real-time Clock
        setInterval(() => {
            const now = new Date();
            document.getElementById('liveClock').textContent = now.toLocaleTimeString('id-ID');
        }, 1000);

        let currentIdPenjualan = null;
        let paymentSuccessModalInstance = null;
        let surveyModalInstance = null;

        let paymentTimer = null;
        let paymentInterval = null;
        let surveyTimer = null;
        let surveyInterval = null;

        document.addEventListener('DOMContentLoaded', () => {
            paymentSuccessModalInstance = new bootstrap.Modal(document.getElementById('paymentSuccessModal'));
            surveyModalInstance = new bootstrap.Modal(document.getElementById('surveyModal'));
        });

        // BroadcastChannel Sync with Cashier Tab
        const displayChannel = new BroadcastChannel('pos_customer_display');

        displayChannel.onmessage = (event) => {
            const data = event.data;
            if (!data) return;

            if (data.type === 'CART_UPDATE') {
                renderCart(data.items, data.total);
            } else if (data.type === 'TRANSACTION_COMPLETE') {
                currentIdPenjualan = data.id_penjualan;
                showCheckoutSuccess(data);
            } else if (data.type === 'RESET_DISPLAY') {
                resetDisplay();
            }
        };

        function renderCart(items, total) {
            const welcomeState = document.getElementById('welcomeState');
            const cartState = document.getElementById('cartState');
            const tbody = document.getElementById('cartItemsBody');
            const badge = document.getElementById('badgeCount');
            const checkoutInfo = document.getElementById('checkoutInfo');

            checkoutInfo.style.display = 'none';

            if (!items || items.length === 0) {
                welcomeState.style.display = 'flex';
                cartState.style.display = 'none';
                badge.textContent = '0 item';
                document.getElementById('displayTotal').textContent = 'Rp 0';
                return;
            }

            welcomeState.style.display = 'none';
            cartState.style.display = 'block';
            badge.textContent = items.length + ' item';

            tbody.innerHTML = items.map(i => {
                const subtotal = i.harga_satuan * i.jumlah;
                return `<tr>
                    <td>
                        <strong class="d-block text-dark">${escHtml(i.nama_barang)}</strong>
                        <small class="text-muted">${formatRupiah(i.harga_satuan)}</small>
                    </td>
                    <td class="text-center fw-bold fs-6">${i.jumlah}</td>
                    <td class="text-end fw-bold text-dark fs-6">${formatRupiah(subtotal)}</td>
                </tr>`;
            }).join('');

            document.getElementById('displayTotal').textContent = formatRupiah(total);
        }

        // TAHAP 1: Tampilkan Modal Sukses Pembayaran Selama 10 Detik
        function showCheckoutSuccess(data) {
            clearAllTimers();

            document.getElementById('checkoutInfo').style.display = 'block';
            document.getElementById('displayMetode').textContent = data.metode_bayar || 'TUNAI';
            document.getElementById('displayBayar').textContent = formatRupiah(data.bayar || 0);
            document.getElementById('displayKembali').textContent = formatRupiah(data.kembali || 0);
            document.getElementById('displayTotal').textContent = formatRupiah(data.total || 0);

            document.getElementById('modalSuccessId').textContent = data.id_penjualan || '-';
            document.getElementById('modalSuccessTotal').textContent = formatRupiah(data.total || 0);
            document.getElementById('modalSuccessMetode').textContent = data.metode_bayar || 'TUNAI';
            document.getElementById('modalSuccessBayar').textContent = formatRupiah(data.bayar || 0);
            document.getElementById('modalSuccessKembali').textContent = formatRupiah(data.kembali || 0);

            // Buka Modal Sukses Pembayaran
            paymentSuccessModalInstance.show();

            let secondsLeftPayment = 10;
            document.getElementById('paymentCountdown').textContent = secondsLeftPayment;

            paymentInterval = setInterval(() => {
                secondsLeftPayment--;
                if (secondsLeftPayment >= 0) {
                    document.getElementById('paymentCountdown').textContent = secondsLeftPayment;
                }
            }, 1000);

            paymentTimer = setTimeout(() => {
                clearInterval(paymentInterval);
                paymentSuccessModalInstance.hide();
                // Lanjut ke TAHAP 2: Modal Survei Kepuasan
                startSurveyStep();
            }, 10000);
        }

        // TAHAP 2: Tampilkan Modal Survei Kepuasan Pelanggan (Otomatis Hilang Dalam 10 Detik Jika Tidak Ditekan)
        function startSurveyStep() {
            document.getElementById('surveyQuestionState').style.display = 'block';
            document.getElementById('surveyThanksState').style.display = 'none';
            document.getElementById('surveyTimerText').style.display = 'block';

            surveyModalInstance.show();

            let secondsLeftSurvey = 10;
            document.getElementById('surveyCountdown').textContent = secondsLeftSurvey;

            surveyInterval = setInterval(() => {
                secondsLeftSurvey--;
                if (secondsLeftSurvey >= 0) {
                    document.getElementById('surveyCountdown').textContent = secondsLeftSurvey;
                }
            }, 1000);

            // 10 Detik Timeout jika customer tidak menekan apapun
            surveyTimer = setTimeout(() => {
                clearInterval(surveyInterval);
                surveyModalInstance.hide();
                resetDisplay();
            }, 10000);
        }

        function submitSurvey(kepuasanVal) {
            clearAllTimers();

            document.getElementById('surveyQuestionState').style.display = 'none';
            document.getElementById('surveyThanksState').style.display = 'block';
            document.getElementById('surveyTimerText').style.display = 'none';

            if (currentIdPenjualan) {
                $.ajax({
                    url: '<?= site_url('penjualan/survey') ?>',
                    type: 'POST',
                    data: {
                        id_penjualan: currentIdPenjualan,
                        kepuasan: kepuasanVal,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    }
                });
            }

            setTimeout(() => {
                surveyModalInstance.hide();
                resetDisplay();
            }, 3000);
        }

        function clearAllTimers() {
            if (paymentTimer) clearTimeout(paymentTimer);
            if (paymentInterval) clearInterval(paymentInterval);
            if (surveyTimer) clearTimeout(surveyTimer);
            if (surveyInterval) clearInterval(surveyInterval);
        }

        function resetDisplay() {
            clearAllTimers();
            renderCart([], 0);
            document.getElementById('checkoutInfo').style.display = 'none';
            currentIdPenjualan = null;
        }

        function formatRupiah(angka) {
            return 'Rp ' + Number(angka).toLocaleString('id-ID');
        }

        function escHtml(str) {
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }
    </script>
</body>
</html>
