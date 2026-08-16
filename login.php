<?php
include "config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];

  ob_start(); // cegah halaman putih

  // Check admin login
  $admin_query = "SELECT * FROM admin WHERE username = ? AND password = ?";
  $admin_stmt = $conn->prepare($admin_query);
  $admin_stmt->bind_param("ss", $username, $password);
  $admin_stmt->execute();
  $admin_result = $admin_stmt->get_result();

  if ($admin_result->num_rows === 1) {
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'admin';
    echo "<script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: 'success',
          title: 'Login Berhasil!',
          text: 'Login berhasil sebagai admin, mengalihkan ke dashboard...',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        }).then(() => {
          window.location.href = 'dashboard.php';
        });
      });
    </script>";
  } else {
    // Check user login
    $user_query = 'SELECT * FROM users WHERE username = ? AND password = ?';
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param('ss', $username, $password);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows === 1) {
      $_SESSION['username'] = $username;
      $_SESSION['role'] = 'user';
      echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
          Swal.fire({
            icon: 'success',
            title: 'Login Berhasil!',
            text: 'Login berhasil , mengalihkan ke home page...',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          }).then(() => {
            window.location.href = 'home_page.php';
          });
        });
      </script>";
    } else {
      echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
          Swal.fire({
            icon: 'error',
            title: 'Login Gagal!',
            text: 'Username atau password salah!',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
        });
      </script>";
    }

    $user_stmt->close();
  }

  $admin_stmt->close();
  $conn->close();

  ob_end_flush();
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Account</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <link rel="icon" href="img/images-removebg-preview (1).png" type="image/png" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    .bg-right { background-image: url('img/Group 51.png'); background-size: cover; background-position: center; }
    .password-container { position: relative; }
    .password-toggle {
      position: absolute; right: 15px; top: 50%; transform: translateY(-50%);
      cursor: pointer; color: #6b7280;
    }
    .hidden { display: none; }
    .form-container { width: 100%; max-width: 480px; }
    .form-input {
      width: 100%; padding: 18px; border-radius: 14px;
      background-color: #FFFF; border: none; font-size: 18px;
      letter-spacing: -0.8px; outline: none; transition: background-color 0.3s ease;
    }
    .form-input:focus { background-color: #E0E0E0; }
    .form-button {
      width: 100%; padding: 18px; border-radius: 14px;
      background-color: #00a59a; color: white; font-size: 18px;
      font-weight: bold; letter-spacing: -0.8px; transition: background-color 0.3s ease;
    }
    .form-button:hover { background-color: #008e85; }
    .form-text { font-size: 16px; color: #000; letter-spacing: -0.8px; }
    .form-title { font-size: 40px; font-weight: bold; letter-spacing: -1.5px; }
    .form-subtitle { font-size: 22px; font-weight: 600; letter-spacing: -0.8px; }
    .form-icon { font-size: 60px; margin-bottom: 16px; }
  </style>
</head>
<body class="flex h-screen">
  <!-- Left Side -->
  <div class="w-1/2 flex items-center justify-center " style="background-color: #FFF4E8;">
    <div class="form-container px-6">
      <div class="text-center mb-12">
        <i class="fas fa-user form-icon"></i>
        <h1 class="form-title">Welcome Back!</h1>
        <p class="form-subtitle text-black" style="letter-spacing: -1.5px;">Login Account</p>
      </div>
      <form id="login-form" method="POST" action="">
        <div class="mb-5">
          <input id="username-input" type="text" name="username" placeholder="Username" required class="form-input" />
        </div>
        <div class="mb-5 password-container">
          <input id="password-input" type="password" name="password" placeholder="Password" required class="form-input" />
          <i id="password-toggle" class="fas fa-eye password-toggle hidden"></i>
        </div>
        <button id="login-button" type="submit" class="form-button">Login</button>
      </form>
      <p class="mt-5 text-center form-text">
        First time here?
        <a href="register.php" class="font-bold" style="color: #00a59a;"> Let’s get you signed up!</a>
      </p>
    </div>
  </div>
  <div class="w-1/2 bg-right"></div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    // === SWEETALERT UNTUK LINK REGISTER ===
    const registerLink = document.querySelector('a[href="register.php"]');
    if (registerLink) {
      registerLink.addEventListener("click", function(event) {
        event.preventDefault();
        Swal.fire({
          title: 'Belum Punya Akun?',
          text: 'Ayo daftar sekarang!',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#00a59a',
          cancelButtonColor: '#d33',
          cancelButtonText: 'Batal',
          confirmButtonText: 'Ya, daftar sekarang!'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = 'register.php';
          }
        });
      });
    }

    // === PASSWORD TOGGLE ===
    const passwordInput = document.getElementById('password-input');
    const passwordToggle = document.getElementById('password-toggle');

    if (passwordToggle && passwordInput) {
      passwordToggle.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        passwordToggle.classList.toggle('fa-eye');
        passwordToggle.classList.toggle('fa-eye-slash');
      });

      passwordInput.addEventListener('input', () => {
        if (passwordInput.value.trim() !== '') {
          passwordToggle.classList.remove('hidden');
        } else {
          passwordToggle.classList.add('hidden');
          passwordInput.type = 'password';
          passwordToggle.classList.remove('fa-eye-slash');
          passwordToggle.classList.add('fa-eye');
        }
      });
    }
  });
</script>

</body>
</html>
