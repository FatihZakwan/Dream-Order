<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

include "config.php";

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM menu WHERE nama_produk LIKE '%$search%'";
$result = mysqli_query($conn, $query);

$updated_prices = isset($_SESSION['updated_prices']) ? $_SESSION['updated_prices'] : [];

// =========== SIMPAN PRODUK KE SESSION (BUKAN DATABASE) ===========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents("php://input"), true);

  if ($data && isset($data['products'])) {
    // Pastikan session keranjang sudah ada
    if (!isset($_SESSION['cart'])) {
      $_SESSION['cart'] = [];
    }

    foreach ($data['products'] as $produk_id) {
      $produk_id = intval($produk_id);

      // Ambil data produk dari tabel menu
      $getProduk = $conn->query("SELECT * FROM menu WHERE id = $produk_id");
      $produk = $getProduk->fetch_assoc();

      if ($produk) {
        // Jika produk sudah ada di keranjang, tambahkan jumlahnya
        if (isset($_SESSION['cart'][$produk_id])) {
          $_SESSION['cart'][$produk_id]['qty'] += 1;
        } else {
          // Kalau belum ada, tambahkan sebagai item baru
          $_SESSION['cart'][$produk_id] = [
            'id' => $produk['id'],
            'nama_produk' => $produk['nama_produk'],
            'harga' => $produk['harga'],
            'gambar' => $produk['gambar'],
            'deskripsi' => $produk['catatan'],
            'stand' => 'Stand 1', // kamu bisa ubah nanti kalau ada kolom stand asli
            'qty' => 1
          ];
        }
      }
    }

    echo json_encode(['success' => true]);
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home Page - Dream Order</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  
  <style>
body {
  font-family: 'Inter', sans-serif;
  background-color: #FFF6ED;
  background-image: url('img/Pattern Group.png');
  background-size: auto;
  background-repeat: repeat;
  background-position: center;
  position: relative;
}

body::before {
  content: "";
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 246, 237, 0.6);
  pointer-events: none;
  z-index: -1;
}

.selected {
  background-color: #00A59A !important;
  border: 3px solid #00A59A;
  transform: scale(1.02);
  transition: all 0.25s ease;
}

.selected h3,
.selected p {
  color: white !important;
}

#addToCartBtn:disabled {
  background-color: #9ca3af !important;
  cursor: not-allowed !important;
}

.product-img {
  width: 100%;
  height: 180px;
  object-fit: contain;
  background-color: #ffffff;
}

.navbar-container {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

@media (min-width: 640px) {
  .navbar-container {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
  }
}
  </style>
</head>

<body class="flex flex-col min-h-screen">

  <!-- NAVBAR -->
  <nav class="mx-3 sm:mx-10 bg-white rounded-2xl px-4 sm:px-6 py-3 mt-5 shadow-md top-0 z-50">
    <div class="navbar-container w-full">
      <div class="flex items-center justify-between sm:justify-start gap-3">
        <div class="flex items-center gap-3">
          <img src="img/logo.png" alt="Logo" class="h-9 sm:h-10">
          <a href="home_page.php" class="flex items-center gap-2 text-[#F7931E] font-semibold hover:underline text-sm sm:text-base">
            <img src="img/lets-icons_back.png" alt="Back" class="w-5 h-5 sm:w-6 sm:h-6">
            Back to Home
          </a>
        </div>
      </div>

      <form method="GET" action="" class="flex justify-center w-full sm:w-auto">
        <input 
          type="text" 
          name="search" 
          placeholder="Cari produk..." 
          value="<?php echo htmlspecialchars($search); ?>"
          class="border border-gray-300 rounded-full px-4 py-2 w-[80%] sm:w-[280px] md:w-[350px] focus:outline-none focus:ring-2 focus:ring-[#00A59A] text-sm sm:text-base"
        >
      </form>

      <div class="flex justify-center sm:justify-end gap-2 w-full sm:w-auto">
        <button onclick="goToCart()" class="flex items-center justify-center gap-2 border-2 border-[#00A59A] bg-[#00A59A] text-white px-4 py-2 rounded-full font-semibold hover:bg-[#008C83] transition text-sm sm:text-base whitespace-nowrap">
          <img src="img/Vector (1).png" alt="Cart" class="w-4 h-4 brightness-0 invert">
          Cart
        </button>

        <button onclick="logout()" class="border-2 border-[#00A59A] text-[#00A59A] px-4 py-2 rounded-full font-semibold hover:bg-[#00A59A] hover:text-white transition text-sm sm:text-base whitespace-nowrap">
          Logout
        </button>
      </div>
    </div>
  </nav>

  <!-- HEADER -->
  <section class="mx-3 sm:mx-10 bg-[#F7931E] text-center text-white py-6 sm:py-10 rounded-2xl mt-5 shadow-md">
    <h1 class="text-2xl sm:text-4xl font-extrabold">Dream Order</h1>
    <p class="text-sm sm:text-lg mt-2">Solusi Pintar Antrian!</p>
    <div class="flex flex-wrap justify-center gap-2 sm:gap-3 mt-4">
      <button class="bg-white text-[#F7931E] px-3 sm:px-5 py-2 rounded-full font-semibold hover:bg-[#FFE5CC] transition text-xs sm:text-sm">Buat Antrian Mudah</button>
      <button class="bg-white text-[#F7931E] px-3 sm:px-5 py-2 rounded-full font-semibold hover:bg-[#FFE5CC] transition text-xs sm:text-sm">Pantau Giliran</button>
      <button class="bg-white text-[#F7931E] px-3 sm:px-5 py-2 rounded-full font-semibold hover:bg-[#FFE5CC] transition text-xs sm:text-sm">Hemat Waktu</button>
    </div>
  </section>

  <!-- PRODUK -->
  <section class="mx-3 sm:mx-10 mt-6 flex-grow">
    <h2 class="mx-auto bg-[#F7931E] text-white py-2 px-6 sm:px-10 rounded-xl text-base sm:text-lg font-bold text-center w-fit shadow-sm">
      Welcome to Stand 1 Product!
    </h2>

    <div id="product-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-5">
      <?php
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $id = $row['id'];
          $nama = htmlspecialchars($row['nama_produk']);
          $harga_db = htmlspecialchars($row['harga']);
          $harga_final = isset($updated_prices[$id]) ? htmlspecialchars($updated_prices[$id]) : $harga_db;
          $gambar = !empty($row['gambar']) ? htmlspecialchars($row['gambar']) : 'noimage.png';
          $catatan = !empty($row['catatan']) ? htmlspecialchars($row['catatan']) : 'Tidak ada catatan.';
          $gambar_path = "uploads/" . $gambar;

          echo "
          <div class='bg-white rounded-xl shadow-md overflow-hidden cursor-pointer transition hover:shadow-lg product-card' data-id='$id'>
            <img src='$gambar_path' alt='$nama' class='product-img'>
            <div class='p-3 sm:p-4'>
              <h3 class='font-bold text-base sm:text-lg text-gray-800'>$nama</h3>
              <p class='text-gray-600 text-xs sm:text-sm mt-1'>$catatan</p>
              <p class='font-bold text-[#F7931E] mt-2 text-sm sm:text-lg'>Rp $harga_final</p>
            </div>
          </div>";
        }
      } else {
        echo '<p class="text-center text-gray-500 col-span-2 sm:col-span-3">Produk tidak ditemukan.</p>';
      }
      ?>
    </div>
  </section>

  <!-- BUTTON -->
  <div class="text-center mt-8 mb-10">
    <a href="#" class="text-[#00A59A] font-semibold hover:underline">See All</a>
    <br><br>
    <button 
      id="addToCartBtn" 
      class="bg-gray-400 text-white font-semibold px-6 py-2 rounded-full cursor-not-allowed transition"
      disabled>
      Add to Cart
    </button>
  </div>

  <!-- FOOTER -->
  <footer class="bg-white text-center text-gray-500 text-xs sm:text-sm mt-auto py-3 rounded-t-2xl shadow-inner">
    © 2025 Dream Order. All rights reserved.
  </footer>

  <!-- SCRIPT -->
  <script>
    let selectedProducts = new Set();

    document.querySelectorAll('.product-card').forEach(card => {
      card.addEventListener('click', () => {
        const id = card.dataset.id;

        if (selectedProducts.has(id)) {
          selectedProducts.delete(id);
          card.classList.remove('selected');
        } else {
          selectedProducts.add(id);
          card.classList.add('selected');
        }

        toggleAddButton();
      });
    });

    function toggleAddButton() {
      const button = document.getElementById('addToCartBtn');
      if (selectedProducts.size > 0) {
        button.disabled = false;
        button.classList.remove('bg-gray-400', 'cursor-not-allowed');
        button.classList.add('bg-[#00A59A]', 'hover:bg-[#008C83]');
      } else {
        button.disabled = true;
        button.classList.add('bg-gray-400', 'cursor-not-allowed');
        button.classList.remove('bg-[#00A59A]', 'hover:bg-[#008C83]');
      }
    }

    document.getElementById('addToCartBtn').addEventListener('click', () => {
      if (selectedProducts.size > 0) {
        const productsArray = Array.from(selectedProducts);
        fetch('home_page.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ products: productsArray })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            window.location.href = 'cart.php';
          } else {
            Swal.fire('Gagal', 'Gagal menambahkan ke keranjang!', 'error');
          }
        });
      }
    });

    function logout() {
      Swal.fire({
        icon: 'question',
        title: 'Logout?',
        text: 'Apakah kamu yakin ingin keluar?',
        showCancelButton: true,
        confirmButtonText: 'Ya, keluar',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#00A59A',
        cancelButtonColor: '#f87171'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'logout.php';
        }
      });
    }

    function goToCart() {
      window.location.href = 'cart.php';
    }
  </script>

</body>
</html>
