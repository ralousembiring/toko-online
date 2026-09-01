<?php

require_once "../config/database.php";

$id = $_GET["id"] ?? 0;

$stmt = $pdo->prepare(
    "SELECT * FROM produk WHERE id = ?"
);

$stmt->execute([$id]);

$produk = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produk) {
    die("Produk tidak ditemukan.");
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        <?= htmlspecialchars($produk["nama"]) ?>
    </title>

</head>

<body>

    <h1>
        <?= htmlspecialchars($produk["nama"]) ?>
    </h1>

    <p>
        Harga:
        Rp<?= number_format($produk["harga"], 0, ',', '.') ?>
    </p>

    <p>
        Stok:
        <?= $produk["stok"] ?>
    </p>

    <p>
        <?= htmlspecialchars($produk["deskripsi"]) ?>
    </p>

    <br>

    <a href="index.php">
        ← Kembali ke Produk
    </a>

</body>

</html>