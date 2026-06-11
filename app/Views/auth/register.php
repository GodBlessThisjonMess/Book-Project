<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Book Journey</title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #FFF0E4;
            --bg-card: #FFFFFF;
            --border-color: rgba(0, 121, 121, 0.12);
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --primary: #007979;
            --primary-hover: #005c5c;
            --danger: #be123c;
            --font-sans: 'Inter', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: var(--font-sans);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 121, 121, 0.06);
            animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .auth-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
        }

        .auth-logo i {
            font-size: 28px;
            color: var(--primary);
        }

        .auth-logo span {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #009696, #007979);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .auth-header h2 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .auth-header p {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 6px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px 12px 42px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 121, 121, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            margin-top: 24px;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
            box-shadow: 0 8px 20px rgba(0, 121, 121, 0.2);
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .auth-footer a:hover {
            color: var(--primary-hover);
        }

        .alert-error {
            background: rgba(190, 18, 60, 0.08);
            border: 1px solid rgba(190, 18, 60, 0.2);
            color: var(--danger);
            padding: 12px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-logo">
        <i class="fa-solid fa-book-bookmark"></i>
        <span>Book Journey</span>
    </div>

    <div class="auth-header">
        <h2>Daftar Akun Baru</h2>
        <p>Bergabunglah untuk mulai melacak aktivitas membaca Anda.</p>
    </div>

    <!-- Alert error jika ada -->
    <?php if (!empty($error)): ?>
        <div class="alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/register" method="POST">
        <div class="form-group">
            <label for="username">Username <span style="color: var(--danger);">*</span></label>
            <div class="input-wrapper">
                <i class="fa-regular fa-user"></i>
                <input type="text" id="username" name="username" class="form-control" placeholder="Pilih username unik..." required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label for="bio">Deskripsi / Peran Akun (Bio)</label>
            <div class="input-wrapper">
                <i class="fa-regular fa-address-card"></i>
                <input type="text" id="bio" name="bio" class="form-control" placeholder="Contoh: Pembaca Santai, Single Account" value="Single Account">
            </div>
        </div>

        <div class="form-group">
            <label for="password">Kata Sandi <span style="color: var(--danger);">*</span></label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password" name="password" class="form-control" placeholder="Buat kata sandi aman..." required>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label for="confirm_password">Konfirmasi Kata Sandi <span style="color: var(--danger);">*</span></label>
            <div class="input-wrapper">
                <i class="fa-solid fa-shield-halved"></i>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Ketik ulang kata sandi..." required>
            </div>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-user-plus"></i> Daftar Akun Sekarang
        </button>
    </form>

    <div class="auth-footer">
        Sudah memiliki akun? <a href="<?= BASE_URL ?>/login">Masuk Halaman Login</a>
    </div>
</div>

</body>
</html>
