<?php

require_once __DIR__ . '/../../config/config.php';
$ch = curl_init(API_BASE_URL . "/notifications/user");

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

$notificationResponse =
    json_decode($response, true);

$notifications =
    $notificationResponse['content'] ?? [];
?>

<!-- HISTORY -->
<div class="history-card">

    <!-- TOP BAR -->
    <div class="history-top">
        <div class="left-actions">
            <div class="dropdown">

                <button class="dropdown-btn">
                    ☰ Semua ▾
                </button>
                <button class="read-all-btn" onclick="openReadAllPopup()">
                    Tandai Semua Dibaca
                </button>

                <div class="dropdown-menu">
                    <div data-filter="ALL">Semua</div>
                    <div data-filter="BAHAYA">Bahaya</div>
                    <div data-filter="PERHATIAN">Perhatian</div>

                </div>

            </div>
        </div>


        <div class="right">
            <input type="text" placeholder="Search..." class="search">
        </div>

    </div>

    <!-- TABLE -->
    <table class="history-table">

        <thead>
            <tr>
                <th>Sensor</th>
                <th>Kondisi</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Detail</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($notifications as $notification): ?>

                <tr class="notification-row" data-severity="<?= htmlspecialchars($notification['severity']) ?>">

                    <td>
                        <?= htmlspecialchars(
                            $notification['title'] ?? '-'
                        ) ?>
                    </td>

                    <td>

                        <?php
                        $severity =
                            $notification['severity'] ?? 'PERHATIAN';

                        $severityClass =
                            $severity === 'BAHAYA'
                            ? 'severity-danger'
                            : 'severity-warning';
                        ?>

                        <span class="severity-badge <?= $severityClass ?>">

                            <?= htmlspecialchars($severity) ?>

                        </span>

                    </td>

                    <td>

                        <?= date(
                            "d M Y H:i",
                            strtotime($notification['createdAt'])
                        ) ?>

                    </td>

                    <td id="status-<?= $notification['id'] ?>">

                        <?= $notification['isRead']
                            ? 'Sudah Dibaca'
                            : 'Belum Dibaca' ?>

                    </td>

                    <td>

                        <button class="detail-btn" onclick='openDetail(
<?= htmlspecialchars(
            json_encode($notification),
            ENT_QUOTES,
            "UTF-8"
        ) ?>
)'>

                            Detail

                        </button>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</div>


<script>
    async function openDetail(data) {

        try {

            await fetch(
                "./notifikasi_dibaca.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id=" + data.id
            }
            );

            const statusCell =
                document.getElementById(
                    "status-" + data.id
                );

            if (statusCell) {
                statusCell.innerText =
                    "Sudah Dibaca";
            }
            if (!data.isRead) {

                const badge =
                    document.getElementById(
                        "notification-badge"
                    );

                if (badge) {

                    let count =
                        parseInt(badge.innerText);

                    count--;

                    if (count <= 0) {
                        badge.style.display = "none";
                    } else {
                        badge.innerText = count;
                    }
                }

                data.isRead = true;
            }
            const severityElement =
                document.getElementById(
                    "popupSeverity"
                );

            severityElement.innerText =
                data.severity === "BAHAYA" ?
                    "🚨 BAHAYA" :
                    "⚠️ PERHATIAN";

            severityElement.className =
                "severity-badge " +
                (
                    data.severity === "BAHAYA" ?
                        "severity-danger" :
                        "severity-warning"
                );

        } catch (e) {
            console.error(e);
        }

        document.getElementById(
            "detailPopup"
        ).style.display = "flex";

        document.getElementById(
            "detailPopupTitle"
        ).innerText = data.title;

        const tanggal = new Date(data.createdAt);

        const tanggalFormat = tanggal.toLocaleDateString("id-ID", {
            day: "numeric",
            month: "long",
            year: "numeric"
        });

        const waktuFormat = tanggal.toLocaleTimeString("id-ID", {
            hour: "2-digit",
            minute: "2-digit"
        });

        document.getElementById("popupTime").innerText =
            `${tanggalFormat} • ${waktuFormat} WIB`;
        document.getElementById(
            "popupMessage"
        ).value = data.message ?? "-";
    }

    async function confirmReadAll() {

        try {

            const response = await fetch(
                "./baca_semua_notifikasi.php", {
                method: "POST"
            }
            );

            if (!response.ok) {
                throw new Error(
                    "Gagal menandai notifikasi"
                );
            }

            document
                .querySelectorAll(
                    "[id^='status-']"
                )
                .forEach(status => {
                    status.innerText =
                        "Sudah Dibaca";
                });

            const badge =
                document.getElementById(
                    "notification-badge"
                );

            if (badge) {
                badge.style.display =
                    "none";
            }
            closeReadAllPopup();

        } catch (e) {
            console.error(e);
            alert(
                "Gagal menandai semua notifikasi"
            );
        }
    }

    function closeDetailPopup() {

        document.getElementById(
            "detailPopup"
        ).style.display = "none";
    }

    function openReadAllPopup() {

        document.getElementById(
            "readAllPopup"
        ).style.display = "flex";
    }

    function closeReadAllPopup() {

        document.getElementById(
            "readAllPopup"
        ).style.display = "none";
    }
</script>