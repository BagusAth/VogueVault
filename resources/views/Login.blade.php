<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VogueVault</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(to right, #e9f0f5, #ffffff);
            font-family: 'Poppins', sans-serif;
        }

        .header-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 22px;
            margin-bottom: 40px;
            animation: fadeInDown 0.8s ease;
        }

        .header-logo img {
            width: 115px;
            height: auto;
        }

        .header-logo h2 {
            font-size: 50px;
            font-weight: 800;
            color: #1d1d1d;
            letter-spacing: 2px;
            margin: 0;
        }

        .header-logo h2 span {
            color: #56756d;
        }

        .header-subtitle {
            text-align: center;
            font-size: 16px;
            color: #555;
            margin-top: 8px;
            margin-bottom: 25px;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            position: relative;
            width: 820px;
            height: 500px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: all 0.6s ease-in-out;
        }

        .form-box h1 {
            margin-bottom: 25px; /* Tambah jarak bawah dari heading */
        }

        .alert-success {
            margin-top: -5px;
            margin-bottom: 15px; /* Tambah jarak antara alert dan input */
        }

        @media (max-width: 850px) {
            .container {
                width: 90%;
                height: auto;
            }

            .header-logo h2 {
                font-size: 36px;
            }

            .header-logo img {
                width: 85px;
            }
        }
    </style>
</head>

<body>
    @php
        $activeTab = $activeTab
            ?? session('auth_tab')
            ?? ((($errors->any() && !$errors->has('email')) || old('name') || old('password')) ? 'register' : 'login');
    @endphp

    <!-- Bagian logo dan judul -->
    <div class="header-logo">
        <img src="{{ asset('images/logo1.png') }}" alt="VogueVault Logo">
        <h2>Vogue<span>Vault</span></h2>
    </div>

    <div class="container {{ $activeTab === 'register' ? 'active' : '' }}">
        <!-- Login Form -->
        <div class="form-box login">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <h1>Login</h1>
                @if(session('success'))
                    <div class="alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->has('email'))
                    <div class="alert-error">{{ $errors->first('email') }}</div>
                @endif
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="forgot-link">
                    <a href="#">Forgot Password?</a>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>

        <!-- Register Form -->
        <div class="form-box register">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <h1>Register</h1>
                @if($errors->any() && !$errors->has('email'))
                    <div class="alert-error">
                        @foreach($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif
                <div class="input-box">
                    <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
        </div>

        <!-- Toggle Panel -->
        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Hello, Welcome!</h1>
                <p>Don't have an account?</p>
                <button class="btn register-btn">Register</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Welcome back!</h1>
                <p>Already have an account?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
