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

        .quick-tester {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px dashed var(--border-color);
            text-align: left;
        }
        .quick-tester-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .quick-btns {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        .btn-account {
            background: #fafafa;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            cursor: pointer;
            transition: var(--transition);
            text-align: left;
        }
        .btn-account:hover {
            background: #fff;
            border-color: var(--primary);
            color: var(--primary-hover);
            box-shadow: var(--shadow-sm);
        }
        .btn-account span {
            display: block;
            font-weight: 400;
            font-size: 11px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    <div class="logo-circle">
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

    <script>
        function fillAccount(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
