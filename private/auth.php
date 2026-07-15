<?php
session_start();
require_once __DIR__ . '/../config/config.php';
$action = $_GET['action'] ?? '';

switch ($action) {

  case 'login':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      $username = $_POST['username'];
      $password = $_POST['password'];

      $data = [
        "username" => $username,
        "password" => $password
      ];
      $ch = curl_init(API_BASE_URL . "/auth/login");

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
      ]);

      $response = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($response, true);

      if (isset($result['token'])) {

        $_SESSION['token'] = $result['token'];

        $_SESSION['username'] =
          $result['username'];

        $_SESSION['email'] =
          $result['email'];

        header("Location: index.php");
        exit();
      } else {
        $error = "Login gagal!";
      }
    }
    break;

  case 'logout':
    session_destroy();
    header("Location: auth.php");
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>Masuk</title>
  <link rel="icon" type="image/png" href="assets/logo_circle.png">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">

</head>

<body class="auth_body">

  <div class="overlay"></div>

  <div class="login-box">
    <h1>Masuk</h1>
    <?php if (isset($_SESSION['success_message'])): ?>

      <div class="success-message">
        <?= $_SESSION['success_message']; ?>
      </div>

      <?php unset($_SESSION['success_message']); ?>

    <?php endif; ?>
    <div class="line"></div>
    <?php if (isset($error)): ?>
      <p style="color:red; text-align:center;">
        <?= $error ?>
      </p>
    <?php endif; ?>
    <?php if (isset($_GET['expired'])): ?>
      <div class="error-message">
        Sesi Anda telah berakhir. Silakan masuk kembali.
      </div>
    <?php endif; ?>

    <form method="POST" action="auth.php?action=login">

      <div class="input-group">
        <input type="text" name="username" placeholder=" " required>

        <label>Nama Pengguna atau Email</label>
      </div>

      <div class="input-group">
        <input type="password" name="password" placeholder=" " required>

        <label>Kata Sandi</label>
      </div>

      <div class="links">
        <a href="forgot_password.php">lupa sandi?</a>
        <a href="proses/register.php">daftar</a>
      </div>

      <button type="submit" class="btn">
        Masuk
      </button>
      <button type="button" class="btn btn-secondary" onclick="window.location.href='../index.php'">Kembali</button>

    </form>
  </div>

</body>

</html>