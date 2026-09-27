<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? "Akses Darurat - POS System") ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-primary: #f8f6f0;
            --primary: #a8732d;
            --primary-hover: #8a5d22;
            --danger: #dc3545;
            --danger-hover: #bb2d3b;
            --secondary: #2c3e50;
            --text-main: #333333;
            --text-muted: #7f8c8d;
            --border-color: #e2d7c5;
            --shadow-sm: 0 2px 5px rgba(0,0,0,0.05);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.08);
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
            font-family: "Plus Jakarta Sans", system-ui, sans-serif;
            margin: 0;
            padding: 30px 15px;
            box-sizing: border-box;
        }

        .logo-circle {
            width: 80px;
            height: 80px;
            border: 4px solid var(--danger);
            border-radius: 50%;
            margin-bottom: 20px;
            background: #fff;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--danger);
            font-size: 32px;
        }

        .brand-text {
            font-size: 28px;
            font-weight: 800;
            color: var(--danger);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
        }

        .brand-sub {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 30px;
            text-align: center;
        }

        .recovery-card {
            width: 100%;
            max-width: 460px;
            padding: 36px 30px;
            border: 2px solid #f5c6cb;
            border-radius: var(--radius-lg);
            background: #fff;
            box-shadow: var(--shadow-lg);
            text-align: center;
            box-sizing: border-box;
        }

        .card-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--danger);
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .card-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: var(--radius-sm);
            padding: 14px 16px;
            font-size: 13px;
            color: #664d03;
            text-align: left;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .warning-box strong { color: #521e04; }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 700;
            font-size: 13px;
            color: var(--text-main);
            display: block;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #f5c6cb;
            border-radius: var(--radius-md);
            font-size: 14px;
            color: var(--text-main);
            outline: none;
            transition: var(--transition);
            background: #fafafa;
            box-sizing: border-box;
            font-family: "Courier New", monospace;
            letter-spacing: 2px;
        }

        .form-input:focus {
            border-color: var(--danger);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.12);
        }

        ::placeholder { color: #ccc; font-style: italic; letter-spacing: 0; }

        .btn-recovery {
            width: 100%;
            background-color: var(--danger);
            color: white;
            padding: 13px;
            border: none;
            border-radius: var(--radius-md);
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.25);
            margin-bottom: 16px;
        }

        .btn-recovery:hover {
            background-color: var(--danger-hover);
            transform: translateY(-1px);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
            transition: var(--transition);
            margin-top: 4px;
        }

        .back-link:hover {
            color: var(--primary);
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

        .alert-warning {
            background: #fff3cd;
            color: #664d03;
            border-color: #ffc107;
        }

        .divider {
            border: none;
            border-top: 1px dashed var(--border-color);
            margin: 20px 0 16px;
        }
    </style>
</head>
<body>

    <div class="logo-circle">
        <i class="fa-solid fa-key"></i>
    </div>

    <div class="brand-text">Akses Darurat</div>
    <p class="brand-sub">POS System — Emergency Recovery Access</p>

    <div class="recovery-card">

        <h2 class="card-title"><i class="fa-solid fa-shield-halved me-1"></i> Masukkan Recovery Key</h2>
        <p class="card-subtitle">
            Gunakan fitur ini hanya jika akun admin tidak dapat diakses.<br>
            Recovery Key hanya bisa digunakan <strong>satu kali</strong>.
        </p>

        <!-- Flash Error Alert -->
        <?php if (session()->getFlashdata("error")) : ?>
            <div class="alert">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= esc(session()->getFlashdata("error")) ?>
            </div>
        <?php endif; ?>

        <!-- Flash Warning Alert -->
        <?php if (session()->getFlashdata("warning")) : ?>
            <div class="alert alert-warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <?= esc(session()->getFlashdata("warning")) ?>
            </div>
        <?php endif; ?>

        <div class="warning-box">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <strong>Perhatian:</strong> Halaman ini hanya untuk situasi darurat.
            Setelah berhasil masuk, Anda <strong>hanya bisa mengakses halaman Master Users</strong>
            untuk membuat atau mereset akun admin. Segera logout setelah selesai.
        </div>

        <form action="<?= site_url("recovery") ?>" method="post" id="recoveryForm">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="recovery_key">
                    <i class="fa-solid fa-key"></i> Recovery Key
                </label>
                <input
                    type="password"
                    name="recovery_key"
                    id="recovery_key"
                    class="form-input"
                    placeholder="Masukkan recovery key..."
                    required
                    autofocus
                    autocomplete="off"
                    spellcheck="false"
                >
            </div>

            <button type="submit" class="btn-recovery">
                <i class="fa-solid fa-unlock me-1"></i> Verifikasi & Masuk
            </button>
        </form>

        <hr class="divider">

        <a href="<?= site_url("login") ?>" class="back-link">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke halaman Login biasa
        </a>

    </div>

</body>
</html>
