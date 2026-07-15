<?php
date_default_timezone_set('Asia/Jakarta');
session_start();
$claimError = $_SESSION['claimError'] ?? "";
unset($_SESSION['claimError']);
require_once __DIR__ . '/helper/auth_helper.php';
require_once __DIR__ . '/../config/config.php';
if (!function_exists('checkUnauthorized')) {
  function checkUnauthorized(int $httpCode)
  {
    if ($httpCode === 401 || $httpCode === 403) {
      session_unset();
      session_destroy();
      header("Location: auth.php");
      exit();
    }
  }
}
$page = $_GET['page'] ?? 'dashboard';
if (!isset($_SESSION['token'])) {
  header("Location: auth.php");
  exit();
}
if (isset($_POST['claim_device'])) {

  $data = [
    "kodePerangkat" => $_POST['kode_perangkat'],
    "kodeAktivasi" => $_POST['kode_aktivasi'],
    "nama" => $_POST['nama'],
    "lokasi" => $_POST['lokasi'],
    "intervalPengiriman" => (int) $_POST['interval_pengiriman']
  ];
  $ch = curl_init(API_BASE_URL . "/devices/claim");

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);

  curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
      "Content-Type: application/json",
      "Authorization: Bearer " . $_SESSION['token']
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

    header("Location: ?page=device");
    exit;
  }

  // Ambil pesan dari backend
  $result = json_decode($response, true);
  $_SESSION['claimError'] =
    $result["message"] ?? "Terjadi kesalahan.";

  header("Location: ?page=device");
  exit;
}
if (isset($_POST['unclaim_device'])) {

  $deviceId = $_POST['device_id'];
  $ch = curl_init(API_BASE_URL . "/devices/" .
    $deviceId .
    "/unclaim");

  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

  curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
      "Authorization: Bearer " .
      $_SESSION['token']
    ]
  );

  curl_setopt(
    $ch,
    CURLOPT_RETURNTRANSFER,
    true
  );

  $response = curl_exec($ch);

  $httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
  );

  curl_close($ch);

  checkUnauthorized($httpCode);

  if ($httpCode == 200) {

    header("Location: ?page=device");
    exit;
  }

  echo $response;
}
$page = $_GET['page'] ?? 'dashboard';

if (isset($_POST['update_device'])) {

  $deviceId = $_POST['device_id'];

  $payload = [
    "nama" => $_POST['nama'],
    "lokasi" => $_POST['lokasi'],
    "intervalPengiriman" => (int) $_POST['interval_pengiriman']
  ];

  $ch = curl_init(API_BASE_URL . "/devices/" . $deviceId);

  curl_setopt(
    $ch,
    CURLOPT_CUSTOMREQUEST,
    "PUT"
  );

  curl_setopt(
    $ch,
    CURLOPT_RETURNTRANSFER,
    true
  );

  curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
      "Content-Type: application/json",
      "Authorization: Bearer " . $_SESSION['token']
    ]
  );

  curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    json_encode($payload)
  );

  $response = curl_exec($ch);

  $httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
  );

  curl_close($ch);

  checkUnauthorized($httpCode);

  if ($httpCode == 200) {

    header("Location: ?page=device");
    exit;
  }

  echo $response;
}
$ch = curl_init(API_BASE_URL . "/notifications/user/unread-count");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer " . $_SESSION['token']
]);

$response = curl_exec($ch);

$httpCode = curl_getinfo(
  $ch,
  CURLINFO_HTTP_CODE
);

curl_close($ch);

checkUnauthorized($httpCode);
$unreadCount = (int) $response;

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
$profile = json_decode($response, true);
?>
<!DOCTYPE html>
<html>

<head>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <link rel="icon" type="image/png" href="assets/logo_circle.png">
  <title>
    <?php
    switch ($page) {
      case 'dashboard':
        echo 'Dashboard';
        break;
      case 'mingguan':
        echo 'Data Mingguan';
        break;
      case 'device':
        echo 'Perangkat';
        break;
      case 'history':
        echo 'Riwayat';
        break;
      default:
        echo 'Dashboard';
    }
    ?>
  </title>

  <!-- Chart.js -->
  <script>
    const TOKEN = "<?= $_SESSION['token'] ?? '' ?>";
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

  <!-- SIDEBAR -->
  <div class="sidebar">



    <div class="logo">
      <img src="assets/nobg.png" class="logo-img">
    </div>

    <div class="profile">
      <?php if (!empty($profile['profileImage'])): ?>

        <img src="data:image/jpeg;base64,<?= $profile['profileImage'] ?>" class="profile-img">

      <?php else: ?>

        <img src="assets/default-profil.png" class="profile-img">

      <?php endif; ?>
      <h3>

        <?= htmlspecialchars($profile['username'] ?? $_SESSION['username']) ?>

      </h3>
      <a href="proses/profil.php" class="btn-dashboard">Edit</a>
    </div>

    <div class="menu">

      <a href="?page=dashboard" class="<?= $page == 'dashboard' ? 'active' : '' ?>">
        🏠 Dashboard
      </a>

      <a href="?page=mingguan" class="<?= $page == 'mingguan' ? 'active' : '' ?>">
        📊 Data Mingguan
      </a>

      <a href="?page=device" class="<?= $page == 'device' ? 'active' : '' ?>">
        🖥 Perangkat
      </a>

      <a href="?page=history" class="<?= $page == 'history' ? 'active' : '' ?>">

        ⚠ Riwayat

        <?php if ($unreadCount > 0): ?>
          <span class="notification-badge" id="notification-badge">
            <?= $unreadCount ?>
          </span>
        <?php endif; ?>

      </a>
    </div>

    <div class="bottom">
      <a href="auth.php?action=logout" class="btn-dashboard">Keluar</a>
    </div>

  </div>

  <!-- CONTENT -->
  <!-- CONTENT -->
  <div class="content">

    <!-- TOP NAVBAR -->
    <div class="top-navbar">
      <div class="navbar-left">
        <h2>
          <?php
          switch ($page) {
            case 'dashboard':
              echo 'Dashboard';
              break;
            case 'mingguan':
              echo 'Data Mingguan';
              break;
            case 'device':
              echo 'Perangkat Anda';
              break;
            case 'history':
              echo 'Riwayat Peringatan';
              break;
            default:
              echo 'Dashboard';
          }
          ?>
        </h2>
      </div>


    </div>

    <?php
    switch ($page) {
      case 'dashboard':
        require_once 'pages/dashboard.php';
        break;
      case 'mingguan':
        require_once 'pages/mingguan.php';
        break;
      case 'device':
        require_once 'pages/device.php';
        break;
      case 'history':
        require_once 'pages/history.php';
        break;
      default:
        require_once 'pages/dashboard.php';
    }
    ?>



  </div>
  <div class="popup-overlay" id="detailPopup">

    <div class="popup-box">

      <h2>Detail Peringatan</h2>

      <p>
        <strong>Jenis Peringatan:</strong>
        <span id="detailPopupTitle"></span>
      </p>

      <p>
        <strong>Waktu:</strong>
        <span id="popupTime"></span>
      </p>

      <p>
        <strong>Kondisi:</strong>
        <span id="popupSeverity" class="severity-badge"></span>
      </p>

      <p>
        <strong>Pesan:</strong>
      </p>

      <textarea id="popupMessage" readonly></textarea>

      <button onclick="closeDetailPopup()">
        Tutup
      </button>

    </div>

  </div>

  <div id="readAllPopup" class="popup">

    <div class="popup-content">

      <h2>Tandai Semua Dibaca</h2>

      <p>
        Apakah Anda yakin ingin menandai semua notifikasi sebagai dibaca?
      </p>

      <div class="popup-actions">

        <button class="cancel-btn" onclick="closeReadAllPopup()">

          Batal

        </button>

        <button class="confirm-btn" onclick="confirmReadAll()">

          Ya

        </button>

      </div>

    </div>

  </div>
  <!-- POPUP -->
  <div id="popup" class="popup">

    <div class="popup-content">

      <span class="close" onclick="closePopup()">&times;</span>

      <h2 id="popupTitle"></h2>

      <div id="popupValue" class="popup-value"></div>

      <div id="popupStatus" class="popup-status"></div>

      <p id="sensorPopupMessage"></p>

    </div>

  </div>
  <div id="editDevicePopup" class="modal">

    <div class="modal-content">

      <form method="POST">

        <h2>Edit Perangkat</h2>

        <input type="hidden" id="editDeviceId" name="device_id">

        <label>Nama Perangkat</label>

        <input type="text" id="editNama" name="nama" required>

        <label>Lokasi</label>

        <input type="text" id="editLokasi" name="lokasi" required>

        <label>Kode Perangkat</label>

        <input type="text" id="editKodePerangkat" readonly>

        <label>Interval Pengiriman</label>

        <select name="interval_pengiriman" id="editInterval" required>
          <option value="60000">1 Menit</option>
          <option value="300000">5 Menit</option>
          <option value="600000">10 Menit</option>
          <option value="1800000">30 Menit</option>
          <option value="3600000" selected>1 Jam</option>
          <option value="7200000">2 Jam</option>
          <option value="21600000">6 Jam</option>
          <option value="43200000">12 Jam</option>
          <option value="86400000">24 Jam</option>
        </select>

        <button type="button" class="cancel-btn" onclick="closeEditDevicePopup()">

          Batal

        </button>
        <button type="submit" name="update_device" class="submit-btn">

          Simpan Perubahan

        </button>


      </form>

    </div>

  </div>
  <script>
    const API_BASE_URL = "<?= API_BASE_URL ?>";
  </script>

  <script src="js/main.js"></script>
</body>

</html>