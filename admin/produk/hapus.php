<?php

require_once "../../config/database.php";


// ========================================
// AMBIL ID PRODUK
// ========================================

$id = $_GET["id"] ?? 0;


// ========================================
// VALIDASI ID
// ========================================

if (!is_numeric($id) || $id <= 0) {

    die("ID produk tidak valid.");

}


// ========================================
// CEK APAKAH PRODUK ADA
// ========================================

$stmt = $pdo->prepare(
    "SELECT * FROM produk WHERE id = ?"
);

$stmt->execute([$id]);

$produk = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$produk) {

    die("Produk tidak ditemukan.");

}


// ========================================
// HAPUS PRODUK
// ========================================

$stmtDelete = $pdo->prepare(
    "DELETE FROM produk WHERE id = ?"
);

$stmtDelete->execute([$id]);


// ========================================
// KEMBALI KE DAFTAR PRODUK
// ========================================

header("Location: index.php");

exit();

?>