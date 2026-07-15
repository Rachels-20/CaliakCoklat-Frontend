<?php
session_start();
require_once 'helper/auth_helper.php';
require_once __DIR__ . '/../config/config.php';
$error = "";
if (isset($_GET['cancel'])) {

  unset($_SESSION['reset_step']);
  unset($_SESSION['reset_email']);
  unset($_SESSION['reset_code']);

  header("Location: auth.php");
  exit;
}
if (
  !isset($_POST['send_code']) &&
  !isset($_POST['verify_code']) &&
  !isset($_POST['reset_password']) &&
  !isset($_GET['back'])
) {

  if (!isset($_SESSION['reset_email'])) {
    $_SESSION['reset_step'] = 1;
  }
}

if (!isset($_SESSION['reset_step'])) {
  $_SESSION['reset_step'] = 1;
}

$step = $_SESSION['reset_step'];
if (isset($_POST['send_code'])) {

  $email = $_POST['email'];

  $data = [
    "email" => $email
  ];
  $ch = curl_init(API_BASE_URL . "/auth/forgot-password");

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);

  curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
      "Content-Type: application/json"
    ]
  );

  curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    json_encode($data)
  );

  $response = curl_exec($ch);
  $httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
  );

  curl_close($ch);
  checkUnauthorized($httpCode);

  if ($httpCode == 200) {

    $_SESSION['reset_email'] = $email;
    $_SESSION['reset_step'] = 2;

    header("Location: forgot_password.php");
    exit;
  }

  $error = $response;
}

if (isset($_POST['verify_code'])) {

  $data = [
    "email" => $_SESSION['reset_email'],
    "code" => $_POST['code']
  ];

  $ch = curl_init(API_BASE_URL . "/auth/verify-reset-code");

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);

  curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
      "Content-Type: application/json"
    ]
  );

  curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    json_encode($data)
  );

  $response = curl_exec($ch);

  $httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
  );

  curl_close($ch);

  if (
    $httpCode == 200 &&
    trim($response) == "true"
  ) {

    $_SESSION['reset_code'] = $_POST['code'];
    $_SESSION['reset_step'] = 3;

    header("Location: forgot_password.php");
    exit;
  }

  $error = trim($response);
}

if (isset($_POST['reset_password'])) {

  if ($_POST['new_password'] != $_POST['confirm_password']) {

    $error = "Konfirmasi kata sandi tidak sesuai.";

  } else {

    $data = [
      "email" => $_SESSION['reset_email'],
      "code" => $_SESSION['reset_code'],
      "newPassword" => $_POST['new_password']
    ];
    $ch = curl_init(API_BASE_URL . "/auth/reset-password");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt(
      $ch,
      CURLOPT_HTTPHEADER,
      [
        "Content-Type: application/json"
      ]
    );

    curl_setopt(
      $ch,
      CURLOPT_POSTFIELDS,
      json_encode($data)
    );

    $response = curl_exec($ch);

    $httpCode = curl_getinfo(
      $ch,
      CURLINFO_HTTP_CODE
    );

    curl_close($ch);

    if ($httpCode == 200) {

      unset($_SESSION['reset_step']);
      unset($_SESSION['reset_email']);
      unset($_SESSION['reset_code']);

      $_SESSION['success_message'] =
        "Kata sandi berhasil diubah. Silakan masuk menggunakan kata sandi baru.";

      header("Location: auth.php");
      exit;
    }

    $error = trim($response, '"');
  }
}

if (isset($_GET['back'])) {

  $_SESSION['reset_step'] = (int) $_GET['back'];

  header("Location: forgot_password.php");
  exit;
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Lupa Kata Sandi</title>
  <link rel="icon" type="image/png" href="assets/logo_circle.png">
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body class="auth_body">

  <div class="overlay"></div>
  <div class="login-box">



    <?php if ($step == 1): ?>
      <h2>Lupa Kata Sandi?</h2>
      <?php if (!empty($error)): ?>
        <div class="error-message">
          <?= $error ?>
        </div>
      <?php endif; ?>
      <form method="POST">

        <div class="input-group">
          <input type="email" name="email" placeholder=" " required>

          <label>Email</label>
        </div>

        <button type="submit" name="send_code" class="btn">
          Kirim Kode OTP
        </button>
        <a href="forgot_password.php?cancel=1" class="backtologin">
          Kembali ke Halaman Masuk
        </a>


      </form>

    <?php endif; ?>
    <?php if ($step == 2): ?>
      <h2>Verifikasi Kode OTP</h2>
      <?php if (!empty($error)): ?>
        <div class="error-message">
          <?= $error ?>
        </div>
      <?php endif; ?>

      <form method="POST">

        <div class="input-group">
          <input type="text" name="code" placeholder=" " required>

          <label>Kode OTP</label>
        </div>

        <p class="otp-info">
          Kode OTP telah dikirim ke
          <span>
            <?= htmlspecialchars($_SESSION['reset_email']) ?>
          </span>
        </p>

        <div class="button-group">

          <button type="button" class="btn-secondary" onclick="window.location='forgot_password.php?back=1'">

            Kembali

          </button>

          <button type="submit" name="verify_code" class="btn">

            Verifikasi

          </button>


        </div>
        <a href="forgot_password.php?cancel=1" class="backtologin">Kembali ke Login</a>

      </form>

    <?php endif; ?>

    <?php if ($step == 3): ?>
      <h2>Buat Kata Sandi Baru</h2>


      <?php if (!empty($error)): ?>
        <div class="error-message">
          <?= $error ?>
        </div>
      <?php endif; ?>

      <form method="POST">

        <div class="input-group">
          <input type="password" name="new_password" placeholder=" " required>

          <label>Kata Sandi Baru</label>
        </div>

        <div class="input-group">
          <input type="password" name="confirm_password" placeholder=" " required>

          <label>Konfirmasi Kata Sandi</label>
        </div>

        <div class="button-group">

          <button type="button" class="btn-secondary" onclick="window.location='forgot_password.php?back=2'">

            Kembali

          </button>

          <button type="submit" name="reset_password" class="btn">

            Simpan Kata Sandi

          </button>


        </div>
        <a href="forgot_password.php?cancel=1" class="backtologin">Kembali ke Login</a>

      </form>

    <?php endif; ?>

  </div>

</body>

</html>