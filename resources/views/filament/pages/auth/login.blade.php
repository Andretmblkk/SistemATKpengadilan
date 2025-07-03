<div style="position:fixed;top:0;left:0;z-index:9999;background:#22c55e;color:white;padding:8px;font-weight:bold;letter-spacing:1px;">INI VIEW ADMIN LOGIN CUSTOM</div>
@php
    $logo = asset('images/logo.png');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi ATK PTA Jayapura</title>
    <link rel="stylesheet" href="{{ asset('css/filament/custom-login.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7ef 0%, #c1e2d0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', Arial, sans-serif;
        }
        .login-container {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 12px 40px rgba(34,197,94,0.10), 0 2px 8px rgba(60,72,88,0.08);
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
            transition: box-shadow 0.2s;
        }
        .login-container:hover {
            box-shadow: 0 16px 48px rgba(34,197,94,0.18), 0 4px 16px rgba(60,72,88,0.10);
        }
        .login-logo {
            width: 110px;
            height: 110px;
            margin-bottom: 1.4rem;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(34,197,94,0.10);
            background: #f0fdf4;
            object-fit: contain;
            border: 3px solid #22c55e;
        }
        .login-title {
            font-size: 1.8rem;
            font-weight: 900;
            margin-bottom: 0.4rem;
            color: #15803d;
            letter-spacing: 0.5px;
        }
        .login-subtitle {
            color: #475569;
            margin-bottom: 2.2rem;
            font-size: 1.08rem;
        }
        .login-form label {
            display: block;
            text-align: left;
            margin-bottom: 0.3rem;
            font-weight: 600;
            color: #15803d;
        }
        .login-form input[type="email"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 0.95rem;
            margin-bottom: 1.2rem;
            border: 1.5px solid #bbf7d0;
            border-radius: 10px;
            font-size: 1.08rem;
            background: #f9fafb;
            transition: border 0.2s;
        }
        .login-form input:focus {
            border: 1.5px solid #22c55e;
            outline: none;
            background: #f0fdf4;
        }
        .login-form button {
            width: 100%;
            padding: 0.95rem;
            background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1.13rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(34,197,94,0.10);
            transition: background 0.2s, box-shadow 0.2s;
            letter-spacing: 0.5px;
        }
        .login-form button:hover {
            background: linear-gradient(90deg, #16a34a 0%, #22c55e 100%);
            box-shadow: 0 4px 16px rgba(34,197,94,0.18);
        }
        .login-footer {
            margin-top: 2.5rem;
            color: #64748b;
            font-size: 0.97rem;
        }
        .login-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 0.7rem 1rem;
            margin-bottom: 1.2rem;
            font-size: 1.01rem;
            text-align: left;
        }
        @media (max-width: 600px) {
            .login-container {
                padding: 1.2rem 0.5rem 1.2rem 0.5rem;
                max-width: 98vw;
            }
            .login-logo {
                width: 70px;
                height: 70px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="{{ $logo }}" alt="Logo PTA Jayapura" class="login-logo">
        <div class="login-title">Sistem Informasi ATK PTA Jayapura</div>
        <div class="login-subtitle">Selamat datang di Sistem Informasi ATK PTA Jayapura.<br>Silakan login menggunakan akun Anda untuk mengelola permintaan dan stok ATK secara efisien, aman, dan profesional.</div>
        @if ($errors->any())
            <div class="login-error">
                <ul style="margin:0; padding-left:1.2em;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('filament.auth.login') }}" class="login-form" autocomplete="off">
            @csrf
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Masukkan email Anda" required autofocus>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Masukkan password" required>
            <button type="submit">Masuk ke Sistem</button>
        </form>
        <div class="login-footer">
            &copy; {{ date('Y') }} Sistem Informasi ATK PTA Jayapura.<br>Pengadilan Tinggi Agama Jayapura
        </div>
    </div>
</body>
</html> 