<?php
include "config.php"; // koneksi ke database
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-inter bg-[#FFF4E8] bg-[url('Pattern (1).png')] bg-repeat bg-[length:180px]">

<!-- NAVBAR -->
<nav class="flex items-center justify-between bg-white rounded-2xl px-8 py-4 mx-6 mt-5 shadow-md">
  <div class="flex items-center gap-6">
    <img src="img/logo.png" alt="Logo" class="h-10">
    <a href="home_page.php" class="text-[#FBBF77] font-semibold hover:text-[#F7931E] transition">Home</a>
    <a href="dashboard.php" class="text-[#F7931E] font-bold">Dashboard</a>
  </div>
  <button onclick="logout()" class="border-2 border-[#009688] text-[#009688] px-5 py-2 rounded-full font-semibold hover:bg-[#009688] hover:text-white transition">
    Logout
  </button>
</nav>


<!-- MAIN CONTENT -->
<div class="flex justify-between gap-6 px-6 py-8">

  <!-- CUSTOMER LIST -->
  <div class="w-1/2 bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="bg-[#F7931E] text-white font-bold text-lg px-6 py-3">
      Customer Names
    </div>
    <div class="p-6 space-y-3">
      <?php
      $no = 1;
      $q = mysqli_query($conn, "SELECT * FROM pesanan WHERE status != 'Success'");
      while ($row = mysqli_fetch_assoc($q)) {
          echo "
          <form method='post'>
            <div class='flex justify-between items-center border border-gray-200 rounded-xl p-3 bg-white hover:bg-[#FFF4E8] transition'>
              <div class='flex items-center gap-3'>
                <div class='bg-[#F7931E] text-white font-bold w-7 h-7 flex items-center justify-center rounded-md'>$no</div>
                <b class='text-gray-800'>{$row['nama_pemesan']}</b>
              </div>
              <div>
                <input type='hidden' name='id' value='{$row['id']}'>
                <select name='status' onchange='this.form.submit()' class='border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[#009688]'>
                  <option ".($row['status']=="Pending"?"selected":"").">Pending</option>
                  <option ".($row['status']=="On Process"?"selected":"").">On Process</option>
                  <option ".($row['status']=="Success"?"selected":"").">Success</option>
                </select>
                <input type='hidden' name='update_status'>
              </div>
            </div>
          </form>";
          $no++;
      }
      ?>
    </div>
  </div>

  <!-- ADD PRODUCT -->
  <div class="w-1/2 bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="bg-[#F7931E] text-white font-bold text-lg px-6 py-3">
      Add Product
    </div>
    <div class="p-6">
      <form method="post" enctype="multipart/form-data">
        <!-- Upload Box -->
        <div id="uploadBox" class="w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl bg-gray-100 flex flex-col items-center justify-center cursor-pointer hover:bg-gray-200 transition relative overflow-hidden">
          <img src="img/Vector.png" alt="Upload Vector" class="w-12 opacity-80" id="vectorIcon">
          <p class="text-gray-400 font-semibold mt-2" id="uploadText">+ Add Photo For Product</p>
          <input type="file" name="gambar" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(event)">
          <img id="preview" class="absolute inset-0 w-full h-full object-contain p-3 hidden bg-gray-100 rounded-2xl" alt="Preview">
        </div>

        <!-- Nama Produk -->
        <label class="block font-bold text-gray-700 mt-5">Product Name</label>
        <input 
          type="text" 
          name="nama_produk" 
          placeholder="Enter your product name..." 
          required 
          class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 bg-[#ffffff] hover:bg-[#f2f2f2] focus:outline-none transition duration-200"
        >

        <!-- Harga Produk -->
        <label class="block font-bold text-gray-700 mt-4">Product Price</label>
        <input 
          type="text" 
          name="harga_produk" 
          id="harga_produk" 
          placeholder="Masukkan harga produk..." 
          required 
          oninput="formatHarga(this)" 
          class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 bg-[#ffffff] hover:bg-[#f2f2f2] focus:outline-none transition duration-200"
        >

        <!-- Catatan Produk -->
        <label class="block font-bold text-gray-700 mt-4">Product Notes</label>
        <textarea 
          name="catatan" 
          placeholder="Tambahkan catatan tentang produk ini (opsional)..."
          class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 bg-[#ffffff] hover:bg-[#f2f2f2] focus:outline-none transition duration-200"
        ></textarea>

        <button type="submit" name="tambah_produk" class="bg-[#009688] hover:bg-[#00796b] text-white font-semibold rounded-lg px-5 py-2 mt-6 float-right transition">
          + Add
        </button> 
        <br><br><br><br>
      </form>
    </div>
  </div>
</div>

<script>
function previewImage(event) {
  const preview = document.getElementById('preview');
  const vectorIcon = document.getElementById('vectorIcon');
  const uploadText = document.getElementById('uploadText');
  const file = event.target.files[0];

  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.classList.remove('hidden');
      vectorIcon.classList.add('hidden');
      uploadText.classList.add('hidden');
    }
    reader.readAsDataURL(file);
  }
}

function logout() {
  Swal.fire({
    title: "Yakin ingin keluar?",
    text: "Anda akan logout dari dashboard.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#009688",
    cancelButtonColor: "#d33",
    confirmButtonText: "Ya, keluar",
    cancelButtonText: "Batal"
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = 'logout.php';
    }
  });
}

function formatHarga(input) {
  let angka = input.value.replace(/\D/g, "");
  input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
</script>

<?php
// === TAMBAH PRODUK ===
if (isset($_POST['tambah_produk'])) {
    $nama = trim($_POST['nama_produk']);
    $harga = trim($_POST['harga_produk']);
    $catatan = trim($_POST['catatan']);
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    if (empty($nama) || empty($harga) || empty($gambar)) {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Data tidak lengkap!',
                text: 'Mohon isi semua kolom wajib!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => window.location = 'dashboard.php');
        </script>";
        exit;
    }

    $folder = "uploads/";
    if (!file_exists($folder)) mkdir($folder, 0777, true);

    $ext = pathinfo($gambar, PATHINFO_EXTENSION);
    $gambar_baru = uniqid('img_', true) . "." . strtolower($ext);
    $path_gambar = $folder . $gambar_baru;

    if (move_uploaded_file($tmp, $path_gambar)) {
        $stmt = $conn->prepare("INSERT INTO menu (nama_produk, harga, gambar, catatan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $harga, $gambar_baru, $catatan);

        if ($stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Produk berhasil ditambahkan!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => window.location = 'dashboard.php');
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Tidak dapat menambahkan produk ke database!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => window.location = 'dashboard.php');
            </script>";
        }

        $stmt->close();
    } else {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Upload gagal!',
                text: 'Gagal mengupload gambar, coba lagi!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => window.location = 'dashboard.php');
        </script>";
    }
}
?>

</body>
</html>
