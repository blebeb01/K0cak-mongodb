<?php
session_start();

function logout() {
    // Hancurkan semua data sesi
    session_destroy();

    // Kembali ke halaman utama
    header('Location: index.php');
    exit;
}

// Panggil fungsi logout saat pengguna menekan tombol logout
if (isset($_GET['logout'])) {
    logout();
}
?>
