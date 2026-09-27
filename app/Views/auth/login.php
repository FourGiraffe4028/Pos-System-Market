<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Login - POS System') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --bg-primary: #f8f6f0;
            --primary: #a8732d;
            --primary-hover: #8a5d22;
            --secondary: #2c3e50;
            --text-main: #333333;
            --text-muted: #7f8c8d;
            --border-color: #e2d7c5;
            --shadow-sm: 0 2px 5px rgba(0,0,0,0.05);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.08);
            --font-primary: 'Plus Jakarta Sans', sans-serif, system-ui;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 16px;
            --transition: all 0.3s ease;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: var(--bg-primary);
            font-family: var(--font-primary);
            margin: 0;
            padding: 30px 15px;
            box-sizing: border-box;
        }

        /* Huruf '1' di On1ine agar unik */
        .brand-text span { color: var(--primary-hover); }

        .logo-circle {
            width: 80px;
            height: 80px;
            border: 4px solid var(--primary);
            border-radius: 50%;
            margin-bottom: 20px;
            background: #fff;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--primary);
            font-size: 32px;
        }

        .brand-text {
            font-family: var(--font-primary);
            font-size: 32px;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 40px 30px;
            border: 2px solid #e2d7c5;
            border-radius: var(--radius-lg);
            background: #fff;
            box-shadow: var(--shadow-lg);
            text-align: center;
            box-sizing: border-box;
        }

        .login-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 5px;
            text-transform: uppercase;
            font-family: var(--font-primary);
        }
        
        .login-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 25px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 700;
            font-size: 14px;
            color: var(--text-main);
            display: block;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 14px;
            color: var(--text-main);
            outline: none;
            transition: var(--transition);
            background: #fafafa;
            box-sizing: border-box;
        }

        .form-input:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(168, 115, 45, 0.15);
        }

        ::placeholder { color: #ccc; font-style: italic; }

        .terms-text {
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 25px;
            line-height: 1.5;
            text-align: left;
        }
        .terms-text a { color: #3498db; text-decoration: none; }

        .btn-login {
            width: 100%;
            background-color: var(--primary);
            color: white;
            padding: 12px;
            border: none;
            border-radius: var(--radius-md);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            font-family: var(--font-primary);
            box-shadow: 0 4px 10px rgba(168, 115, 45, 0.2);
        }

        .btn-login:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .alert {
            background: #ffecec;
            color: #cc0000;
            padding: 12px 15px;
            font-size: 13px;
            margin-bottom: 20px;
            border-radius: var(--radius-sm);
            border: 1px solid #ffcccc;
            text-align: left;
        }
        .alert-success {
            background: #eef9f1;
            color: #10b981;
            border-color: #bcf0da;
        }

        /* Cheat Code Activated Overlay */
        .cheat-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(8px);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            animation: cheatFadeIn 0.3s ease;
        }

        .cheat-box {
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border: 2px solid #f59e0b;
            border-radius: 16px;
            padding: 32px 36px;
            text-align: center;
            box-shadow: 0 0 50px rgba(245, 158, 11, 0.4), inset 0 0 20px rgba(245, 158, 11, 0.1);
            max-width: 420px;
            width: 90%;
            color: #fff;
            animation: cheatPop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            box-sizing: border-box;
        }

        .cheat-icon {
            font-size: 52px;
            color: #fbbf24;
            margin-bottom: 14px;
            animation: cheatPulse 1s infinite alternate;
        }

        .cheat-title {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #fef08a;
            margin-bottom: 8px;
            font-family: 'Courier New', Courier, monospace;
        }

        .cheat-code-badge {
            display: inline-block;
            background: rgba(245, 158, 11, 0.2);
            border: 1px dashed #f59e0b;
            color: #fde047;
            padding: 6px 16px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 14px;
        }

        .cheat-desc {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 18px;
            line-height: 1.5;
        }

        .cheat-progress {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            overflow: hidden;
        }

        .cheat-progress-bar {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
            animation: cheatFill 1.3s ease forwards;
        }

        /* Hidden Cheat Console Modal */
        .cheat-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99998;
        }

        .cheat-modal-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 14px;
            padding: 24px;
            width: 90%;
            max-width: 370px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
            color: #e2e8f0;
            box-sizing: border-box;
            animation: cheatPop 0.25s ease;
        }

        .cheat-modal-input {
            width: 100%;
            padding: 12px 14px;
            background: #0f172a;
            border: 2px solid #475569;
            border-radius: 8px;
            color: #fde047;
            font-family: monospace;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-top: 10px;
            margin-bottom: 16px;
            box-sizing: border-box;
            text-align: center;
            transition: all 0.2s;
        }

        .cheat-modal-input:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 12px rgba(245, 158, 11, 0.35);
        }

        .cheat-modal-btn {
            width: 100%;
            padding: 12px;
            background: #f59e0b;
            color: #0f172a;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }

        .cheat-modal-btn:hover {
            background: #fbbf24;
            transform: translateY(-1px);
        }

        .cheat-secret-trigger {
            opacity: 0.25;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 18px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            user-select: none;
        }

        .cheat-secret-trigger:hover {
            opacity: 0.9;
            color: var(--primary);
            background: rgba(168, 115, 45, 0.08);
        }

        @keyframes cheatFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes cheatPop {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @keyframes cheatPulse {
            from { transform: scale(1); filter: drop-shadow(0 0 5px rgba(245, 158, 11, 0.6)); }
            to { transform: scale(1.1); filter: drop-shadow(0 0 16px rgba(245, 158, 11, 0.9)); }
        }

        @keyframes cheatFill {
            0% { width: 0%; }
            100% { width: 100%; }
        }
    </style>
</head>
<body>

    <div class="logo-circle" title="Klik beberapa kali untuk membuka konsol rahasia">
        <i class="fa-solid fa-cash-register"></i>
    </div>

    <div class="brand-text">POS S<span>y</span>stem</div>

    <div class="login-card">
        
        <h2 class="login-title">Login Akun</h2>
        <p class="login-subtitle">Silahkan Login untuk mengakses sistem</p>

        <!-- Flash Error Alert -->
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert">
                <i class="fa-solid fa-circle-exclamation"></i> <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <!-- Flash Success Alert -->
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <!-- Validation Errors -->
        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="alert">
                <?php foreach (session()->getFlashdata('errors') as $err) : ?>
                    <div><i class="fa-solid fa-circle-xmark"></i> <?= esc($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('login') ?>" method="post" id="loginForm">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" 
                       name="username" 
                       id="username" 
                       class="form-input" 
                       placeholder="Masukkan Username Anda..." 
                       value="<?= esc(old('username')) ?>" 
                       required 
                       autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="form-input" 
                       placeholder="Masukkan Password Anda..." 
                       required>
            </div>

            <p class="terms-text">
                Dengan melanjutkan saya menyetujui <a href="#">Syarat dan Ketentuan</a> serta <a href="#">Kebijakan Privasi</a> yang berlaku.
            </p>

            <button type="submit" class="btn-login">Login</button>
        </form>

    </div>

    <!-- Hidden Trigger Link -->
    <div class="cheat-secret-trigger" onclick="openCheatModal()" title="Tekan Ctrl+Shift+R atau klik di sini">
        <i class="fa-solid fa-shield-halved"></i> <span>Akses Rahasia</span>
    </div>

    <!-- Hidden Cheat Console Modal -->
    <div id="cheatModal" class="cheat-modal" onclick="if(event.target===this) closeCheatModal()">
        <div class="cheat-modal-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="font-weight: 700; font-size: 14px; color: #fde047; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-terminal"></i> Konsol Akses Darurat
                </div>
                <button type="button" onclick="closeCheatModal()" style="background: none; border: none; color: #94a3b8; font-size: 20px; cursor: pointer; padding: 0 4px; line-height: 1;">&times;</button>
            </div>
            <p style="font-size: 12px; color: #94a3b8; margin: 0 0 10px 0; line-height: 1.4;">
                Masukkan kode rahasia / cheat code untuk membuka akses darurat ke sistem.
            </p>
            <input type="password" id="cheatInput" class="cheat-modal-input" placeholder="Ketik kode cheat..." autocomplete="off" onkeydown="if(event.key==='Enter') checkModalCheat()">
            <div style="display: flex; gap: 8px;">
                <button type="button" class="cheat-modal-btn" onclick="checkModalCheat()">
                    <i class="fa-solid fa-key me-1"></i> Buka Akses
                </button>
                <button type="button" onclick="closeCheatModal()" style="padding: 10px 14px; background: #334155; color: #cbd5e1; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600;">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- Cheat Activated Fullscreen Overlay -->
    <div id="cheatOverlay" class="cheat-overlay">
        <div class="cheat-box">
            <div class="cheat-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="cheat-title">CHEAT CODE ACTIVATED</div>
            <div class="cheat-code-badge">147258@ASDF</div>
            <div class="cheat-desc">
                Kode cheat valid! Mengalihkan ke halaman pemulihan darurat (Recovery)...
            </div>
            <div class="cheat-progress">
                <div class="cheat-progress-bar"></div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const TARGET_CHEAT = '147258@ASDF';
            const RECOVERY_URL = '<?= site_url("recovery") ?>';
            let keyBuffer = '';
            let isTriggered = false;

            // Suara retro 8-bit cheat activated
            function playCheatSound() {
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContext) return;
                    const ctx = new AudioContext();
                    const notes = [440, 554.37, 659.25, 880]; // A4, C#5, E5, A5
                    notes.forEach((freq, idx) => {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'triangle';
                        osc.frequency.setValueAtTime(freq, ctx.currentTime + idx * 0.08);
                        gain.gain.setValueAtTime(0.18, ctx.currentTime + idx * 0.08);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + idx * 0.08 + 0.22);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(ctx.currentTime + idx * 0.08);
                        osc.stop(ctx.currentTime + idx * 0.08 + 0.25);
                    });
                } catch (e) {}
            }

            // Fungsi aktivasi cheat
            function triggerCheat() {
                if (isTriggered) return;
                isTriggered = true;

                playCheatSound();

                // Tutup modal jika sedang terbuka
                closeCheatModal();

                // Tampilkan animasi overlay
                const overlay = document.getElementById('cheatOverlay');
                if (overlay) {
                    overlay.style.display = 'flex';
                }

                // Redirect ke halaman recovery setelah animasi berjalan
                setTimeout(function() {
                    window.location.href = RECOVERY_URL;
                }, 1300);
            }

            // 1. GLOBAL KEYBOARD LISTENER (Ketik 147258@ASDF langsung di keyboard)
            document.addEventListener('keydown', function(e) {
                // Shortcut keyboard: Ctrl + Shift + R atau Ctrl + Alt + C membuka modal
                if ((e.ctrlKey && e.shiftKey && (e.key === 'R' || e.key === 'r')) ||
                    (e.ctrlKey && e.altKey && (e.key === 'C' || e.key === 'c'))) {
                    e.preventDefault();
                    openCheatModal();
                    return;
                }

                if (e.key === 'Escape') {
                    closeCheatModal();
                    return;
                }

                // Simpan karakter tunggal ke buffer
                if (e.key && e.key.length === 1) {
                    keyBuffer += e.key;
                    if (keyBuffer.length > 30) {
                        keyBuffer = keyBuffer.slice(-30);
                    }
                    if (keyBuffer.toUpperCase().endsWith(TARGET_CHEAT.toUpperCase())) {
                        keyBuffer = '';
                        triggerCheat();
                    }
                }
            });

            // 2. Pantau jika diketik langsung di input form username atau password
            ['username', 'password'].forEach(function(fieldId) {
                const el = document.getElementById(fieldId);
                if (el) {
                    el.addEventListener('input', function() {
                        if (this.value.toUpperCase().includes(TARGET_CHEAT.toUpperCase())) {
                            this.value = '';
                            triggerCheat();
                        }
                    });
                }
            });

            // 3. Easter Egg: Klik logo register 4 kali cepat
            let logoClicks = 0;
            let logoTimer = null;
            const logoEl = document.querySelector('.logo-circle');
            if (logoEl) {
                logoEl.style.cursor = 'pointer';
                logoEl.addEventListener('click', function() {
                    logoClicks++;
                    clearTimeout(logoTimer);
                    if (logoClicks >= 4) {
                        logoClicks = 0;
                        openCheatModal();
                    } else {
                        logoTimer = setTimeout(function() { logoClicks = 0; }, 1200);
                    }
                });
            }

            // Fungsi modal konsol
            window.openCheatModal = function() {
                const modal = document.getElementById('cheatModal');
                const input = document.getElementById('cheatInput');
                if (modal) {
                    modal.style.display = 'flex';
                    if (input) {
                        input.value = '';
                        setTimeout(function() { input.focus(); }, 120);
                    }
                }
            };

            window.closeCheatModal = function() {
                const modal = document.getElementById('cheatModal');
                if (modal) {
                    modal.style.display = 'none';
                }
            };

            window.checkModalCheat = function() {
                const input = document.getElementById('cheatInput');
                if (!input) return;

                if (input.value.trim().toUpperCase() === TARGET_CHEAT.toUpperCase()) {
                    triggerCheat();
                } else {
                    input.style.borderColor = '#ef4444';
                    input.animate([
                        { transform: 'translateX(-8px)' },
                        { transform: 'translateX(8px)' },
                        { transform: 'translateX(-5px)' },
                        { transform: 'translateX(5px)' },
                        { transform: 'translateX(0)' }
                    ], { duration: 300 });
                    setTimeout(function() {
                        input.style.borderColor = '#475569';
                    }, 1000);
                }
            };
        })();
    </script>
</body>
</html>
