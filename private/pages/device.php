<?php
require_once __DIR__ . '/../../config/config.php';
$ch = curl_init(API_BASE_URL . "/devices/me");

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

$devices = json_decode($response, true); ?>

<div class="device-card">

    <div class="header">
        <h3>Device Management</h3>

        <!-- BUTTON -->
        <button class="detail-btn" onclick="openDevicePopup()">
            + Tambah Perangkat
        </button>
    </div>

    <div class="tools">
        <input type="text" id="deviceSearch" placeholder="🔍 Cari perangkat berdasarkan nama atau lokasi........ "
            class="search">
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Perangkat</th>
                <th>Lokasi</th>
                <th>Interval</th>
                <th>Status</th>
                <th>Terakhir Aktif</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            <?php if (!empty($devices)): ?>

                <?php foreach ($devices as $device): ?>

                    <tr class="device-row">

                        <td>
                            <?= $device['nama'] ?>
                        </td>

                        <td>
                            <?= $device['lokasi'] ?>
                        </td>

                        <td>
                            <?php
                            $interval = $device['intervalPengiriman'] ?? 3600000;

                            if ($interval < 60000) {
                                echo ($interval / 1000) . " detik";
                            } elseif ($interval < 3600000) {
                                echo ($interval / 60000) . " menit";
                            } else {
                                echo ($interval / 3600000) . " jam";
                            }
                            ?>
                        </td>

                        <td>
                            <?php

                            $status = "Belum Aktif";
                            $badgeClass = "pending";
                            $lastSeenText = "-";

                            // Status perangkat dari backend
                            if ($device['aktif'] === true) {

                                $status = "Aktif";
                                $badgeClass = "active";

                            } elseif ($device['aktif'] === false) {

                                $status = "Tidak Aktif";
                                $badgeClass = "inactive";
                            }

                            // Hanya format lastSeen
                            if (!empty($device['lastSeen'])) {

                                $lastSeen = strtotime($device['lastSeen']);

                                $selisihDetik = time() - $lastSeen;

                                if ($selisihDetik < 60) {

                                    $lastSeenText = "Baru saja";

                                } elseif ($selisihDetik < 3600) {

                                    $lastSeenText =
                                        floor($selisihDetik / 60) . " menit lalu";

                                } elseif ($selisihDetik < 86400) {

                                    $lastSeenText =
                                        floor($selisihDetik / 3600) . " jam lalu";

                                } else {

                                    $lastSeenText =
                                        floor($selisihDetik / 86400) . " hari lalu";
                                }
                            }
                            ?>
                            <span class="badge <?= $badgeClass ?>">
                                ● <?= $status ?>
                            </span>

                        </td>
                        <td>

                            <?= $lastSeenText ?>

                            <?php if (!empty($device['lastSeen'])): ?>
                                <br>
                                <small>
                                    <?= date(
                                        'd M Y, H:i',
                                        strtotime($device['lastSeen'])
                                    ) ?>
                                </small>
                            <?php endif; ?>

                        </td>

                        <td>

                            <button type="button" class="delete-device-btn" onclick="openDeleteModal(
        <?= $device['id'] ?>,
        '<?= htmlspecialchars($device['nama']) ?>'
    )">

                                🗑️

                            </button>

                            <button type="button" class="edit-device-btn" onclick='openEditDevice(
        <?= htmlspecialchars(
            json_encode($device),
            ENT_QUOTES,
            "UTF-8"
        ) ?>
    )'>

                                ✏️

                            </button>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5">
                        Tidak ada perangkat
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>
    </table>

</div>

<!-- POPUP -->
<div id="devicePopup" class="modal">

    <div class="modal-content">

        <form method="POST">

            <h2>Tambah Perangkat</h2>
            <?php if (!empty($claimError)): ?>
                <div id="claimErrorMessage" class="error-message">
                    <?= htmlspecialchars($claimError) ?>
                </div>
            <?php endif; ?>
            <label>Nama Perangkat</label>
            <input type="text" name="nama" placeholder="Alat Blok A" required>

            <label>Kode Perangkat</label>
            <input type="text" name="kode_perangkat" placeholder="CC003" required>

            <label>Kode Aktivasi</label>
            <input type="text" name="kode_aktivasi" placeholder="ABC123" required>

            <label>Lokasi</label>
            <input type="text" name="lokasi" placeholder="Kebun Utara" required>

            <label>Interval Pengiriman</label>

            <select name="interval_pengiriman" placeholder="Pilih Interval Pengiriman" required>
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

            <button type="button" class="btn-cancel" onclick="closeDevicePopup()">
                Batal
            </button>
            <button type="submit" name="claim_device" class="submit-btn">
                Tambah
            </button>


        </form>

    </div>
</div>
<?php if (!empty($claimError)): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("devicePopup").style.display = "flex";
        });
    </script>
<?php endif; ?>

<div id="deleteModal" class="popup">

    <div class="popup-content">

        <h2>Lepaskan Perangkat</h2>

        <p id="deleteMessage">
            Apakah Anda yakin?
        </p>

        <form method="POST">

            <input type="hidden" id="deleteDeviceId" name="device_id">
            <div class="modal-actions"> <button type="button" class="btn-cancel" onclick="closeDeleteModal()">
                    Batal
                </button>

                <button type="submit" name="unclaim_device" class="btn-delete">
                    Lepaskan
                </button>
            </div>


        </form>

    </div>

</div>