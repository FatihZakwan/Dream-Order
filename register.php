<?php
include "config.php"; // Koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  // Check if username already exists
  $check_stmt = $conn->prepare("SELECT username FROM admin WHERE username = ?");
  $check_stmt->bind_param("s", $username);
  $check_stmt->execute();
  $check_stmt->store_result();

  if ($check_stmt->num_rows > 0) {
    echo "<script>alert('Username sudah terdaftar! Silakan pilih username lain.');</script>";
  } else if ($password !== $confirm_password) {
    echo "<script>alert('Password dan konfirmasi tidak cocok!');</script>";
  } else {
    // Store password directly (not hashed)
    $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
      echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href='login.php';</script>";
    } else {
      echo "<script>alert('Registrasi gagal: " . $stmt->error . "');</script>";
    }
    $stmt->close();
  }
  $check_stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Form Register</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="icon" href="img/images-removebg-preview (1).png" type="image/png" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
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
      background: linear-gradient(to right, rgba(255,255,255,0.4) 30%, rgba(255,255,255,0.9) 90%, rgba(255,255,255,1) 100%);
    }

    /* Animasi shake */
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      20%, 60% { transform: translateX(-10px); }
      40%, 80% { transform: translateX(10px); }
    }
    .shake {
      animation: shake 0.5s;
    }

    /* Animasi sukses (background hijau sebentar) */
    @keyframes success-bg {
      0% { background-color: #34D399; }
      100% { background-color: white; }
    }
    .success {
      animation: success-bg 1s forwards;
    }

    .password-container {
      position: relative;
    }

    .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6b7280;
    }

    .hidden {
      display: none;
    }

    /* Penyesuaian ukuran form agar lebih besar */
    .form-container {
      width: 100%;
      max-width: 480px; /* Lebar form diperbesar dari 400px menjadi 480px */
    }

    .form-input {
      width: 100%;
      padding: 18px; /* Padding diperbesar dari 14px menjadi 18px */
      border-radius: 14px; /* Sudut lebih melengkung */
      background-color: #F0F0F0; /* Warna background input */
      border: none; /* Menghilangkan border */
      font-size: 18px; /* Ukuran font diperbesar dari 16px menjadi 18px */
      letter-spacing: -0.8px;
      outline: none;
      transition: background-color 0.3s ease;
    }

    .form-input:focus {
      background-color: #E0E0E0;
    }

    .form-button {
      width: 100%;
      padding: 18px; /* Padding tombol diperbesar dari 14px menjadi 18px */
      border-radius: 14px; /* Sudut tombol melengkung */
      background-color: #00a59a;
      color: white;
      font-size: 18px; /* Ukuran font diperbesar dari 16px menjadi 18px */
      font-weight: bold;
      letter-spacing: -0.8px;
      transition: background-color 0.3s ease;
    }

    .form-button:hover {
      background-color: #008e85;
    }

    
    .form-container {
      width: 100%;
      max-width: 480px;
    }

    .form-title {
      font-size: 40px; /* Diperbesar dari 32px menjadi 40px */
      font-weight: bold;
      letter-spacing: -1.5px;
    }

    .form-subtitle {
      font-size: 22px; /* Diperbesar dari 18px menjadi 22px */
      font-weight: 600;
      letter-spacing: -1.5px;
    }

    .form-icon {
      font-size: 60px; /* Diperbesar dari 48px menjadi 60px */
      margin-bottom: 16px; /* Margin bawah diperbesar dari 12px menjadi 16px */
    }
  </style>
</head>
<body class="flex h-screen">
  <!-- Kiri: Background -->
  <div class="w-1/2 relative bg-custom"></div>

  <!-- Kanan: Form Register -->
  <!-- Kanan: Form Register -->
  <div class="w-1/2 flex items-center justify-center bg-white">
    <div class="form-container px-6">
      <div class="text-center mb-12"> <!-- Margin bawah diperbesar dari mb-10 menjadi mb-12 -->
        <i class="fa-solid fa-user form-icon"></i>
        <h1 class="form-title">Hi, Welcome!</h1>
        <p class="form-subtitle text-black">Register Your Account</p>
      </div>
      <form method="POST" action="">
        <input 
            type="text" 
            name="username" 
            placeholder="Username" 
            class="form-input mb-5" 
            required 
        />
        <!-- Password -->
        <div class="mb-5 password-container"> <!-- Margin bawah diperbesar dari mb-4 menjadi mb-5 -->
          <input 
            id="password-input"
            type="password" 
            name="password" 
            placeholder="Password" 
            class="form-input"
            required 
          />
          <i id="password-toggle" class="fas fa-eye password-toggle hidden"></i>
        </div>
        <!-- Confirm Password -->
        <div class="mb-5 password-container"> <!-- Margin bawah diperbesar dari mb-4 menjadi mb-5 -->
          <input 
            id="confirm-password-input"
            type="password" 
            name="confirm_password" 
            placeholder="Confirm Password" 
            class="form-input" 
            required 
          />
          <i id="confirm-password-toggle" class="fas fa-eye password-toggle hidden"></i>
        </div>
        <button 
          type="submit"
          class="form-button">
          Register
        </button>
      </form>
      <p class="mt-5 text-center form-text" style="letter-spacing: -0.8px;"> <!-- Margin atas diperbesar dari mt-4 menjadi mt-5 -->
          Back again?
        <a href="login.php" class="font-bold" style="color: #00a59a;"> Let’s log you in!</a>
      </p>
    </div>
  </div>

  <script>
    // Toggle password visibility for password field
    const passwordInput = document.getElementById('password-input');
    const passwordToggle = document.getElementById('password-toggle');
    const confirmPasswordInput = document.getElementById('confirm-password-input');
    const confirmPasswordToggle = document.getElementById('confirm-password-toggle');

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

    // Toggle password visibility for confirm password field
    if (confirmPasswordToggle && confirmPasswordInput) {
      confirmPasswordToggle.addEventListener('click', () => {
        const isPassword = confirmPasswordInput.type === 'password';
        confirmPasswordInput.type = isPassword ? 'text' : 'password';
        confirmPasswordToggle.classList.toggle('fa-eye');
        confirmPasswordToggle.classList.toggle('fa-eye-slash');
      });

      confirmPasswordInput.addEventListener('input', () => {
        if (confirmPasswordInput.value.trim() !== '') {
          confirmPasswordToggle.classList.remove('hidden');
        } else {
          confirmPasswordToggle.classList.add('hidden');
          confirmPasswordInput.type = 'password';
          confirmPasswordToggle.classList.remove('fa-eye-slash');
          confirmPasswordToggle.classList.add('fa-eye');
        }
      });
    }
  </script>
</body>
</html>