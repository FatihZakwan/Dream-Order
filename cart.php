<?php
session_start();
include "config.php";

// Inisialisasi cart
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Tambah item ke cart
if (isset($_POST['menu_id'])) {
  $menu_id = intval($_POST['menu_id']);
  $result = $conn->query("SELECT * FROM menu WHERE id_utama = $menu_id");

  if ($result && $row = $result->fetch_assoc()) {
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
      if ($item['id'] == $menu_id) {
        $item['qty']++;
        $found = true;
        break;
      }
    }
    if (!$found) {
      $_SESSION['cart'][] = [
        'id' => $menu_id,
        'nama_produk' => $row['nama_produk'],
        'harga' => (int) $row['harga'], // pastikan jadi integer
        'gambar' => $row['gambar'] ?? 'no-image.png',
        'catatan' => $row['catatan'] ?? '',
        'qty' => 1
      ];
    }
  }
}

// Update qty lewat AJAX (tanpa reload halaman)
if (isset($_POST['update_cart'])) {
  $id = intval($_POST['id']);
  $qty = intval($_POST['qty']);
  foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] == $id) {
      $item['qty'] = $qty;
      break;
    }
  }
  exit; // hentikan output supaya hanya balas AJAX
}

$cart = $_SESSION['cart'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart - Dream Order</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #FFF6ED;
      background-image: url('img/Pattern Group.png');
      background-size: 1500px auto;
      background-repeat: repeat;
      background-attachment: fixed;
      background-position: center;
    }
    .btn-outline {
      border: 2px solid #00A59A;
      color: #00A59A;
      font-weight: 600;
      border-radius: 9999px;
      padding: 0.5rem 1.3rem;
      transition: 0.2s;
    }
    .btn-outline:hover {
      background-color: #00A59A;
      color: white;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <!-- Navbar -->
  <nav class="bg-white mx-3 sm:mx-10 mt-5 rounded-2xl shadow-md p-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <img src="img/logo.png" alt="Logo" class="h-10">
      <a href="home_page.php" class="flex items-center text-[#F7931E] font-semibold hover:underline text-sm sm:text-base">
        <img src="img/lets-icons_back.png" alt="Back" class="w-5 h-5 mr-2">Back to Stand Menu
      </a>
    </div>
    <button onclick="logout()" class="btn-outline rounded-full px-5 py-2 text-sm sm:text-base">Logout</button>
  </nav>

  <main class="flex-grow container mx-auto px-4 py-6">
    <?php if (empty($cart)) : ?>
      <div class="flex flex-col justify-center items-center text-center h-[70vh]">
        <img src="img/cart.png" alt="Empty Cart" class="w-28 sm:w-36 mb-6 opacity-90">
        <h2 class="text-[#F7931E] text-3xl sm:text-4xl font-bold">Your cart’s bored.</h2>
        <p class="text-gray-600 mt-3 text-lg sm:text-xl">Add something to make it happy!</p>
        <a href="home_page.php" class="mt-8 bg-[#00A59A] hover:bg-[#008C83] text-white px-6 py-3 rounded-full text-lg font-semibold transition">
          Let's Go Shopping!
        </a>
      </div>
    <?php else : ?>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-6">Your Cart</h1>

      <div class="flex flex-col lg:flex-row gap-6">
        <!-- LEFT -->
        <div class="flex-1 space-y-4">
          <div class="flex items-center gap-2 mb-2">
            <input type="checkbox" id="pickAll" class="w-5 h-5 accent-[#00A59A] cursor-pointer">
            <label for="pickAll" class="font-semibold text-gray-700">Pick All</label>
          </div>

          <?php foreach ($cart as $item) : ?>
            <div class="cart-item bg-white p-4 rounded-2xl shadow-lg flex flex-col sm:flex-row items-start sm:items-center gap-4 transition hover:shadow-xl">
              <input type="checkbox" class="item-checkbox w-5 h-5 accent-[#00A59A] cursor-pointer" data-id="<?= $item['id']; ?>">

              <img src="uploads/<?= htmlspecialchars($item['gambar']); ?>" alt="<?= htmlspecialchars($item['nama_produk']); ?>" class="w-24 h-24 object-cover rounded-xl">

              <div class="flex flex-col flex-1">
                <h3 class="font-bold text-gray-800 text-lg"><?= htmlspecialchars($item['nama_produk']); ?></h3>
                <?php if (!empty($item['catatan'])) : ?>
                  <p class="text-gray-600 text-sm"><?= htmlspecialchars($item['catatan']); ?></p>
                <?php endif; ?>
                <p class="font-bold text-[#F7931E] mt-1 text-lg">Rp <?= htmlspecialchars($item['harga']); ?></p>
              </div>

              <div class="flex items-center gap-2 mt-3 sm:mt-0">
                <button class="qty-btn w-8 h-8 border-2 border-[#00A59A] text-[#00A59A] rounded-full font-bold text-lg hover:bg-[#00A59A] hover:text-white transition" data-action="minus" data-id="<?= $item['id']; ?>">−</button>
                <span class="font-semibold text-gray-700 text-lg qty-display" data-id="<?= $item['id']; ?>"><?= $item['qty']; ?></span>
                <button class="qty-btn w-8 h-8 border-2 border-[#00A59A] text-[#00A59A] rounded-full font-bold text-lg hover:bg-[#00A59A] hover:text-white transition" data-action="plus" data-id="<?= $item['id']; ?>">+</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- RIGHT -->
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full lg:w-80 h-fit sticky top-10">
          <h2 class="font-bold text-xl mb-3 text-gray-800">Here’s Your Order</h2>
          <div class="flex justify-between items-center text-gray-700 mb-4">
            <span>Total</span>
            <span id="totalHarga" class="font-semibold text-[#00A59A] text-lg">Rp 0</span>
          </div>
          <button id="checkoutBtn" class="w-full bg-[#00A59A] hover:bg-[#008C83] text-white py-3 rounded-full font-semibold transition text-lg">
            Checkout (0)
          </button>
        </div>
      </div>
    <?php endif; ?>
  </main>

  <footer class="bg-white text-center text-gray-500 text-xs sm:text-sm mt-auto py-3 rounded-t-2xl shadow-inner">
    © 2025 Dream Order
  </footer>

  <script>
    // Hitung total harga dinamis
    function updateTotal() {
      let total = 0, count = 0;
      $('.item-checkbox:checked').each(function() {
        const card = $(this).closest('.cart-item');
        const hargaText = card.find('p.font-bold.text-[#F7931E]').text().replace(/[Rp\s.]/g, '');
        const qty = parseInt(card.find('.qty-display').text());
        total += parseInt(hargaText) * qty;
        count++;
      });
      $('#totalHarga').text('Rp ' + total.toLocaleString('id-ID'));
      $('#checkoutBtn').text('Checkout (' + count + ')');
    }

    $('#pickAll').on('change', function() {
      $('.item-checkbox').prop('checked', $(this).prop('checked'));
      updateTotal();
    });
    $(document).on('change', '.item-checkbox', updateTotal);

    // Tombol +/−
    $('.qty-btn').click(function() {
      const id = $(this).data('id');
      const action = $(this).data('action');
      const qtyDisplay = $('.qty-display[data-id="' + id + '"]');
      let qty = parseInt(qtyDisplay.text());
      if (action === 'plus') qty++;
      else if (action === 'minus' && qty > 1) qty--;
      qtyDisplay.text(qty);
      updateTotal();

      // Kirim ke server untuk update session
      $.post(window.location.href, { update_cart: true, id: id, qty: qty });
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
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
