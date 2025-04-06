<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login with Map Background</title>
    <link rel="stylesheet" href="{{ asset('css/style_login.css') }}">
    <!-- Google Fonts + Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    @if (session('error'))
        <div id="popup-error" class="popup-error">
            {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="login-container">
        <form class="login-form" action="{{ route('login.post') }}" method="POST">
            @csrf
            <h2>LOGIN HERE</h2>

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
                <i class="fas fa-envelope"></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
            </div>

            <div class="options">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="btn">LOGIN</button>

            <p class="register-link">To Register New Account → <a href="{{ route('register') }}">Click Here</a></p>
        </form>
    </div>
</body>
</html>
