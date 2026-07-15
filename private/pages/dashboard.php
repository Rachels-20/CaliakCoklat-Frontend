<!-- DASHBOARD -->
<div class="cards">

    <!-- SUHU TANAH -->
    <div class="card-wrapper besar-wrapper">
        <h3 class="title-card">Suhu Tanah</h3>

        <div class="card besar" onclick="openPopup('temp', document.getElementById('temp').innerText)">
            <img src="assets/temperature.png">
            <div id="temp" class="value">-</div>
            <div id="temp-status" class="sensor-status">-</div>
        </div>
    </div>

    <!-- SUHU UDARA -->
    <div class="card-wrapper besar-wrapper">
        <h3 class="title-card">Suhu Udara</h3>

        <div class="card besar" onclick="openPopup('airtemp', document.getElementById('airtemp').innerText)">
            <img src="assets/suhu-udara.png">
            <div id="airtemp" class="value">-</div>
            <div id="airtemp-status" class="sensor-status">-</div>
        </div>
    </div>

    <!-- PH TANAH -->
    <div class="card-wrapper kecil-wrapper">
        <h3 class="title-card kecil-title">pH Tanah</h3>

        <div class="card kecil" onclick="openPopup('ph', document.getElementById('ph').innerText)">
            <img src="assets/ph.png">
            <div id="ph" class="value">-</div>
            <div id="ph-status" class="sensor-status">-</div>
        </div>
    </div>

    <!-- KELEMBAPAN TANAH -->
    <div class="card-wrapper kecil-wrapper">
        <h3 class="title-card kecil-title">Kelembapan Tanah</h3>

        <div class="card kecil" onclick="openPopup('hum', document.getElementById('hum').innerText)">
            <img src="assets/kelembapan-tanah.png">
            <div id="hum" class="value">-</div>
            <div id="hum-status" class="sensor-status">-</div>
        </div>
    </div>

    <!-- KELEMBAPAN UDARA -->
    <div class="card-wrapper kecil-wrapper">
        <h3 class="title-card kecil-title">Kelembapan Udara</h3>

        <div class="card kecil" onclick="openPopup('earthhum', document.getElementById('earthhum').innerText)">
            <img src="assets/kelembapan-udara.png">
            <div id="earthhum" class="value">-</div>
            <div id="earthhum-status" class="sensor-status">-</div>
        </div>
    </div>

</div>

<div class="section-title" style="margin-top:40px;">
    Kondisi & Saran
</div>

<div class="recommendation-box">

    <div id="recommendation-status" class="recommendation-status">
        -
    </div>

    <p id="recommendation-condition" class="recommendation-condition">
        Memuat data...
    </p>

    <ul id="recommendation-list" class="recommendation-list">
    </ul>

</div>