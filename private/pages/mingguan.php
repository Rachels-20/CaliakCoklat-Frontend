<div class="weekly-card">
    <h3>Data Mingguan</h3>
    <p class="subtitle">Data Kebun Anda Perminggu</p>

    <div class="summary">

        <div class="item">
            <p>Kelembaban Tanah</p>
            <span id="hum">-</span>
        </div>

        <div class="item">
            <p>Kelembaban Udara</p>
            <span id="earthhum">-</span>
        </div>

        <div class="item">
            <p>pH Tanah</p>
            <span id="ph">-</span>
        </div>

        <div class="item">
            <p>Suhu Udara</p>
            <span id="airtemp">-</span>
        </div>

        <div class="item">
            <p>Suhu Tanah</p>
            <span id="temp">-</span>
        </div>

    </div>

    <div class="filters">
        <select class="select">
            <option>Minggu 1</option>
            <option>Minggu 2</option>
            <option>Minggu 3</option>
            <option>Minggu 4</option>
        </select>

        <select class="select">
            <option>Bulan</option>
            <option>Januari</option>
            <option>Februari</option>
            <option>Maret</option>
            <option>April</option>
            <option>Mei</option>
            <option>Juni</option>
            <option>Juli</option>
            <option>Agustus</option>
            <option>September</option>
            <option>Oktober</option>
            <option>November</option>
            <option>Desember</option>
        </select>

        <button class="filter-btn">
            Terapkan
        </button>
    </div>

    <div class="chart-group">

        <div class="chart-item">
            <h4>Grafik Suhu (°C)</h4>
            <canvas id="temperatureChart"></canvas>
        </div>

        <div class="chart-item">
            <h4>Grafik Kelembapan (%)</h4>
            <canvas id="humidityChart"></canvas>
        </div>

        <div class="chart-item full-width">
            <h4>Grafik pH Tanah</h4>
            <canvas id="phChart"></canvas>
        </div>

    </div>

</div>