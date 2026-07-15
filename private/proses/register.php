<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = [
        "username" => trim($_POST['username']),
        "email" => trim($_POST['email']),
        "password" => trim($_POST['password']),
        "phone" => trim($_POST['phone'])
    ];
    $ch = curl_init(API_BASE_URL . "/auth/register");


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt(
        $ch,
        CURLOPT_POSTFIELDS,
        json_encode($data)
    );

    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        [
            "Content-Type: application/json"
        ]
    );

    $response = curl_exec($ch);

    $httpCode = curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    curl_close($ch);

    if ($httpCode == 200) {

        $_SESSION['success_message'] =
            "Pendaftaran berhasil. Silakan masuk.";

        header("Location: ../auth.php");

        exit();
    } else {

        $error = trim($response, '"');
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Daftar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/logo_circle.png">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* =========================
           LEFT — SLIDE IN DARI KIRI
        ========================= */
        .left {
            width: 50%;
            background: url('../assets/bc2.jpeg') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;

            animation: slideInLeft 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .left h1 {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .left p {
            margin-bottom: 20px;
        }

        /* =========================
           RIGHT — SLIDE IN DARI KANAN
        ========================= */
        .right {
            color: #FEFAE0;
            width: 50%;
            background: #606C38A6;
            display: flex;
            justify-content: center;
            align-items: center;

            animation: slideInRight 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-box {
            width: 70%;
            color: white;
        }

        .form-box h2 {
            font-size: 40px;
            color: #FEFAE0;
            margin-bottom: 30px;
            font-weight: 400;

            animation: fadeDown 0.5s ease 0.2s both;
        }

        /* =========================
           INPUT GROUPS — STAGGERED
        ========================= */
        .input-group {
            position: relative;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.7);
            margin-bottom: 25px;
            padding-bottom: 5px;
            transition: border-color 0.3s ease;

            animation: fadeUp 0.5s ease both;
        }

        .input-group:nth-child(1) {
            animation-delay: 0.25s;
        }

        .input-group:nth-child(2) {
            animation-delay: 0.35s;
        }

        .input-group:nth-child(3) {
            animation-delay: 0.45s;
        }

        .input-group:nth-child(4) {
            animation-delay: 0.55s;
        }

        .input-group:focus-within {
            border-bottom-color: rgba(255, 255, 255, 1);
            filter: drop-shadow(0 2px 6px rgba(255, 255, 255, 0.2));
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .input-group i {
            color: #f1f1dc;
            font-size: 18px;
            margin-right: 10px;
            width: 20px;
            text-align: center;
            transition: color 0.3s ease;
        }

        .input-group:focus-within i {
            color: white;
        }

        .input-group input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: white;
            font-size: 16px;
            padding-top: 8px;
        }

        .input-group label {
            position: absolute;
            left: 30px;
            top: 40%;
            transform: translateY(-50%);
            color: #f1f1dc;
            transition: 0.3s ease;
        }

        .input-group input:focus+label,
        .input-group input:not(:placeholder-shown)+label {
            top: -10px;
            font-size: 12px;
            color: white;
        }

        /* =========================
           CHECKBOX & BUTTON
        ========================= */
        .checkbox {
            color: #FEFAE0;
            font-size: 12px;
            margin-bottom: 20px;

            animation: fadeUp 0.5s ease 0.65s both;
        }

        .signup-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 20px;
            background: #c98b4f;
            font-size: 16px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;

            animation: fadeUp 0.5s ease 0.75s both;
        }

        .signup-btn:hover {
            background: #d1914f;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 255, 255, 0.15);
        }

        .signup-btn:active {
            transform: translateY(1px);
        }

        .login-btn {
            padding: 12px 30px;
            border-radius: 20px;
            border: none;
            background: #c98b4f;
            color: white;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background: #d1914f;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 255, 255, 0.15);
        }

        .login-btn:active {
            transform: translateY(1px);
        }

        /* =========================
           ERROR MESSAGE
        ========================= */
        .error-msg {
            color: #ffb4b4;
            margin-bottom: 20px;
            font-size: 14px;
            background: rgba(255, 100, 100, 0.15);
            border-radius: 10px;
            padding: 10px 14px;

            animation: fadeDown 0.4s ease both;
        }
    </style>
</head>

<body>

    <!-- LEFT -->
    <div class="left">
        <h1>Selamat Datang</h1>
        <p>Sudah memiliki akun?</p>
        <a href="../auth.php" class="login-btn">Masuk</a>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <div class="form-box">

            <h2>Daftar Akun</h2>

            <?php if (!empty($error)): ?>
                <p class="error-msg">
                    <?= htmlspecialchars($error) ?>
                </p>
            <?php endif; ?>

            <form method="POST">

                <div class="input-group">
                    <i class="fa-regular fa-user"></i>
                    <input type="text" id="username" name="username" placeholder=" " required>
                    <label for="username">Nama Pengguna</label>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder=" " required>
                    <label for="password">Kata Sandi</label>
                </div>

                <div class="input-group">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder=" " required>
                    <label for="email">Email</label>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-phone"></i>
                    <input type="text" id="phone" name="phone" placeholder=" " required>
                    <label for="phone">Nomor Telepon</label>
                </div>

                <div class="checkbox">
                    <input type="checkbox" required>
                    Saya menyetujui syarat dan ketentuan yang berlaku.
                </div>

                <button type="submit" class="signup-btn">
                    Daftar
                </button>

            </form>

        </div>
    </div>

</body>

</html>