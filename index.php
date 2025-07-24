<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Infinite Slider</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <header class="navbar">
    <div class="logo">SMP Bina Informatika</div>
    <nav>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Informasi</a></li>
        <li><a href="#">Prosedur</a></li>
        <li><a href="#">Admission</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </nav>
  </header>

  <section class="slider-wrapper">
    <button class="nav-btn left" id="prevBtn">&#10094;</button>
    <div class="slider-container">
      <div class="slider" id="slider">
        <div class="slide" style="background-image: url('https://source.unsplash.com/1600x900/?tech,1');">
          <h1>Slide Pertama</h1>
          <p>Selamat datang di masa depan.</p>
        </div>
        <div class="slide" style="background-image: url('https://source.unsplash.com/1600x900/?tech,2');">
          <h1>Slide Kedua</h1>
          <p>Kami hadir dengan teknologi terbaru.</p>
        </div>
        <div class="slide" style="background-image: url('https://source.unsplash.com/1600x900/?tech,3');">
          <h1>Slide Ketiga</h1>
          <p>Masa depan dimulai dari sini.</p>
        </div>
      </div>
    </div>
    <button class="nav-btn right" id="nextBtn">&#10095;</button>
    <div class="dots" id="dots"></div>
  </section>

  <section class="extra-section">
    <h2>Tentang Kami</h2>
    <p>Ini adalah bagian tambahan dari halaman yang bisa di-scroll ke bawah. Gunakan bagian ini untuk menjelaskan layanan atau fitur Anda.</p>
  </section>

  <script src="script.js"></script>
</body>
</html>
