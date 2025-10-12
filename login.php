<?php
include "config.php";

session_start(); // Start session at the top

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Query to check username and password (plaintext, consistent with register.php)
  $query = "SELECT * FROM admin WHERE username = ? AND password = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $_SESSION['username'] = $username;
    echo "<script>alert('Login berhasil! Mengalihkan ke dashboard...'); window.location.href='dashboard.php';</script>";
  } else {
    echo "<script>alert('Username atau password salah!');</script>";
  }

  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Account</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="icon" href="img/images-removebg-preview (1).png" type="image/png" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    .bg-right {
      background-image: url('img/Group 54.png');
      background-size: cover;
      background-position: center;
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
    .form-container {
      width: 100%;
      max-width: 480px;
    }
    .form-input {
      width: 100%;
      padding: 18px;
      border-radius: 14px;
      background-color: #F0F0F0;
      border: none;
      font-size: 18px;
      letter-spacing: -0.8px;
      outline: none;
      transition: background-color 0.3s ease;
    }
    .form-input:focus {
      background-color: #E0E0E0;
    }
    .form-button {
      width: 100%;
      padding: 18px;
      border-radius: 14px;
      background-color: #00a59a;
      color: white;
      font-size: 18px;
      font-weight: bold;
      letter-spacing: -0.8px;
      transition: background-color 0.3s ease;
    }
    .form-button:hover {
      background-color: #008e85;
    }
    .form-text {
      font-size: 16px;
      color: #000;
      letter-spacing: -0.8px;
    }
    .form-title {
      font-size: 40px;
      font-weight: bold;
      letter-spacing: -1.5px;
    }
    .form-subtitle {
      font-size: 22px;
      font-weight: 600;
      letter-spacing: -0.8px;
    }
    .form-icon {
      font-size: 60px;
      margin-bottom: 16px;
    }
  </style>
</head>
<body class="flex h-screen">
  <!-- Left Side: Form -->
  <div class="w-1/2 flex items-center justify-center bg-white">
    <div class="form-container px-6">
      <div class="text-center mb-12">
        <i class="fas fa-user form-icon"></i>
        <h1 class="form-title">Welcome Back!</h1>
        <p class="form-subtitle text-black" style="letter-spacing: -1.5px;">Login Account</p>
      </div>
      <form id="login-form" method="POST" action="">
        <div class="mb-5">
          <input 
            id="username-input" 
            type="text" 
            name="username" 
            placeholder="Username" 
            required
            class="form-input" />
        </div>
        <div class="mb-5 password-container">
          <input 
            id="password-input" 
            type="password" 
            name="password" 
            placeholder="Password" 
            required
            class="form-input" />
          <i id="password-toggle" class="fas fa-eye password-toggle hidden"></i>
        </div>
        <button 
          id="login-button" 
          type="submit"
          class="form-button">
          Login
        </button>
      </form>
      <p class="mt-5 text-center form-text">
        First time here?
        <a href="register.php" class="font-bold" style="color: #00a59a;"> Let’s get you signed up!</a>
      </p>
    </div>
  </div>
  <!-- Right Side: Image -->
  <div class="w-1/2 bg-right"></div>

  <script>
    // Password toggle functionality
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
  </script>
</body>
</html>