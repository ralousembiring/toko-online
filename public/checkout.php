<?php

session_start();

require_once "../config/database.php";


// ========================================
// CEK LOGIN
// ========================================

if (!isset($_SESSION["pelanggan_id"])) {

    header("Location: login.php");

    exit();

}


// ========================================
// AMBIL DATA PELANGGAN
// ========================================

$pelanggan_id = (int) $_SESSION["pelanggan_id"];


// ========================================
// CEK KERANJANG
// ========================================

if (empty($_SESSION["cart"])) {

    header("Location: cart.php");

    exit();

}


// ========================================
// AMBIL PRODUK
// ========================================

$ids = array_keys($_SESSION["cart"]);

$placeholders = implode(
    ",",
    array_fill(0, count($ids), "?")
);

$stmt = $pdo->prepare(
    "SELECT * FROM produk
     WHERE id IN ($placeholders)"
);

$stmt->execute($ids);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


$items = [];

$total = 0;


// ========================================
// HITUNG TOTAL
// ========================================

foreach ($products as $product) {

    $jumlah = $_SESSION["cart"][$product["id"]];


    // Pastikan jumlah valid

    $jumlah = (int) $jumlah;


    if ($jumlah <= 0) {

        continue;

    }


    // Pastikan stok mencukupi

    if ($jumlah > $product["stok"]) {

        die(
            "Stok produk " .
            htmlspecialchars($product["nama"]) .
            " tidak mencukupi."
        );

    }


    $subtotal =
        $product["harga"] * $jumlah;


    $total += $subtotal;


    $items[] = [

        "id" => $product["id"],

        "nama" => $product["nama"],

        "harga" => $product["harga"],

        "jumlah" => $jumlah,

        "subtotal" => $subtotal

    ];

}


// ========================================
// CEK PRODUK
// ========================================

if (empty($items)) {

    header("Location: cart.php");

    exit();

}


// ========================================
// PROSES CHECKOUT
// ========================================

$error = "";

$berhasil = false;

$orderId = null;


// ========================================
// DATA FORM
// ========================================

$nama = $_SESSION["pelanggan_nama"] ?? "";

$email = "";

$no_hp = "";

$alamat = "";


// ========================================
// PROSES POST
// ========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $nama = trim($_POST["nama"] ?? "");

    $email = trim($_POST["email"] ?? "");

    $no_hp = trim($_POST["no_hp"] ?? "");

    $alamat = trim($_POST["alamat"] ?? "");


    // ========================================
    // VALIDASI
    // ========================================

    if (
        $nama === "" ||
        $no_hp === "" ||
        $alamat === ""
    ) {

        $error =
            "Nama, nomor HP, dan alamat wajib diisi.";

    } else {


        try {


            // ========================================
            // MULAI TRANSAKSI
            // ========================================

            $pdo->beginTransaction();


            // ========================================
            // INSERT PESANAN
            // ========================================

            $stmtOrder = $pdo->prepare("
                INSERT INTO pesanan
                (
                    pelanggan_id,
                    nama_pelanggan,
                    email,
                    no_hp,
                    alamat,
                    total_harga
                )
                VALUES (?, ?, ?, ?, ?, ?)
            ");


            $stmtOrder->execute([

                $pelanggan_id,

                $nama,

                $email,

                $no_hp,

                $alamat,

                $total

            ]);


            // Ambil ID pesanan

            $orderId = $pdo->lastInsertId();


            // ========================================
            // INSERT DETAIL PESANAN
            // + KURANGI STOK
            // ========================================

            $stmtDetail = $pdo->prepare("
                INSERT INTO detail_pesanan
                (
                    pesanan_id,
                    produk_id,
                    jumlah,
                    harga
                )
                VALUES (?, ?, ?, ?)
            ");


            $stmtStock = $pdo->prepare("
                UPDATE produk
                SET stok = stok - ?
                WHERE id = ?
                AND stok >= ?
            ");


            foreach ($items as $item) {


                // ----------------------------------------
                // DETAIL PESANAN
                // ----------------------------------------

                $stmtDetail->execute([

                    $orderId,

                    $item["id"],

                    $item["jumlah"],

                    $item["harga"]

                ]);


                // ----------------------------------------
                // KURANGI STOK
                // ----------------------------------------

                $stmtStock->execute([

                    $item["jumlah"],

                    $item["id"],

                    $item["jumlah"]

                ]);


                // ----------------------------------------
                // CEK STOK
                // ----------------------------------------

                if ($stmtStock->rowCount() === 0) {

                    throw new Exception(
                        "Stok produk berubah. Silakan coba lagi."
                    );

                }

            }


            // ========================================
            // SIMPAN TRANSAKSI
            // ========================================

            $pdo->commit();


            // ========================================
            // SIMPAN ID PESANAN
            // ========================================

            $_SESSION["last_order_id"] = $orderId;


            // ========================================
            // KOSONGKAN KERANJANG
            // ========================================

            $_SESSION["cart"] = [];


            // ========================================
            // BERHASIL
            // ========================================

            $berhasil = true;


        } catch (Exception $e) {


            // ========================================
            // BATALKAN TRANSAKSI
            // ========================================

            if ($pdo->inTransaction()) {

                $pdo->rollBack();

            }


            $error =
                "Checkout gagal: " .
                $e->getMessage();

        }

    }

}

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Checkout - Toko Online
    </title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6fa;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            padding: 20px;
        }

        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow:
                0 3px 10px rgba(0,0,0,0.06);
        }

        h1 {
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .total {
            font-size: 22px;
            font-weight: bold;
            margin: 25px 0;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
            margin-left: 8px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .success {
            text-align: center;
        }

        .success h1 {
            color: #166534;
        }

    </style>

</head>

<body>


<div class="container">

    <div class="box">


        <?php if ($berhasil): ?>


            <div class="success">

                <h1>
                    🎉 Pesanan Berhasil!
                </h1>

                <p>
                    Terima kasih,
                    <strong>
                        <?= htmlspecialchars($nama) ?>
                    </strong>.
                </p>

                <p>
                    Nomor pesanan kamu:
                </p>

                <h2>
                    #<?= htmlspecialchars($orderId) ?>
                </h2>

                <p>
                    Total pesanan:
                    <strong>
                        Rp<?= number_format(
                            $total,
                            0,
                            ',',
                            '.'
                        ) ?>
                    </strong>
                </p>

                <br>


                <a
                    href="pesanan.php"
                    class="btn btn-primary"
                >
                    📋 Lihat Detail Pesanan
                </a>


                <a
                    href="index.php"
                    class="btn btn-secondary"
                >
                    🛒 Kembali ke Toko
                </a>


            </div>


        <?php else: ?>


            <h1>
                🧾 Checkout
            </h1>


            <?php if ($error !== ""): ?>

                <div class="error">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <div class="total">

                Total:

                Rp<?= number_format(
                    $total,
                    0,
                    ',',
                    '.'
                ) ?>

            </div>


            <form method="POST">


                <div class="form-group">

                    <label for="nama">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="<?= htmlspecialchars($nama) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($email) ?>"
                    >

                </div>


                <div class="form-group">

                    <label for="no_hp">
                        Nomor HP
                    </label>

                    <input
                        type="text"
                        id="no_hp"
                        name="no_hp"
                        value="<?= htmlspecialchars($no_hp) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="alamat">
                        Alamat Pengiriman
                    </label>

                    <textarea
                        id="alamat"
                        name="alamat"
                        required
                    ><?= htmlspecialchars($alamat) ?></textarea>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    🛍️ Buat Pesanan
                </button>


                <a
                    href="cart.php"
                    class="btn btn-secondary"
                >
                    ← Kembali
                </a>


            </form>


        <?php endif; ?>


    </div>

</div>

</body>

</html>