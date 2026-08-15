<?php
include("../config.php"); // koneksi ke database
include("../qrcodes/phpqrcode/qrlib.php"); // library QR code

// ambil data dari database
$result = $conn->query("SELECT * FROM stands");

echo "<h1>Generate QR Code Stand</h1>";

while($row = $result->fetch_assoc()){
    $standName = $row['stand_name'];
    $url = $row['stand_url'];

    $filename = "qrcode_" . str_replace(" ", "_", $standName) . ".png";

    if(!file_exists($filename)){
        QRcode::png($url, $filename, QR_ECLEVEL_L, 5);
    }

    echo "<h3>$standName</h3>";
    echo "<img src='$filename'><br>";
    echo "<a href='$url' target='_blank'>$url</a><br><br>";
}
?>
