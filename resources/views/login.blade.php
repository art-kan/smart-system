<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,700;1,400;1,700&display=swap"
          rel="stylesheet">
</head>
<body>
<img class="logo" src="/images/logo-register.png" alt="Smart System">
<div class="register-top">
    <h1>Войти в систему...</h1>
    <div class="register">
        <img src="/images/user-register.png" alt="LogIn">
        <h2>Для входа в систему укажите свой email адресс</h2>
        <form class="register-input" method="POST">
            @csrf
            <div class="input-container">
                <label for="email-input">
                    <h3>E-mail</h3>
                    <input type="email" name="email" id="email-input" autofocus required>
                </label>
                <label for="password-input">
                    <h3>Пароль</h3>
                    <input type="password" name="password" id="password-input" required>
                </label>
            </div>
            <button class="login-btn" type="submit">Войти</button>
        </form>
    </div>
</div>
</body>
</html>
