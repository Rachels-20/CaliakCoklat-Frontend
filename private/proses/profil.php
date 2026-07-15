<?php
session_start();

require_once '../helper/auth_helper.php';
require_once __DIR__ . '/../../config/config.php';
$error = "";
$success = "";

$profile = [
    "username" => "",
    "email" => "",
    "phone" => "",
    "profileImage" => ""
];
$ch = curl_init(API_BASE_URL . "/auth/profile");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
        "Authorization: Bearer " . $_SESSION['token']
    ]
);

$response = curl_exec($ch);
$httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);
curl_close($ch);
checkUnauthorized($httpCode);

if ($response) {
    $profile = json_decode($response, true);

    if (
        isset($profile['phone']) &&
        substr($profile['phone'], 0, 2) == '62'
    ) {
        $profile['phone'] =
            '0' . substr($profile['phone'], 2);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = [];

    if (!empty($_POST['username'])) {
        $data["username"] = trim($_POST['username']);
    }

    if (!empty($_POST['email'])) {
        $data["email"] = trim($_POST['email']);
    }

    if (!empty($_POST['phone'])) {

        $phone = trim($_POST['phone']);

        if (substr($phone, 0, 1) == '0') {
            $phone =
                '62' . substr($phone, 1);
        }

        $data["phone"] = $phone;
    }
    if (
        isset($_FILES['profileImage']) &&
        $_FILES['profileImage']['error'] === 0
    ) {

        if ($_FILES['profileImage']['size'] > 2 * 1024 * 1024) {

            $error = "Ukuran foto maksimal 2 MB";

        } else {

            $data['profileImage'] =
                base64_encode(
                    file_get_contents(
                        $_FILES['profileImage']['tmp_name']
                    )
                );
        }
    }
    $ch = curl_init(API_BASE_URL . "/auth/profile");

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

    if ($httpCode == 200) {

        $success = "Profil berhasil diperbarui";
        $ch = curl_init(API_BASE_URL . "/auth/profile");

        curl_setopt(
            $ch,
            CURLOPT_RETURNTRANSFER,
            true
        );

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                "Authorization: Bearer " . $_SESSION['token']
            ]
        );

        $profileResponse = curl_exec($ch);

        curl_close($ch);

        if ($profileResponse) {

            $profile = json_decode(
                $profileResponse,
                true
            );

            if (
                isset($profile['phone']) &&
                substr($profile['phone'], 0, 2) == '62'
            ) {
                $profile['phone'] =
                    '0' . substr($profile['phone'], 2);
            }
        }

    } else {

        $error = trim($response, '"');
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="edit.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/logo_circle.png">
</head>

<body>

    <div class="container">

        <?php if (!empty($success)): ?>
            <div class="message-wrapper">
                <div class="success-message">
                    <?= $success ?>
                </div>
            </div>

        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">

            <div class="profile-wrapper">

                <?php if (!empty($profile['profileImage'])): ?>
                    <img src="data:image/jpeg;base64,<?= $profile['profileImage'] ?>" class="profile-photo"
                        id="previewPhoto">
                <?php else: ?>
                    <img src="../assets/default-profil.png" class="profile-photo" id="previewPhoto">
                <?php endif; ?>

                <label for="profileImage" class="edit-photo-btn">
                    <img src="../assets/pen.png" class="edit-icon">
                </label>

                <input type="file" id="profileImage" name="profileImage" accept="image/*" hidden>

            </div>
            <h3>Edit Profil</h3>

            <div class="form-area">



                <div class="input-group">
                    <input type="text" name="username" value="<?= htmlspecialchars($profile['username']) ?>"
                        placeholder=" " required>
                    <label>Nama</label>
                </div>

                <div class="input-group">
                    <input type="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" placeholder=" "
                        required>
                    <label>Email</label>
                </div>

                <div class="input-group">
                    <input type="text" name="phone" value="<?= htmlspecialchars($profile['phone']) ?>" placeholder=" "
                        required>
                    <label>Nomor HP</label>
                </div>

                <button type="submit" class="main-btn">
                    Simpan Perubahan
                </button>
                <div class="bottom">
                    <a href="../index.php?page=dashboard" class="btn">Kembali</a>
                    <a href="password.php" class="btn">Edit Kata Sandi</a>
                </div>

            </div>
        </form>





    </div>
    <script>

        document
            .getElementById("profileImage")
            .addEventListener("change", function (e) {

                const file = e.target.files[0];

                if (file) {

                    document.getElementById("previewPhoto").src =
                        URL.createObjectURL(file);
                }
            });

    </script>
</body>

</html>