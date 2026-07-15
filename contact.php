<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hubungi Kami</title>
    <link rel="icon" type="image/png" href="assets/logo_circle.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <!-- NAVBAR -->
    <header class="navbar">
        <div class="logo">
            <img src="assets/nobg.png" alt="logo">
        </div>
        <nav>
            <a href="index.php#manfaat">Manfaat</a>
            <a href="index.php#about">Tentang Kami</a>
            <a href="contact.php">Kontak</a>
            <a href="dokumentasi.php">Dokumentasi</a>
            <a href="private/auth.php">Masuk</a>
        </nav>
    </header>

    <!-- ISI CONTACT -->
    <div class="container">

        <!-- LEFT -->
        <div class="left">
            <h1>Hubungi Kami</h1>
            <p class="desc">
                Jika Anda memiliki pertanyaan mengenai Caliak Coklat, silakan hubungi kami melalui
                informasi berikut.
            </p>

            <div class="info-row">
                <i class="fa-brands fa-instagram icon"></i>
                <div class="text">
                    <p>Instagram</p>
                    <span>
                        <a href="https://instagram.com/caliakcoklat" target="_blank" class="link-text">
                            @caliakcoklat
                        </a>
                    </span>
                </div>
            </div>

            <div class="info-row">
                <i class="fa-solid fa-envelope icon"></i>
                <div class="text">
                    <p>Email</p>
                    <span>
                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to=ptpriasolo6767@gmail.com" target="_blank"
                            class="email-link">
                            ptpriasolo6767@gmail.com
                        </a>
                    </span>
                </div>
            </div>

            <div class="info-row">
                <i class="fa-solid fa-phone icon"></i>
                <div class="text">
                    <p>Nomor HP</p>
                    <span>
                        <a href="https://wa.me/6283126068790" target="_blank" class="link-text">
                            +62 831 2606 8790
                        </a>
                    </span>
                </div>
            </div>

            <div class="info-row">
                <i class="fa-solid fa-location-dot icon"></i>
                <div class="text">
                    <p>Alamat</p>
                    <span>
                        <a href="https://maps.google.com/?q=Padang,Sumatera+Barat,Indonesia" target="_blank"
                            class="link-text">
                            Padang, Sumatera Barat, Indonesia
                        </a>
                    </span>
                </div>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="right">
            <div class="form-container">
                <h2>Ready to Get Started?</h2>
                <form>
                    <div class="input-group">
                        <input type="text" id="namaDepan" placeholder=" " required>
                        <label for="namaDepan">Nama Depan</label>
                    </div>

                    <div class="input-group">
                        <input type="text" id="namaBelakang" placeholder=" " required>
                        <label for="namaBelakang">Nama Belakang</label>
                    </div>

                    <div class="input-group">
                        <input type="text" id="hp" placeholder=" " required>
                        <label for="hp">Nomor Handphone</label>
                    </div>

                    <div class="input-group">
                        <textarea id="pertanyaan" placeholder=" " required></textarea>
                        <label for="pertanyaan">Pertanyaan</label>
                    </div>

                    <div class="action-buttons">
                        <a href="index.php" class="btn-kembali">Kembali</a>
                        <button type="submit" class="submit">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>