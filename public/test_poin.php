<?php

session_start();

require_once "../config/database.php";
require_once "functions/poin.php";


// ========================================
// ID PELANGGAN
// ========================================

$pelanggan_id = 1;


// ========================================
// TES TAMBAH 50 POIN
// ========================================

$hasil = tambahPoin(
    $pdo,
    $pelanggan_id,
    50,
    "Tes sistem poin",
    "TEST-POIN-001"
);


// ========================================
// HASIL
// ========================================

if ($hasil) {

    echo "Poin berhasil ditambahkan.";

} else {

    echo "Poin gagal ditambahkan.";

}