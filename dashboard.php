<?php
include "config.php";
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit();
}

// Dummy data pelanggan dengan pesanan
$customers = [
    [
        "name" => "Iwan Setiawan",
        "orders" => [
            ["item" => "Jenin's Sunshine", "qty" => 2],
            ["item" => "Es Kepal Catur", "qty" => 3],
            ["item" => "Jenin's Sunshine", "qty" => 2]
        ]
    ],
    ["name" => "Sahid bin Suhad", "orders" => []],
    ["name" => "Pepep Botak", "orders" => []],
    ["name" => "Ngabdi Tidur", "orders" => []],
    ["name" => "Ngoki Mer", "orders" => []],
    ["name" => "Prean Ros", "orders" => []],
    ["name" => "Petrutnas", "orders" => []]
];

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    echo "<script>alert('Logout berhasil!'); window.location.href='login.php';</script>";
    exit();
}

// Jika form produk dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $image_url = '';

    if (isset($_FILES['product_photo']) && $_FILES['product_photo']['error'] === 0) {
        $target_dir = "Uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $imageFileName = basename($_FILES["product_photo"]["name"]);
        $imageFileType = strtolower(pathinfo($imageFileName, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            $unique_name = uniqid('product_') . '.' . $imageFileType;
            $target_file = $target_dir . $unique_name;
            if (move_uploaded_file($_FILES["product_photo"]["tmp_name"], $target_file)) {
                $image_url = $target_file;
            } else {
                echo "<script>alert('Upload gambar gagal!');</script>";
            }
        } else {
            echo "<script>alert('Hanya file JPG, JPEG, PNG, dan GIF yang diizinkan.');</script>";
        }
    }

    $stmt = $conn->prepare("INSERT INTO menu (nama_menu, harga, image_url) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $product_name, $product_price, $image_url);
    if ($stmt->execute()) {
        echo "<script>alert('Produk $product_name berhasil ditambahkan!'); window.location.href='home_page.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan produk: " . $conn->error . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="icon" href="img/images-removebg-preview (1).png" type="image/png" />
    <style>
        body {
            background-color: #F3F3F3;
        }
        .bg-orange-custom {
            background-color: #F6921F;
        }
        .bg-teal-custom {
            background-color: #26A69A;
        }
        .text-teal-custom {
            color: #26A69A;
        }
        .dashboard-container {
            display: flex;
            gap: 20px; /* Jarak antar elemen kiri dan kanan */
            margin: 20px;
            height: calc(100vh - 64px);
        }
        .customer-list, .add-product {
            width: 50%;
            padding: 0;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Shadow */
            outline: 1px solid #d1d5db; /* Outline */
        }
        .customer-list {
            overflow-y: auto;
        }
        .customer-list h2 {
            margin-top: 0;
        }
        .customer-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
            outline: 1px solid #d1d5db;
            min-height: 60px;
        }
        .customer-item:last-child {
            border-bottom: none;
        }
        .customer-number {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            margin-right: 10px;
            font-weight: bold;
        }
        .no-customer-container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .text-gray-500 {
            font-size: 16px;
            font-style: italic;
            color: #6b7280;
        }
        .text-sm {
            font-size: 14px;
        }
        .add-product {
            padding: 0;
        }
        .photo-upload {
            border: 2px dashed #d1d5db;
            height: 200px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            background-color: #f3f4f6;
            position: relative;
            border-radius: 10px;
            outline: 1px solid #d1d5db;
        }
        .photo-upload img {
            width: 92%;
            height: 92%;
            object-fit: cover;
            border-radius: 10px;
        }
        .photo-upload-label {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
            color: #6b7280;
        }
        .photo-upload input[type="file"] {
            opacity: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .form-container {
            padding: 20px;
        }
        input[type="text"],
        input[type="number"] {
            border: 1px solid #d1d5db;
            border-radius: 5px;
            padding: 10px;
            font-size: 14px;
            color: #6b7280;
            outline: 1px solid #d1d5db;
        }
        input[type="text"]::placeholder,
        input[type="number"]::placeholder {
            color: #6b7280;
        }
        label {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
        }
        button[type="submit"] {
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 14px;
        }
        header {
            padding: 10px 20px;
        }
        header nav a {
            font-size: 16px;
            padding: 0 10px;
        }
        header .bg-teal-custom {
            border-radius: 20px;
            padding: 8px 20px;
            font-size: 14px;
        }
        .fa-image {
            font-size: 40px;
        }
        .fa-logo {
            font-size: 80px;
            color: #26A69A;
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md p-4 flex justify-between items-center">
        <div class="flex items-center">
            <img src="img/logo2.png" alt="Logo" class="h-12 mr-4" />
            <nav>
                <a href="home_page.php" class="text-gray-600 mx-2 hover:text-gray-800">Home</a>
                <a href="#" class="text-gray-600 mx-2 hover:text-gray-800">Order Status</a>
                <a href="#" class="text-gray-600 mx-2 font-bold hover:text-gray-800">Dashboard</a>
                <a href="#" class="text-gray-600 mx-2 hover:text-gray-800">Help</a>
            </nav>
        </div>
        <a href="?logout=true" class="bg-teal-custom text-white px-4 py-2 rounded-full hover:bg-teal-600 transition duration-300">Logout</a>
    </header>

    <div class="dashboard-container">
        <div class="customer-list">
            <h2 class="text-xl font-bold text-white bg-orange-custom p-4 rounded-t-lg">Customer Names</h2>
            <?php if (count($customers) > 0): ?>
                <?php foreach ($customers as $index => $customer): ?>
                    <div class="customer-item">
                        <div class="flex items-center">
                            <span class="customer-number bg-orange-custom text-white"><?php echo $index + 1; ?></span>
                            <span class="ml-2"><?php echo htmlspecialchars($customer['name']); ?></span>
                            <?php if (!empty($customer['orders'])): ?>
                                <div class="ml-4 text-sm">
                                    <?php foreach ($customer['orders'] as $order): ?>
                                        <div>Order: <?php echo $order['item']; ?> | Qty: <?php echo $order['qty']; ?></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-customer-container">
                    <p class="text-gray-500">No more customer...</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="add-product">
            <h2 class="text-xl font-bold text-white bg-orange-custom p-4 rounded-t-lg">Add Product</h2>
            <!-- Form Upload -->
            <form method="POST" action="" enctype="multipart/form-data" class="form-container">
                <div class="photo-upload" id="photo-upload">
                    <label for="product-photo" class="photo-upload-label text-gray-500 flex flex-col items-center justify-center space-y-2" id="photo-label">
                        <i class="fa-solid fa-image fa-3x"></i>
                        <span>+Add Photo For Product</span>
                    </label>
                    <input type="file" id="product-photo" name="product_photo" accept="image/*" />
                    <div id="preview" class="mt-2"></div>
                </div>

                <div class="mb-4">
                    <label for="product-name" class="block text-gray-700 font-semibold mb-2">Product Name</label>
                    <input 
                        type="text" 
                        id="product-name" 
                        name="product_name" 
                        placeholder="Enter your product name..." 
                        class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500" 
                        required 
                    />
                </div>
                <div class="mb-4">
                    <label for="product-price" class="block text-gray-700 font-semibold mb-2">Product Price</label>
                    <input 
                        type="number" 
                        id="product-price" 
                        name="product_price" 
                        placeholder="Enter your product price" 
                        class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500" 
                        required 
                    />
                </div>
                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="bg-teal-custom text-white px-4 py-2 rounded hover:bg-teal-600 transition duration-300 flex items-center"
                    >
                        <span class="mr-2">+</span> Add
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Preview -->
    <script>
    document.getElementById('product-photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('preview');
        const label = document.getElementById('photo-label');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('rounded', 'w-32', 'h-32', 'object-cover', 'border');

                preview.innerHTML = '';
                preview.appendChild(img);
                label.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            label.style.display = 'block';
            preview.innerHTML = '';
        }
    });
    </script>
</body>
</html>