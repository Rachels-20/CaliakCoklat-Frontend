let temperatureChart;
let humidityChart;
let phChart;
let dashboardData = {};
// ==========================================
// MENENTUKAN STATUS SENSOR
// ==========================================


function getStatusClass(status) {

    switch (status) {

        case "Baik":
            return "status-optimal";

        case "Perlu Perhatian":
            return "status-warning";

        case "Bahaya":
            return "status-danger";

        default:
            return "";
    }
}

// ==========================================
// UPDATE STATUS DI DASHBOARD
// ==========================================
function updateSensorStatus(sensorId, rawValue) {
    const numericValue = parseFloat(rawValue);

    if (isNaN(numericValue)) return;

    const statusElement =
        document.getElementById(sensorId + "-status");

    if (!statusElement) return;
    let status = "";

    switch (sensorId) {

        case "temp":
            status = dashboardData.suhuTanahStatus;
            break;

        case "airtemp":
            status = dashboardData.suhuUdaraStatus;
            break;

        case "ph":
            status = dashboardData.phTanahStatus;
            break;

        case "hum":
            status = dashboardData.kelembapanTanahStatus;
            break;

        case "earthhum":
            status = dashboardData.kelembapanUdaraStatus;
            break;
    }

    statusElement.innerText = status;

    statusElement.className =
        "sensor-status " + getStatusClass(status);
}

// =========================
// HELPER: BENTUK TANGGAL DARI FILTER
// =========================
function getSelectedDate() {
    const selects = document.querySelectorAll('.select');

    // Jika bukan halaman mingguan
    if (selects.length < 2) {
        return getTodayLocal();
    }

    const weekSelect = selects[0];
    const monthSelect = selects[1];

    // Jika user belum menekan filter dan masih menggunakan default,
    // gunakan tanggal hari ini agar chart langsung tampil.
    if (
        weekSelect.value === "Minggu 1" &&
        monthSelect.value === "Bulan"
    ) {
        return getTodayLocal();
    }

    const currentYear = new Date().getFullYear();

    const monthMap = {
        "Januari": "01",
        "Februari": "02",
        "Maret": "03",
        "April": "04",
        "Mei": "05",
        "Juni": "06",
        "Juli": "07",
        "Agustus": "08",
        "September": "09",
        "Oktober": "10",
        "November": "11",
        "Desember": "12",
        "Bulan": String(new Date().getMonth() + 1).padStart(2, '0')
    };

    const weekMap = {
        "Minggu 1": "01",
        "Minggu 2": "08",
        "Minggu 3": "15",
        "Minggu 4": "22"
    };

    const selectedMonth =
        monthMap[monthSelect.value] ||
        String(new Date().getMonth() + 1).padStart(2, '0');

    const selectedDay =
        weekMap[weekSelect.value] || "01";

    return `${currentYear}-${selectedMonth}-${selectedDay}`;
}
// Tanggal hari ini berdasarkan timezone lokal
function getTodayLocal() {
    const now = new Date();

    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

// Bangun URL API mingguan
function getWeeklyApiUrl() {
    const selectedDate = getSelectedDate();
    return `${API_BASE_URL}/reports/weekly-report?date=${selectedDate}`;
}
// =========================
// LOAD DATA SENSOR TERBARU
// =========================
async function loadData() {
    if (!TOKEN) {
        console.error("Token tidak tersedia.");
        return;
    }
    try {
        // Ambil token yang disimpan setelah login

        const res = await fetch(
            API_BASE_URL + "/dashboard/overview", {
            headers: {
                "Authorization": "Bearer " + TOKEN
            }
        }
        );

        const device = await res.json();
        dashboardData = device;
        document.getElementById("temp").innerText =
            (device.suhuTanah ?? "-") + " C";

        document.getElementById("airtemp").innerText =
            (device.suhuUdara ?? "-") + " C";

        document.getElementById("ph").innerText =
            (device.phTanah ?? "-") + " pH";

        document.getElementById("hum").innerText =
            (device.kelembapanTanah ?? "-") + " %";

        document.getElementById("earthhum").innerText =
            (device.kelembapanUdara ?? "-") + " %";

        updateSensorStatus("temp", device.suhuTanah);
        updateSensorStatus("airtemp", device.suhuUdara);
        updateSensorStatus("ph", device.phTanah);
        updateSensorStatus("hum", device.kelembapanTanah);
        updateSensorStatus("earthhum", device.kelembapanUdara);

    } catch (error) {
        console.error("Gagal mengambil data dashboard:", error);
    }
}

async function loadRecommendation() {

    if (!TOKEN) return;

    try {

        const response = await fetch(
            API_BASE_URL + "/dashboard/recommendation", {
            headers: {
                "Authorization": "Bearer " + TOKEN
            }
        }
        );

        const data = await response.json();

        const statusEl =
            document.getElementById("recommendation-status");

        const conditionEl =
            document.getElementById("recommendation-condition");

        const listEl =
            document.getElementById("recommendation-list");

        if (data.status === "Optimal") {

            statusEl.innerHTML = "🟢 OPTIMAL";

        } else if (data.status === "Perlu Perhatian") {

            statusEl.innerHTML = "🟡 PERLU PERHATIAN";

        } else {

            statusEl.innerHTML = "🔴 KRITIS";
        }
        conditionEl.innerText = data.kondisi;

        statusEl.className =
            "recommendation-status";

        if (data.status === "Optimal") {

            statusEl.classList.add("status-optimal");

        } else if (
            data.status === "Perlu Perhatian"
        ) {

            statusEl.classList.add("status-warning");

        } else {

            statusEl.classList.add("status-danger");
        }

        listEl.innerHTML = "";

        data.saran.forEach(item => {

            listEl.innerHTML += `
        <li>${item}</li>
      `;

        });

    } catch (error) {

        console.error(
            "Gagal mengambil recommendation:",
            error
        );
    }
}
// =========================
// LOAD CHART DATA MINGGUAN
// =========================
async function loadWeeklyChart() {
    if (!document.getElementById("temperatureChart")) return;

    if (!TOKEN) {
        console.error("Token tidak tersedia.");
        return;
    }

    try {
        const WEEKLY_API_URL = getWeeklyApiUrl();

        const res = await fetch(WEEKLY_API_URL, {
            headers: {
                "Authorization": "Bearer " + TOKEN
            }
        });

        const weeklyData = await res.json();

        if (!weeklyData || typeof weeklyData !== "object") {
            console.error("Response tidak valid:", weeklyData);
            return;
        }

        // Karena response langsung berupa object
        const report = weeklyData;
        // =========================
        // UPDATE SUMMARY
        // =========================
        document.getElementById("hum").innerText =
            (report.kelembapanTanah?.averageWeekly ?? 0).toFixed(1) + " %";

        document.getElementById("earthhum").innerText =
            (report.kelembapanUdara?.averageWeekly ?? 0).toFixed(1) + " %";

        document.getElementById("ph").innerText =
            (report.phTanah?.averageWeekly ?? 0).toFixed(1) + " pH";

        document.getElementById("airtemp").innerText =
            (report.suhuUdara?.averageWeekly ?? 0).toFixed(1) + " C";

        document.getElementById("temp").innerText =
            (report.suhuTanah?.averageWeekly ?? 0).toFixed(1) + " C";

        // Gunakan daily averages dari suhu tanah sebagai referensi label
        // ==========================================
        // SORT DATA SUHU TANAH
        // ==========================================
        // ==========================================
        // URUTAN HARI TETAP
        // ==========================================
        const orderedDays = [
            "MONDAY",
            "TUESDAY",
            "WEDNESDAY",
            "THURSDAY",
            "FRIDAY",
            "SATURDAY",
            "SUNDAY"
        ];

        // Bahasa Indonesia
        const dayMap = {
            MONDAY: "Senin",
            TUESDAY: "Selasa",
            WEDNESDAY: "Rabu",
            THURSDAY: "Kamis",
            FRIDAY: "Jumat",
            SATURDAY: "Sabtu",
            SUNDAY: "Minggu"
        };

        // Label chart
        const labels = orderedDays.map(day => dayMap[day]);

        // ==========================================
        // FUNGSI AMBIL DATA BERDASARKAN HARI
        // ==========================================
        function mapDataByDay(dataArray) {

            return orderedDays.map(day => {

                const found = (dataArray || []).find(item => {

                    const itemDay = new Date(item.date)
                        .toLocaleDateString("en-US", {
                            weekday: "long"
                        })
                        .toUpperCase();

                    return itemDay === day;
                });

                // jika data tidak ada -> null
                if (!found) return null;

                // jika average null -> tetap null
                if (found.average === null) return null;

                return found.average;
            });
        }

        // ==========================================
        // DATASETS
        // ==========================================
        const temperatureDatasets = [

            {
                label: "Suhu Tanah",
                data: mapDataByDay(report.suhuTanah?.dailyAverages),
                borderColor: "#ff8800",
                backgroundColor: "#ff8800",
                tension: .4,
                fill: false,
                pointRadius: 5,
                spanGaps: true
            },

            {
                label: "Suhu Udara",
                data: mapDataByDay(report.suhuUdara?.dailyAverages),
                borderColor: "#e2c3a2",
                backgroundColor: "#e2c3a2",
                tension: .4,
                fill: false,
                pointRadius: 5,
                spanGaps: true
            }

        ];
        const humidityDatasets = [

            {
                label: "Kelembapan Tanah",
                data: mapDataByDay(report.kelembapanTanah?.dailyAverages),
                borderColor: "#4caf50",
                backgroundColor: "#4caf50",
                tension: .4,
                fill: false,
                pointRadius: 5,
                spanGaps: true
            },

            {
                label: "Kelembapan Udara",
                data: mapDataByDay(report.kelembapanUdara?.dailyAverages),
                borderColor: "#2196f3",
                backgroundColor: "#2196f3",
                tension: .4,
                fill: false,
                pointRadius: 5,
                spanGaps: true
            }

        ];
        const phDatasets = [

            {
                label: "pH Tanah",
                data: mapDataByDay(report.phTanah?.dailyAverages),
                borderColor: "#7a4a21",
                backgroundColor: "#7a4a21",
                tension: .4,
                fill: false,
                pointRadius: 5,
                spanGaps: true
            }

        ];
        // =========================
        // HAPUS CHART LAMA
        // =========================
        // =========================
        // OPTIONS CHART
        // =========================
        const temperatureOptions = {
            responsive: true,
            plugins: {
                legend: {
                    position: "right"
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    min: 0,
                    suggestedMax: 50,
                    grid: {
                        color: "#eee"
                    }
                }
            }
        };
        const humidityOptions = {
            responsive: true,
            plugins: {
                legend: {
                    position: "right"
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    min: 0,
                    max: 100,
                    grid: {
                        color: "#eee"
                    }
                }
            }
        };
        const phOptions = {
            responsive: true,
            plugins: {
                legend: {
                    position: "right"
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    min: 0,
                    max: 14,
                    grid: {
                        color: "#eee"
                    }
                }
            }
        };

        // =========================
        // HAPUS CHART LAMA
        // =========================
        if (temperatureChart) temperatureChart.destroy();
        if (humidityChart) humidityChart.destroy();
        if (phChart) phChart.destroy();

        // =========================
        // GRAFIK SUHU
        // =========================
        temperatureChart = new Chart(
            document.getElementById("temperatureChart"),
            {
                type: "line",
                data: {
                    labels: labels,
                    datasets: temperatureDatasets
                },
                options: temperatureOptions
            }
        );

        // =========================
        // GRAFIK KELEMBAPAN
        // =========================
        humidityChart = new Chart(
            document.getElementById("humidityChart"),
            {
                type: "line",
                data: {
                    labels: labels,
                    datasets: humidityDatasets
                },
                options: humidityOptions
            }
        );

        // =========================
        // GRAFIK PH
        // =========================
        phChart = new Chart(
            document.getElementById("phChart"),
            {
                type: "line",
                data: {
                    labels: labels,
                    datasets: phDatasets
                },
                options: phOptions
            }
        );

    } catch (error) {
        console.error("Gagal mengambil data mingguan:", error);
    }
}

// =========================
// INIT HALAMAN
// =========================
document.addEventListener("DOMContentLoaded", function () {


    // Dropdown history
    const dropdownBtn = document.querySelector(".dropdown-btn");
    const dropdownMenu = document.querySelector(".dropdown-menu");

    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle("show");
        });

        window.addEventListener("click", function () {
            dropdownMenu.classList.remove("show");
        });
    }

    // Event tombol Filter (hanya di halaman Data Mingguan)
    const filterBtn = document.querySelector(".weekly-card .filter-btn");
    if (filterBtn) {
        filterBtn.addEventListener("click", function () {
            loadWeeklyChart();
        });
    }

    // Load awal
    const isDashboardPage =
        document.querySelector(".cards") !== null;

    if (isDashboardPage) {

        loadData();
        loadRecommendation();

        setInterval(loadData, 2000);

        setInterval(loadRecommendation, 2000);
    }

    loadWeeklyChart();
    const filterItems =
        document.querySelectorAll(
            ".dropdown-menu div"
        );

    filterItems.forEach(item => {

        item.addEventListener(
            "click",
            function () {

                const filter =
                    this.dataset.filter;

                const rows =
                    document.querySelectorAll(
                        ".notification-row"
                    );

                rows.forEach(row => {

                    const severity =
                        row.dataset.severity;

                    if (
                        filter === "ALL" ||
                        severity === filter
                    ) {

                        row.style.display = "";

                    } else {

                        row.style.display = "none";
                    }
                });

                dropdownBtn.innerHTML =
                    "☰ " +
                    this.innerText +
                    " ▾";
                dropdownMenu.classList.remove("show");
            }
        );
    });
});

// =========================
// POPUP CARD
// =========================
function openPopup(sensor) {

    let title = "";
    let value = "";
    let condition = null;

    switch (sensor) {

        case "temp":
            title = "Suhu Tanah";
            value = dashboardData.suhuTanah + " °C";
            condition = {
                status: dashboardData.suhuTanahStatus,
                message: dashboardData.suhuTanahMessage,
                className: getStatusClass(dashboardData.suhuTanahStatus)
            };
            break;

        case "airtemp":
            title = "Suhu Udara";
            value = dashboardData.suhuUdara + " °C";
            condition = {
                status: dashboardData.suhuUdaraStatus,
                message: dashboardData.suhuUdaraMessage,
                className: getStatusClass(dashboardData.suhuUdaraStatus)
            };
            break;

        case "ph":
            title = "pH Tanah";
            value = dashboardData.phTanah + " pH";
            condition = {
                status: dashboardData.phTanahStatus,
                message: dashboardData.phTanahMessage,
                className: getStatusClass(dashboardData.phTanahStatus)
            };
            break;

        case "hum":
            title = "Kelembapan Tanah";
            value = dashboardData.kelembapanTanah + " %";
            condition = {
                status: dashboardData.kelembapanTanahStatus,
                message: dashboardData.kelembapanTanahMessage,
                className: getStatusClass(dashboardData.kelembapanTanahStatus)
            };
            break;

        case "earthhum":
            title = "Kelembapan Udara";
            value = dashboardData.kelembapanUdara + " %";
            condition = {
                status: dashboardData.kelembapanUdaraStatus,
                message: dashboardData.kelembapanUdaraMessage,
                className: getStatusClass(dashboardData.kelembapanUdaraStatus)
            };
            break;
    }

    document.getElementById("popupTitle").innerText = title;
    document.getElementById("popupValue").innerText = value;
    document.getElementById("popupStatus").innerText = condition.status;
    document.getElementById("sensorPopupMessage").innerText = condition.message;

    document.getElementById("popup").style.display = "flex";
    console.log("Status :", condition.status);
    console.log("Message:", condition.message);

    const popupStatus = document.getElementById("popupStatus");

    popupStatus.innerText = condition.status;
    popupStatus.className = "popup-status " + condition.className;
}

function closePopup() {
    document.getElementById("popup").style.display = "none";
}

// =========================
// DEVICE POPUP
// =========================
function openDevicePopup() {
    document.getElementById("devicePopup").style.display = "flex";
}

function closeDevicePopup() {

    document.getElementById("devicePopup").style.display = "none";

    const error =
        document.getElementById("claimErrorMessage");

    if (error) {
        error.remove(); // atau error.style.display = "none";
    }
}

function openDeleteModal(id, nama) {

    document.getElementById(
        "deleteDeviceId"
    ).value = id;

    document.getElementById(
        "deleteMessage"
    ).innerHTML =
        'Lepaskan perangkat <b>' +
        nama +
        '</b> dari akun Anda?';

    document.getElementById(
        "deleteModal"
    ).style.display = "flex";
}

function closeDeleteModal() {

    document.getElementById(
        "deleteModal"
    ).style.display = "none";
}
// Tutup popup jika klik area luar
window.addEventListener("click", function (event) {

    const popup =
        document.getElementById("devicePopup");

    if (event.target === popup) {
        popup.style.display = "none";
    }
});
document.addEventListener("DOMContentLoaded", function () {

    const searchInput =
        document.querySelector(".search");

    const rows =
        document.querySelectorAll(".notification-row");

    if (searchInput) {

        searchInput.addEventListener("keyup", function () {

            const keyword =
                this.value.toLowerCase().trim();

            rows.forEach(row => {

                const text =
                    row.innerText.toLowerCase();

                row.style.display =
                    text.includes(keyword) ? "" : "none";

            });

        });

    }

});
document.addEventListener("DOMContentLoaded", function () {

    const searchInput =
        document.getElementById("deviceSearch");

    const rows =
        document.querySelectorAll(".device-row");

    if (searchInput) {

        searchInput.addEventListener("keyup", function () {

            const keyword =
                this.value.toLowerCase().trim();

            rows.forEach(row => {

                const text =
                    row.innerText.toLowerCase();

                row.style.display =
                    text.includes(keyword) ? "" : "none";

            });

        });

    }

});

function openEditDevice(device) {

    document.getElementById(
        "editDeviceId"
    ).value = device.id;

    document.getElementById(
        "editNama"
    ).value = device.nama;

    document.getElementById(
        "editLokasi"
    ).value = device.lokasi;

    document.getElementById(
        "editInterval"
    ).value = device.intervalPengiriman;

    document.getElementById(
        "editKodePerangkat"
    ).value = device.kodePerangkat;

    document.getElementById(
        "editDevicePopup"
    ).style.display = "flex";
}

function closeEditDevicePopup() {

    document.getElementById(
        "editDevicePopup"
    ).style.display = "none";
}