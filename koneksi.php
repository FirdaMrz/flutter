<?php
$host = "localhost";     // Nama host database, biasanya "localhost"
$username = "root";      // Username untuk database (default: "root")
$password = "";          // Password untuk database (kosong secara default)
$database = "tokobuku"; // Nama database yang ingin dihubungkan

// Membuat koneksi
$conn = mysqli_connect($host, $username, $password, $database);

// Mengecek apakah koneksi berhasil
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
} else {
     //echo "Koneksi berhasil!";
}
?>

