<?php

session_start();


// ========================================
// CEK LOGIN ADMIN
// ========================================

if (!isset($_SESSION["admin_id"])) {

    header("Location: ../login.php");

    exit();

}


require_once "../../config/database.php";


// ========================================
// AMBIL ID PESANAN
// ========================================

$id = (int) ($_GET["id"] ?? 0);


if ($id <= 0) {

    header("Location: index.php");

    exit();

}


// ========================================
// PROSES UPDATE STATUS
// ========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $statusBaru = $_POST["status"] ?? "";


    $statusValid = [
        "Menunggu",
        "Diproses",
        "Dikirim",
        "Selesai",
        "Dibatalkan"
    ];


    // ========================================
    // CEK STATUS VALID
    // ========================================

    if (in_array($statusBaru, $statusValid, true)) {

        try {

            // ========================================
            // MULAI TRANSAKSI
            // ========================================

            $pdo->beginTransaction();


            // ========================================
            // AMBIL DATA PESANAN TERBARU
            // ========================================

            $stmtOrder = $pdo->prepare("
                SELECT
                    id,
                    pelanggan_id,
                    total_harga,
                    status
                FROM pesanan
                WHERE id = ?
                FOR UPDATE
            ");

            $stmtOrder->execute([$id]);

            $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);


            if (!$order) {

                throw new Exception(
                    "Pesanan tidak ditemukan."
                );

            }


            $statusLama = $order["status"];

            $pelangganId = $order["pelanggan_id"];

            $totalHarga = (float) $order["total_harga"];


            // ========================================
            // UPDATE STATUS PESANAN
            // ========================================

            $stmtUpdate = $pdo->prepare("
                UPDATE pesanan
                SET status = ?
                WHERE id = ?
            ");

            $stmtUpdate->execute([
                $statusBaru,
                $id
            ]);


            // ========================================
            // SISTEM POIN
            // ========================================
            //
            // Poin diberikan HANYA ketika:
            //
            // status lama  != Selesai
            // status baru  = Selesai
            //
            // Rp10.000 = 1 poin
            //
            // ========================================

            if (
                $statusLama !== "Selesai" &&
                $statusBaru === "Selesai" &&
                !empty($pelangganId)
            ) {


                // ========================================
                // HITUNG POIN
                // ========================================

                $poinDidapat = (int) floor(
                    $totalHarga / 10000
                );


                // ========================================
                // HANYA LANJUT JIKA POIN > 0
                // ========================================

                if ($poinDidapat > 0) {


                    // ========================================
                    // REFERENSI UNIK PESANAN
                    // ========================================

                    $referensi =
                        "PESANAN-" . $id;


                    // ========================================
                    // CEK APAKAH SUDAH PERNAH DAPAT POIN
                    // ========================================

                    $stmtCekPoin = $pdo->prepare("
                        SELECT id
                        FROM riwayat_poin
                        WHERE referensi = ?
                        LIMIT 1
                    ");

                    $stmtCekPoin->execute([
                        $referensi
                    ]);

                    $poinSudahAda =
                        $stmtCekPoin->fetch(
                            PDO::FETCH_ASSOC
                        );


                    // ========================================
                    // JIKA BELUM ADA → TAMBAHKAN
                    // ========================================

                    if (!$poinSudahAda) {


                        // ========================================
                        // TAMBAH POIN PELANGGAN
                        // ========================================

                        $stmtTambahPoin = $pdo->prepare("
                            UPDATE pelanggan
                            SET poin = poin + ?
                            WHERE id = ?
                        ");

                        $stmtTambahPoin->execute([
                            $poinDidapat,
                            $pelangganId
                        ]);


                        // ========================================
                        // SIMPAN RIWAYAT POIN
                        // ========================================

                        $stmtRiwayat = $pdo->prepare("
                            INSERT INTO riwayat_poin
                            (
                                pelanggan_id,
                                jumlah,
                                tipe,
                                keterangan,
                                referensi
                            )
                            VALUES (?, ?, ?, ?, ?)
                        ");

                        $stmtRiwayat->execute([

                            $pelangganId,

                            $poinDidapat,

                            "masuk",

                            "Poin dari pesanan #" . $id,

                            $referensi

                        ]);

                    }

                }

            }


            // ========================================
            // SELESAI
            // ========================================

            $pdo->commit();


        } catch (Exception $e) {


            // ========================================
            // BATALKAN TRANSAKSI
            // ========================================

            if ($pdo->inTransaction()) {

                $pdo->rollBack();

            }


            die(
                "Gagal memperbarui pesanan: " .
                htmlspecialchars(
                    $e->getMessage()
                )
            );

        }

    }


    // ========================================
    // KEMBALI KE DETAIL PESANAN
    // ========================================

    header(
        "Location: detail.php?id=" . $id
    );

    exit();

}


// ========================================
// AMBIL DATA PESANAN
// ========================================

$stmt = $pdo->prepare("
    SELECT *
    FROM pesanan
    WHERE id = ?
");

$stmt->execute([$id]);

$pesanan = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$pesanan) {

    die("Pesanan tidak ditemukan.");

}


// ========================================
// AMBIL DETAIL PRODUK
// ========================================

$stmt = $pdo->prepare("
    SELECT
        detail_pesanan.*,
        produk.nama AS nama_produk
    FROM detail_pesanan
    INNER JOIN produk
        ON produk.id = detail_pesanan.produk_id
    WHERE detail_pesanan.pesanan_id = ?
    ORDER BY detail_pesanan.id ASC
");

$stmt->execute([$id]);

$detail = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        Detail Pesanan #<?= $pesanan["id"] ?>
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }


        .navbar {
            background: #1f2937;
            color: white;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .navbar a {
            color: white;
            text-decoration: none;
        }


        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 20px;
        }


        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }


        .header h1 {
            margin: 0;
        }


        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }


        .btn-back {
            background: #6b7280;
            color: white;
        }


        .box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow:
                0 3px 10px rgba(0,0,0,0.05);
        }


        .box h2 {
            margin-top: 0;
        }


        .info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }


        .info-item {
            padding: 15px;
            background: #f8fafc;
            border-radius: 7px;
        }


        .info-item strong {
            display: block;
            margin-bottom: 5px;
            color: #6b7280;
        }


        table {
            width: 100%;
            border-collapse: collapse;
        }


        th,
        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }


        th {
            background: #f8fafc;
        }


        .total {
            text-align: right;
            font-size: 22px;
            font-weight: bold;
            margin-top: 20px;
        }


        .status-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }


        select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }


        .btn-update {
            background: #2563eb;
            color: white;
        }


        .status {
            padding: 7px 12px;
            border-radius: 20px;
            display: inline-block;
            font-size: 13px;
        }


        .menunggu {
            background: #fef3c7;
            color: #92400e;
        }


        .diproses {
            background: #dbeafe;
            color: #1e40af;
        }


        .dikirim {
            background: #e0e7ff;
            color: #3730a3;
        }


        .selesai {
            background: #dcfce7;
            color: #166534;
        }


        .dibatalkan {
            background: #fee2e2;
            color: #991b1b;
        }


        .poin-info {
            margin-top: 15px;
            padding: 15px;
            background: #f0fdf4;
            color: #166534;
            border-radius: 7px;
        }


        @media (max-width: 700px) {

            .info {
                grid-template-columns: 1fr;
            }

            .status-form {
                flex-direction: column;
                align-items: stretch;
            }

        }

    </style>

</head>


<body>


<!-- ========================================
     NAVBAR
======================================== -->

<div class="navbar">

    <strong>
        🛒 Admin Toko Online
    </strong>


    <a href="../index.php">
        ← Dashboard
    </a>

</div>



<div class="container">


    <!-- ====================================
         HEADER
    ===================================== -->

    <div class="header">

        <h1>
            🧾 Pesanan #<?= $pesanan["id"] ?>
        </h1>


        <a
            href="index.php"
            class="btn btn-back"
        >
            ← Kembali
        </a>

    </div>



    <!-- ====================================
         INFORMASI PELANGGAN
    ===================================== -->

    <div class="box">

        <h2>
            👤 Informasi Pelanggan
        </h2>


        <div class="info">


            <div class="info-item">

                <strong>
                    ID Pelanggan
                </strong>

                <?= $pesanan["pelanggan_id"]
                    ? htmlspecialchars(
                        $pesanan["pelanggan_id"]
                    )
                    : "-"
                ?>

            </div>


            <div class="info-item">

                <strong>
                    Nama
                </strong>

                <?= htmlspecialchars(
                    $pesanan["nama_pelanggan"]
                ) ?>

            </div>


            <div class="info-item">

                <strong>
                    No. HP
                </strong>

                <?= htmlspecialchars(
                    $pesanan["no_hp"]
                ) ?>

            </div>


            <div class="info-item">

                <strong>
                    Email
                </strong>

                <?= htmlspecialchars(
                    $pesanan["email"] ?: "-"
                ) ?>

            </div>


            <div class="info-item">

                <strong>
                    Tanggal Pesanan
                </strong>

                <?= date(
                    "d-m-Y H:i",
                    strtotime(
                        $pesanan["tanggal_pesanan"]
                    )
                ) ?>

            </div>


            <div
                class="info-item"
                style="grid-column: 1 / -1;"
            >

                <strong>
                    Alamat Pengiriman
                </strong>

                <?= nl2br(
                    htmlspecialchars(
                        $pesanan["alamat"]
                    )
                ) ?>

            </div>


        </div>

    </div>



    <!-- ====================================
         PRODUK
    ===================================== -->

    <div class="box">

        <h2>
            📦 Produk Pesanan
        </h2>


        <table>

            <thead>

                <tr>

                    <th>
                        Produk
                    </th>

                    <th>
                        Harga
                    </th>

                    <th>
                        Jumlah
                    </th>

                    <th>
                        Subtotal
                    </th>

                </tr>

            </thead>


            <tbody>


                <?php foreach ($detail as $item): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                $item["nama_produk"]
                            ) ?>

                        </td>


                        <td>

                            Rp<?= number_format(
                                $item["harga"],
                                0,
                                ',',
                                '.'
                            ) ?>

                        </td>


                        <td>

                            <?= $item["jumlah"] ?>

                        </td>


                        <td>

                            Rp<?= number_format(
                                $item["harga"]
                                * $item["jumlah"],
                                0,
                                ',',
                                '.'
                            ) ?>

                        </td>

                    </tr>

                <?php endforeach; ?>


            </tbody>

        </table>


        <div class="total">

            Total:

            Rp<?= number_format(
                $pesanan["total_harga"],
                0,
                ',',
                '.'
            ) ?>

        </div>


        <?php if (!empty($pesanan["pelanggan_id"])): ?>

            <div class="poin-info">

                ⭐

                Jika pesanan menjadi

                <strong>Selesai</strong>,

                pelanggan akan mendapatkan

                <strong>
                    <?= (int) floor(
                        $pesanan["total_harga"] / 10000
                    ) ?>
                    poin
                </strong>.

                <br>

                <small>
                    Sistem poin:
                    Rp10.000 = 1 poin.
                </small>

            </div>

        <?php endif; ?>


    </div>



    <!-- ====================================
         UPDATE STATUS
    ===================================== -->

    <div class="box">

        <h2>
            🔄 Status Pesanan
        </h2>


        <p>

            Status sekarang:

            <span
                class="status
                <?= strtolower(
                    str_replace(
                        " ",
                        "",
                        $pesanan["status"]
                    )
                ) ?>"
            >

                <?= htmlspecialchars(
                    $pesanan["status"]
                ) ?>

            </span>

        </p>


        <form
            method="POST"
            class="status-form"
        >


            <select name="status">


                <option
                    value="Menunggu"
                    <?= $pesanan["status"] === "Menunggu"
                        ? "selected"
                        : "" ?>
                >
                    Menunggu
                </option>


                <option
                    value="Diproses"
                    <?= $pesanan["status"] === "Diproses"
                        ? "selected"
                        : "" ?>
                >
                    Diproses
                </option>


                <option
                    value="Dikirim"
                    <?= $pesanan["status"] === "Dikirim"
                        ? "selected"
                        : "" ?>
                >
                    Dikirim
                </option>


                <option
                    value="Selesai"
                    <?= $pesanan["status"] === "Selesai"
                        ? "selected"
                        : "" ?>
                >
                    Selesai
                </option>


                <option
                    value="Dibatalkan"
                    <?= $pesanan["status"] === "Dibatalkan"
                        ? "selected"
                        : "" ?>
                >
                    Dibatalkan
                </option>


            </select>


            <button
                type="submit"
                class="btn btn-update"
            >
                💾 Update Status
            </button>


        </form>


    </div>


</div>


</body>

</html>