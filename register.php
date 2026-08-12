<?php
include "config.php"; // Koneksi ke database
ob_start(); // pastikan output tidak langsung dikirim agar JS bisa tampil

$alert = ""; // variabel untuk menampung SweetAlert JS

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  // Cek apakah username sudah ada
  $check_stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
  $check_stmt->bind_param("s", $username);
  $check_stmt->execute();
  $check_stmt->store_result();

  if ($check_stmt->num_rows > 0) {
    $alert = "
      Swal.fire({
        icon: 'warning',
        title: 'Username sudah terdaftar!',
        text: 'Silakan pilih username lain.',
        confirmButtonColor: '#00a59a'
      });
    ";
  } else if ($password !== $confirm_password) {
    $alert = "
      Swal.fire({
        icon: 'error',
        title: 'Password tidak cocok!',
        text: 'Pastikan konfirmasi password sama dengan password.',
        confirmButtonColor: '#00a59a'
      });
    ";
  } else {
    // Simpan ke database (tanpa hashing untuk contoh)
    $stmt = $conn->prepare("INSERT INTO users (username, password, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
      $alert = "
        Swal.fire({
          icon: 'success',
          title: 'Registrasi Berhasil!',
          text: 'Akun berhasil dibuat, silakan login.',
          confirmButtonColor: '#00a59a'
        }).then(() => {
          window.location.href = 'login.php';
        });
      ";
    } else {
      $alert = "
        Swal.fire({
          icon: 'error',
          title: 'Registrasi Gagal!',
          text: 'Terjadi kesalahan saat menyimpan data.',
          confirmButtonColor: '#00a59a'
        });
      ";
    }
    $stmt->close();
  }

  $check_stmt->close();
  $conn->close();
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Form Register</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
  <link rel="icon" href="img/images-removebg-preview (1).png" type="image/png" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body { font-family: 'Inter', sans-serif; }
    .bg-custom {
      background-image: url('img/Screenshot 2025-05-14 204700.png');
      background-size: cover;
      background-position: center;
      position: relative;
    }
    .bg-custom::before {
      content: "";
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: linear-gradient(to right, rgba(255,244,232,0.4) 30%, rgba(255,244,232,0.9) 90%, rgba(255,244,232,1) 100%);
    }
    .password-container { position: relative; }
    .password-toggle {
      position: absolute;
      right: 15px; top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6b7280;
    }
    .hidden { display: none; }
    .form-container { width: 100%; max-width: 480px; }
    .form-input {
      width: 100%; padding: 18px; border-radius: 14px;
      background-color: #fff; border: none; font-size: 18px;
      letter-spacing: -0.8px; outline: none; transition: background-color 0.3s ease;
    }
    .form-input:focus { background-color: #E0E0E0; }
    .form-button {
      width: 100%; padding: 18px; border-radius: 14px;
      background-color: #00a59a; color: white;
      font-size: 18px; font-weight: bold; letter-spacing: -0.8px;
      transition: background-color 0.3s ease;
    }
    .form-button:hover { background-color: #008e85; }
    .form-title { font-size: 40px; font-weight: bold; letter-spacing: -1.5px; }
    .form-subtitle { font-size: 22px; font-weight: 600; letter-spacing: -1.5px; }
    .form-icon { font-size: 60px; margin-bottom: 16px; }
  </style>
</head>

<body class="flex h-screen">
  <!-- Kiri -->
  <div class="w-1/2 relative bg-custom"></div>

  <!-- Kanan -->
  <div class="w-1/2 flex items-center justify-center" style="background-color: #FFF4E8;">
    <div class="form-container px-6">
      <div class="text-center mb-12">
        <i class="fa-solid fa-user form-icon"></i>
        <h1 class="form-title">Hi, Welcome!</h1>
        <p class="form-subtitle text-black">Register Your Account</p>
      </div>

      <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" class="form-input mb-5" required />

        <div class="mb-5 password-container">
          <input id="password-input" type="password" name="password" placeholder="Password" class="form-input" required />
          <i id="password-toggle" class="fas fa-eye password-toggle hidden"></i>
        </div>

        <div class="mb-5 password-container">
          <input id="confirm-password-input" type="password" name="confirm_password" placeholder="Confirm Password" class="form-input" required />
          <i id="confirm-password-toggle" class="fas fa-eye password-toggle hidden"></i>
        </div>

        <button type="submit" class="form-button">Register</button>
      </form>

      <p class="mt-5 text-center" style="letter-spacing: -0.8px;">
        Back again? <a href="login.php" class="font-bold" style="color: #00a59a;">Let’s log you in!</a>
      </p>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // === SWEETALERT untuk tombol "Let’s log you in!" ===
  document.addEventListener("DOMContentLoaded", function() {
    const loginLink = document.querySelector('a[href="login.php"]');
    if (loginLink) {
      loginLink.addEventListener("click", function(event) {
        event.preventDefault();
        Swal.fire({
          title: 'Sudah Memiliki Akun?',
          text: 'Jika sudah, silakan login sekarang!',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#00a59a',
          cancelButtonColor: '#d33',
          cancelButtonText: 'Batal',
          confirmButtonText: 'Ya, lanjut!'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = 'login.php';
          }
        });
      });
    }

    // === TOGGLE PASSWORD ===
    const passwordInput = document.getElementById('password-input');
    const passwordToggle = document.getElementById('password-toggle');
    const confirmPasswordInput = document.getElementById('confirm-password-input');
    const confirmPasswordToggle = document.getElementById('confirm-password-toggle');

    function setupToggle(input, toggle) {
      toggle.addEventListener('click', () => {
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        toggle.classList.toggle('fa-eye');
        toggle.classList.toggle('fa-eye-slash');
      });

      input.addEventListener('input', () => {
        if (input.value.trim() !== '') {
          toggle.classList.remove('hidden');
        } else {
          toggle.classList.add('hidden');
          input.type = 'password';
          toggle.classList.remove('fa-eye-slash');
          toggle.classList.add('fa-eye');
        }
      });
    }

    if (passwordInput && passwordToggle) setupToggle(passwordInput, passwordToggle);
    if (confirmPasswordInput && confirmPasswordToggle) setupToggle(confirmPasswordInput, confirmPasswordToggle);
  });
</script>

<!-- Tampilkan SweetAlert jika ada -->
<?php if (!empty($alert)): ?>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    <?= $alert ?>
  });
</script>
<?php endif; ?>

</body>
</html>
