<?php
session_start();
include "config.php";

if (!isset($_POST['cart_ids'])) {
    echo "<p>Tidak ada item yang dipilih. <a href='cart.php'>Kembali ke cart</a></p>";
    exit;
}

$selected_ids = $_POST['cart_ids'];
$cart = $_SESSION['cart'] ?? [];
$order_items = [];

$total = 0;
foreach ($cart as $item) {
    if (in_array($item['id'], $selected_ids)) {
        $subtotal = $item['harga'] * $item['qty'];
        $total += $subtotal;
        $order_items[] = [
            'id' => $item['id'],
            'nama_produk' => $item['nama_produk'],
            'harga' => $item['harga'],
            'qty' => $item['qty'],
            'subtotal' => $subtotal
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - Dream Order</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FFF6ED] font-sans">

<div class="container mx-auto p-6">
<h1 class="text-3xl font-bold text-gray-800 mb-6">Checkout</h1>

<table class="w-full bg-white rounded-xl shadow-md overflow-hidden">
<thead class="bg-[#00A59A] text-white">
<tr>
<th class="p-3 text-left">Nama Produk</th>
<th class="p-3 text-center">Harga</th>
<th class="p-3 text-center">Qty</th>
<th class="p-3 text-center">Subtotal</th>
</tr>
</thead>
<tbody>
<?php foreach ($order_items as $item): ?>
<tr class="border-b">
<td class="p-3"><?= htmlspecialchars($item['nama_produk']); ?></td>
<td class="p-3 text-center">Rp <?= number_format($item['harga'],0,'','.'); ?></td>
<td class="p-3 text-center"><?= $item['qty']; ?></td>
<td class="p-3 text-center font-bold text-[#F7931E]">Rp <?= number_format($item['subtotal'],0,'','.'); ?></td>
</tr>
<?php endforeach; ?>
<tr class="font-bold bg-gray-100">
<td class="p-3 text-right" colspan="3">Total</td>
<td class="p-3 text-center text-[#00A59A]">Rp <?= number_format($total,0,'','.'); ?></td>
</tr>
</tbody>
</table>

<div class="mt-6">
<a href="cart.php" class="px-6 py-3 bg-[#00A59A] text-white rounded-full font-semibold hover:bg-[#008C83]">Kembali ke
