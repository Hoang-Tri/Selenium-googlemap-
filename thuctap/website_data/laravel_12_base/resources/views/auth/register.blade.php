<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
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

    <div class="login-container">
        <form class="login-form" action="{{ route('register.post') }}" method="POST">
            @csrf
            <h2>REGISTER HERE</h2>

            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <i class="fas fa-user"></i>
            </div>

            <div class="input-box">
                <input type="text" name="fullname" placeholder="Fullname" required>
                <i class="fas fa-user"></i>
            </div>

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
                <i class="fas fa-envelope"></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
            </div>

            <div class="input-box">
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                <i class="fas fa-lock"></i>
            </div>

            <button type="submit" class="btn">REGISTER</button>
            <!-- <button type="submit" class="btn">REGISTER</button> -->

            <p class="register-link">Already have an account? → <a href="{{ route('login') }}">Login Here</a></p>
        </form>
    </div>
</body>
</html>
