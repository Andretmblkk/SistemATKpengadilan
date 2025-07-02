@php
    $logo = asset('images/logo.png'); // Pastikan logo ada di public/images/logo.png
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem ATK Pengadilan</title>
    <link rel="stylesheet" href="{{ asset('css/filament/custom-login.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #cfd9df 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(60,72,88,0.15);
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .login-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
        }
        .login-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #1a202c;
        }
        .login-subtitle {
            color: #4a5568;
            margin-bottom: 2rem;
        }
        .login-form input[type="email"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1rem;
        }
        .login-form button {
            width: 100%;
            padding: 0.75rem;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .login-form button:hover {
            background: #1d4ed8;
        }
        .login-footer {
            margin-top: 2rem;
            color: #a0aec0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="{{ $logo }}" alt="Logo" class="login-logo">
        <div class="login-title">Sistem ATK Pengadilan</div>
        <div class="login-subtitle">Silakan login untuk melanjutkan</div>
        <form method="POST" action="{{ route('filament.auth.login') }}" class="login-form">
            @csrf
            <input type="email" name="email" placeholder="Email" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Masuk</button>
        </form>
        <div class="login-footer">
            &copy; {{ date('Y') }} Sistem ATK Pengadilan. All rights reserved.
        </div>
    </div>
</body>
</html> 