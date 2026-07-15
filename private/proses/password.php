<?php
session_start();
require_once '../helper/auth_helper.php';
require_once __DIR__ . '/../../config/config.php';
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old = trim($_POST['old']);
    $new = trim($_POST['new']);
    $confirm = trim($_POST['confirm']);

    if ($new !== $confirm) {

        $error = "Konfirmasi password tidak cocok";

    } else {

        $data = [
            "currentPassword" => $old,
            "newPassword" => $new,
            "confirmPassword" => $confirm
        ];
        $ch = curl_init(API_BASE_URL . "/auth/change-password");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                "Authorization: Bearer " . $_SESSION['token'],
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

            session_destroy();

            header(
                "Location: ../auth.php?password_changed=1"
            );

            exit();

        } else {

            $error = trim($response, '"');
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Kata Sandi</title>
    <link rel="stylesheet" href="edit.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/logo_circle.png">
</head>

<body>

    <div class="container">

        <img src="../assets/password.png">

        <h3>Kata Sandi</h3>
        <?php if (!empty($error)): ?>
            <p style="color: red; text-align: center; margin-bottom: 15px;">
                <?= $error ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <div class="input-group">
                <input type="password" id="old" name="old" placeholder=" " required>
                <label for="old">Kata Sandi Saat Ini</label>
            </div>

            <div class="input-group">
                <input type="password" id="new" name="new" placeholder=" " required>
                <label for="new">Kata Sandi Baru</label>
            </div>

            <div class="input-group">
                <input type="password" id="confirm" name="confirm" placeholder=" " required>
                <label for="confirm">Konfirmasi Kata Sandi</label>
            </div>

            <button type="submit" class="main-btn">
                Simpan
            </button>

            <div class="bottom">
                <a href="profil.php" class="btn">
                    Kembali
                </a>
            </div>

        </form>

    </div>

</body>

</html>