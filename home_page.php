<?php
include "config.php";

session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit();
}

// Ambil data dari tabel menu
$query = "SELECT * FROM menu";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dream Order - Solusi Pintar Antrian</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="font-sans bg-gray-100 min-h-screen flex flex-col">
  <header class="bg-yellow-500 text-white p-4 text-center rounded-b-lg shadow-md mb-6">
    <h1 class="text-2xl font-bold">Dream Order</h1>
    <p class="text-sm">Solusi Pintar Antrian!</p>
    <div class="mt-2 flex justify-center space-x-2">
      <a href="#" class="bg-white text-yellow-500 px-3 py-1 rounded text-sm font-semibold hover:bg-gray-200">Buat Antrian Lebih Mudah</a>
      <a href="#" class="bg-white text-yellow-500 px-3 py-1 rounded text-sm font-semibold hover:bg-gray-200">Pantau Giliran dari HP Kamu</a>
      <a href="#" class="bg-white text-yellow-500 px-3 py-1 rounded text-sm font-semibold hover:bg-gray-200">Hemat Waktu, Nongkrong Asyik</a>
    </div>
  </header>

  <main class="flex-grow container mx-auto px-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              // Cek apakah file gambar ada di server
              $image_path = file_exists($row['image_url']) ? $row['image_url'] : 'https://via.placeholder.com/200x150';
              echo '<div class="bg-white p-4 rounded-lg shadow-md text-center">';
              echo '<img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($row['nama_menu']) . '" class="w-full rounded-md" />';
              echo '<h3 class="text-lg font-semibold mt-2">' . htmlspecialchars($row['nama_menu']) . '</h3>';
              echo '<p class="text-lg font-bold mt-1">Rp ' . number_format($row['harga'], 0, ',', '.') . '</p>';
              echo '</div>';
          }
      } else {
          echo '<p class="text-center col-span-full text-gray-600">Tidak ada produk tersedia.</p>';
      }
      ?>
    </div>
    <div class="text-center mt-6 text-gray-600">See all</div>
    <button class="bg-teal-500 text-white px-8 py-3 rounded-full font-semibold mt-4 mx-auto block hover:bg-teal-600 transition duration-300">
      Buy
    </button>
  </main>

  <footer class="bg-gray-100 text-center p-4 text-gray-600 mt-6">
    <p>Copyright Dream Order ©2025</p>
  </footer>

  <?php $conn->close(); ?>
</body>
</html>